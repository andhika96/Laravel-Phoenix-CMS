<?php

namespace App\Support\PageBuilderElementorV24\CompiledNative;

final class AutomaticCompiledNativeResponsiveDelta
{
    /** @param array<int,array<string,mixed>> $nodes @param array<int,array<string,mixed>> $viewports @return array<string,array<string,mixed>> */
    public function compare(array $nodes, array $viewports): array
    {
        $names = array_values(array_filter(array_map(static fn (mixed $viewport): string => is_array($viewport) ? strtolower(trim((string) ($viewport['name'] ?? ''))) : '', $viewports)));
        $result = [];
        foreach ($nodes as $node) {
            if (! is_array($node) || trim((string) ($node['sourceId'] ?? '')) === '') continue;
            $sourceId = (string) $node['sourceId'];
            $records = [];
            foreach ($names as $name) {
                $style = $this->styleFor($node, $name);
                $rect = $this->rectFor($node, $name);
                $records[$name] = [
                    'mode' => $this->mode($style),
                    'columns' => $this->columns($style),
                    'tracks' => (string) ($style['gridTemplateColumns'] ?? ''),
                    'direction' => (string) ($style['flexDirection'] ?? 'row'),
                    'visibility' => $this->visibility($style),
                    'position' => (string) ($style['position'] ?? 'static'),
                    'positionX' => (string) ($style['left'] ?? 'auto'),
                    'positionY' => (string) ($style['top'] ?? 'auto'),
                    'width' => (string) ($style['width'] ?? (($rect['width'] ?? null) !== null ? $rect['width'].'px' : 'auto')),
                    'height' => (string) ($style['height'] ?? (($rect['height'] ?? null) !== null ? $rect['height'].'px' : 'auto')),
                    'gap' => (string) ($style['gap'] ?? '0px'),
                    'order' => (int) ($style['order'] ?? 0),
                    'padding' => implode(' ', array_map(static fn (string $key): string => (string) ($style[$key] ?? '0px'), ['paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft'])),
                ];
            }
            foreach (array_keys($records[$names[0]] ?? []) as $property) {
                $values = array_values(array_map(static fn (string $name): mixed => $records[$name][$property], $names));
                if (count(array_unique(array_map('serialize', $values))) > 1) {
                    $result[$sourceId][$property] = ['viewports' => $names, 'values' => $values];
                }
            }
        }
        return $result;
    }

    /** @param array<string,mixed> $delta */
    public function strategy(array $delta): string
    {
        $modeValues = array_values(array_unique(array_map('strval', $delta['mode']['values'] ?? [])));
        if (count($modeValues) > 1) {
            $desktopMode = strtolower((string) ($delta['mode']['values'][0] ?? ''));
            if ($desktopMode !== 'grid' || array_diff($modeValues, ['grid', 'stack']) !== []) return 'manual-decision';
        }
        if (isset($delta['transform'])) return 'custom-css';
        return 'native';
    }

    /** @param array<string,mixed> $node @return array<string,string> */
    private function styleFor(array $node, string $viewport): array
    {
        $styles = is_array($node['computedStyleByViewport'] ?? null) ? $node['computedStyleByViewport'] : [];
        $desktop = is_array($styles['desktop'] ?? null) ? $styles['desktop'] : (is_array($node['computedStyle'] ?? null) ? $node['computedStyle'] : []);
        return $viewport === 'desktop' ? $desktop : array_merge($desktop, is_array($styles[$viewport] ?? null) ? $styles[$viewport] : []);
    }

    /** @param array<string,mixed> $node @return array<string,mixed> */
    private function rectFor(array $node, string $viewport): array
    {
        $rects = is_array($node['rectByViewport'] ?? null) ? $node['rectByViewport'] : [];
        return is_array($rects[$viewport] ?? null) ? $rects[$viewport] : (is_array($rects['desktop'] ?? null) ? $rects['desktop'] : []);
    }

    /** @param array<string,string> $style */
    private function mode(array $style): string
    {
        $display = strtolower(trim((string) ($style['display'] ?? 'block')));
        return $display === 'grid' ? 'grid' : (in_array($display, ['flex', 'inline-flex'], true) ? 'flex' : 'stack');
    }

    /** @param array<string,string> $style */
    private function columns(array $style): int
    {
        if ($this->mode($style) !== 'grid') return 1;
        $tracks = trim((string) ($style['gridTemplateColumns'] ?? ''));
        if (preg_match('/^repeat\(\s*(\d+)\s*,/i', $tracks, $match)) return max(1, (int) $match[1]);
        if ($tracks === '' || in_array(strtolower($tracks), ['none', 'auto'], true)) return 1;
        return max(1, count(preg_split('/\s+/', $tracks) ?: []));
    }

    /** @param array<string,string> $style */
    private function visibility(array $style): string
    {
        return strtolower((string) ($style['display'] ?? '')) === 'none' || in_array(strtolower((string) ($style['visibility'] ?? 'visible')), ['hidden', 'collapse'], true) ? 'hidden' : 'visible';
    }
}
