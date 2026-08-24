<?php

namespace App\Http\Controllers\Web\Event;

use App\Http\Controllers\Api\Base_API_Rev_Controller;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manage_Event\EventOccurrenceResource;
use App\Http\Resources\Manage_Event\EventRegistrationResource;
use App\Http\Resources\Manage_Event\ManageEventListResource;
use App\Models\Event\Event;
use App\Models\Event\EventOccurrence;
use App\Models\Event\EventRegistration;
use App\Models\Awesome_Admin\Account;
use App\Services\Event\EventRegistrationService;
use DomainException;
use Illuminate\Http\Request;

class Event_Controller extends Controller
{
    public function __construct(private readonly EventRegistrationService $registrationService)
    {
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index(): mixed
    {
        return view('event.event');
    }

    public function detail(string $idOrSlug): mixed
    {
        $event = $this->publishedEvent($idOrSlug)?->load(['category', 'occurrences' => fn ($query) => $query->orderBy('starts_at')]);
        abort_unless($event, 404);

        return view('event.event_detail', ['event' => $event]);
    }

    public function listData(Request $request): mixed
    {
        $result = Event::query()
            ->with(['category', 'occurrences' => fn ($query) => $query->where('lifecycle_status', 'scheduled')->orderBy('starts_at')])
            ->where('publication_status', 'published')
            ->where('visibility', 'public')
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->input('search').'%'))
            ->latest('updated_at')
            ->paginate(15);

        $api = new Base_API_Rev_Controller();
        $formatted = $api->paginateResponse($result, ManageEventListResource::class);

        return $api->setStatusMsg($formatted['total'] ? 'success' : 'failed')
            ->respondOK($formatted, $formatted['total'] ? t('Data found') : t('No data found'), (bool) $formatted['total']);
    }

    public function occurrence(string $occurrenceId): mixed
    {
        $occurrence = EventOccurrence::query()
            ->with(['event', 'registrations'])
            ->whereHas('event', fn ($query) => $query->where('publication_status', 'published')->where('visibility', 'public'))
            ->findOrFail($occurrenceId);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => t('Data found'),
            'data' => new EventOccurrenceResource($occurrence),
        ]);
    }

    public function registrations(): mixed
    {
        $registrations = EventRegistration::query()
            ->with(['account', 'occurrence.event'])
            ->where('account_id', auth()->id())
            ->latest('registered_at')
            ->paginate(15);

        $api = new Base_API_Rev_Controller();
        $formatted = $api->paginateResponse($registrations, EventRegistrationResource::class);

        return $api->respondOK($formatted, t('Data found'), (bool) $formatted['total']);
    }

    public function register(Request $request, string $occurrenceId): mixed
    {
        try {
            $registration = $this->registrationService->register(
                $this->publicOccurrence($occurrenceId),
                Account::query()->findOrFail(auth()->id()),
            );

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => t($registration->status === 'confirmed' ? 'Event registration confirmed' : 'You are on the event waitlist'),
                'data' => new EventRegistrationResource($registration),
            ], 201);
        } catch (DomainException $exception) {
            return response()->json(['success' => false, 'status' => 'failed', 'message' => t($exception->getMessage())], 422);
        }
    }

    public function cancel(Request $request, string $occurrenceId): mixed
    {
        try {
            $registration = $this->registrationService->cancel(
                $this->publicOccurrence($occurrenceId),
                Account::query()->findOrFail(auth()->id()),
                $request->input('reason'),
            );

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => t('Event registration cancelled'),
                'data' => new EventRegistrationResource($registration),
            ]);
        } catch (DomainException $exception) {
            return response()->json(['success' => false, 'status' => 'failed', 'message' => t($exception->getMessage())], 422);
        }
    }

    private function publishedEvent(string $idOrSlug): ?Event
    {
        return Event::query()
            ->where(is_numeric($idOrSlug) ? 'id' : 'uri', $idOrSlug)
            ->where('publication_status', 'published')
            ->where('visibility', 'public')
            ->first();
    }

    private function publicOccurrence(string $occurrenceId): EventOccurrence
    {
        return EventOccurrence::query()
            ->whereHas('event', fn ($query) => $query->where('publication_status', 'published')->where('visibility', 'public'))
            ->findOrFail($occurrenceId);
    }

}
