<?php

use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeFrameworkLoader;
use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeLayoutClassifier;
use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeLayoutMapper;
use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeMeasurement;
use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeSectionDetector;
use App\Support\PageBuilderElementorV24\CompiledNative\SourcePackage;
use Illuminate\Contracts\Console\Kernel;

require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$sourcePath = 'E:\Apps\Laragon\www\ceo-masters\index.html';
$html = file_get_contents($sourcePath);
if ($html === false) {
    fwrite(STDERR, "Source could not be read.\n");
    exit(1);
}

$source = new SourcePackage($html, 'index.html', dirname($sourcePath), []);
$viewports = [
    ['name' => 'desktop', 'width' => 1180, 'height' => 900],
    ['name' => 'tablet', 'width' => 768, 'height' => 1024],
    ['name' => 'mobile', 'width' => 390, 'height' => 900],
];
$bundle = (new AutomaticCompiledNativeFrameworkLoader)->prepare($source, 'auto');
$snapshot = (new AutomaticCompiledNativeMeasurement)->measure($source, $viewports, $bundle);
$sections = (new AutomaticCompiledNativeSectionDetector)->detect($snapshot);
$blueprint = (new AutomaticCompiledNativeLayoutClassifier)->classify($sections, $snapshot);
$mapper = new AutomaticCompiledNativeLayoutMapper;
$target = $mapper->toPhoenixLayout($blueprint);

$result = [
    'source' => $sourcePath,
    'framework' => $bundle->framework,
    'measuredNodes' => count($snapshot['nodes'] ?? []),
    'sections' => [],
];
foreach ($blueprint['sections'] as $section) {
    if (($section['kind'] ?? '') !== 'section') continue;
    $id = (string) ($section['sourceId'] ?? $section['id'] ?? '');
    if (! in_array($id, ['home', 'register'], true)) continue;
    $mapped = collect($target['nodes'])->firstWhere('sourceSectionId', $id);
    $result['sections'][$id] = [
        'layoutByViewport' => collect($section['layoutByViewport'] ?? [])->map(fn (array $layout): array => [
            'mode' => $layout['mode'] ?? null,
            'columns' => $layout['columns'] ?? null,
            'evidenceRule' => $layout['evidence']['rule'] ?? null,
            'diagnostics' => $layout['diagnostics'] ?? [],
        ])->all(),
        'positionedLayers' => $section['layoutRelationships']['positionedLayers'] ?? [],
        'overlapPairs' => $section['layoutRelationships']['overlapPairs'] ?? [],
        'responsiveDeltas' => $section['responsiveDeltas'] ?? [],
        'mappingNodes' => count($section['normalizedBlocks']['mappingNodes'] ?? []),
        'compileStatus' => $mapped['compileStatus'] ?? null,
        'unsupported' => $mapped['unsupported'] ?? [],
    ];
}

$output = __DIR__.'/MAPPER_EXPANSION_REPLAY.json';
file_put_contents($output, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL);
echo $output.PHP_EOL;
