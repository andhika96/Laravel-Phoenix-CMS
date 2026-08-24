@extends('themes.'.custom_theme('cms'))

@section('title')
    {{ $event->title }}
@endsection

@section('content')
    <div id="ph-event-detail" data-registrations-url="{{ route('cms.core.event.registrations') }}">
        <div class="mb-3"><a href="{{ route('cms.core.event') }}" class="text-decoration-none"><i class="fas fa-arrow-left fa-fw me-1"></i>{{ t('Back to events') }}</a></div>
        <article class="ph-content rounded p-4 mb-3"><div class="d-flex flex-wrap gap-2 mb-2"><span class="badge text-bg-success">{{ $event->publication_status }}</span><span class="badge text-bg-light">{{ $event->category?->name ?? t('Uncategorized') }}</span></div><h1 class="h3">{{ $event->title }}</h1>@if ($event->summary)<div class="lead text-muted event-summary">{!! $event->summary !!}</div>@endif@if ($event->thumb_l && Storage::disk('public')->exists($event->thumb_l))<img src="{{ Storage::url($event->thumb_l) }}" alt="{{ $event->title }}" class="img-fluid rounded mb-4" style="max-height:420px;width:100%;object-fit:cover">@endif<div class="event-rich-content">{!! $event->content !!}</div></article>
        <section class="ph-content rounded p-4"><h2 class="h5 mb-3">{{ t('Sessions') }}</h2><div class="row g-3">@forelse ($event->occurrences as $occurrence)<div class="col-md-6"><div class="border rounded p-3 h-100" data-occurrence-card="{{ $occurrence->id }}"><div class="d-flex justify-content-between gap-2"><h3 class="h6 mb-1">{{ $occurrence->label ?: t('Event session') }}</h3><span class="badge text-bg-secondary">{{ $occurrence->lifecycle_status }}</span></div><div class="small text-muted mb-2"><i class="fas fa-clock fa-fw me-1"></i>{{ $occurrence->starts_at->timezone($occurrence->timezone)->format('d M Y H:i') }} - {{ $occurrence->ends_at->timezone($occurrence->timezone)->format('H:i') }} ({{ $occurrence->timezone }})</div><div class="small mb-2"><i class="fas fa-map-marker-alt fa-fw me-1"></i>{{ $occurrence->location_text ?: ($occurrence->online_url ?: t('Location not specified')) }}</div><div class="small text-muted mb-3">{{ t('Capacity') }}: {{ $occurrence->capacity }}</div><div class="d-flex align-items-center gap-2"><button type="button" class="btn btn-sm ph-btn-theme event-register-button" data-occurrence-id="{{ $occurrence->id }}">{{ t('Register') }}</button><button type="button" class="btn btn-sm btn-outline-danger event-cancel-button d-none" data-occurrence-id="{{ $occurrence->id }}">{{ t('Cancel registration') }}</button><span class="small event-registration-status" aria-live="polite"></span></div></div></div>@empty<div class="text-muted">{{ t('No sessions available') }}</div>@endforelse</div></section>
    </div>
@endsection

@push('js')
    <script>
        (() => {
            const root = document.getElementById('ph-event-detail');
            if (!root) return;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const statusText = (card, text, isError = false) => { const target = card.querySelector('.event-registration-status'); target.textContent = text; target.classList.toggle('text-danger', isError); };
            const syncRegistration = (card, registration) => { const register = card.querySelector('.event-register-button'); const cancel = card.querySelector('.event-cancel-button'); register.classList.toggle('d-none', ['confirmed', 'waitlisted'].includes(registration?.status)); cancel.classList.toggle('d-none', !['confirmed', 'waitlisted'].includes(registration?.status)); statusText(card, registration?.status || ''); };
            const request = (url, options = {}) => fetch(url, { ...options, headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf, ...(options.headers || {}) } }).then(async response => { const data = await response.json(); if (!response.ok) throw new Error(data.message || '{{ t('Request failed') }}'); return data; });
            request(root.dataset.registrationsUrl).then(data => { (data.data || []).forEach(registration => { const card = document.querySelector(`[data-occurrence-card="${registration.occurrence_id}"]`); if (card) syncRegistration(card, registration); }); }).catch(() => {});
            root.querySelectorAll('.event-register-button').forEach(button => button.addEventListener('click', () => { const card = button.closest('[data-occurrence-card]'); button.disabled = true; request(`{{ url('event/occurrence') }}/${button.dataset.occurrenceId}/register`, { method: 'POST' }).then(data => { syncRegistration(card, data.data); statusText(card, data.message); }).catch(error => statusText(card, error.message, true)).finally(() => { button.disabled = false; }); }));
            root.querySelectorAll('.event-cancel-button').forEach(button => button.addEventListener('click', () => { const card = button.closest('[data-occurrence-card]'); button.disabled = true; request(`{{ url('event/occurrence') }}/${button.dataset.occurrenceId}/cancel`, { method: 'POST' }).then(data => { syncRegistration(card, data.data); statusText(card, data.message); }).catch(error => statusText(card, error.message, true)).finally(() => { button.disabled = false; }); }));
        })();
    </script>
@endpush
