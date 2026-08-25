<?php

namespace App\Support\Article;

use Illuminate\Support\Str;

final class ArticleTemplateOptions
{
    private const POSITIONS = ['left', 'center', 'right'];

    private const GRID_TEMPLATES = ['editorial-journal', 'mosaic-magazine', 'balanced-card-grid'];

    private const HEADING_TAGS = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    private const DIMENSION_UNITS = ['px', 'em', 'rem', '%', 'pt'];

    private const BORDER_UNITS = ['px', 'em', 'rem', 'pt'];

    private const DEVICES = ['desktop', 'tablet', 'mobile'];

    private const BOX_SIDES = ['top', 'right', 'bottom', 'left'];

    public function __construct(private readonly ArticleTemplateCatalog $catalog)
    {
    }

    public function archive(string $template, array $input = []): array
    {
        $definition = $this->catalog->archive()[$template] ?? $this->catalog->archive()['minimal-reading-list'];
        $toolbarDefaults = match ($template) {
            'minimal-reading-list' => ['search' => [true, 'left'], 'category' => [false, 'right']],
            'mosaic-magazine', 'mosaic-classic' => ['search' => [true, 'right'], 'category' => [false, 'right']],
            'balanced-card-grid' => ['search' => [false, 'left'], 'category' => [true, 'right']],
            default => ['search' => [false, 'left'], 'category' => [false, 'right']],
        };

        $result = [
            'header' => [
                'eyebrow' => $this->copyOption($input, 'eyebrow', (string) ($definition['eyebrow'] ?? '')),
                'title' => $this->copyOption($input, 'title', (string) ($definition['label'] ?? '')),
                'description' => $this->copyOption($input, 'description', (string) ($definition['header_description'] ?? ''), 280),
            ],
            'toolbar' => [
                'search' => $this->toolbarOption($input, 'search', $toolbarDefaults['search'][0], $toolbarDefaults['search'][1]),
                'category' => $this->toolbarOption($input, 'category', $toolbarDefaults['category'][0], $toolbarDefaults['category'][1]),
            ],
            'thumbnail' => $this->thumbnail($input),
            'pagination' => $this->pagination($input),
            'article_title' => [
                'tag' => $this->headingTag(data_get($input, 'article_title.tag')),
            ],
            'shell' => $this->shell($input),
        ];

        if (in_array($template, self::GRID_TEMPLATES, true)) {
            $result['grid'] = [
                'desktop' => $this->column($input, 'desktop', 3, 4),
                'tablet' => $this->column($input, 'tablet', 2, 3),
                'mobile' => $this->column($input, 'mobile', 1, 2),
            ];
        }

        return $result;
    }

    public function detail(string $template, array $input = []): array
    {
        return [
            'header' => [
                'eyebrow' => $this->detailCopyOption($input, 'eyebrow'),
                'title' => ['enabled' => $this->boolean(data_get($input, 'header.title.enabled'), true)],
                'description' => $this->detailCopyOption($input, 'description', 280),
            ],
            'shell' => $this->shell($input),
        ];
    }

    public function archives(array $input = []): array
    {
        $result = [];

        foreach (array_keys($this->catalog->archive()) as $template) {
            $result[$template] = $this->archive($template, $this->array(data_get($input, $template)));
        }

        return $result;
    }

    public function details(array $input = []): array
    {
        $result = [];

        foreach (array_keys($this->catalog->detail()) as $template) {
            $result[$template] = $this->detail($template, $this->array(data_get($input, $template)));
        }

        return $result;
    }

    private function copyOption(array $input, string $field, string $defaultText, int $limit = 160): array
    {
        return [
            'enabled' => $this->boolean(data_get($input, "header.{$field}.enabled"), true),
            'text' => $this->text(data_get($input, "header.{$field}.text"), $defaultText, $limit),
        ];
    }

    private function detailCopyOption(array $input, string $field, int $limit = 80): array
    {
        $mode = data_get($input, "header.{$field}.mode", 'dynamic');
        $mode = in_array($mode, ['dynamic', 'custom'], true) ? $mode : 'dynamic';

        return [
            'enabled' => $this->boolean(data_get($input, "header.{$field}.enabled"), true),
            'mode' => $mode,
            'text' => $mode === 'custom' ? $this->text(data_get($input, "header.{$field}.text"), '', $limit) : '',
        ];
    }

    private function toolbarOption(array $input, string $field, bool $enabled, string $position): array
    {
        $candidate = data_get($input, "toolbar.{$field}.position", $position);

        return [
            'enabled' => $this->boolean(data_get($input, "toolbar.{$field}.enabled"), $enabled),
            'position' => in_array($candidate, self::POSITIONS, true) ? $candidate : $position,
        ];
    }

    private function column(array $input, string $device, int $default, int $maximum): int
    {
        return min($maximum, max(1, (int) data_get($input, "grid.{$device}", $default)));
    }

    private function thumbnail(array $input): array
    {
        $mode = data_get($input, 'thumbnail.mode', 'background');
        $fit = data_get($input, 'thumbnail.fit', 'cover');

        return [
            'mode' => in_array($mode, ['background', 'asset'], true) ? $mode : 'background',
            'fit' => in_array($fit, ['cover', 'contain'], true) ? $fit : 'cover',
            'background_color' => $this->color(data_get($input, 'thumbnail.background_color'), '#f2f4f7'),
            'frame' => $this->frame($input, 'thumbnail.frame', false, '#e1e6ee', '#f2f4f7'),
        ];
    }

    private function pagination(array $input): array
    {
        $position = data_get($input, 'pagination.position', 'right');

        return [
            'show_total' => $this->boolean(data_get($input, 'pagination.show_total'), true),
            'position' => in_array($position, self::POSITIONS, true) ? $position : 'right',
            'frame' => $this->frame($input, 'pagination.frame', true, '#e6e9ef', '#ffffff'),
            'padding' => $this->responsiveBox($input, 'pagination.padding'),
            'margin' => $this->responsiveBox($input, 'pagination.margin'),
        ];
    }

    private function shell(array $input): array
    {
        return [
            'padding' => $this->responsiveBox($input, 'shell.padding'),
            'margin' => $this->responsiveBox($input, 'shell.margin'),
            'frame' => $this->frame($input, 'shell.frame', false, '#e1e6ee', '#ffffff'),
        ];
    }

    private function frame(array $input, string $path, bool $defaultEnabled, string $defaultBorderColor, string $defaultBackgroundColor): array
    {
        return [
            'enabled' => $this->boolean(data_get($input, "{$path}.enabled"), $defaultEnabled),
            'border_color' => $this->color(data_get($input, "{$path}.border_color"), $defaultBorderColor),
            'border_width' => $this->dimension(data_get($input, "{$path}.border_width"), '1px', self::BORDER_UNITS),
            'radius' => $this->dimension(data_get($input, "{$path}.radius"), '1rem', self::DIMENSION_UNITS),
            'background_color' => $this->color(data_get($input, "{$path}.background_color"), $defaultBackgroundColor),
        ];
    }

    private function responsiveBox(array $input, string $path): array
    {
        $result = [
            'enabled' => $this->boolean(data_get($input, "{$path}.enabled"), false),
        ];

        foreach (self::DEVICES as $device) {
            $result[$device] = $this->box(data_get($input, "{$path}.{$device}"));
        }

        return $result;
    }

    private function box(mixed $input): array
    {
        $input = $this->array($input);
        $result = [];

        foreach (self::BOX_SIDES as $side) {
            $result[$side] = $this->dimension(data_get($input, $side), '0px', self::DIMENSION_UNITS);
        }

        return $result;
    }

    private function headingTag(mixed $candidate): string
    {
        $candidate = is_string($candidate) ? strtolower($candidate) : '';

        return in_array($candidate, self::HEADING_TAGS, true) ? $candidate : 'h4';
    }

    private function color(mixed $value, string $default): string
    {
        if (! is_string($value)) {
            return $default;
        }

        $value = strtolower(trim($value));

        if (preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $value) === 1) {
            return $value;
        }

        if (preg_match('/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})(?:\s*,\s*(0|1|0?\.\d+))?\s*\)$/', $value, $matches) === 1) {
            $channels = [(int) $matches[1], (int) $matches[2], (int) $matches[3]];
            $alpha = isset($matches[4]) ? (float) $matches[4] : 1.0;

            if (max($channels) <= 255 && $alpha >= 0 && $alpha <= 1) {
                return $value;
            }
        }

        if (preg_match('/^hsla?\(\s*(\d{1,3}(?:\.\d+)?)\s*,\s*(\d{1,3}(?:\.\d+)?)%\s*,\s*(\d{1,3}(?:\.\d+)?)%(?:\s*,\s*(0|1|0?\.\d+))?\s*\)$/', $value, $matches) === 1) {
            $hue = (float) $matches[1];
            $saturation = (float) $matches[2];
            $lightness = (float) $matches[3];
            $alpha = isset($matches[4]) ? (float) $matches[4] : 1.0;

            if ($hue <= 360 && $saturation <= 100 && $lightness <= 100 && $alpha >= 0 && $alpha <= 1) {
                return $value;
            }
        }

        return $default;
    }

    private function dimension(mixed $value, string $default, array $units): string
    {
        $fallback = $this->parseDimension($default, $units);
        if ($fallback === null) {
            $fallback = ['value' => 0.0, 'unit' => 'px'];
        }

        $parsed = $this->parseDimension($value, $units, $fallback['unit']);
        if ($parsed === null || ! in_array($parsed['unit'], $units, true)) {
            return $this->formatDimension($fallback['value'], $fallback['unit']);
        }

        $limit = match ($parsed['unit']) {
            '%' => 100,
            'em', 'rem' => 30,
            default => 400,
        };

        return $this->formatDimension(min($limit, max(0, $parsed['value'])), $parsed['unit']);
    }

    private function parseDimension(mixed $value, array $units, string $defaultUnit = 'px'): ?array
    {
        if (! is_string($value) && ! is_int($value) && ! is_float($value)) {
            return null;
        }

        if (preg_match('/^(\d+(?:\.\d+)?)(px|em|rem|%|pt)?$/i', trim((string) $value), $matches) !== 1) {
            return null;
        }

        $unit = strtolower($matches[2] ?? $defaultUnit);
        if (! in_array($unit, $units, true)) {
            return null;
        }

        $number = (float) $matches[1];

        return is_finite($number) ? ['value' => $number, 'unit' => $unit] : null;
    }

    private function formatDimension(float $value, string $unit): string
    {
        $value = round($value, 2);
        $formatted = rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');

        return ($formatted === '' ? '0' : $formatted).$unit;
    }

    private function boolean(mixed $value, bool $default): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    private function text(mixed $value, string $default, int $limit): string
    {
        if (!is_string($value)) {
            return $default;
        }

        return Str::limit(trim(strip_tags($value)), $limit, '');
    }

    private function array(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }
}
