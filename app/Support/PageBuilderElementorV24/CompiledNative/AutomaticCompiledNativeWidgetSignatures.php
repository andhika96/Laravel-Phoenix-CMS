<?php

namespace App\Support\PageBuilderElementorV24\CompiledNative;

final class AutomaticCompiledNativeWidgetSignatures
{
    /** @param array<string,mixed> $mappingNode @param array<string,array<string,mixed>> $rawNodesById @param array<string,mixed> $relationships @return array<int,array<string,mixed>> */
    public function candidates(array $mappingNode, array $rawNodesById = [], array $relationships = []): array
    {
        $role = strtolower((string) ($mappingNode['mappingRole'] ?? ''));
        $kind = strtolower((string) ($mappingNode['mappingKind'] ?? ''));
        $tag = strtolower((string) ($mappingNode['tag'] ?? 'div'));
        $members = array_values(array_map('strval', is_array($mappingNode['memberSourceIds'] ?? null) ? $mappingNode['memberSourceIds'] : []));

        if ($kind === 'icon_box') {
            return [$this->candidate('icon_box', 0.96, [
                'icon member detected',
                'label/value content pair detected',
                'nested wrapper is a single visual item',
            ], $members)];
        }

        if ($kind === 'image_box') {
            $reasons = ['media member detected', 'nested media wrapper detected'];
            if ($this->hasTextMember($members, $rawNodesById)) {
                $reasons[] = 'overlay/text member requires review';
            }

            return [$this->candidate('image_box', 0.9, $reasons, $members)];
        }

        if ($role === 'container' && $this->isDivider($mappingNode)) {
            return [$this->candidate('divider', 0.94, [
                'thin visual rule detected',
                'no text or nested mapping children',
            ], $members)];
        }

        if ($role === 'container') {
            $isPositioned = $this->isPositionedLayer((string) ($mappingNode['sourceId'] ?? ''), $relationships);
            return [$this->candidate('container', 0.9, [
                'structural wrapper preserved',
                'nested content remains independently traceable',
                ...($isPositioned ? ['positioned layer maps to Advanced positioning'] : []),
            ], $members, $isPositioned ? 'native-positioning' : 'native')];
        }

        if ($role === 'layout') {
            $display = strtolower(trim((string) (($mappingNode['computedStyleByViewport']['desktop']['display'] ?? $mappingNode['computedStyle']['display'] ?? ''))));
            if ($display === 'grid') {
                return [$this->candidate('grid', 0.98, ['computed display is grid', 'dynamic track evidence is available'], $members)];
            }
            if (in_array($display, ['flex', 'inline-flex'], true)) {
                return [$this->candidate('container', 0.98, ['computed display is flex', 'child placement is a flow layout'], $members)];
            }

            return [$this->candidate('container', 0.82, ['structural layout role detected'], $members)];
        }

        if ($role === 'content') {
            return match (true) {
                preg_match('/^h[1-6]$/', $tag) === 1 => [$this->candidate('heading', 0.98, ['semantic heading tag detected'], $members)],
                in_array($tag, ['p', 'blockquote', 'pre', 'code'], true) => [$this->candidate('text_editor', 0.94, ['semantic text content detected'], $members)],
                in_array($tag, ['img', 'picture'], true) => [$this->candidate('image', 0.98, ['media source element detected'], $members)],
                $tag === 'video' => [$this->candidate('video', 0.94, ['video media element detected'], $members)],
                $tag === 'form' => [$this->candidate('form', 0.94, ['form element detected'], $members)],
                $tag === 'button' || ($tag === 'a' && $this->hasAttribute($mappingNode, 'href')) => [$this->candidate('button', $tag === 'button' ? 0.98 : 0.88, ['action element detected', 'href/interaction attribute detected'], $members)],
                default => [$this->candidate('text_editor', 0.68, ['text-bearing content needs review'], $members)],
            };
        }

        return [];
    }

    /** @param array<string,mixed> $normalized @return array<string,mixed> */
    public function withCandidates(array $normalized, array $relationships = []): array
    {
        $rawNodesById = is_array($normalized['rawNodesById'] ?? null) ? $normalized['rawNodesById'] : [];
        $mappingNodes = is_array($normalized['mappingNodes'] ?? null) ? $normalized['mappingNodes'] : [];
        foreach ($mappingNodes as &$mappingNode) {
            if (is_array($mappingNode)) {
                $mappingNode['candidateWidgets'] = $this->candidates($mappingNode, $rawNodesById, $relationships);
            }
        }
        unset($mappingNode);
        $normalized['mappingNodes'] = $mappingNodes;

        return $normalized;
    }

    /** @param array<int,string> $members @param array<int,string> $reasons @return array<string,mixed> */
    private function candidate(string $type, float $score, array $reasons, array $members, string $representability = 'native'): array
    {
        return [
            'type' => $type,
            'score' => $score,
            'confidence' => $score >= 0.85 ? 'high' : ($score >= 0.7 ? 'medium' : 'low'),
            'reasons' => array_values($reasons),
            'memberSourceIds' => array_values($members),
            'diagnostics' => [],
            'representability' => $representability,
        ];
    }

    /** @param array<string,mixed> $relationships */
    private function isPositionedLayer(string $sourceId, array $relationships): bool
    {
        foreach (is_array($relationships['positionedLayers'] ?? null) ? $relationships['positionedLayers'] : [] as $layer) {
            if (is_array($layer) && (string) ($layer['sourceId'] ?? '') === $sourceId) return true;
        }
        return false;
    }

    /** @param array<int,string> $members @param array<string,array<string,mixed>> $rawNodesById */
    private function hasTextMember(array $members, array $rawNodesById): bool
    {
        foreach ($members as $memberId) {
            if (trim((string) ($rawNodesById[$memberId]['textContent'] ?? '')) !== '') {
                return true;
            }
        }

        return false;
    }

    /** @param array<string,mixed> $node */
    private function hasAttribute(array $node, string $name): bool
    {
        foreach (is_array($node['attributes'] ?? null) ? $node['attributes'] : [] as $attribute) {
            if (strtolower((string) ($attribute['name'] ?? '')) === strtolower($name) && trim((string) ($attribute['value'] ?? '')) !== '') {
                return true;
            }
        }

        return false;
    }

    /** @param array<string,mixed> $node */
    private function isDivider(array $node): bool
    {
        if (strtolower((string) ($node['tag'] ?? 'div')) !== 'div'
            || trim((string) ($node['textContent'] ?? '')) !== ''
            || ! empty($node['memberSourceIds'])
            || ! empty($node['childMappingIds'])) {
            return false;
        }

        $style = $node['computedStyleByViewport']['desktop'] ?? $node['computedStyle'] ?? [];
        if (! is_array($style)) {
            return false;
        }
        $height = $this->pixelValue($style['height'] ?? null);
        $width = $this->pixelValue($style['width'] ?? null);
        $background = strtolower(trim((string) ($style['backgroundColor'] ?? 'transparent')));
        $border = strtolower(trim((string) ($style['borderTopStyle'] ?? 'none')));

        return $height !== null && $height > 0 && $height <= 4
            && $width !== null && $width > 0
            && ($background !== '' && ! in_array($background, ['transparent', 'rgba(0, 0, 0, 0)'], true) || $border !== '' && $border !== 'none');
    }

    private function pixelValue(mixed $value): ?float
    {
        $raw = trim((string) $value);
        if (preg_match('/^-?\d+(?:\.\d+)?px$/i', $raw) !== 1) {
            return null;
        }

        return (float) substr($raw, 0, -2);
    }
}
