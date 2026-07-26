<?php

namespace Tests\Feature\FileManagerV2;

use Tests\TestCase;

class FileManagerV2ActionModalTest extends TestCase
{
    public function test_file_actions_use_one_feedback_modal_instead_of_browser_dialogs(): void
    {
        $template = file_get_contents(resource_path('js/filemanager_v2/App.vue'));
        $styles = file_get_contents(resource_path('js/filemanager_v2/styles.css'));

        $this->assertStringContainsString("const actionModal = ref(null);", $template);
        $this->assertStringContainsString("const actionSubmitState = ref('idle');", $template);
        $this->assertStringNotContainsString('window.prompt(', $template);
        $this->assertStringNotContainsString('window.confirm(', $template);
        $this->assertStringContainsString('<Transition name="action-modal">', $template);
        $this->assertStringContainsString("actionSubmitState.value = 'submitting';", $template);
        $this->assertStringContainsString("notify(error.message, 'failed');", $template);
        $this->assertStringContainsString('bi-x-circle-fill', $template);
        $this->assertStringContainsString('.fm-toast.is-failed {', $styles);
        $this->assertStringContainsString('.action-modal-enter-active,', $styles);
        $this->assertStringContainsString('@keyframes action-modal-leave', $styles);
    }
}
