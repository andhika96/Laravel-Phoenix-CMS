<?php

namespace App\Http\Controllers\Web\Manage_Event;

use App\Http\Controllers\Api\Base_API_Rev_Controller;
use App\Http\Controllers\Controller;
use App\Http\Requests\Event\AddEventRequest;
use App\Http\Requests\Event\EditEventRequest;
use App\Http\Requests\Event\EventCategoryRequest;
use App\Http\Requests\Event\EventOccurrenceRequest;
use App\Http\Resources\Manage_Event\EventCategoryResource;
use App\Http\Resources\Manage_Event\EventOccurrenceResource;
use App\Http\Resources\Manage_Event\EventRegistrationResource;
use App\Http\Resources\Manage_Event\ManageEventEditResource;
use App\Http\Resources\Manage_Event\ManageEventListResource;
use App\Models\Event\Event;
use App\Models\Event\EventCategory;
use App\Models\Event\EventOccurrence;
use App\Models\Event\EventRegistration;
use App\Services\Event\EventRegistrationService;
use App\Support\CkfinderSessionBridge;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class Manage_Event_Controller extends Controller
{
    public function __construct(private readonly EventRegistrationService $registrationService)
    {
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index(): mixed
    {
        return view('manage_event.manage_event', [
            'categories' => EventCategory::query()->where('status', '!=', 'hide')->orderBy('name')->get(),
        ]);
    }

    public function add(Request $request): mixed
    {
        app(CkfinderSessionBridge::class)->prepare($request);

        return view('manage_event.manage_event_add', [
            'categories' => EventCategory::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function edit(Request $request, string $idOrSlug): mixed
    {
        app(CkfinderSessionBridge::class)->prepare($request);
        $event = $this->findEvent($idOrSlug)?->load(['category', 'occurrences' => fn ($query) => $query->orderBy('starts_at')]);

        abort_unless($event, 404);

        return view('manage_event.manage_event_edit', [
            'event' => $event,
            'categories' => EventCategory::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(AddEventRequest $request): mixed
    {
        try {
            $event = DB::transaction(function () use ($request): Event {
                $event = Event::query()->create([
                    'uri' => $this->uniqueUri($request->input('uri') ?: $request->input('title')),
                    'category_id' => $request->integer('category_id') ?: null,
                    'created_by' => auth()->id(),
                    'title' => $request->string('title')->toString(),
                    'summary' => $request->input('summary'),
                    'content' => $request->string('content')->toString(),
                    'tags' => $request->input('tags'),
                    'publication_status' => $request->input('publication_status', 'draft'),
                    'visibility' => $request->input('visibility', 'public'),
                    'reminder_lead_minutes' => $request->input('reminder_lead_minutes'),
                    'cancel_cutoff_minutes' => $request->input('cancel_cutoff_minutes'),
                ]);

                if ($request->hasFile('thumbnail')) {
                    $event->fill($this->storeThumbnail($request->file('thumbnail')))->save();
                } elseif ($request->input('thumbnail_source') === 'ckfinder' && ($thumbnail = $this->storeCkfinderThumbnail($request->input('thumbnail_ckfinder_url')))) {
                    $event->fill($thumbnail)->save();
                }

                return $event;
            });

            return $this->success($request, 'Event created successfully', ['id' => $event->id, 'uri' => $event->uri], 'manage_event.edit', $event->id);
        } catch (Throwable $throwable) {
            report($throwable);

            return $this->failure($request, 'Failed to create event');
        }
    }

    public function update(EditEventRequest $request, string $idOrSlug): mixed
    {
        $event = $this->findEvent($idOrSlug);
        abort_unless($event, 404);

        try {
            DB::transaction(function () use ($request, $event): void {
                $event->fill([
                    'uri' => $this->uniqueUri($request->input('uri') ?: $event->uri ?: $request->input('title'), $event->id),
                    'category_id' => $request->integer('category_id') ?: null,
                    'title' => $request->string('title')->toString(),
                    'summary' => $request->input('summary'),
                    'content' => $request->string('content')->toString(),
                    'tags' => $request->input('tags'),
                    'publication_status' => $request->input('publication_status', 'draft'),
                    'visibility' => $request->input('visibility', 'public'),
                    'reminder_lead_minutes' => $request->input('reminder_lead_minutes'),
                    'cancel_cutoff_minutes' => $request->input('cancel_cutoff_minutes'),
                ]);

                if ($request->hasFile('thumbnail')) {
                    $this->deleteFile($event->thumb_s);
                    $this->deleteFile($event->thumb_l);
                    $event->fill($this->storeThumbnail($request->file('thumbnail')));
                } elseif ($request->input('thumbnail_source') === 'ckfinder' && ($thumbnail = $this->storeCkfinderThumbnail($request->input('thumbnail_ckfinder_url')))) {
                    $this->deleteFile($event->thumb_s);
                    $this->deleteFile($event->thumb_l);
                    $event->fill($thumbnail);
                } elseif ($request->boolean('remove_thumbnail')) {
                    $this->deleteFile($event->thumb_s);
                    $this->deleteFile($event->thumb_l);
                    $event->fill(['thumb_s' => null, 'thumb_l' => null]);
                }

                $event->save();
            });

            return $this->success($request, 'Event updated successfully', ['id' => $event->id, 'uri' => $event->uri]);
        } catch (Throwable $throwable) {
            report($throwable);

            return $this->failure($request, 'Failed to update event');
        }
    }

    public function delete(Request $request, string $idOrSlug): mixed
    {
        $event = $this->findEvent($idOrSlug);
        abort_unless($event, 404);

        if ($event->occurrences()->whereHas('registrations', fn ($query) => $query->whereIn('status', ['confirmed', 'waitlisted']))->exists()) {
            return $this->failure($request, 'Event with active registrations cannot be deleted', 409);
        }

        try {
            DB::transaction(function () use ($event): void {
                $event->occurrences()->each(function (EventOccurrence $occurrence): void {
                    $occurrence->registrations()->delete();
                    $occurrence->delete();
                });
                $this->deleteFile($event->thumb_s);
                $this->deleteFile($event->thumb_l);
                $event->delete();
            });

            return $this->success($request, 'Event deleted successfully');
        } catch (Throwable $throwable) {
            report($throwable);

            return $this->failure($request, 'Failed to delete event');
        }
    }

    public function bulkUpdate(Request $request): mixed
    {
        $validated = $request->validate([
            'getSelected' => ['required', 'array', 'min:1'],
            'getSelected.*' => ['integer'],
            'changeStatus' => ['required', 'in:draft,published,hidden'],
        ]);

        Event::query()->whereIn('id', $validated['getSelected'])->update([
            'publication_status' => $validated['changeStatus'],
            'updated_at' => now(),
        ]);

        return $this->success($request, 'Event status updated successfully');
    }

    public function detailData(string $idOrSlug): mixed
    {
        $event = $this->findEvent($idOrSlug)?->load(['category', 'occurrences' => fn ($query) => $query->orderBy('starts_at')]);

        if (! $event) {
            return response()->json(['success' => false, 'status' => 'failed', 'message' => t('Event not found')], 404);
        }

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => t('Data found'),
            'data' => new ManageEventEditResource($event),
        ]);
    }

    public function listData(Request $request): mixed
    {
        $result = Event::query()
            ->with(['category', 'occurrences' => fn ($query) => $query->orderBy('starts_at')])
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->input('search').'%'))
            ->when($request->filled('publication_status'), fn ($query) => $query->where('publication_status', $request->input('publication_status')))
            ->when($request->filled('visibility'), fn ($query) => $query->where('visibility', $request->input('visibility')))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->input('category_id')))
            ->latest('updated_at')
            ->paginate(15);

        $api = new Base_API_Rev_Controller();
        $formatted = $api->paginateResponse($result, ManageEventListResource::class);

        return $api->setStatusMsg($formatted['total'] ? 'success' : 'failed')
            ->respondOK($formatted, $formatted['total'] ? t('Data found') : t('No data found'), (bool) $formatted['total']);
    }

    public function listCategories(Request $request): mixed
    {
        $result = EventCategory::query()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->input('search').'%'))
            ->orderBy('name')
            ->paginate(25);

        $api = new Base_API_Rev_Controller();
        $formatted = $api->paginateResponse($result, EventCategoryResource::class);

        return $api->setStatusMsg($formatted['total'] ? 'success' : 'failed')
            ->respondOK($formatted, $formatted['total'] ? t('Data found') : t('No data found'), (bool) $formatted['total']);
    }

    public function storeCategory(EventCategoryRequest $request): mixed
    {
        $category = EventCategory::query()->create([
            'name' => $request->input('category_name'),
            'code' => $request->input('category_code') ?: Str::slug($request->input('category_name')),
            'status' => $request->input('category_status', 'active'),
        ]);

        return $this->success($request, 'Event category created successfully', ['id' => $category->id]);
    }

    public function updateCategory(EventCategoryRequest $request): mixed
    {
        $category = EventCategory::query()->findOrFail($request->input('idOrSlug'));
        $category->update([
            'name' => $request->input('category_name'),
            'code' => $request->input('category_code') ?: Str::slug($request->input('category_name')),
            'status' => $request->input('category_status', 'active'),
        ]);

        return $this->success($request, 'Event category updated successfully');
    }

    public function deleteCategory(Request $request, string $idOrSlug): mixed
    {
        $category = EventCategory::query()->findOrFail($idOrSlug);

        if ($category->events()->exists()) {
            return $this->failure($request, 'Event category with events cannot be deleted', 409);
        }

        $category->delete();

        return $this->success($request, 'Event category deleted successfully');
    }

    public function listOccurrences(string $idOrSlug): mixed
    {
        $event = $this->findEvent($idOrSlug);
        abort_unless($event, 404);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => t('Data found'),
            'data' => EventOccurrenceResource::collection($event->occurrences()->with('registrations')->orderBy('starts_at')->get()),
        ]);
    }

    public function storeOccurrence(EventOccurrenceRequest $request, string $idOrSlug): mixed
    {
        $event = $this->findEvent($idOrSlug);
        abort_unless($event, 404);

        $occurrence = $event->occurrences()->create($request->validated());

        return $this->success($request, 'Event occurrence created successfully', new EventOccurrenceResource($occurrence));
    }

    public function updateOccurrence(EventOccurrenceRequest $request, string $idOrSlug, string $occurrenceId): mixed
    {
        $event = $this->findEvent($idOrSlug);
        abort_unless($event, 404);
        $occurrence = $event->occurrences()->findOrFail($occurrenceId);
        $confirmedCount = $occurrence->registrations()->where('status', 'confirmed')->count();

        if ((int) $request->input('capacity') < $confirmedCount) {
            return $this->failure($request, 'Capacity cannot be lower than confirmed registrations', 422);
        }

        $data = $request->validated();
        if (($data['lifecycle_status'] ?? null) === 'cancelled' && $occurrence->lifecycle_status !== 'cancelled') {
            $this->registrationService->cancelOccurrence($occurrence, $request->input('cancellation_reason'));
        }
        $occurrence->update($data);

        return $this->success($request, 'Event occurrence updated successfully', new EventOccurrenceResource($occurrence->fresh()));
    }

    public function deleteOccurrence(Request $request, string $idOrSlug, string $occurrenceId): mixed
    {
        $event = $this->findEvent($idOrSlug);
        abort_unless($event, 404);
        $occurrence = $event->occurrences()->findOrFail($occurrenceId);

        if ($occurrence->registrations()->whereIn('status', ['confirmed', 'waitlisted'])->exists()) {
            return $this->failure($request, 'Occurrence with active registrations cannot be deleted', 409);
        }

        $occurrence->registrations()->delete();
        $occurrence->delete();

        return $this->success($request, 'Event occurrence deleted successfully');
    }

    public function cancelOccurrence(Request $request, string $idOrSlug, string $occurrenceId): mixed
    {
        $event = $this->findEvent($idOrSlug);
        abort_unless($event, 404);
        $occurrence = $event->occurrences()->findOrFail($occurrenceId);
        $cancelled = $this->registrationService->cancelOccurrence($occurrence, $request->input('reason'));

        return $this->success($request, 'Event occurrence cancelled successfully', ['cancelled_registrations' => $cancelled]);
    }

    public function registrations(string $idOrSlug): mixed
    {
        $event = $this->findEvent($idOrSlug);
        abort_unless($event, 404);

        $registrations = EventRegistration::query()
            ->with(['account', 'occurrence'])
            ->whereHas('occurrence', fn ($query) => $query->where('event_id', $event->id))
            ->latest('registered_at')
            ->paginate(25);

        $api = new Base_API_Rev_Controller();
        $formatted = $api->paginateResponse($registrations, EventRegistrationResource::class);

        return $api->respondOK($formatted, t('Data found'), (bool) $formatted['total']);
    }

    public function markAttendance(Request $request, string $registrationId): mixed
    {
        $validated = $request->validate(['status' => ['required', 'in:attended,no_show']]);
        $registration = $this->registrationService->markAttendance(
            EventRegistration::query()->findOrFail($registrationId),
            $validated['status'],
        );

        return $this->success($request, 'Attendance updated successfully', new EventRegistrationResource($registration->fresh()));
    }

    private function findEvent(string $idOrSlug): ?Event
    {
        return Event::query()->where(is_numeric($idOrSlug) ? 'id' : 'uri', $idOrSlug)->first();
    }

    private function uniqueUri(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'event';
        $candidate = $base;
        $suffix = 2;

        while (Event::query()->where('uri', $candidate)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $candidate = $base.'-'.$suffix++;
        }

        return $candidate;
    }

    private function storeThumbnail(UploadedFile $file): array
    {
        $folder = 'events/'.date('mY').'/date_'.date('d');
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $large = $folder.'/'.random_string('md5').'.'.$extension;
        $small = $folder.'/'.random_string('md5').'_small.'.$extension;

        Storage::disk('public')->makeDirectory($folder);
        $file->storeAs($folder, basename($large), 'public');
        $manager = new ImageManager(Driver::class);
        $image = $manager->read($file->getRealPath());
        $image->scale(width: 720);
        $image->save(Storage::disk('public')->path($small));

        return ['thumb_l' => $large, 'thumb_s' => $small];
    }

    private function storeCkfinderThumbnail(?string $url): ?array
    {
        $urlPath = parse_url(trim((string) $url), PHP_URL_PATH);
        $prefix = '/storage/ckfinder/events/';
        if (! is_string($urlPath) || ! str_starts_with($urlPath, $prefix)) {
            return null;
        }

        $source = 'ckfinder/events/'.ltrim(rawurldecode(substr($urlPath, strlen($prefix))), '/');
        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        if ($source === 'ckfinder/events/' || str_contains($source, '..') || ! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return null;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($source)) {
            return null;
        }

        $folder = 'events/'.date('mY').'/date_'.date('d');
        $large = $folder.'/'.random_string('md5').'.'.$extension;
        $small = $folder.'/'.random_string('md5').'_small.'.$extension;
        $disk->makeDirectory($folder);
        $disk->copy($source, $large);

        $manager = new ImageManager(Driver::class);
        $image = $manager->read($disk->path($source));
        $image->scale(width: 720);
        $image->save($disk->path($small));

        return ['thumb_l' => $large, 'thumb_s' => $small];
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function success(Request $request, string $message, mixed $data = null, ?string $redirectRoute = null, mixed $redirectParameter = null, int $status = 200): mixed
    {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => t($message),
                'data' => $data,
            ], $status);
        }

        return $redirectRoute
            ? redirect()->route('cms.core.'.$redirectRoute, $redirectParameter)->with('success', t($message))
            : redirect()->back()->with('success', t($message));
    }

    private function failure(Request $request, string $message, int $status = 500): mixed
    {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'status' => 'failed',
                'message' => t($message),
            ], $status);
        }

        return redirect()->back()->withInput()->with('error', t($message));
    }
}
