@extends('workflow_qa::layout')

@php
    $workflowBootstrap = $bootstrap ?? ['urls' => ['index' => url('workflow'), 'create' => url('workflow/create'), 'data' => url('workflow/data'), 'base' => url('workflow')], 'permissions' => ['write' => false]];
@endphp

@section('title')
    {{ $qaTranslate('Workflows') }}
@endsection

@section('content')
    <div id="ph-app-workflow-list" class="workflow-page workflow-list-page">
        <div class="mb-3">{{ Breadcrumbs::render('workflow') }}</div>

        <section class="workflow-list-hero ph-content rounded mb-3">
            <div class="workflow-list-hero__copy">
                <span class="workflow-eyebrow"><i class="fas fa-project-diagram me-1" aria-hidden="true"></i>{{ $qaTranslate('Automation workspace') }}</span>
                <h1 class="h3 mb-1">{{ $qaTranslate('Workflows') }}</h1>
                <p class="mb-0 text-body-secondary">{{ $qaTranslate('Build clear, testable flows for your CMS operations.') }}</p>
            </div>
            <a class="btn ph-btn-theme workflow-primary-action" :class="{ disabled: !canWrite }" :aria-disabled="!canWrite" :href="canWrite ? createUrl() : null">
                <i class="fas fa-plus me-1" aria-hidden="true"></i>{{ $qaTranslate('New workflow') }}
            </a>
        </section>

        <section class="ph-content rounded mb-3 workflow-list-toolbar" aria-label="{{ $qaTranslate('Workflow filters') }}">
            <form class="row g-2 align-items-end" @submit.prevent="applyFilters">
                <div class="col-12 col-lg-5">
                    <label for="workflow-search" class="form-label mb-1">{{ $qaTranslate('Search') }}</label>
                    <div class="input-group">
                        <span class="input-group-text" aria-hidden="true"><i class="fas fa-search"></i></span>
                        <input id="workflow-search" v-model.trim="search" type="search" class="form-control" autocomplete="off" placeholder="{{ $qaTranslate('Search by name or description') }}">
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <label for="workflow-category" class="form-label mb-1">{{ $qaTranslate('Category') }}</label>
                    <select id="workflow-category" v-model="category" class="form-select">
                        <option value="">{{ $qaTranslate('All categories') }}</option>
                        <option value="general">{{ $qaTranslate('General') }}</option>
                        <option value="approval">{{ $qaTranslate('Approval') }}</option>
                        <option value="form">{{ $qaTranslate('Form') }}</option>
                        <option value="automation">{{ $qaTranslate('Automation') }}</option>
                        <option value="diagram">{{ $qaTranslate('Diagram') }}</option>
                    </select>
                </div>
                <div class="col-6 col-lg-2">
                    <label for="workflow-status" class="form-label mb-1">{{ $qaTranslate('Status') }}</label>
                    <select id="workflow-status" v-model="status" class="form-select">
                        <option value="">{{ $qaTranslate('All statuses') }}</option>
                        <option value="draft">{{ $qaTranslate('Draft') }}</option>
                        <option value="published">{{ $qaTranslate('Published') }}</option>
                        <option value="archived">{{ $qaTranslate('Archived') }}</option>
                    </select>
                </div>
                <div class="col-12 col-lg-auto d-flex gap-2">
                    <button type="submit" class="btn ph-btn-theme flex-grow-1"><i class="fas fa-filter me-1" aria-hidden="true"></i>{{ $qaTranslate('Apply') }}</button>
                    <button type="button" class="btn ph-btn-theme-outline" :disabled="!hasFilters" @click="resetFilters">{{ $qaTranslate('Reset') }}</button>
                </div>
            </form>
        </section>

        <section class="ph-content rounded workflow-list-card" aria-labelledby="workflow-list-heading">
            <div class="workflow-list-card__header">
                <div>
                    <h2 id="workflow-list-heading" class="h5 mb-1">{{ $qaTranslate('Saved workflows') }}</h2>
                    <p class="small text-body-secondary mb-0" aria-live="polite">@{{ total }} {{ $qaTranslate('workflow(s)') }}</p>
                </div>
                <span v-if="loading" class="workflow-inline-status" role="status"><span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>{{ $qaTranslate('Loading') }}</span>
            </div>

            <div v-if="error" class="alert alert-danger m-3" role="alert">
                <div class="d-flex gap-2 align-items-start"><i class="fas fa-exclamation-circle mt-1" aria-hidden="true"></i><div class="flex-grow-1">@{{ error }}</div><button type="button" class="btn btn-sm btn-outline-danger" @click="load(page)">{{ $qaTranslate('Try again') }}</button></div>
            </div>

            <div v-else-if="loading && !loaded" class="workflow-empty-state" role="status" aria-live="polite">
                <span class="spinner-border text-primary" aria-hidden="true"></span><strong>{{ $qaTranslate('Loading workflows') }}</strong><span>{{ $qaTranslate('Your saved flows will appear here.') }}</span>
            </div>

            <div v-else-if="loaded && items.length === 0" class="workflow-empty-state">
                <span class="workflow-empty-state__icon" aria-hidden="true"><i class="fas fa-route"></i></span>
                <strong>{{ $qaTranslate('No workflows found') }}</strong>
                <span>@{{ hasFilters ? 'Try a different search or filter.' : 'Start with a blank workflow or a ready template.' }}</span>
                <a v-if="canWrite" class="btn ph-btn-theme btn-sm mt-2" :href="createUrl()"><i class="fas fa-plus me-1" aria-hidden="true"></i>{{ $qaTranslate('Create workflow') }}</a>
            </div>

            <div v-else class="table-responsive">
                <table class="table table-hover align-middle workflow-table mb-0">
                    <thead>
                        <tr><th scope="col">{{ $qaTranslate('Workflow') }}</th><th scope="col">{{ $qaTranslate('Category') }}</th><th scope="col">{{ $qaTranslate('Status') }}</th><th scope="col">{{ $qaTranslate('Draft') }}</th><th scope="col">{{ $qaTranslate('Published') }}</th><th scope="col">{{ $qaTranslate('Updated') }}</th><th scope="col" class="text-end">{{ $qaTranslate('Action') }}</th></tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.id">
                            <td>
                                <div class="workflow-table__name"><a :href="editUrl(item)" class="fw-semibold text-decoration-none">@{{ item.name || 'Untitled workflow' }}</a><small v-if="item.description" class="d-block text-body-secondary text-truncate">@{{ item.description }}</small></div>
                            </td>
                            <td><span class="workflow-category">@{{ categoryLabel(item.category) }}</span></td>
                            <td><span class="badge" :class="statusClass(item.status)">@{{ item.status || 'draft' }}</span></td>
                            <td><span class="workflow-version">v@{{ item.draft_revision || 0 }}</span></td>
                            <td><span v-if="item.published_version" class="workflow-version workflow-version--published">v@{{ item.published_version }}</span><span v-else class="text-body-secondary">-</span></td>
                            <td class="text-body-secondary text-nowrap">@{{ formatDate(item.updated_at) }}</td>
                            <td class="text-end"><a class="btn btn-sm ph-btn-theme-outline" :href="editUrl(item)" :aria-label="'Open workflow ' + (item.name || 'workflow')"><i class="fas fa-arrow-right" aria-hidden="true"></i><span class="visually-hidden">{{ $qaTranslate('Open workflow') }}</span></a></td>
                        </tr>
                    </tbody>
                </table>
                <nav v-if="lastPage > 1" class="workflow-pagination p-3" aria-label="{{ $qaTranslate('Workflow pages') }}">
                    <button type="button" class="btn btn-sm btn-outline-secondary" :disabled="page <= 1 || loading" @click="load(page - 1)"><i class="fas fa-chevron-left" aria-hidden="true"></i><span class="visually-hidden">{{ $qaTranslate('Previous page') }}</span></button>
                    <span class="small text-body-secondary">{{ $qaTranslate('Page') }} @{{ page }} {{ $qaTranslate('of') }} @{{ lastPage }}</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" :disabled="page >= lastPage || loading" @click="load(page + 1)"><i class="fas fa-chevron-right" aria-hidden="true"></i><span class="visually-hidden">{{ $qaTranslate('Next page') }}</span></button>
                </nav>
            </div>
        </section>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ url('assets/css/workflow-editor.css?v=').time() }}">
@endpush

@push('js')
    <script>window.WorkflowListBootstrap = {{ Js::from($workflowBootstrap) }};</script>
    <script type="module" src="{{ url('assets/js/vue3/workflow/workflow-list.js?v=').time() }}"></script>
@endpush
