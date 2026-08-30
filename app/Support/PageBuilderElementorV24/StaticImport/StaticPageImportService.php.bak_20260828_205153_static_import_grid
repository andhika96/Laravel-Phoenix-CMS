<?php

namespace App\Support\PageBuilderElementorV24\StaticImport;

use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use ZipArchive;

final class StaticPageImportService
{
    private int $sequence = 0;

    /** @return array<string, mixed> */
    public function convert(UploadedFile $source, string $framework = 'auto', ?string $entry = null): array
    {
        $extension = strtolower((string) ($source->getClientOriginalExtension() ?: pathinfo($source->getClientOriginalName(), PATHINFO_EXTENSION)));
        if ($extension === 'zip') {
            return $this->convertZip($source, $framework, $entry);
        }
        if (! in_array($extension, ['html', 'htm'], true)) {
            throw new InvalidArgumentException('Only HTML or ZIP files are supported.');
        }
        if (($source->getSize() ?: 0) > 2 * 1024 * 1024) {
            throw new InvalidArgumentException('HTML source exceeds the conservative 2 MB limit.');
        }

        $html = file_get_contents($source->getRealPath());
        if ($html === false || trim($html) === '') {
            throw new InvalidArgumentException('The HTML file is empty or unreadable.');
        }

        return $this->convertHtml($html, $framework, pathinfo($source->getClientOriginalName(), PATHINFO_FILENAME));
    }

    /** @return array<string, mixed> */
    private function convertZip(UploadedFile $source, string $framework, ?string $requestedEntry): array
    {
        if (($source->getSize() ?: 0) > 20 * 1024 * 1024) {
            throw new InvalidArgumentException('ZIP source exceeds the 20 MB compressed limit.');
        }

        $zip = new ZipArchive();
        if ($zip->open($source->getRealPath()) !== true) {
            throw new InvalidArgumentException('The ZIP source could not be opened.');
        }

        $htmlEntries = [];
        $assetCount = 0;
        $expandedBytes = 0;
        try {
            if ($zip->numFiles > 500) throw new InvalidArgumentException('ZIP contains more than 500 files.');
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $stat = $zip->statIndex($index);
                $name = str_replace('\\', '/', (string) ($stat['name'] ?? ''));
                if ($name === '' || str_ends_with($name, '/')) continue;
                if (str_starts_with($name, '/') || preg_match('/^[A-Za-z]:/', $name) || preg_match('~(^|/)\.\.(?:/|$)~', $name)) {
                    throw new InvalidArgumentException('ZIP contains an unsafe path.');
                }
                if (substr_count(trim($name, '/'), '/') > 8) throw new InvalidArgumentException('ZIP path depth exceeds the limit.');
                $expandedBytes += max(0, (int) ($stat['size'] ?? 0));
                if ($expandedBytes > 100 * 1024 * 1024) throw new InvalidArgumentException('ZIP expanded content exceeds the 100 MB limit.');
                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (in_array($extension, ['html', 'htm'], true)) $htmlEntries[] = $name;
                if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'woff', 'woff2', 'ttf', 'otf'], true)) $assetCount++;
            }

            $entry = $this->selectZipEntry($htmlEntries, $requestedEntry);
            $html = $zip->getFromName($entry);
            if ($html === false || trim($html) === '') throw new InvalidArgumentException('The selected ZIP HTML entry is empty.');
        } finally {
            $zip->close();
        }

        // ponytail: ZIP assets stay as reported paths in this vertical slice; promote through FileManagerV2 batches when asset persistence is scheduled.
        $result = $this->convertHtml($html, $framework, pathinfo($entry, PATHINFO_FILENAME), [
            'entry' => $entry,
            'missingAssets' => $assetCount,
        ]);
        if ($assetCount > 0) {
            $result['report']['warnings'][] = 'ZIP assets were detected but are not uploaded to File Manager in this slice.';
            $result['status'] = 'partial';
        }
        return $result;
    }

    /** @param array<int, string> $entries */
    private function selectZipEntry(array $entries, ?string $requestedEntry): string
    {
        $normalized = array_values(array_unique($entries));
        if ($requestedEntry !== null && trim($requestedEntry) !== '') {
            $requested = str_replace('\\', '/', trim($requestedEntry));
            foreach ($normalized as $entry) if (strcasecmp($entry, $requested) === 0) return $entry;
            throw new InvalidArgumentException('Requested ZIP HTML entry was not found.');
        }
        foreach (['home.html', 'index.html'] as $preferred) {
            foreach ($normalized as $entry) if (strcasecmp(basename($entry), $preferred) === 0) return $entry;
        }
        if (count($normalized) === 1) return $normalized[0];
        throw new InvalidArgumentException('ZIP must contain home.html, index.html, or one unambiguous HTML entry.');
    }

    /** @return array<string, mixed> */
    private function convertHtml(string $html, string $framework, string $fallbackName, array $reportOverrides = []): array
    {
        $this->sequence = 0;
        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML($html, LIBXML_NONET | LIBXML_HTML_NODEFDTD | LIBXML_COMPACT);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            throw new InvalidArgumentException('The HTML document could not be parsed.');
        }

        $report = array_merge([
            'frameworks' => $this->detectFrameworks($html, $framework),
            'entry' => $fallbackName.'.html',
            'mappedNodes' => 0,
            'placeholderNodes' => 0,
            'importedAssets' => 0,
            'missingAssets' => 0,
            'droppedScripts' => 0,
            'droppedStyles' => 0,
            'warnings' => [],
        ], $reportOverrides);

        foreach (['script', 'link', 'style'] as $tag) {
            $elements = iterator_to_array($dom->getElementsByTagName($tag));
            foreach ($elements as $element) {
                if ($tag === 'script') $report['droppedScripts']++;
                if ($tag === 'style' || $tag === 'link') $report['droppedStyles']++;
                $element->parentNode?->removeChild($element);
            }
        }
        if ($report['droppedStyles'] > 0) {
            // ponytail: ignore full framework CSS for deterministic native mapping; add scoped CSS extraction when CSS sanitizer is ready.
            $report['warnings'][] = 'Stylesheets were not copied; only supported framework utilities were mapped.';
        }
        if ($report['droppedScripts'] > 0) {
            $report['warnings'][] = 'Scripts and inline behavior were removed from the imported page.';
        }

        $title = '';
        $titles = $dom->getElementsByTagName('title');
        if ($titles->length > 0) $title = trim((string) $titles->item(0)?->textContent);
        $body = $dom->getElementsByTagName('body')->item(0);
        $children = [];
        if ($body instanceof DOMElement) {
            foreach (iterator_to_array($body->childNodes) as $child) {
                $mapped = $this->mapNode($child, $report);
                if ($mapped !== null) $children[] = $mapped;
            }
        }

        if ($children === []) {
            $report['warnings'][] = 'No supported visible body content was found.';
        }

        return [
            'status' => $report['warnings'] === [] ? 'success' : 'partial',
            'frameworks' => $report['frameworks'],
            'pageName' => $title !== '' ? $title : ($fallbackName !== '' ? $fallbackName : 'Imported Page'),
            'layout' => [$this->node('container', [
                'displayType' => 'flex',
                'direction' => 'column',
                'contentWidth' => 'full',
                'cssClass' => 'pb-import-root',
            ], $children)],
            'customCss' => '',
            'assetBatchId' => null,
            'report' => $report,
        ];
    }

    /** @param array<string, mixed> $report */
    private function mapNode(DOMNode $source, array &$report): ?array
    {
        if ($source->nodeType === XML_TEXT_NODE) {
            return trim((string) $source->textContent) === '' ? null : $this->textNode((string) $source->textContent, $report);
        }
        if (! $source instanceof DOMElement) return null;

        $tag = strtolower($source->tagName);
        if (in_array($tag, ['script', 'style', 'link', 'meta', 'noscript', 'iframe', 'object', 'embed'], true)) return null;
        if ($tag === 'form') {
            $report['placeholderNodes']++;
            $report['warnings'][] = 'Form markup was kept as a static placeholder; submit behavior was not imported.';
            return $this->textNode('Imported form (static preview only).', $report, true);
        }

        if (preg_match('/^h([1-6])$/', $tag, $match)) {
            $report['mappedNodes']++;
            return $this->node('heading', [
                'text' => trim((string) $source->textContent),
                'tag' => 'h'.$match[1],
            ]);
        }

        if ($tag === 'img') {
            $report['mappedNodes']++;
            $src = $this->safeUrl($source->getAttribute('src'));
            if ($src === '') {
                $report['missingAssets']++;
                $report['warnings'][] = 'An image source was unsafe or empty and was omitted.';
            }
            return $this->node('image', [
                'src' => $src,
                'alt' => trim($source->getAttribute('alt')),
                'imageSource' => 'url',
            ]);
        }

        if ($tag === 'hr') {
            $report['mappedNodes']++;
            return $this->node('divider', []);
        }

        if ($tag === 'a' && $this->hasClass($source, 'btn')) {
            $report['mappedNodes']++;
            $href = $this->safeUrl($source->getAttribute('href')) ?: '#';
            return $this->node('button', [
                'text' => trim((string) $source->textContent) ?: 'Button',
                'url' => $href,
            ]);
        }

        if (in_array($tag, ['p', 'span', 'strong', 'em', 'b', 'i', 'a', 'ul', 'ol', 'li', 'blockquote'], true)) {
            return $this->textNode($this->innerHtml($source), $report);
        }

        $childNodes = [];
        foreach (iterator_to_array($source->childNodes) as $child) {
            $mapped = $this->mapNode($child, $report);
            if ($mapped !== null) $childNodes[] = $mapped;
        }
        if ($childNodes === []) {
            return trim((string) $source->textContent) === '' ? null : $this->textNode($this->innerHtml($source), $report);
        }

        $settings = $this->layoutSettings($source);
        $type = $this->hasClass($source, 'container-fluid') ? 'container_fluid' : 'container';
        $report['mappedNodes']++;
        return $this->node($type, $settings, $childNodes);
    }

    /** @param array<string, mixed> $report */
    private function textNode(string $html, array &$report, bool $placeholder = false): array
    {
        $report['mappedNodes']++;
        return $this->node('text_editor', ['html' => $this->sanitizeHtml($html), 'align' => 'left'], null, $placeholder ? 'Imported Placeholder' : null);
    }

    /** @return array<string, mixed> */
    private function layoutSettings(DOMElement $element): array
    {
        $classes = $this->classes($element);
        $settings = [
            'displayType' => in_array('grid', $classes, true) ? 'grid' : 'flex',
            'direction' => in_array('flex-col', $classes, true) ? 'column' : 'row',
            'contentWidth' => 'full',
        ];

        if (in_array('row', $classes, true) || in_array('flex-row', $classes, true)) $settings['direction'] = 'row';
        if (in_array('flex-col', $classes, true)) $settings['direction'] = 'column';

        foreach ($classes as $class) {
            if (preg_match('/^(?:gap|gap-y)-([0-9]+)$/', $class, $match)) {
                $settings['flexRowGap'] = $this->tailwindSpacing($match[1]);
            }
            if (preg_match('/^p-([0-9]+)$/', $class, $match)) {
                $padding = $this->spacing($match[1]);
                $settings['paddingTop'] = $padding;
                $settings['paddingRight'] = $padding;
                $settings['paddingBottom'] = $padding;
                $settings['paddingLeft'] = $padding;
            }
            if (preg_match('/^col(?:-([0-9]+))?$/', $class, $match)) {
                $settings['containerWidth'] = $this->percentFromColumns($match[1] ?: '12');
            }
            if (preg_match('/^col-md-([0-9]+)$/', $class, $match)) {
                $settings['containerWidthTablet'] = $this->percentFromColumns($match[1]);
            }
            if (preg_match('/^col-lg-([0-9]+)$/', $class, $match)) {
                $settings['containerWidth'] = $this->percentFromColumns($match[1]);
            }
        }

        return $settings;
    }

    /** @return array<int, string> */
    private function detectFrameworks(string $html, string $requested): array
    {
        $source = strtolower($html);
        $frameworks = [];
        $bootstrap = str_contains($source, 'bootstrap@5')
            || str_contains($source, 'bootstrap.min.css')
            || preg_match('/\b(?:container-fluid|row|col(?:-[a-z]+)?-\d+)\b/i', $html);
        $tailwind = str_contains($source, 'cdn.tailwindcss.com')
            || str_contains($source, '@tailwind')
            || preg_match('/\b(?:flex-col|grid-cols-\d+|gap-\d+|p-\d+|md:|lg:)\b/i', $html);
        if ($requested === 'bootstrap5') $bootstrap = true;
        if ($requested === 'tailwind') $tailwind = true;
        if ($bootstrap) $frameworks[] = 'bootstrap5';
        if ($tailwind) $frameworks[] = 'tailwind';
        return $frameworks ?: ['unknown'];
    }

    private function spacing(string $value): string
    {
        return [
            '0' => '0', '1' => '.25rem', '2' => '.5rem', '3' => '.75rem', '4' => '1rem',
            '5' => '1.25rem', '6' => '1.5rem', '8' => '2rem', '10' => '2.5rem', '12' => '3rem',
            '16' => '4rem', '20' => '5rem', '24' => '6rem',
        ][$value] ?? '0';
    }

    private function tailwindSpacing(string $value): string
    {
        return $this->spacing($value);
    }

    private function percentFromColumns(string $columns): string
    {
        return (string) (round((max(1, min(12, (int) $columns)) / 12) * 100, 4)).'%';
    }

    /** @return array<int, string> */
    private function classes(DOMElement $element): array
    {
        return array_values(array_filter(preg_split('/\s+/', trim($element->getAttribute('class'))) ?: []));
    }

    private function hasClass(DOMElement $element, string $class): bool
    {
        return in_array($class, $this->classes($element), true);
    }

    private function safeUrl(string $value): string
    {
        $value = trim($value);
        return $value !== '' && ! str_starts_with($value, '//') && preg_match('/^(?:https?:|\/|#|data:image\/(?:png|gif|jpe?g|webp);base64,)/i', $value)
            ? $value
            : '';
    }

    private function innerHtml(DOMElement $element): string
    {
        $html = '';
        foreach (iterator_to_array($element->childNodes) as $child) $html .= $element->ownerDocument?->saveHTML($child) ?: '';
        return $html !== '' ? $html : trim((string) $element->textContent);
    }

    private function sanitizeHtml(string $html): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<!doctype html><html><body><div id="pb-import-text">'.$html.'</div></body></html>', LIBXML_NONET | LIBXML_HTML_NODEFDTD | LIBXML_COMPACT);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $root = $dom->getElementById('pb-import-text');
        if (! $root) return strip_tags($html);
        $allowed = ['P', 'BR', 'STRONG', 'EM', 'B', 'I', 'A', 'UL', 'OL', 'LI', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE'];
        $walk = function (DOMNode $parent) use (&$walk, $allowed): void {
            foreach (iterator_to_array($parent->childNodes) as $child) {
                if (! $child instanceof DOMElement) continue;
                $tag = strtoupper($child->tagName);
                if (! in_array($tag, $allowed, true)) {
                    while ($child->firstChild) $parent->insertBefore($child->firstChild, $child);
                    $parent->removeChild($child);
                    continue;
                }
                foreach (iterator_to_array($child->attributes) as $attribute) {
                    $name = strtolower($attribute->name);
                    $value = (string) $attribute->value;
                    $allowedAttribute = $tag === 'A' && in_array($name, ['href', 'title', 'target', 'rel'], true);
                    if (! $allowedAttribute || ($name === 'href' && ! preg_match('/^(?:https?:|mailto:|tel:|\/|#)/i', $value))) {
                        $child->removeAttribute($attribute->name);
                    }
                }
                $walk($child);
            }
        };
        $walk($root);
        $output = '';
        foreach (iterator_to_array($root->childNodes) as $child) $output .= $dom->saveHTML($child) ?: '';
        return $output;
    }

    /** @param array<string, mixed> $settings */
    private function node(string $type, array $settings, ?array $children = null, ?string $label = null): array
    {
        $node = [
            'id' => 'import-node-'.$this->sequence++,
            'type' => $type,
            'label' => $label ?: ucwords(str_replace('_', ' ', $type)),
            'labelSuffix' => '',
            'settings' => $settings,
        ];
        if ($children !== null) $node['children'] = $children;
        return $node;
    }
}
