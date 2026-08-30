<?php

use App\Support\PageBuilderElementorV24\StaticImport\StaticPageImportService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\UploadedFile;

require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$fixture = 'E:/Apps/Laragon/www/ceo-masters/index.html';
if (! is_file($fixture)) {
    http_response_code(404);
    echo 'CEO Masters fixture is not available.';
    exit;
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
            if (! is_string($block['recommendedWidget'] ?? null) || $block['recommendedWidget'] === '') continue;
            $widgetType = $block['recommendedWidget'];
            if (($region['sourceId'] ?? '') === 'about' && ($block['role'] ?? '') === 'card_collection' && in_array('grid', $block['allowedWidgets'] ?? [], true)) $widgetType = 'grid';
            $blockMappings[] = ['blockId' => $block['id'], 'widgetType' => $widgetType];
        }
        if (count($blockMappings) !== count($blocks)) $strategy = 'exact_visual';
    }
    if ($strategy === 'auto_native' && $hasUnmappedBlock) $strategy = 'exact_visual';
    $mappingRegions[] = ['regionId' => $region['id'], 'strategy' => $strategy, 'blocks' => $strategy === 'guided_native' ? $blockMappings : []];
}

$mapping = ['version' => 1, 'regions' => $mappingRegions];
$compiled = $service->compile($makeSource(), 'auto', null, $mapping, $analysis['sourceHash'], null);
if (! ($compiled['valid'] ?? false) || ! is_array($compiled['layout'] ?? null)) {
    http_response_code(422);
    echo '<pre>'.htmlspecialchars(json_encode($compiled, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</pre>';
    exit;
}

$payload = $compiled['compilePayload'];
$layout = $compiled['layout'];
?><!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Guided Compiled Native browser QA</title>
  <script src="/public/js/pagebuilder_elementor_v24/static-import-compiler.js"></script>
  <script src="/public/js/pagebuilder_elementor_v24/static-import-native.js"></script>
  <style>
    html, body { margin: 0; min-height: 100%; font-family: Arial, sans-serif; background: #eef2f7; color: #23304a; }
    header { padding: 14px 18px; background: #fff; border-bottom: 1px solid #dbe3f2; }
    header strong, header span { display: block; }
    header span { margin-top: 4px; color: #64728b; font-size: 12px; }
    #result { margin: 16px; padding: 14px; background: #fff; border: 1px solid #dbe3f2; border-radius: 8px; white-space: pre-wrap; overflow-wrap: anywhere; }
  </style>
</head>
<body>
  <header><strong>Guided Compiled Native browser QA</strong><span id="status" aria-live="polite">Running Analyze-selected mapping through browser compiler...</span></header>
  <pre id="result">pending</pre>
  <script>
    const payload = <?= json_encode($payload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?>;
    const layout = <?= json_encode($layout, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?>;
    const mapping = <?= json_encode($mapping, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?>;

    function geometryChecks(snapshot, viewports, excludedMarkers) {
      const excluded = new Set(excludedMarkers || []);
      const byMarker = new Map((snapshot.nodes || []).map((node) => [node.marker, node]));
      function excludedNode(node) {
        const seen = new Set();
        let current = node;
        while (current && current.marker && !seen.has(current.marker)) {
          if (excluded.has(current.marker)) return true;
          seen.add(current.marker);
          current = byMarker.get(current.parentMarker);
        }
        return false;
      }
      const overflow = [];
      const overlaps = [];
      Object.entries(viewports || {}).forEach(([viewport, width]) => {
        const nodes = (snapshot.nodes || []).filter((node) => !excludedNode(node) && !['absolute', 'fixed', 'sticky'].includes(String(node.computed?.[viewport]?.position || '').toLowerCase()));
        nodes.forEach((node) => {
          const bounds = node.bounds?.[viewport];
          if (!bounds || bounds.width <= 0 || bounds.height <= 0) return;
          if (bounds.x < -1 || bounds.right > width + 1) overflow.push({ viewport, marker: node.marker, right: bounds.right, width });
        });
        const groups = new Map();
        nodes.forEach((node) => {
          const bounds = node.bounds?.[viewport];
          if (!bounds || bounds.width <= 0 || bounds.height <= 0) return;
          const group = groups.get(node.parentMarker || '__root__') || [];
          group.push({ marker: node.marker, bounds });
          groups.set(node.parentMarker || '__root__', group);
        });
        groups.forEach((group, parentMarker) => group.forEach((left, index) => group.slice(index + 1).forEach((right) => {
          const intersects = left.bounds.x < right.bounds.right && left.bounds.right > right.bounds.x && left.bounds.y < right.bounds.bottom && left.bounds.bottom > right.bounds.y;
          if (intersects) overlaps.push({ viewport, parentMarker, left: left.marker, right: right.marker });
        })));
      });
      return { overflow, overlaps };
    }

    async function run() {
      const stages = [];
      try {
        const compiler = window.PhoenixStaticImportCompiler;
        const native = window.PhoenixStaticImportNative;
        const compiled = await compiler.compile(payload, { onProgress: (stage) => stages.push(stage) });
        const snapshot = await compiler.scanComputedStyles(payload, { viewports: payload.viewports, onProgress: (stage) => stages.push(stage) });
        native.applyComputedSnapshot(layout, snapshot);
        const fallbackSections = (payload.sections || []).filter((section) => section && section.fallback);
        native.replaceFallbackSections(layout, fallbackSections, (section, currentNode) => currentNode);
        const ownedMarkers = native.collectNodes(layout).map((node) => node?.settings?.importNodeKey).filter(Boolean);
        const residual = compiler.filterResidualCss(compiled.css, snapshot, { ownedMarkers });
        const viewports = Object.fromEntries((payload.viewports || []).map((viewport) => [viewport.key, viewport.width]));
        const result = {
          mappingReport: <?= json_encode($compiled['mappingReport'] ?? null, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
          selectedMapping: mapping,
          compile: compiled.stats,
          scan: snapshot.stats,
          residual: residual.stats,
          geometry: geometryChecks(snapshot, viewports, fallbackSections.map((section) => section.marker)),
          frameworkFreeResidual: !/(tailwind|bootstrap|cdn\.tailwindcss|--tw-)/i.test(residual.css),
          compilerIframesAfterCleanup: document.querySelectorAll('iframe[data-pb-compiler]').length,
          sourceScriptsExecuted: false,
          layoutRootTypes: layout.map((node) => node?.type || ''),
          stages,
        };
        window.__guidedStaticImportQaResult = result;
        document.getElementById('status').textContent = 'Complete';
        document.getElementById('result').textContent = JSON.stringify(result, null, 2);
      } catch (error) {
        const result = { stages, error: { name: error.name, code: error.code, message: error.message }, compilerIframesAfterCleanup: document.querySelectorAll('iframe[data-pb-compiler]').length, sourceScriptsExecuted: false };
        window.__guidedStaticImportQaResult = result;
        document.getElementById('status').textContent = 'Failed';
        document.getElementById('result').textContent = JSON.stringify(result, null, 2);
      }
    }
    window.__guidedStaticImportQaReady = run();
  </script>
</body>
</html>
