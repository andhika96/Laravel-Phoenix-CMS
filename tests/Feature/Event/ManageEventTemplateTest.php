<?php

namespace Tests\Feature\Event;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class ManageEventTemplateTest extends TestCase
{
    public function test_manage_event_templates_do_not_nest_blade_interpolation_inside_vue_expressions(): void
    {
        $listSource = file_get_contents(resource_path('views/manage_event/manage_event.blade.php'));
        $formSource = file_get_contents(resource_path('views/manage_event/partials/form.blade.php'));
        $listCompiled = Blade::compileString($listSource);
        $formCompiled = Blade::compileString($formSource);

        $this->assertStringNotContainsString("{{ t('Save') }}", $listCompiled);
        $this->assertStringNotContainsString("{{ t('Create') }}", $listCompiled);
        $this->assertStringNotContainsString('{{ categoryForm.id ?', $listCompiled);
        $this->assertStringNotContainsString('{{ occurrenceForm.id ?', $formCompiled);
        $this->assertSame(0, preg_match('/@\{\{[^}]*\bt\s*\(/', $listSource));
        $this->assertSame(0, preg_match('/@\{\{[^}]*\bt\s*\(/', $formSource));
        $this->assertStringContainsString('openCategoryEdit(category)', $listSource);
        $this->assertStringContainsString('eventCategoryUpdateModal', $listSource);
        $this->assertStringContainsString('v-if="occurrenceForm.id"', $formSource);
        $this->assertStringContainsString('v-else', $listSource);
        $this->assertStringContainsString('v-else', $formSource);
    }

    public function test_event_editor_uses_existing_content_and_date_plugins(): void
    {
        $formSource = file_get_contents(resource_path('views/manage_event/partials/form.blade.php'));
        $addSource = file_get_contents(resource_path('views/manage_event/manage_event_add.blade.php'));
        $editSource = file_get_contents(resource_path('views/manage_event/manage_event_edit.blade.php'));
        $scriptSource = file_get_contents(public_path('assets/js/vue3/manage_event/vueV3-manage-event-form-2026.js'));

        $this->assertStringContainsString('id="editor"', $formSource);
        $this->assertStringNotContainsString('v-model="form.content"', $formSource);
        $this->assertStringContainsString('<vue-date-picker', $formSource);
        $this->assertStringNotContainsString('type="datetime-local"', $formSource);
        $this->assertStringContainsString('v-for="timezone in timezoneOptions"', $formSource);
        $this->assertStringContainsString('@input="syncUriFromTitle"', $formSource);
        $this->assertStringContainsString('ckeditor5/build/ckeditor.js', $addSource);
        $this->assertStringContainsString('vue-datepicker/js/vue-datepicker-11.0.3.js', $addSource);
        $this->assertStringContainsString('ckeditor5/build/ckeditor.js', $editSource);
        $this->assertStringContainsString('vue-datepicker/js/vue-datepicker-11.0.3.js', $editSource);
        $this->assertStringContainsString('ClassicEditor', $scriptSource);
        $this->assertStringContainsString('type=Events', $scriptSource);
        $this->assertStringContainsString('VueDatePicker', $scriptSource);
        $this->assertStringContainsString('Vue.markRaw(editor)', $scriptSource);
        $this->assertStringContainsString('syncRichEditors', $scriptSource);
    }

    public function test_summary_thumbnail_duration_and_submit_controls_follow_the_event_editor_contract(): void
    {
        $formSource = file_get_contents(resource_path('views/manage_event/partials/form.blade.php'));
        $formScript = file_get_contents(public_path('assets/js/vue3/manage_event/vueV3-manage-event-form-2026.js'));
        $eventListSource = file_get_contents(resource_path('views/event/event.blade.php'));
        $eventDetailSource = file_get_contents(resource_path('views/event/event_detail.blade.php'));
        $eventScript = file_get_contents(public_path('assets/js/vue3/event/vueV3-event-2026.js'));

        $this->assertStringContainsString('id="summary-editor"', $formSource);
        $this->assertStringNotContainsString('v-model="form.summary"', $formSource);
        $this->assertStringContainsString('summary-word-count', $formSource);
        $this->assertStringContainsString('time-picker', $formSource);
        $this->assertStringContainsString('setDurationMinutes', $formSource);
        $this->assertStringContainsString('ref="thumbnailInput"', $formSource);
        $this->assertStringContainsString('removeThumbnailPreview', $formSource);
        $this->assertStringContainsString('event-thumbnail-preview', $formSource);
        $this->assertStringContainsString('name="remove_thumbnail"', $formSource);
        $this->assertStringContainsString('summaryEditor', $formScript);
        $this->assertStringContainsString('setDurationMinutes', $formScript);
        $this->assertStringContainsString('removeThumbnailPreview', $formScript);
        $this->assertStringContainsString('summaryText(event.summary)', $eventListSource);
        $this->assertStringContainsString('{!! $event->summary !!}', $eventDetailSource);
        $this->assertStringContainsString('summaryText(value)', $eventScript);

        $submitPosition = strpos($formSource, '<button type="submit"');
        $thumbnailPosition = strpos($formSource, '<h6 class="card-title border-bottom pb-3 mb-3">{{ t(\'Thumbnail\') }}</h6>');
        $this->assertNotFalse($submitPosition);
        $this->assertNotFalse($thumbnailPosition);
        $this->assertLessThan($thumbnailPosition, $submitPosition);
    }

    public function test_thumbnail_source_picker_and_compact_duration_controls_follow_the_selected_design(): void
    {
        $formSource = file_get_contents(resource_path('views/manage_event/partials/form.blade.php'));
        $scriptSource = file_get_contents(public_path('assets/js/vue3/manage_event/vueV3-manage-event-form-2026.js'));

        $this->assertStringContainsString('event-duration-picker', $formSource);
        $this->assertStringContainsString('font-size: .875rem', $formSource);
        $this->assertStringContainsString('padding: .45rem 2.25rem .45rem 2.25rem;', $formSource);
        $this->assertStringContainsString('name="thumbnail_source"', $formSource);
        $this->assertStringContainsString('name="thumbnail_ckfinder_url"', $formSource);
        $this->assertStringContainsString('Upload file', $formSource);
        $this->assertStringContainsString('CKFinder library', $formSource);
        $this->assertStringContainsString('Browse CKFinder', $formSource);
        $this->assertStringContainsString('btn btn-outline-danger', $formSource);
        $this->assertStringContainsString('thumbnailSource', $scriptSource);
        $this->assertStringContainsString('setThumbnailSource', $scriptSource);
        $this->assertStringContainsString('openThumbnailCkfinder', $scriptSource);
        $this->assertStringContainsString('CKFinder.modal', $scriptSource);
        $this->assertStringNotContainsString('CKFinder.popup', $scriptSource);
        $this->assertStringContainsString("resourceType: 'Events'", $scriptSource);
    }

    public function test_event_category_management_uses_the_manage_article_modal_hierarchy(): void
    {
        $listSource = file_get_contents(resource_path('views/manage_event/manage_event.blade.php'));
        $scriptSource = file_get_contents(public_path('assets/js/vue3/manage_event/vueV3-manage-event-2026.js'));

        $this->assertStringContainsString('<Teleport to="body">', $listSource);
        $this->assertStringContainsString('id="eventCategoryListModal"', $listSource);
        $this->assertStringContainsString('id="eventCategoryCreateModal"', $listSource);
        $this->assertStringContainsString('id="eventCategoryUpdateModal"', $listSource);
        $this->assertStringContainsString('id="eventCategoryDeleteModal"', $listSource);
        $this->assertStringContainsString('modal-dialog ph-modal-dialog modal-dialog-centered', $listSource);
        $this->assertStringContainsString('You can manage your categories here', $listSource);
        $this->assertStringContainsString('Add New Category', $listSource);
        $this->assertStringContainsString('data-bs-backdrop="static"', $listSource);
        $this->assertStringNotContainsString('id="eventCategoryModal"', $listSource);
        $this->assertStringContainsString('categoriesLoading', $scriptSource);
        $this->assertStringContainsString('resetCategoryForm', $scriptSource);
        $this->assertStringContainsString('openCategoryCreate', $scriptSource);
        $this->assertStringContainsString('openCategoryEdit', $scriptSource);
    }
}
