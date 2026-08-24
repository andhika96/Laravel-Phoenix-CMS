<?php

namespace App\Support\Article;

final class ArticleTemplateCatalog
{
    private const ARCHIVE = [
        'minimal-reading-list' => [
            'label' => 'Minimal Reading List',
            'description' => 'Focused rows for calm, long-form discovery.',
            'best_for' => 'Long-form reading',
            'view' => 'article.templates.archive.minimal-reading-list',
            'preview_image' => 'assets/images/article/template-previews/minimal-reading-list.svg',
        ],
        'editorial-journal' => [
            'label' => 'Editorial Journal',
            'description' => 'Featured story and balanced editorial grid.',
            'best_for' => 'Featured stories',
            'view' => 'article.templates.archive.editorial-journal',
            'preview_image' => 'assets/images/article/template-previews/editorial-journal.svg',
        ],
        'mosaic-magazine' => [
            'label' => 'Mosaic Magazine',
            'description' => 'Visual lead story with varied editorial cards.',
            'best_for' => 'Visual storytelling',
            'view' => 'article.templates.archive.mosaic-magazine',
            'preview_image' => 'assets/images/article/template-previews/mosaic-magazine.svg',
        ],
        'mosaic-classic' => [
            'label' => 'Mosaic Classic',
            'description' => 'Feature mosaic with a classic editorial rhythm.',
            'best_for' => 'Lead + side stories',
            'view' => 'article.templates.archive.mosaic-classic',
            'preview_image' => 'assets/images/article/template-previews/mosaic-classic.svg',
        ],
        'balanced-card-grid' => [
            'label' => 'Balanced Card Grid',
            'description' => 'Equal-weight cards for broad article discovery.',
            'best_for' => 'Visual discovery',
            'view' => 'article.templates.archive.balanced-card-grid',
            'preview_image' => 'assets/images/article/template-previews/balanced-card-grid.svg',
        ],
    ];

    private const DETAIL = [
        'focused-reader' => [
            'label' => 'Focused Reader',
            'description' => 'Clean long-form reading without distractions.',
            'best_for' => 'Quiet reading',
            'view' => 'article.templates.detail.focused-reader',
            'preview_image' => 'assets/images/article/template-previews/focused-reader.svg',
        ],
        'editorial-feature' => [
            'label' => 'Editorial Feature',
            'description' => 'Cover-led story treatment with richer metadata.',
            'best_for' => 'Immersive feature',
            'view' => 'article.templates.detail.editorial-feature',
            'preview_image' => 'assets/images/article/template-previews/editorial-feature.svg',
        ],
        'knowledge-toc' => [
            'label' => 'Knowledge + TOC',
            'description' => 'Structured reading view with a table of contents.',
            'best_for' => 'Structured knowledge',
            'view' => 'article.templates.detail.knowledge-toc',
            'preview_image' => 'assets/images/article/template-previews/knowledge-toc.svg',
        ],
    ];

    public function archive(): array
    {
        return self::ARCHIVE;
    }

    public function detail(): array
    {
        return self::DETAIL;
    }

    public function templates(string $surface): array
    {
        return $surface === 'detail' ? self::DETAIL : self::ARCHIVE;
    }

    public function defaultSettings(): array
    {
        return [
            'archive_template' => 'minimal-reading-list',
            'detail_template' => 'focused-reader',
            'archive_per_page' => 12,
        ];
    }

    public function isAllowed(string $surface, string $key): bool
    {
        return array_key_exists($key, $this->templates($surface));
    }

    public function view(string $surface, string $key): string
    {
        $templates = $this->templates($surface);

        return $templates[$key]['view'] ?? $templates[array_key_first($templates)]['view'];
    }
}
