<?php

use App\Support\PageBuilderElementorV24\StaticImport\StaticPageImportService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\UploadedFile;

require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$fixture = 'E:/Apps/Laragon/www/ceo-masters/index.html';
if (! is_file($fixture)) {
    fwrite(STDERR, "CEO Masters fixture is not available.\n");
    exit(1);
}

$service = $app->make(StaticPageImportService::class);
$makeSource = static fn (): UploadedFile => new UploadedFile($fixture, 'index.html', 'text/html', null, true);
$analysis = $service->analyze($makeSource(), 'auto', null, null);

$flatten = static function (array $blocks) use (&$flatten): array {
    $items = [];
    foreach ($blocks as $block) {
        if (! is_array($block)) continue;
        $items[] = $block;
        if (is_array($block['children'] ?? null)) $items = array_merge($items, $flatten($block['children']));
    }
    return $items;
};

$mappingRegions = [];
foreach ($analysis['regions'] as $region) {
    $blocks = $flatten(is_array($region['blocks'] ?? null) ? $region['blocks'] : []);
    $hasUnmappedBlock = (bool) array_filter($blocks, static fn (array $block): bool => ! is_string($block['recommendedWidget'] ?? null) || $block['recommendedWidget'] === '');
    $strategy = match ($region['sourceId'] ?? '') {
        'about' => 'guided_native',
        default => ($region['kind'] === 'footer' ? 'skip' : 'auto_native'),
    };
    $blockMappings = [];
    if ($strategy === 'guided_native') {
        foreach ($blocks as $block) {
            if (is_string($block['recommendedWidget'] ?? null) && $block['recommendedWidget'] !== '') {
                $widgetType = $block['recommendedWidget'];
                if (($region['sourceId'] ?? '') === 'about' && ($block['role'] ?? '') === 'card_collection' && in_array('grid', $block['allowedWidgets'] ?? [], true)) {
                    $widgetType = 'grid';
                }
                $blockMappings[] = ['blockId' => $block['id'], 'widgetType' => $widgetType];
            }
        }
        if (count($blockMappings) !== count($blocks)) $strategy = 'exact_visual';
    }
    if ($strategy === 'auto_native' && $hasUnmappedBlock) $strategy = 'exact_visual';
    $mappingRegions[] = [
        'regionId' => $region['id'],
        'strategy' => $strategy,
        'blocks' => $strategy === 'guided_native' ? $blockMappings : [],
    ];
}

$mapping = ['version' => 1, 'regions' => $mappingRegions];
$compiled = $service->compile($makeSource(), 'auto', null, $mapping, $analysis['sourceHash'], null);
$layoutSummary = [];
foreach (($compiled['layout'][0]['children'] ?? []) as $node) {
    if (! is_array($node)) continue;
    $settings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
    $layoutSummary[] = [
        'type' => $node['type'] ?? '',
        'marker' => $settings['importNodeKey'] ?? '',
    ];
}
$layoutNodes = [];
$walkLayout = static function (array $nodes) use (&$walkLayout, &$layoutNodes): void {
    foreach ($nodes as $node) {
        if (! is_array($node)) continue;
        $layoutNodes[] = $node;
        if (is_array($node['children'] ?? null)) $walkLayout($node['children']);
        foreach (is_array($node['columns'] ?? null) ? $node['columns'] : [] as $column) {
            if (is_array($column['children'] ?? null)) $walkLayout($column['children']);
        }
    }
};
$walkLayout(is_array($compiled['layout'] ?? null) ? $compiled['layout'] : []);
$nativeForms = array_values(array_filter($layoutNodes, static fn (array $node): bool => ($node['type'] ?? '') === 'form'));
$contactForm = $nativeForms[0] ?? null;

$summarizeBlocks = static function (array $blocks) use (&$summarizeBlocks): array {
    $result = [];
    foreach ($blocks as $block) {
        if (! is_array($block)) continue;
        $result[] = [
            'id' => $block['id'] ?? '',
            'role' => $block['role'] ?? '',
            'recommendedWidget' => $block['recommendedWidget'] ?? null,
            'allowedWidgets' => $block['allowedWidgets'] ?? [],
            'warnings' => $block['warnings'] ?? [],
            'children' => $summarizeBlocks(is_array($block['children'] ?? null) ? $block['children'] : []),
        ];
    }
    return $result;
};

echo json_encode([
    'fixture' => $fixture,
    'sourceBytes' => filesize($fixture),
    'analysis' => [
        'phase' => $analysis['phase'] ?? '',
        'sourceHash' => $analysis['sourceHash'] ?? '',
        'regionCount' => count($analysis['regions'] ?? []),
        'regions' => array_map(static fn (array $region): array => [
            'id' => $region['id'] ?? '',
            'marker' => $region['marker'] ?? '',
            'kind' => $region['kind'] ?? '',
            'sourceId' => $region['sourceId'] ?? '',
            'label' => $region['label'] ?? '',
            'recommendedStrategy' => $region['recommendedStrategy'] ?? '',
            'recommendedWidget' => $region['recommendedWidget'] ?? null,
            'allowedWidgets' => $region['allowedWidgets'] ?? [],
            'stats' => $region['stats'] ?? [],
            'blocks' => $summarizeBlocks(is_array($region['blocks'] ?? null) ? $region['blocks'] : []),
        ], $analysis['regions'] ?? []),
        'hasLayout' => array_key_exists('layout', $analysis),
        'hasCustomCss' => array_key_exists('customCss', $analysis),
    ],
    'mapping' => $mapping,
    'compile' => [
        'phase' => $compiled['phase'] ?? '',
        'valid' => $compiled['valid'] ?? false,
        'sourceHashMatchesAnalyze' => ($compiled['sourceHash'] ?? '') === ($analysis['sourceHash'] ?? ''),
        'mappingReport' => $compiled['mappingReport'] ?? null,
        'layoutChildren' => $layoutSummary,
        'containsGridSelection' => is_array($compiled['layout'] ?? null) && str_contains(json_encode($compiled['layout'], JSON_THROW_ON_ERROR), '"type":"grid"'),
        'errors' => $compiled['errors'] ?? [],
        'contactNativeForm' => [
            'count' => count($nativeForms),
            'fieldCount' => is_array($contactForm) && is_array($contactForm['settings']['fields'] ?? null) ? count($contactForm['settings']['fields']) : 0,
            'placeholderNodes' => $compiled['report']['placeholderNodes'] ?? 0,
        ],
    ],
    'report' => [
        'relativeAssets' => $compiled['report']['relativeAssets'] ?? [],
        'warnings' => $compiled['report']['warnings'] ?? [],
        'sourceScripts' => $compiled['report']['sourceScripts'] ?? [],
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
echo PHP_EOL;
