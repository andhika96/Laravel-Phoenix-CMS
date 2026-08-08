<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class PageBuilderElementorGeneralWidgetsTest extends TestCase
{
    private const WIDGETS = [
        'counter' => ['label' => 'Counter', 'directory' => 'counter', 'view' => 'render_counter'],
        'progress_bar' => ['label' => 'Progress Bar', 'directory' => 'progress-bar', 'view' => 'render_progress_bar'],
        'testimonial' => ['label' => 'Testimonial', 'directory' => 'testimonial', 'view' => 'render_testimonial'],
        'social_icons' => ['label' => 'Social Icons', 'directory' => 'social-icons', 'view' => 'render_social_icons'],
        'alert' => ['label' => 'Alert', 'directory' => 'alert', 'view' => 'render_alert'],
    ];

    public function test_the_five_general_widgets_are_registered_with_their_editor_and_frontend_modules(): void
    {
        foreach (self::WIDGETS as $type => $widget) {
            $definition = config("pagebuilder_elementor_widgets.{$type}");

            $this->assertIsArray($definition, "Missing {$type} widget definition.");
            $this->assertSame($type, $definition['type']);
            $this->assertSame($widget['label'], $definition['label']);
            $this->assertSame('general', $definition['category']);
            $this->assertTrue($definition['toolbox']);
            $this->assertFileExists(public_path("{$definition['definition']}"));
            $this->assertFileExists(public_path("{$definition['canvas']}"));
            $this->assertFileExists(public_path("{$definition['settings']}"));
            $this->assertTrue(view()->exists($definition['view']));
        }
    }

    public function test_each_widget_has_compact_content_style_advanced_controls_and_canvas_markers(): void
    {
        foreach (self::WIDGETS as $type => $widget) {
            $definition = config("pagebuilder_elementor_widgets.{$type}");
            $settings = File::get(public_path($definition['settings']));
            $canvas = File::get(public_path($definition['canvas']));

            $this->assertStringContainsString("editor.settingsTab==='content'", $settings, "{$type} is missing Content tab.");
            $this->assertStringContainsString("editor.settingsTab==='style'", $settings, "{$type} is missing Style tab.");
            $this->assertStringContainsString("editor.settingsTab==='advanced'", $settings, "{$type} is missing Advanced tab.");
            $this->assertStringContainsString('editor.widgetAdvancedControls', $settings, "{$type} is missing shared Advanced controls.");
            $this->assertStringContainsString('pb-form-group', $settings, "{$type} is missing the compact General form pattern.");
            $this->assertStringContainsString('pb-', $canvas);
        }
    }

    public function test_widget_specific_controls_and_frontend_renderers_are_wired(): void
    {
        $counter = File::get(public_path('js/pagebuilder_elementor/widgets/general/counter/Settings.vue'));
        $this->assertStringContainsString('Starting Number', $counter);
        $this->assertStringContainsString('Animation Duration (ms)', $counter);
        $this->assertStringContainsString('numberTextShadow', $counter);

        $progress = File::get(public_path('js/pagebuilder_elementor/widgets/general/progress-bar/Settings.vue'));
        $this->assertStringContainsString('Display Percentage', $progress);
        $this->assertStringContainsString('Inner Text', $progress);

        $testimonial = File::get(public_path('js/pagebuilder_elementor/widgets/general/testimonial/Settings.vue'));
        $this->assertStringContainsString('Image Resolution', $testimonial);
        $this->assertStringContainsString('Image Position', $testimonial);
        $this->assertStringContainsString('editor.linkControl', $testimonial);
        $this->assertStringContainsString('pb-bg-media-field pb-widget-settings__media-field', $testimonial);
        $this->assertStringContainsString("editor.chooseMedia(node.settings,'imageUrl')", $testimonial);
        $this->assertStringContainsString('label="Border Width" base="imageBorderWidth"', $testimonial);
        $this->assertStringContainsString('label="Border Radius" base="imageBorderRadius"', $testimonial);
        $this->assertStringNotContainsString('v-model="node.settings.imageBorderWidth"', $testimonial);
        $this->assertStringNotContainsString('v-model="node.settings.imageBorderRadius"', $testimonial);

        $social = File::get(public_path('js/pagebuilder_elementor/widgets/general/social-icons/Settings.vue'));
        $this->assertStringContainsString('editor.openSocialIconLibrary', $social);
        $this->assertStringContainsString('Rows Gap', $social);
        $this->assertStringContainsString('Hover Animation', $social);
        $this->assertStringContainsString('pb-icon-picker-field', $social);
        $this->assertStringContainsString('label="Border Radius" base="borderRadius"', $social);
        $this->assertStringNotContainsString('v-model="node.settings.borderRadius"', $social);

        $alert = File::get(public_path('js/pagebuilder_elementor/widgets/general/alert/Settings.vue'));
        $this->assertStringContainsString('Dismiss Icon', $alert);
        $this->assertStringContainsString('Transition Duration', $alert);
        $this->assertStringContainsString('pb-icon-picker-field', $alert);
        $this->assertStringContainsString('label="Left Border Width" base="borderWidth"', $alert);
        $this->assertStringNotContainsString('v-model="node.settings.borderWidth"', $alert);

        foreach (self::WIDGETS as $type => $widget) {
            $view = File::get(resource_path("views/pagebuilder_elementor/partials/{$widget['view']}.blade.php"));
            $this->assertStringContainsString('WidgetAdvancedStyleResolver', $view, "{$type} renderer misses Advanced output.");
            $this->assertStringContainsString('el-widget-', $view, "{$type} renderer misses frontend widget class.");
        }
    }

    public function test_elementor_control_mapping_and_canvas_interactions_stay_in_the_correct_layer(): void
    {
        $counter = File::get(public_path('js/pagebuilder_elementor/widgets/general/counter/Settings.vue'));
        $counterContent = Str::before($counter, "\n\t\t<div v-show=\"editor.settingsTab==='style'\"");
        $counterStyle = Str::after($counter, "\n\t\t<div v-show=\"editor.settingsTab==='style'\"");

        $this->assertStringContainsString('Title HTML Tag', $counterContent);
        $this->assertStringNotContainsString('Title Position', $counterContent);
        $this->assertStringContainsString('<summary>Layout</summary>', $counterStyle);
        $this->assertStringContainsString('Title Position', $counterStyle);
        $this->assertStringContainsString('Number Position', $counterStyle);

        $social = File::get(public_path('js/pagebuilder_elementor/widgets/general/social-icons/Settings.vue'));
        $socialContent = Str::before($social, "\n\t\t<div v-show=\"editor.settingsTab==='style'\"");
        $socialStyle = Str::after($social, "\n\t\t<div v-show=\"editor.settingsTab==='style'\"");

        $this->assertStringContainsString('Shape', $socialContent);
        $this->assertStringContainsString('Columns', $socialContent);
        $this->assertStringContainsString('label="Alignment"', $socialContent);
        $this->assertStringNotContainsString('<label class="pb-form-label">Shape</label>', $socialStyle);
        $this->assertStringNotContainsString('<label class="pb-form-label">Columns</label>', $socialStyle);
        $this->assertStringNotContainsString('label="Alignment"', $socialStyle);
        $this->assertStringContainsString('pb-state-tabs pb-state-tabs--two', $socialStyle);

        $alertCanvas = File::get(public_path('js/pagebuilder_elementor/widgets/general/alert/Canvas.vue'));
        $this->assertStringContainsString('@click.stop="dismissed = true"', $alertCanvas);
        $this->assertStringContainsString(':global(.pb-node-alert .pb-preview) { pointer-events: auto; }', $alertCanvas);

        $panelCss = File::get(public_path('assets/css/pagebuilder_elementor.css'));
        $this->assertStringContainsString('.pb-widget-settings--general-new .pb-label-row.pb-label-row-device', $panelCss);
        $this->assertStringContainsString('.pb-widget-settings--general-new .pb-compact-choice .pb-seg-btn', $panelCss);
        $this->assertStringContainsString('height: 30px !important;', $panelCss);
        $this->assertStringContainsString('.pb-widget-settings--general-new .pb-typography-trigger', $panelCss);
        $this->assertStringContainsString('.pb-widget-settings--general-new .pb-text-effect-trigger', $panelCss);
    }

    public function test_shared_editor_runtime_includes_the_new_widgets_and_icon_picker_targets(): void
    {
        $app = File::get(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertStringContainsString("['counter', 'progress_bar', 'testimonial', 'social_icons', 'alert', 'rating', 'text_path']", $app);
        foreach (['counter' => "counter: 'Counter'", 'progress_bar' => "progress_bar: 'Progress Bar'", 'testimonial' => "testimonial: 'Testimonial'", 'social_icons' => "social_icons: 'Social Icons'", 'alert' => "alert: 'Alert'"] as $marker) {
            $this->assertStringContainsString($marker, $app);
        }

        $this->assertStringContainsString('openSocialIconLibrary', $app);
        $this->assertStringContainsString('chooseSocialIconSvg', $app);
        $this->assertStringContainsString('openAlertIconLibrary', $app);
        $this->assertStringContainsString('chooseAlertIconSvg', $app);
        $this->assertStringContainsString("settingKey === 'socialIconItem'", $app);
        $this->assertStringContainsString("settingKey === 'alertDismissIcon'", $app);
    }

    public function test_minimal_widget_settings_render_without_blade_errors(): void
    {
        $settings = [
            'counter' => ['startingNumber' => 0, 'endingNumber' => 100, 'title' => 'Counter'],
            'progress_bar' => ['title' => 'Progress', 'percentage' => 50, 'displayPercentage' => true],
            'testimonial' => ['content' => 'Great work', 'name' => 'Jane Doe', 'title' => 'Customer'],
            'social_icons' => ['items' => [['id' => 'social-1', 'iconName' => 'facebook-f', 'iconClass' => 'fab fa-facebook-f', 'iconSource' => 'library', 'linkUrl' => '#']], 'columns' => 'auto'],
            'alert' => ['type' => 'info', 'title' => 'Notice', 'description' => 'Hello', 'dismissIcon' => true, 'dismissIconClass' => 'fas fa-times'],
        ];

        foreach (self::WIDGETS as $type => $widget) {
            $html = view(config("pagebuilder_elementor_widgets.{$type}.view"), [
                'node' => ['id' => "test-{$type}", 'type' => $type, 'settings' => $settings[$type]],
            ])->render();

            $this->assertStringContainsString('el-widget-', $html, "{$type} did not render its frontend root.");
            $this->assertStringContainsString('pb-node-test-', $html, "{$type} did not receive an Advanced resolver id.");
        }
    }
}
