@extends('themes.'.custom_theme('cms'))

@section('title')
    {{ t('Events') }}
@endsection

@section('content')
    <div id="ph-app-event" data-list-url="{{ route('cms.core.event.listdata') }}" data-detail-base-url="{{ url('event') }}">
        <div class="ph-content rounded p-3 mb-3"><div class="row g-3 align-items-center"><div class="col-md-7"><h4 class="mb-0"><i class="fad fa-calendar-star fa-fw me-1"></i>{{ t('Events') }}</h4></div><div class="col-md-5"><input v-model="search" @input="loadEvents(1)" type="search" class="form-control" placeholder="{{ t('Search events') }}" aria-label="{{ t('Search events') }}"></div></div></div>
        <div v-if="loading" class="ph-content rounded text-center p-5"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">{{ t('Loading') }}...</div></div>
        <div v-else-if="events.length === 0" class="ph-content rounded text-center text-muted p-5">{{ t('No upcoming events found') }}</div>
        <div v-else class="row g-3"><div v-for="event in events" :key="event.id" class="col-md-6 col-xl-4"><article class="card h-100 shadow-sm"><img v-if="event.thumbnail_small_url" :src="event.thumbnail_small_url" class="card-img-top" alt="" style="height:180px;object-fit:cover"><div class="card-body d-flex flex-column"><div class="small text-muted mb-2">@{{ formatDate(event.next_occurrence_at) }}</div><h5 class="card-title">@{{ event.title }}</h5><p class="card-text text-muted">@{{ summaryText(event.summary) }}</p><a :href="detailUrl(event.uri)" class="btn ph-btn-theme mt-auto">{{ t('View event') }}</a></div></article></div></div>
        <div class="d-flex justify-content-end mt-3"><paginate v-if="lastPage > 1" :page-count="lastPage" :click-handler="loadEvents" :prev-text="'‹'" :next-text="'›'" :container-class="'pagination ph-pagination m-0'" v-model="page"></paginate></div>
    </div>
@endsection

@push('js')
    <script src="{{ url('assets/js/vue3/event/vueV3-event-2026.js?v=').time() }}"></script>
@endpush
