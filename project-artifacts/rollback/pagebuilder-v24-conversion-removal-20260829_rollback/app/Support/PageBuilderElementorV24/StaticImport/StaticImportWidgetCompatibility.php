<?php

namespace App\Support\PageBuilderElementorV24\StaticImport;

use App\Support\PageBuilderElementorV24\ModuleCatalog;
use DOMElement;

final class StaticImportWidgetCompatibility
{
    /** @var array<string, array<int, string>> */
    private const COMPATIBILITY = [
        'layout' => ['container', 'container_fluid', 'grid', 'row_grid'],
        'heading' => ['heading', 'text_editor'],
        'text' => ['text_editor', 'heading'],
        'image' => ['image', 'image_box'],
        'button' => ['button', 'call_to_action'],
        'icon' => ['icon', 'icon_box'],
        'form' => ['form'],
        'divider' => ['divider'],
        'video' => ['video'],
        'card_collection' => ['container', 'grid', 'carousel'],
        'navigation' => ['container', 'grid'],
        'unknown' => [],
    ];

    public function __construct(private readonly ModuleCatalog $catalog)
    {
    }

    public function roleFor(DOMElement $element): string
    {
        $tag = strtolower($element->tagName);

        return match (true) {
            $tag === 'nav' => 'navigation',
            preg_match('/^h[1-6]$/', $tag) === 1 => 'heading',
            $tag === 'img' => 'image',
            $tag === 'form' => 'form',
            $tag === 'video' => 'video',
            $tag === 'hr' => 'divider',
            $tag === 'button' || ($tag === 'a' && strtolower(trim($element->getAttribute('role'))) === 'button') => 'button',
            $tag === 'article' || $this->hasRepeatedArticles($element) => 'card_collection',
            in_array($tag, ['p', 'span', 'strong', 'em', 'b', 'i', 'a', 'ul', 'ol', 'li', 'blockquote'], true) => 'text',
            in_array($tag, ['div', 'section', 'main', 'header', 'footer', 'aside', 'figure', 'picture', 'dl', 'dt', 'dd', 'fieldset'], true) => 'layout',
            in_array($tag, ['audio', 'canvas', 'embed', 'iframe', 'object'], true) => 'unknown',
            default => 'unknown',
        };
    }

    /** @return array<int, string> */
    public function allowedWidgets(string $role): array
    {
        $registered = array_fill_keys(array_keys($this->catalog->all()), true);

        return array_values(array_filter(
            self::COMPATIBILITY[$role] ?? [],
            static fn (string $widgetType): bool => isset($registered[$widgetType]),
        ));
    }

    public function recommendedWidget(string $role, DOMElement $element): ?string
    {
        $allowed = $this->allowedWidgets($role);
        if ($allowed === []) return null;

        $preferred = match ($role) {
            'card_collection' => 'carousel',
            'navigation', 'layout' => 'container',
            'heading' => 'heading',
            'text' => 'text_editor',
            'image' => 'image',
            'button' => 'button',
            'icon' => 'icon',
            'form' => 'form',
            'divider' => 'divider',
            'video' => 'video',
            default => null,
        };

        return $preferred !== null && in_array($preferred, $allowed, true) ? $preferred : $allowed[0];
    }

    public function isCompatible(string $role, string $widgetType): bool
    {
        return in_array(trim($widgetType), $this->allowedWidgets($role), true);
    }

    private function hasRepeatedArticles(DOMElement $element): bool
    {
        $articles = 0;
        foreach (iterator_to_array($element->childNodes) as $child) {
            if ($child instanceof DOMElement && strtolower($child->tagName) === 'article') $articles++;
        }

        return $articles >= 2;
    }
}
