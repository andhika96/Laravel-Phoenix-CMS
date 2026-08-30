<?php

namespace Tests\Feature;

use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeLayoutClassifier;
use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeSectionDetector;
use Tests\TestCase;

class PageBuilderElementorV24AutomaticCompiledNativeFixtureTest extends TestCase
{
    public function test_archived_measurements_classify_each_fixture_without_a_fixed_column_default(): void
    {
        $expected = [
            'automatic-one-column.snapshot.json' => ['single' => ['stack', 1]],
            'automatic-two-column-grid.snapshot.json' => ['hero' => ['grid', 2]],
            'automatic-three-column-grid.snapshot.json' => ['cards' => ['grid', 3]],
            'automatic-flex-wrap.snapshot.json' => ['wrap' => ['flex', 4]],
            'automatic-nested-divs.snapshot.json' => ['nested' => ['stack', 1]],
            'automatic-responsive-collapse.snapshot.json' => ['responsive' => ['grid', 3, 2, 1]],
        ];
        $directory = base_path('project-artifacts/qa/pagebuilder-v24-automatic-compiled-native-20260829');
        $this->assertDirectoryExists($directory);

        $detector = new AutomaticCompiledNativeSectionDetector;
        $classifier = new AutomaticCompiledNativeLayoutClassifier;
        foreach ($expected as $file => $sections) {
            $path = $directory.DIRECTORY_SEPARATOR.$file;
            $this->assertFileExists($path);
            $snapshot = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
            $index = $detector->detect($snapshot);
            $blueprint = $classifier->classify($index, $snapshot);
            foreach ($sections as $sectionId => $expectation) {
                $section = collect($blueprint['sections'])->firstWhere('id', $sectionId);
                $this->assertIsArray($section, "Missing classified section {$sectionId} in {$file}");
                $desktop = $section['layoutByViewport']['desktop'];
                $this->assertSame($expectation[0], $desktop['mode'], $file);
                $this->assertSame($expectation[1], $desktop['columns'], $file);
                if (count($expectation) === 4) {
                    $this->assertSame($expectation[2], $section['layoutByViewport']['tablet']['columns'], $file);
                    $this->assertSame($expectation[3], $section['layoutByViewport']['mobile']['columns'], $file);
                }
                $this->assertNotEmpty($desktop['evidence'], $file);
            }
        }
    }
}
