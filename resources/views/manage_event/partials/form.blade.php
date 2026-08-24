@php
    $isEdit = isset($event) && $event;
    $formAction = $isEdit ? route('cms.core.manage_event.update', $event->id) : route('cms.core.manage_event.store');
@endphp

<style>
    #ph-app-manage-event-form .event-duration-picker .dp__input {
        min-height: 38px;
        padding: .45rem 2.25rem .45rem 2.25rem;
        font-size: .875rem;
        line-height: 1.35;
    }

    #ph-app-manage-event-form .event-duration-picker .dp__input_icon {
        width: 1rem;
        height: 1rem;
    }

    #ph-app-manage-event-form .event-thumbnail-source .btn {
        font-size: .875rem;
    }

    #ph-app-manage-event-form .event-thumbnail-ckfinder-path {
        font-size: .875rem;
    }
</style>

<div id="ph-app-manage-event-form" data-event-id="{{ $isEdit ? $event->id : '' }}" data-detail-url="{{ $isEdit ? route('cms.core.manage_event.detaildata', $event->id) : '' }}" data-occurrence-base-url="{{ url('manage_event/occurrences') }}" data-registrations-base-url="{{ url('manage_event/registrations') }}" data-edit-base-url="{{ url('manage_event/edit') }}">
    <div class="ph-notice" v-cloak v-if="notice.message"><div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080"><div class="toast show" role="alert"><div class="toast-header"><strong class="me-auto">{{ t('Notice') }}</strong><button type="button" class="btn-close" @click="notice.message=''" aria-label="{{ t('Close') }}"></button></div><div class="toast-body">@{{ notice.message }}</div></div></div></div>

    <form ref="eventForm" action="{{ $formAction }}" method="post" enctype="multipart/form-data" @submit.prevent="submitEvent">
        @csrf
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="ph-content rounded mb-3 p-4">
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label" for="event-title">{{ t('Title') }}</label><input id="event-title" name="title" v-model="form.title" @input="syncUriFromTitle" class="form-control" required maxlength="255"></div>
                        <div class="col-12"><label class="form-label" for="event-uri">{{ t('Slug') }} ({{ t('Permalink') }})</label><div class="input-group"><span class="input-group-text">.../event/</span><input id="event-uri" name="uri" v-model="form.uri" @input="markUriCustomized" class="form-control" placeholder="{{ t('Auto generated from title') }}"><button v-if="uriManuallyEdited" type="button" class="btn btn-outline-secondary" @click="resetAutoUri">{{ t('Auto') }}</button></div><small class="form-text text-muted"><span v-if="uriManuallyEdited">{{ t('Custom slug') }}</span><span v-else>{{ t('Generated from title') }}</span></small></div>
                        <div class="col-12"><label class="form-label" for="summary-editor">{{ t('Summary') }}</label><textarea id="summary-editor" name="summary" rows="6" class="form-control" aria-label="{{ t('Event summary') }}"></textarea><div id="summary-word-count" class="form-text mt-2"></div></div>
                        <div class="col-12"><label class="form-label" for="editor">{{ t('Content') }}</label><textarea id="editor" name="content" rows="14" class="form-control" required aria-label="{{ t('Event content') }}"></textarea><div id="word-count" class="form-text mt-2"></div></div>
                        <div class="col-12"><label class="form-label" for="event-tags">{{ t('Tags') }}</label><input id="event-tags" name="tags" v-model="form.tags" class="form-control" maxlength="255"></div>
                    </div>
                </div>

                <div class="ph-content rounded p-4" v-if="eventId">
                    <div class="d-flex align-items-center mb-3"><h5 class="mb-0">{{ t('Occurrences') }}</h5><button type="button" class="btn btn-sm ph-btn-theme ms-auto" @click="resetOccurrence"> <i class="fas fa-plus fa-fw me-1"></i>{{ t('Add session') }}</button></div>
                    <div v-if="occurrences.length" class="table-responsive mb-3"><table class="table table-sm align-middle"><thead><tr><th>{{ t('Session') }}</th><th>{{ t('Schedule') }}</th><th>{{ t('Capacity') }}</th><th>{{ t('Status') }}</th><th></th></tr></thead><tbody><tr v-for="occurrence in occurrences" :key="occurrence.id"><td>@{{ occurrence.label || '-' }}</td><td>@{{ formatDate(occurrence.starts_at) }}<small class="d-block text-muted">@{{ formatDate(occurrence.ends_at) }}</small></td><td>@{{ occurrence.confirmed_count }}/@{{ occurrence.capacity }}</td><td><span class="badge text-bg-secondary">@{{ occurrence.lifecycle_status }}</span></td><td class="text-nowrap"><button type="button" class="btn btn-sm btn-link" @click="editOccurrence(occurrence)">{{ t('Edit') }}</button><button type="button" class="btn btn-sm btn-link" @click="loadRegistrations(occurrence)">{{ t('Participants') }}</button><button v-if="occurrence.lifecycle_status === 'scheduled'" type="button" class="btn btn-sm btn-link text-warning" @click="openOccurrenceCancel(occurrence)">{{ t('Cancel session') }}</button><button type="button" class="btn btn-sm btn-link text-danger" @click="openOccurrenceDelete(occurrence)">{{ t('Delete') }}</button></td></tr></tbody></table></div>
                    <div v-else class="text-muted small mb-3">{{ t('No occurrence yet.') }}</div>
                    <div class="border rounded p-3 bg-body-tertiary" v-if="occurrenceForm.visible">
                        <div class="row g-2">
                            <div class="col-md-6"><label class="form-label">{{ t('Session label') }}</label><input v-model="occurrenceForm.label" class="form-control"></div>
                            <div class="col-md-3"><label class="form-label">{{ t('Capacity') }}</label><input v-model.number="occurrenceForm.capacity" type="number" min="1" class="form-control" required></div>
                            <div class="col-md-3"><label class="form-label">{{ t('Timezone') }}</label><select v-model="occurrenceForm.timezone" class="form-select" required><option v-for="timezone in timezoneOptions" :key="timezone" :value="timezone">@{{ timezone }}</option></select></div>
                            <div class="col-md-6"><label class="form-label">{{ t('Starts at') }}</label><vue-date-picker v-model="occurrenceForm.starts_at" :format="'dd/MM/yyyy HH:mm'" locale="id" auto-apply :enable-time-picker="true" :is-24="true" :clearable="false" teleport="body" required></vue-date-picker></div>
                            <div class="col-md-6"><label class="form-label">{{ t('Ends at') }}</label><vue-date-picker v-model="occurrenceForm.ends_at" :format="'dd/MM/yyyy HH:mm'" locale="id" auto-apply :enable-time-picker="true" :is-24="true" :clearable="false" teleport="body" :min-date="occurrenceForm.starts_at || undefined" required></vue-date-picker></div>
                            <div class="col-md-4"><label class="form-label">{{ t('Location mode') }}</label><select v-model="occurrenceForm.location_mode" class="form-select"><option value="offline">{{ t('Offline') }}</option><option value="online">{{ t('Online') }}</option><option value="hybrid">{{ t('Hybrid') }}</option></select></div>
                            <div class="col-md-8"><label class="form-label">{{ t('Location') }}</label><input v-model="occurrenceForm.location_text" class="form-control"></div>
                            <div class="col-12"><label class="form-label">{{ t('Address') }}</label><textarea v-model="occurrenceForm.address" rows="2" class="form-control"></textarea></div>
                            <div class="col-12" v-if="occurrenceForm.location_mode !== 'offline'"><label class="form-label">{{ t('Online URL') }}</label><input v-model="occurrenceForm.online_url" type="url" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">{{ t('Registration opens') }}</label><vue-date-picker v-model="occurrenceForm.registration_open_at" :format="'dd/MM/yyyy HH:mm'" locale="id" auto-apply :enable-time-picker="true" :is-24="true" :clearable="true" teleport="body"></vue-date-picker></div>
                            <div class="col-md-6"><label class="form-label">{{ t('Registration closes') }}</label><vue-date-picker v-model="occurrenceForm.registration_close_at" :format="'dd/MM/yyyy HH:mm'" locale="id" auto-apply :enable-time-picker="true" :is-24="true" :clearable="true" teleport="body" :max-date="occurrenceForm.starts_at || undefined"></vue-date-picker></div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3"><button type="button" class="btn btn-secondary" @click="occurrenceForm.visible=false">{{ t('Cancel') }}</button><button type="button" class="btn ph-btn-theme" @click="saveOccurrence"><span v-if="occurrenceForm.id">{{ t('Save') }}</span><span v-else>{{ t('Create') }}</span></button></div>
                    </div>
                    <div v-if="registrationPanel.visible" class="border rounded p-3 mb-3"><div class="d-flex align-items-center mb-2"><strong>{{ t('Participants') }}: @{{ registrationPanel.occurrence?.label || '-' }}</strong><button type="button" class="btn-close ms-auto" @click="registrationPanel.visible=false" aria-label="{{ t('Close') }}"></button></div><div v-if="registrationPanel.rows.length" class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>{{ t('Participant') }}</th><th>{{ t('Status') }}</th><th>{{ t('Attendance') }}</th></tr></thead><tbody><tr v-for="registration in registrationPanel.rows" :key="registration.id"><td>@{{ registration.account_name || registration.account_email }}</td><td><span class="badge text-bg-secondary">@{{ registration.status }}</span></td><td><button v-if="registration.status === 'confirmed'" type="button" class="btn btn-sm btn-link" @click="markAttendance(registration, 'attended')">{{ t('Attended') }}</button><button v-if="registration.status === 'confirmed'" type="button" class="btn btn-sm btn-link text-warning" @click="markAttendance(registration, 'no_show')">{{ t('No-show') }}</button></td></tr></tbody></table></div><div v-else class="small text-muted">{{ t('No registrations found') }}</div></div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ph-content card mb-3">
                    <div class="card-body">
                        <h6 class="card-title border-bottom pb-3 mb-3">{{ t('Publish') }}</h6>
                        <div class="mb-3"><label class="form-label" for="event-publication-status">{{ t('Publication status') }}</label><select id="event-publication-status" name="publication_status" v-model="form.publication_status" class="form-select"><option value="draft">{{ t('Draft') }}</option><option value="published">{{ t('Published') }}</option><option value="hidden">{{ t('Hidden') }}</option></select></div>
                        <div class="mb-3"><label class="form-label" for="event-visibility">{{ t('Visibility') }}</label><select id="event-visibility" name="visibility" v-model="form.visibility" class="form-select"><option value="public">{{ t('Public') }}</option><option value="private">{{ t('Private') }}</option></select></div>
                        <div class="mb-3 event-duration-picker"><label class="form-label">{{ t('Reminder override') }}</label><input type="hidden" name="reminder_lead_minutes" :value="form.reminder_lead_minutes"><vue-date-picker v-model="reminderTime" time-picker auto-apply :is-24="true" :enable-seconds="false" :minutes-grid-increment="5" :clearable="true" :text-input="false" teleport="body" placeholder="{{ t('Use global default') }}" aria-label="{{ t('Reminder override') }}" @update:model-value="setDurationMinutes('reminder_lead_minutes', $event)"></vue-date-picker><small v-if="form.reminder_lead_minutes !== ''" class="d-block text-muted mt-1">{{ t('Current override') }}: @{{ formatDuration(form.reminder_lead_minutes) }}</small><small v-else class="d-block text-muted mt-1">{{ t('Blank uses the global 24-hour default.') }}</small></div>
                        <div class="mb-3 event-duration-picker"><label class="form-label">{{ t('Cancel cutoff override') }}</label><input type="hidden" name="cancel_cutoff_minutes" :value="form.cancel_cutoff_minutes"><vue-date-picker v-model="cancelCutoffTime" time-picker auto-apply :is-24="true" :enable-seconds="false" :minutes-grid-increment="5" :clearable="true" :text-input="false" teleport="body" placeholder="{{ t('Use global default') }}" aria-label="{{ t('Cancel cutoff override') }}" @update:model-value="setDurationMinutes('cancel_cutoff_minutes', $event)"></vue-date-picker><small v-if="form.cancel_cutoff_minutes !== ''" class="d-block text-muted mt-1">{{ t('Current override') }}: @{{ formatDuration(form.cancel_cutoff_minutes) }}</small><small v-else class="d-block text-muted mt-1">{{ t('Blank uses the global 24-hour default.') }}</small></div>
                        <div class="d-grid mt-4"><button type="submit" class="btn ph-btn-theme">{{ $isEdit ? t('Save Event') : t('Create Event') }}</button></div>
                    </div>
                </div>
                <div class="ph-content card mb-3"><div class="card-body"><h6 class="card-title border-bottom pb-3 mb-3">{{ t('Category') }}</h6><select name="category_id" v-model="form.category_id" class="form-select"><option value="">{{ t('Uncategorized') }}</option>@foreach ($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div></div>
                <div class="ph-content card mb-3">
                    <div class="card-body">
                        <h6 class="card-title border-bottom pb-3 mb-3">{{ t('Thumbnail') }}</h6>
                        <input type="hidden" name="remove_thumbnail" :value="form.remove_thumbnail ? '1' : '0'">
                        <input type="hidden" name="thumbnail_source" :value="thumbnailSource">
                        <input type="hidden" name="thumbnail_ckfinder_url" :value="form.thumbnail_ckfinder_url">
                        <div class="btn-group w-100 event-thumbnail-source mb-3" role="group" aria-label="{{ t('Thumbnail source') }}"><button type="button" class="btn" :class="thumbnailSource === 'upload' ? 'ph-btn-theme' : 'btn-outline-secondary'" :aria-pressed="thumbnailSource === 'upload'" @click="setThumbnailSource('upload')">{{ t('Upload file') }}</button><button type="button" class="btn" :class="thumbnailSource === 'ckfinder' ? 'ph-btn-theme' : 'btn-outline-secondary'" :aria-pressed="thumbnailSource === 'ckfinder'" @click="setThumbnailSource('ckfinder')">{{ t('CKFinder library') }}</button></div>
                        <div v-if="thumbnailSource === 'upload'" class="input-group rounded mb-3"><input ref="thumbnailInput" name="thumbnail" type="file" accept="image/jpeg,image/png,image/webp" class="form-control" @change="previewThumbnail"><button v-if="showThumbnailRemove" type="button" class="btn btn-outline-danger" @click="removeThumbnailPreview" aria-label="{{ t('Remove thumbnail') }}"><i class="fas fa-trash-alt fa-fw"></i></button></div>
                        <div v-else class="mb-3"><button type="button" class="btn ph-btn-theme-outline w-100 mb-2" @click="openThumbnailCkfinder"><i class="fas fa-folder-open fa-fw me-1"></i>{{ t('Browse CKFinder') }}</button><div v-if="form.thumbnail_ckfinder_url" class="input-group rounded"><input :value="thumbnailCkfinderLabel" class="form-control event-thumbnail-ckfinder-path" readonly><button v-if="showThumbnailRemove" type="button" class="btn btn-outline-danger" @click="removeThumbnailPreview" aria-label="{{ t('Remove thumbnail') }}"><i class="fas fa-trash-alt fa-fw"></i></button></div></div>
                        <div class="position-relative text-center d-flex justify-content-center" style="width:auto;height:350px;background-image:linear-gradient(45deg,#c3c4c7 25%,transparent 25%,transparent 75%,#c3c4c7 75%,#c3c4c7),linear-gradient(45deg,#c3c4c7 25%,transparent 25%,transparent 75%,#c3c4c7 75%,#c3c4c7);background-position:0 0,10px 10px;background-size:20px 20px;"><img v-if="form.thumbnailPreview" :src="form.thumbnailPreview" id="event-thumbnail-preview" alt="{{ t('Event thumbnail preview') }}" class="img-fluid object-fit-contain"><span v-else class="align-self-center text-muted small">{{ t('No thumbnail selected') }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade" id="eventOccurrenceDeleteModal" tabindex="-1" aria-labelledby="eventOccurrenceDeleteModalLabel" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 id="eventOccurrenceDeleteModalLabel" class="modal-title">{{ t('Delete occurrence') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ t('Close') }}"></button></div><div class="modal-body">{{ t('Do you really want to delete this occurrence?') }} <strong>@{{ pendingOccurrence?.label }}</strong></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ t('Cancel') }}</button><button type="button" class="btn btn-danger" @click="deleteOccurrence">{{ t('Delete') }}</button></div></div></div></div>
    <div class="modal fade" id="eventOccurrenceCancelModal" tabindex="-1" aria-labelledby="eventOccurrenceCancelModalLabel" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 id="eventOccurrenceCancelModalLabel" class="modal-title">{{ t('Cancel occurrence') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ t('Close') }}"></button></div><div class="modal-body">{{ t('All active registrations will be cancelled and notified.') }} <strong>@{{ pendingOccurrence?.label }}</strong></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ t('Keep') }}</button><button type="button" class="btn btn-warning" @click="cancelOccurrence">{{ t('Cancel session') }}</button></div></div></div></div>
</div>
