@extends('themes.'.custom_theme('cms'))

@section('title')
    {{ t('Add Event') }}
@endsection

@section('content')
    <div class="mb-3">{{ Breadcrumbs::render('manage_event.add') }}</div>
    <div class="ph-content rounded p-3 mb-3"><h4 class="mb-0"><i class="fad fa-plus fa-fw me-1"></i>{{ t('Add Event') }}</h4></div>
    @include('manage_event.partials.form', ['event' => null, 'categories' => $categories])
@endsection

@push('css')
    <link rel="stylesheet" href="{{ url('assets/plugins/vue/plugins/vue-datepicker/css/vue-datepicker-11.0.3.css') }}">
@endpush

@push('js')
    <script src="{{ url('assets/plugins/ckeditor5/build/ckeditor.js?v=0.0.1') }}"></script>
    <script src="{{ url('assets/plugins/ckfinder/ckfinder.js?v=0.0.1') }}"></script>
    <script src="{{ url('assets/plugins/vue/plugins/vue-datepicker/js/vue-datepicker-11.0.3.js') }}"></script>
    <script src="{{ url('assets/js/vue3/manage_event/vueV3-manage-event-form-2026.js?v=').time() }}"></script>
@endpush
