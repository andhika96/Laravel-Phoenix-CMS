<?php

use App\Support\PageBuilderElementorV24\StaticImport\StaticPageImportService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\UploadedFile;

require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$fixture = 'E:/Apps/Laragon/www/ceo-masters/index.html';
if (!is_file($fixture)) {
    http_response_code(404);
    echo 'CEO Masters fixture is not available.';
    exit;
}

$result = $app->make(StaticPageImportService::class)->convert(
    new UploadedFile($fixture, 'index.html', 'text/html', null, true),
    'auto',
    null,
    'compiled',
);
$payload = $result['compilePayload'];
$layout = $result['layout'];
?><!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>CEO Masters computed-style scanner browser QA</title>
  <script src="/public/js/pagebuilder_elementor_v24/static-import-compiler.js"></script>
  <script src="/public/js/pagebuilder_elementor_v24/static-import-native.js"></script>
  <style>
    body { margin: 0; padding: 24px; font-family: sans-serif; }
    #run { min-height: 44px; padding: 8px 14px; }
    #result { white-space: pre-wrap; overflow-wrap: anywhere; }
  </style>
</head>
<body>
  <button id="run" type="button">Run CEO Masters scanner QA</button>
  <pre id="result" aria-live="polite">idle</pre>
  <script>
    const payload = <?= json_encode($payload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?>;
    const layout = <?= json_encode($layout, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?>;

    function geometryChecks(snapshot, viewports, excludedMarkers) {
      const excluded = new Set(excludedMarkers || []);
      const nodeByMarker = new Map((snapshot.nodes || []).map(node => [node.marker, node]));
      function isExcluded(node) {
        let current = node;
        const seen = new Set();
        while (current && current.marker && !seen.has(current.marker)) {
          if (excluded.has(current.marker)) return true;
          seen.add(current.marker);
          current = nodeByMarker.get(current.parentMarker);
        }
        return false;
      }
      function isOverlay(node, viewport) {
        return ['absolute', 'fixed', 'sticky'].includes(String(node.computed?.[viewport]?.position || '').toLowerCase());
      }
      const overflow = [];
      const overlaps = [];
      Object.entries(viewports || {}).forEach(([viewport, width]) => {
        const nodes = (snapshot.nodes || []).filter(node => !isExcluded(node) && !isOverlay(node, viewport));
        nodes.forEach(node => {
          const bounds = node.bounds?.[viewport];
          if (!bounds || bounds.width <= 0 || bounds.height <= 0) return;
          if (bounds.x < -1 || bounds.right > width + 1) overflow.push({ viewport, marker: node.marker, right: bounds.right, width });
        });
        const groups = new Map();
        nodes.forEach(node => {
          const bounds = node.bounds?.[viewport];
          if (!bounds || bounds.width <= 0 || bounds.height <= 0) return;
          const group = groups.get(node.parentMarker || '__root__') || [];
          group.push({ marker: node.marker, bounds });
          groups.set(node.parentMarker || '__root__', group);
        });
        groups.forEach((group, parentMarker) => group.forEach((left, index) => group.slice(index + 1).forEach(right => {
          const intersects = left.bounds.x < right.bounds.right && left.bounds.right > right.bounds.x && left.bounds.y < right.bounds.bottom && left.bounds.bottom > right.bounds.y;
          if (intersects) overlaps.push({ viewport, parentMarker, left: left.marker, right: right.marker });
        })));
      });
      return { overflow, overlaps };
    }

    document.getElementById('run').addEventListener('click', async () => {
      const stages = [];
      try {
        const compiler = window.PhoenixStaticImportCompiler;
        const compiled = await compiler.compile(payload, { onProgress: stage => stages.push(stage) });
        const snapshot = await compiler.scanComputedStyles(payload, { onProgress: stage => stages.push(stage) });
        window.PhoenixStaticImportNative.applyComputedSnapshot(layout, snapshot);
        const fallbackSections = (payload.sections || []).filter(section => section && section.fallback);
        const fallbackMarkers = fallbackSections.map(section => section.marker);
        window.PhoenixStaticImportNative.replaceFallbackSections(layout, fallbackSections, section => ({
          id: String(section.marker || 'fallback') + '-fallback',
          type: 'static_html',
          settings: {},
        }));
        const ownedMarkers = window.PhoenixStaticImportNative.collectNodes(layout)
          .map(node => node?.settings?.importNodeKey)
          .filter(Boolean);
        const residual = compiler.filterResidualCss(compiled.css, snapshot, { ownedMarkers });
        const widths = Object.fromEntries((payload.viewports || []).map(viewport => [viewport.key, viewport.width]));
        const checks = geometryChecks(snapshot, widths, fallbackMarkers);
        document.getElementById('result').textContent = JSON.stringify({
          stages,
          sourceBytes: <?= filesize($fixture) ?>,
          compile: compiled.stats,
          scan: { ...snapshot.stats, fallbackSections: fallbackMarkers.length },
          residual: residual.stats,
          sections: (payload.sections || []).map(section => ({ sourceId: section.sourceId, fallback: section.fallback, reasons: section.fallbackReasons || [] })),
          geometry: checks,
          frameworkFreeResidual: !/(tailwind|bootstrap|cdn\.tailwindcss|--tw-)/i.test(residual.css),
          compilerIframesAfterCleanup: document.querySelectorAll('iframe[data-pb-compiler]').length,
          explicitRootPadding: layout[0]?.settings ? { top: layout[0].settings.paddingTop, right: layout[0].settings.paddingRight, bottom: layout[0].settings.paddingBottom, left: layout[0].settings.paddingLeft } : null,
        }, null, 2);
      } catch (error) {
        document.getElementById('result').textContent = JSON.stringify({ stages, error: { name: error.name, code: error.code, message: error.message }, compilerIframesAfterCleanup: document.querySelectorAll('iframe[data-pb-compiler]').length }, null, 2);
      }
    });
  </script>
</body>
</html>
