<?php

namespace App\Support\PageBuilderElementorV24\CompiledNative;

final class AutomaticCompiledNativeBlockNormalizer
{
    private const INLINE_MEMBER_TAGS = [
        'br', 'span', 'i', 'svg', 'use', 'b', 'strong', 'em', 'small', 'sup', 'sub',
    ];

    private const CONTENT_TAGS = [
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'a', 'button', 'img', 'picture',
        'video', 'audio', 'input', 'textarea', 'select', 'label', 'li', 'dt', 'dd',
        'blockquote', 'pre', 'code', 'time',
    ];

    private const STRUCTURAL_TAGS = [
        'div', 'section', 'article', 'figure', 'nav', 'aside', 'form', 'dl', 'ul', 'ol',
        'main', 'header', 'footer',
    ];

    /**
     * Build a mapping projection while leaving the measured DOM nodes untouched.
     *
     * @return array<string,mixed>
     */
    public function normalizeSection(array $section): array
    {
        $rawNodesById = [];
        foreach (array_values($section['nodes'] ?? []) as $order => $node) {
            if (! is_array($node)) {
                continue;
            }

            $sourceId = trim((string) ($node['sourceId'] ?? ''));
            if ($sourceId === '') {
                continue;
            }

            $rawNodesById[$sourceId] = [
                ...$node,
                'sourceId' => $sourceId,
                'order' => (int) ($node['order'] ?? $order),
                'rawChildSourceIds' => [],
            ];
        }

        $childrenByParent = [];
        foreach ($rawNodesById as $sourceId => $node) {
            $parentId = trim((string) ($node['parentSourceId'] ?? ''));
            $childrenByParent[$parentId][] = $sourceId;
        }
        foreach ($childrenByParent as &$children) {
            usort($children, static fn (string $left, string $right): int => ($rawNodesById[$left]['order'] ?? 0) <=> ($rawNodesById[$right]['order'] ?? 0));
        }
        unset($children);

        foreach ($rawNodesById as $sourceId => &$node) {
            $node['rawChildSourceIds'] = $this->rawChildIds($node, $rawNodesById, $childrenByParent);
        }
        unset($node);

        $rootId = trim((string) ($section['sourceId'] ?? $section['id'] ?? ''));
        $mappingNodesById = [];
        $mappingRoots = [];
        $visited = [];
        foreach ($this->rawChildIds($rawNodesById[$rootId] ?? [], $rawNodesById, $childrenByParent) as $childId) {
            $result = $this->projectNode($childId, null, $rawNodesById, $mappingNodesById, $visited);
            foreach ($result['mappingIds'] as $mappingId) {
                $mappingRoots[] = $mappingId;
            }
        }

        return [
            'rawNodes' => array_values($rawNodesById),
            'rawNodesById' => $rawNodesById,
            'mappingNodes' => array_values($mappingNodesById),
            'mappingRoots' => array_values(array_unique($mappingRoots)),
        ];
    }

    /** @param array<string,mixed> $node @param array<string,array<string,mixed>> $rawNodesById @param array<string,array<int,string>> $childrenByParent @return array<int,string> */
    private function rawChildIds(array $node, array $rawNodesById, array $childrenByParent): array
    {
        if (array_key_exists('children', $node) && is_array($node['children'])) {
            return array_values(array_filter(array_map('strval', $node['children']), static fn (string $id): bool => isset($rawNodesById[$id])));
        }

        $sourceId = trim((string) ($node['sourceId'] ?? ''));

        return array_values($childrenByParent[$sourceId] ?? []);
    }

    /** @param array<string,array<string,mixed>> $rawNodesById @param array<string,array<string,mixed>> $mappingNodesById @param array<string,bool> $visited @return array{mappingIds:array<int,string>,memberIds:array<int,string>} */
    private function projectNode(string $sourceId, ?string $parentMappingId, array &$rawNodesById, array &$mappingNodesById, array &$visited): array
    {
        if (! isset($rawNodesById[$sourceId]) || isset($visited[$sourceId])) {
            return ['mappingIds' => [], 'memberIds' => []];
        }
        $visited[$sourceId] = true;

        $node = $rawNodesById[$sourceId];
        $children = $node['rawChildSourceIds'] ?? [];
        $tag = strtolower((string) ($node['tag'] ?? 'div'));

        if ($parentMappingId !== null
            && $this->isInlineMember($node)
            && in_array($mappingNodesById[$parentMappingId]['mappingRole'] ?? '', ['content', 'composite'], true)) {
            return [
                'mappingIds' => [],
                'memberIds' => [$sourceId, ...$this->descendantIds($sourceId, $rawNodesById)],
            ];
        }

        $compositeKind = $this->compositeKind($node, $rawNodesById);
        if ($compositeKind !== null) {
            $mappingNodesById[$sourceId] = $this->mappingNode($node, $parentMappingId, 'composite', $compositeKind, $this->descendantIds($sourceId, $rawNodesById), [
                'rule' => 'nested.visual-composition',
                'childTags' => array_values(array_map(static fn (string $childId): string => strtolower((string) ($rawNodesById[$childId]['tag'] ?? 'div')), $children)),
            ]);

            return ['mappingIds' => [$sourceId], 'memberIds' => []];
        }

        if ($this->isContentNode($node)) {
            $mappingNodesById[$sourceId] = $this->mappingNode($node, $parentMappingId, 'content', 'content');
            $memberIds = [];
            $childMappingIds = [];
            foreach ($children as $childId) {
                $result = $this->projectNode($childId, $sourceId, $rawNodesById, $mappingNodesById, $visited);
                $memberIds = [...$memberIds, ...$result['memberIds']];
                $childMappingIds = [...$childMappingIds, ...$result['mappingIds']];
            }
            $mappingNodesById[$sourceId]['memberSourceIds'] = array_values(array_unique($memberIds));
            $mappingNodesById[$sourceId]['childMappingIds'] = array_values(array_unique($childMappingIds));

            return ['mappingIds' => [$sourceId], 'memberIds' => []];
        }

        $isLayout = $this->isLayoutNode($node);
        $keepStructural = $isLayout
            || $this->hasDistinctVisualBoundary($node)
            || count($this->meaningfulChildIds($node, $rawNodesById)) > 1
            || in_array($tag, ['article', 'figure', 'form', 'nav', 'dl', 'ul', 'ol'], true);

        if ($keepStructural) {
            $role = $isLayout ? 'layout' : 'container';
            $mappingNodesById[$sourceId] = $this->mappingNode($node, $parentMappingId, $role, $role, [], $isLayout ? [
                'rule' => 'computedStyle.display',
                'display' => $this->style($node)['display'] ?? '',
            ] : [
                'rule' => 'nested.structural-wrapper',
            ]);
            $childMappingIds = [];
            foreach ($children as $childId) {
                $result = $this->projectNode($childId, $sourceId, $rawNodesById, $mappingNodesById, $visited);
                $childMappingIds = [...$childMappingIds, ...$result['mappingIds']];
            }
            $mappingNodesById[$sourceId]['childMappingIds'] = array_values(array_unique($childMappingIds));

            return ['mappingIds' => [$sourceId], 'memberIds' => []];
        }

        $promoted = [];
        foreach ($children as $childId) {
            $result = $this->projectNode($childId, $parentMappingId, $rawNodesById, $mappingNodesById, $visited);
            $promoted = [...$promoted, ...$result['mappingIds']];
        }

        return ['mappingIds' => array_values(array_unique($promoted)), 'memberIds' => []];
    }

    /** @param array<string,mixed> $node @param array<string,array<string,mixed>> $rawNodesById @return string|null */
    private function compositeKind(array $node, array $rawNodesById): ?string
    {
        $tag = strtolower((string) ($node['tag'] ?? 'div'));
        if (in_array($tag, self::CONTENT_TAGS, true) || ! $this->rawChildIds($node, $rawNodesById, $this->childrenByParent($rawNodesById))) {
            return null;
        }

        $children = array_values($node['rawChildSourceIds'] ?? []);
        $hasMedia = false;
        $hasIcon = false;
        $hasText = false;
        $hasLayoutChild = false;
        foreach ($children as $childId) {
            $descendant = $rawNodesById[$childId] ?? [];
            $descendantTag = strtolower((string) ($descendant['tag'] ?? ''));
            $hasMedia = $hasMedia || in_array($descendantTag, ['img', 'picture', 'video'], true);
            $hasIcon = $hasIcon || in_array($descendantTag, ['i', 'svg'], true) || $this->hasIconClass($descendant);
            $hasText = $hasText || trim((string) ($descendant['textContent'] ?? '')) !== '';
            if (! $hasText) {
                foreach ($this->descendantIds($childId, $rawNodesById) as $memberId) {
                    if (trim((string) ($rawNodesById[$memberId]['textContent'] ?? '')) !== '') {
                        $hasText = true;
                        break;
                    }
                }
            }
            $hasLayoutChild = $hasLayoutChild || ($this->isLayoutNode($descendant) && ! in_array($descendantTag, self::CONTENT_TAGS, true));
            if (in_array($descendantTag, ['dt', 'dd'], true)) {
                foreach ($this->descendantIds($childId, $rawNodesById) as $memberId) {
                    $member = $rawNodesById[$memberId] ?? [];
                    $memberTag = strtolower((string) ($member['tag'] ?? ''));
                    $hasIcon = $hasIcon || in_array($memberTag, ['i', 'svg'], true) || $this->hasIconClass($member);
                }
            }
        }

        $childTags = array_map(static fn (string $childId): string => strtolower((string) ($rawNodesById[$childId]['tag'] ?? '')), $children);
        $hasValuePair = in_array('dt', $childTags, true) && in_array('dd', $childTags, true);
        $meaningfulChildCount = count(array_filter($children, fn (string $childId): bool => isset($rawNodesById[$childId]) && ! $this->isInlineMember($rawNodesById[$childId])));
        if (! $hasMedia && $hasIcon && $hasText && ! $hasLayoutChild && ! $this->isLayoutNode($node) && ($hasValuePair || ($tag === 'div' && $meaningfulChildCount <= 4))) {
            return 'icon_box';
        }
        if ($hasMedia && $hasText && ! $this->isLayoutNode($node) && in_array($tag, ['div', 'figure', 'article'], true)) {
            return 'image_box';
        }

        return null;
    }

    /** @param array<string,mixed> $node */
    private function isContentNode(array $node): bool
    {
        $tag = strtolower((string) ($node['tag'] ?? 'div'));
        $hasElementChildren = count($node['rawChildSourceIds'] ?? []) > 0;

        return in_array($tag, self::CONTENT_TAGS, true)
            || trim((string) ($node['textContent'] ?? '')) !== '' && (! in_array($tag, self::STRUCTURAL_TAGS, true) || ! $hasElementChildren);
    }

    /** @param array<string,mixed> $node */
    private function isLayoutNode(array $node): bool
    {
        $display = strtolower(trim((string) ($this->style($node)['display'] ?? '')));

        return in_array($display, ['grid', 'flex', 'inline-flex'], true);
    }

    /** @param array<string,mixed> $node */
    private function isInlineMember(array $node): bool
    {
        $tag = strtolower((string) ($node['tag'] ?? 'div'));

        return in_array($tag, self::INLINE_MEMBER_TAGS, true) && ! $this->isLayoutNode($node);
    }

    /** @param array<string,mixed> $node */
    private function hasDistinctVisualBoundary(array $node): bool
    {
        $style = $this->style($node);
        foreach (['paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft', 'marginTop', 'marginRight', 'marginBottom', 'marginLeft', 'gap', 'rowGap', 'columnGap'] as $property) {
            if (! in_array(trim((string) ($style[$property] ?? '0')), ['', '0', '0px', 'normal'], true)) {
                return true;
            }
        }
        foreach (['borderTopStyle', 'borderRightStyle', 'borderBottomStyle', 'borderLeftStyle'] as $property) {
            if (! in_array(strtolower(trim((string) ($style[$property] ?? 'none'))), ['', 'none'], true)) {
                return true;
            }
        }
        if (! in_array(strtolower(trim((string) ($style['backgroundImage'] ?? 'none'))), ['', 'none'], true)) {
            return true;
        }
        if (! in_array(strtolower(trim((string) ($style['backgroundColor'] ?? 'transparent'))), ['', 'transparent', 'rgba(0, 0, 0, 0)'], true)) {
            return true;
        }

        return ! in_array(strtolower(trim((string) ($style['position'] ?? 'static'))), ['', 'static', 'relative'], true)
            || ! in_array(strtolower(trim((string) ($style['overflow'] ?? 'visible'))), ['', 'visible'], true);
    }

    /** @param array<string,mixed> $node @param array<string,array<string,mixed>> $rawNodesById @return array<int,string> */
    private function meaningfulChildIds(array $node, array $rawNodesById): array
    {
        return array_values(array_filter($node['rawChildSourceIds'] ?? [], function (string $childId) use ($rawNodesById): bool {
            return isset($rawNodesById[$childId]) && ! $this->isInlineMember($rawNodesById[$childId]);
        }));
    }

    /** @param array<string,mixed> $node @param array<int,string> $memberSourceIds @param array<string,mixed> $evidence @return array<string,mixed> */
    private function mappingNode(array $node, ?string $parentMappingId, string $role, string $kind, array $memberSourceIds = [], array $evidence = []): array
    {
        return [
            'sourceId' => (string) ($node['sourceId'] ?? ''),
            'parentSourceId' => $node['parentSourceId'] ?? null,
            'parentMappingId' => $parentMappingId,
            'tag' => (string) ($node['tag'] ?? 'div'),
            'id' => (string) ($node['id'] ?? ''),
            'classList' => is_array($node['classList'] ?? null) ? array_values($node['classList']) : [],
            'mappingRole' => $role,
            'mappingKind' => $kind,
            'mappingEligible' => true,
            'memberSourceIds' => array_values(array_unique($memberSourceIds)),
            'childMappingIds' => [],
            'rawChildSourceIds' => array_values($node['rawChildSourceIds'] ?? []),
            'textSummary' => $this->textSummary((string) ($node['textContent'] ?? '')),
            'textContent' => (string) ($node['textContent'] ?? ''),
            'innerHTML' => (string) ($node['innerHTML'] ?? ''),
            'attributes' => is_array($node['attributes'] ?? null) ? array_values($node['attributes']) : [],
            'order' => (int) ($node['order'] ?? 0),
            'rectByViewport' => is_array($node['rectByViewport'] ?? null) ? $node['rectByViewport'] : [],
            'computedStyleByViewport' => is_array($node['computedStyleByViewport'] ?? null) ? $node['computedStyleByViewport'] : [],
            'candidateWidgets' => [],
            'compositionEvidence' => $evidence,
        ];
    }

    /** @param array<string,mixed> $node @return array<string,string> */
    private function style(array $node): array
    {
        $styles = is_array($node['computedStyleByViewport'] ?? null) ? $node['computedStyleByViewport'] : [];
        $style = $styles['desktop'] ?? ($node['computedStyle'] ?? []);

        return is_array($style) ? $style : [];
    }

    /** @param array<string,mixed> $node */
    private function hasIconClass(array $node): bool
    {
        foreach (is_array($node['classList'] ?? null) ? $node['classList'] : [] as $class) {
            if (preg_match('/(?:^|[-_])(icon|ph|fa)(?:$|[-_])/i', (string) $class) === 1) {
                return true;
            }
        }

        return false;
    }

    /** @param array<string,array<string,mixed>> $rawNodesById @return array<string,array<int,string>> */
    private function childrenByParent(array $rawNodesById): array
    {
        $children = [];
        foreach ($rawNodesById as $sourceId => $node) {
            $parentId = trim((string) ($node['parentSourceId'] ?? ''));
            $children[$parentId][] = $sourceId;
        }

        return $children;
    }

    /** @param array<string,array<string,mixed>> $rawNodesById @return array<int,string> */
    private function descendantIds(string $sourceId, array $rawNodesById): array
    {
        $childrenByParent = $this->childrenByParent($rawNodesById);
        $result = [];
        $visit = function (string $parentId) use (&$visit, &$result, $childrenByParent): void {
            foreach ($childrenByParent[$parentId] ?? [] as $childId) {
                $result[] = $childId;
                $visit($childId);
            }
        };
        $visit($sourceId);

        return array_values(array_unique($result));
    }

    private function textSummary(string $text): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $text));
    }
}
