<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV24AutomaticCompiledNativeIsolationTest extends TestCase
{
    public function test_automatic_compiled_native_sources_do_not_reference_v23_or_mutate_canvas_state(): void
    {
        $files = [
            app_path('Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeEvidence.php'),
            app_path('Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeBlueprint.php'),
            app_path('Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeSource.php'),
            app_path('Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeFrameworkLoader.php'),
            app_path('Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeMeasurement.php'),
            app_path('Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeSectionDetector.php'),
            app_path('Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeLayoutClassifier.php'),
            app_path('Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeResponsiveClassifier.php'),
            app_path('Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeLayoutMapper.php'),
            app_path('Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeValidator.php'),
        ];

        foreach ($files as $file) {
            $this->assertFileExists($file);
            $source = (string) file_get_contents($file);
            $this->assertStringNotContainsString('pagebuilder_elementor_v23', strtolower($source), $file);
            $this->assertStringNotContainsString('PageBuilderElementorV23', $source, $file);
        }

        $controller = (string) file_get_contents(app_path('Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php'));
        $methodStart = strpos($controller, 'public function analyzeAutomaticCompiledNative');
        $methodEnd = strpos($controller, 'public function store', $methodStart);
        $method = substr($controller, $methodStart, $methodEnd - $methodStart);
        $this->assertStringNotContainsString('Page_Builder::create', $method);
        $this->assertStringNotContainsString('DB::', $method);
        $this->assertStringContainsString('finally', $method);
    }

    public function test_route_is_v24_scoped_and_analysis_is_read_only(): void
    {
        $routes = (string) file_get_contents(base_path('routes/pagebuilder_elementor_v24.php'));
        $this->assertStringContainsString('/compiled-native/automatic/analyze', $routes);
        $this->assertStringNotContainsString('pagebuilder_elementor_v23', strtolower($routes));
    }
}
