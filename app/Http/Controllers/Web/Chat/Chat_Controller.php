<?php

namespace App\Http\Controllers\Web\Chat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use App\Models\Chat\Chat_Conversation;
use App\Models\Chat\Chat_Message;
use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Account_Information;
use App\Events\CMS\ChatMessageSent;
use App\Events\CMS\ChatUserTyping;

use Carbon\Carbon;

class Chat_Controller extends Controller
{
	public function __construct()
	{
		date_default_timezone_set('Asia/Jakarta');
	}

	// ─────────────────────────────────────────────────────────
	// PAGE — Render halaman chat
	// ─────────────────────────────────────────────────────────

	public function index()
	{
		return view('chat.chat');
	}

	// ─────────────────────────────────────────────────────────
	// API — Daftar semua user (untuk sidebar "New Chat")
	// ─────────────────────────────────────────────────────────

	public function users(Request $request)
	{
		$me = auth()->user();

		$search = $request->input('search', '');

		$users = Account::where('id', '!=', $me->id)
			->where('status', 1) // status 1 = Active (sesuai account_status seeder)
			->when($search, function ($q) use ($search) {
				$q->where(function ($q2) use ($search) {
					$q2->where('fullname', 'like', "%{$search}%")
					   ->orWhere('username', 'like', "%{$search}%")
					   ->orWhere('email', 'like', "%{$search}%");
				});
			})
			->with('information')
			->orderBy('fullname')
			->limit(30)
			->get()
			->map(fn($u) => $this->formatUser($u, $me->id));

		return response()->json(['success' => true, 'data' => $users]);
	}

	// ─────────────────────────────────────────────────────────
	// API — Daftar conversation milik auth user
	// ─────────────────────────────────────────────────────────

	public function conversations()
	{
		$me = auth()->user();

		$conversations = Chat_Conversation::where('user_one_id', $me->id)
			->orWhere('user_two_id', $me->id)
			->with(['userOne.information', 'userTwo.information', 'lastMessage'])
			->orderByDesc('last_activity_at')
			->get()
			->map(function ($conv) use ($me) {
				$other   = $conv->getOtherUser($me->id);
				$unread  = $conv->unreadCountFor($me->id);
				$last    = $conv->lastMessage;

				return [
					'id'           => $conv->id,
					'other_user'   => $this->formatUser($other, $me->id),
					'last_message' => $last ? [
						'body'       => $last->body,
						'type'       => $last->type,
						'sender_id'  => $last->sender_id,
						'created_at' => $last->created_at->format('H:i'),
					] : null,
					'unread_count'      => $unread,
					'last_activity_at'  => $conv->last_activity_at?->toDateTimeString(),
				];
			});

		return response()->json(['success' => true, 'data' => $conversations]);
	}

	// ─────────────────────────────────────────────────────────
	// API — Buka / mulai conversation dengan user tertentu
	// ─────────────────────────────────────────────────────────

	public function openConversation(Request $request)
	{
		$me     = auth()->user();
		$userId = (int) $request->input('user_id');

		if ($userId === $me->id) {
			return response()->json(['success' => false, 'message' => 'Tidak bisa chat dengan diri sendiri.'], 422);
		}

		$other = Account::find($userId);
		if (!$other) {
			return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
		}

		$conv = Chat_Conversation::findOrCreateBetween($me->id, $userId);

		$other->load('information');

		return response()->json([
			'success' => true,
			'data'    => [
				'conversation_id' => $conv->id,
				'other_user'      => $this->formatUser($other, $me->id),
			],
		]);
	}

	// ─────────────────────────────────────────────────────────
	// API — Ambil pesan dalam sebuah conversation (paginated)
	// ─────────────────────────────────────────────────────────

	public function messages(Request $request, int $conversationId)
	{
		$me   = auth()->user();
		$conv = $this->authorizeConversation($conversationId, $me->id);

		if (!$conv) {
			return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
		}

		// Mark as read — semua pesan dari lawan bicara yang belum dibaca
		Chat_Message::where('conversation_id', $conversationId)
			->where('sender_id', '!=', $me->id)
			->whereNull('read_at')
			->update(['read_at' => Carbon::now()]);

		// Ambil pesan (cursor-based sederhana via before_id)
		$beforeId = $request->input('before_id');
		$limit    = 50;

		$query = Chat_Message::where('conversation_id', $conversationId)
			->with('sender.information')
			->orderByDesc('id')
			->limit($limit);

		if ($beforeId) {
			$query->where('id', '<', $beforeId);
		}

		$messages = $query->get()->reverse()->values()->map(fn($m) => $this->formatMessage($m, $me->id));

		return response()->json(['success' => true, 'data' => $messages]);
	}

	// ─────────────────────────────────────────────────────────
	// API — Kirim pesan
	// ─────────────────────────────────────────────────────────

	public function send(Request $request, int $conversationId)
	{
		$request->validate(['body' => 'required|string|max:5000']);

		$me   = auth()->user();
		$conv = $this->authorizeConversation($conversationId, $me->id);

		if (!$conv) {
			return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
		}

		DB::beginTransaction();
		try {
			$msg = Chat_Message::create([
				'conversation_id' => $conv->id,
				'sender_id'       => $me->id,
				'body'            => $request->input('body'),
				'type'            => 'text',
			]);

			// Update last_message di conversation
			$conv->update([
				'last_message_id'  => $msg->id,
				'last_activity_at' => Carbon::now(),
			]);

			DB::commit();
		} catch (\Throwable $e) {
			DB::rollBack();
			return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
		}

		// Ambil info sender untuk broadcast
		$me->load('information');
		$avatarUrl = $this->getAvatarUrl($me);

		// Broadcast real-time ke channel conversation
		$receiverId = ($conv->user_one_id === $me->id) ? $conv->user_two_id : $conv->user_one_id;

		event(new ChatMessageSent(
			conversationId: $conv->id,
			messageId:      $msg->id,
			senderId:       $me->id,
			senderName:     $me->fullname ?? $me->username,
			senderAvatar:   $avatarUrl,
			body:           $msg->body,
			type:           $msg->type,
			createdAt:      $msg->created_at->toDateTimeString(),
			receiverId:     $receiverId,
		));

		return response()->json([
			'success' => true,
			'data'    => $this->formatMessage($msg->load('sender.information'), $me->id),
		]);
	}

	// ─────────────────────────────────────────────────────────
	// API — Typing indicator
	// ─────────────────────────────────────────────────────────

	public function typing(Request $request, int $conversationId)
	{
		$me   = auth()->user();
		$conv = $this->authorizeConversation($conversationId, $me->id);

		if (!$conv) {
			return response()->json(['success' => false], 403);
		}

		event(new ChatUserTyping(
			conversationId: $conv->id,
			userId:         $me->id,
			userName:       $me->fullname ?? $me->username,
			isTyping:       (bool) $request->input('is_typing', true),
		));

		return response()->json(['success' => true]);
	}

	// ─────────────────────────────────────────────────────────
	// PRIVATE HELPERS
	// ─────────────────────────────────────────────────────────

	private function authorizeConversation(int $conversationId, int $userId): ?Chat_Conversation
	{
		return Chat_Conversation::where('id', $conversationId)
			->where(function ($q) use ($userId) {
				$q->where('user_one_id', $userId)
				  ->orWhere('user_two_id', $userId);
			})
			->first();
	}

	private function formatUser(Account $user, int $myId): array
	{
		$info      = $user->information ?? null;
		$avatarUrl = $this->getAvatarUrl($user);

		return [
			'id'         => $user->id,
			'fullname'   => $user->fullname,
			'username'   => $user->username,
			'email'      => $user->email,
			'avatar_url' => $avatarUrl,
			'initials'   => $this->getInitials($user->fullname ?? $user->username),
		];
	}

	private function formatMessage(Chat_Message $msg, int $myId): array
	{
		return [
			'id'              => $msg->id,
			'conversation_id' => $msg->conversation_id,
			'sender_id'       => $msg->sender_id,
			'sender_name'     => $msg->sender?->fullname ?? $msg->sender?->username,
			'sender_avatar'   => $this->getAvatarUrl($msg->sender),
			'body'            => $msg->body,
			'type'            => $msg->type,
			'is_mine'         => $msg->sender_id === $myId,
			'read_at'         => $msg->read_at?->toDateTimeString(),
			'created_at'      => $msg->created_at->format('H:i'),
			'created_date'    => $msg->created_at->toDateString(),
		];
	}

	private function getAvatarUrl(?Account $user): string
	{
		if (!$user) return '';
		$info = $user->information ?? null;
		if ($info && $info->avatar && Storage::disk('public')->exists($info->avatar)) {
			return url('storage/' . $info->avatar);
		}
		return '';
	}

	private function getInitials(string $name): string
	{
		$parts = explode(' ', trim($name));
		if (count($parts) >= 2) {
			return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
		}
		return strtoupper(substr($name, 0, 2));
	}
}
