<?php

namespace App\Support\PageBuilderElementorV24;

use FilesystemIterator;
use JsonException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Throwable;

final class ModuleCatalog
{
    private const CATEGORY_ORDER = [
        'layout' => 0,
        'basic' => 1,
        'general' => 2,
        'pro' => 3,
    ];

    private const REQUIRED_ASSETS = [
        'definition' => '.js',
        'canvas' => '.vue',
        'settings' => '.vue',
        'view' => '.blade.php',
    ];

    private const OPTIONAL_ASSETS = [
        'runtime' => '.js',
        'styles' => '.css',
    ];

    private const CLIENT_ASSET_PREFIX = '/pagebuilder-elementor/v2.4/module-assets';

    private string $root;

    private ?array $modules = null;

    private array $issues = [];

    public function __construct(?string $root = null)
    {
        $this->root = rtrim($root ?? resource_path('pagebuilder_elementor_v24/modules'), '\\/');
    }

    public function all(): array
    {
        return $this->modules ??= $this->discover();
    }

    public function find(string $type): ?array
    {
        return $this->all()[trim($type)] ?? null;
    }

    public function active(string $type): bool
    {
        return $this->find($type) !== null;
    }

    public function supports(string $type, string $capability): bool
    {
        $module = $this->find($type);

        return is_array($module)
            && in_array(trim($capability), $module['capabilities'] ?? [], true);
    }

    public function anySupports(string $capability): bool
    {
        $needle = trim($capability);
        if ($needle === '') {
            return false;
        }

        foreach ($this->all() as $module) {
            if (in_array($needle, $module['capabilities'] ?? [], true)) {
                return true;
            }
        }

        return false;
    }

    public function toolbox(): array
    {
        $groups = [];

        foreach ($this->all() as $module) {
            if (! $module['toolbox']) {
                continue;
            }

            $groups[$module['category']][] = [
                'type' => $module['type'],
                'label' => $module['label'],
                'icon' => $module['icon'],
            ];
        }

        return $groups;
    }

    public function diagnostics(): array
    {
        $this->all();

        return $this->issues;
    }

    public function clientCatalog(): array
    {
        $catalog = [];

        foreach ($this->all() as $type => $module) {
            $assets = [];
            foreach (array_keys(self::REQUIRED_ASSETS + self::OPTIONAL_ASSETS) as $key) {
                if ($key === 'view' || ! isset($module['assets'][$key])) {
                    continue;
                }

                $extension = (self::REQUIRED_ASSETS + self::OPTIONAL_ASSETS)[$key];
                $assets[$key] = self::CLIENT_ASSET_PREFIX.'/'.rawurlencode($type).'/'.$key.$extension;
            }

            $catalog[$type] = [
                'type' => $type,
                'label' => $module['label'],
                'category' => $module['category'],
                'icon' => $module['icon'],
                'order' => $module['order'],
                'toolbox' => $module['toolbox'],
                'assets' => $assets,
                'advanced' => $module['advanced'],
                'capabilities' => $module['capabilities'],
            ];
        }

        return $catalog;
    }

    private function discover(): array
    {
        if (! is_dir($this->root)) {
            return [];
        }

        $manifestFiles = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->root, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === 'module.json') {
                $manifestFiles[] = $file->getPathname();
            }
        }

        sort($manifestFiles, SORT_STRING);
        $candidates = [];

        foreach ($manifestFiles as $manifestFile) {
            $directory = dirname($manifestFile);

            try {
                $contents = file_get_contents($manifestFile);
                if ($contents === false) {
                    throw new RuntimeException('Manifest cannot be read');
                }

                $manifest = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
                if (! is_array($manifest)) {
                    throw new RuntimeException('Manifest root must be an object');
                }

                $module = $this->validateManifest($manifest, $directory);
                $candidates[$module['type']][] = $module;
            } catch (JsonException $error) {
                $this->addIssue($directory, 'Invalid JSON: '.$error->getMessage());
            } catch (Throwable $error) {
                $this->addIssue($directory, $error->getMessage());
            }
        }

        $modules = [];
        foreach ($candidates as $type => $matches) {
            if (count($matches) !== 1) {
                $this->issues[] = [
                    'type' => $type,
                    'reason' => 'Duplicate module type: '.$type,
                ];

                continue;
            }

            $modules[$type] = $matches[0];
        }

        uasort($modules, static fn (array $left, array $right): int => [
            $left['categoryOrder'],
            $left['order'],
            $left['type'],
        ] <=> [
            $right['categoryOrder'],
            $right['order'],
            $right['type'],
        ]);

        return $modules;
    }

    private function validateManifest(array $manifest, string $directory): array
    {
        if (($manifest['schemaVersion'] ?? null) !== 1) {
            throw new RuntimeException('Unsupported schemaVersion; expected 1');
        }

        $type = trim((string) ($manifest['type'] ?? ''));
        if (! preg_match('/^[a-z][a-z0-9_]*$/', $type)) {
            throw new RuntimeException('Invalid module type');
        }

        $label = trim((string) ($manifest['label'] ?? ''));
        if ($label === '') {
            throw new RuntimeException('Module label is required');
        }

        $category = trim((string) ($manifest['category'] ?? ''));
        if (! array_key_exists($category, self::CATEGORY_ORDER)) {
            throw new RuntimeException('Invalid module category: '.$category);
        }

        $icon = trim((string) ($manifest['icon'] ?? ''));
        if ($icon === '') {
            throw new RuntimeException('Module icon is required');
        }

        if (! is_int($manifest['order'] ?? null)) {
            throw new RuntimeException('Module order must be an integer');
        }

        if (! is_bool($manifest['toolbox'] ?? null)) {
            throw new RuntimeException('Module toolbox must be a boolean');
        }

        $assetManifest = $manifest['assets'] ?? null;
        if (! is_array($assetManifest)) {
            throw new RuntimeException('Module assets must be an object');
        }

        foreach (array_keys(self::REQUIRED_ASSETS) as $key) {
            if (! isset($assetManifest[$key]) || ! is_string($assetManifest[$key]) || trim($assetManifest[$key]) === '') {
                throw new RuntimeException('Missing required asset: '.$key);
            }
        }

        $unknownAssets = array_diff(array_keys($assetManifest), array_keys(self::REQUIRED_ASSETS + self::OPTIONAL_ASSETS));
        if ($unknownAssets !== []) {
            throw new RuntimeException('Unknown asset key: '.reset($unknownAssets));
        }

        $directoryRealPath = realpath($directory);
        if ($directoryRealPath === false) {
            throw new RuntimeException('Module directory cannot be resolved');
        }

        $resolvedAssets = [];
        foreach ($assetManifest as $key => $relativePath) {
            $resolvedAssets[$key] = $this->resolveAssetPath(
                $directoryRealPath,
                $key,
                (string) $relativePath,
            );
        }

        $advanced = $manifest['advanced'] ?? ['profile' => 'widget', 'capabilities' => []];
        if (! is_array($advanced)) {
            throw new RuntimeException('Module advanced settings must be an object');
        }

        $profile = trim((string) ($advanced['profile'] ?? 'widget'));
        if (! in_array($profile, ['widget', 'layout'], true)) {
            throw new RuntimeException('Invalid advanced profile: '.$profile);
        }

        $advancedCapabilities = $this->normalizeCapabilities($advanced['capabilities'] ?? []);
        $capabilities = $this->normalizeCapabilities($manifest['capabilities'] ?? []);

        return [
            'schemaVersion' => 1,
            'type' => $type,
            'label' => $label,
            'category' => $category,
            'categoryOrder' => self::CATEGORY_ORDER[$category],
            'icon' => $icon,
            'order' => $manifest['order'],
            'toolbox' => $manifest['toolbox'],
            'directory' => $directoryRealPath,
            'assets' => $resolvedAssets,
            'advanced' => [
                'profile' => $profile,
                'capabilities' => $advancedCapabilities,
            ],
            'capabilities' => $capabilities,
        ];
    }

    private function resolveAssetPath(string $directory, string $key, string $relativePath): string
    {
        $relativePath = str_replace('\\', '/', trim($relativePath));
        $segments = explode('/', $relativePath);

        if ($relativePath === '' || str_starts_with($relativePath, '/') || preg_match('/^[A-Za-z]:/', $relativePath) || in_array('..', $segments, true)) {
            throw new RuntimeException('Asset path must stay inside the module directory: '.$key);
        }

        if ($key === 'view' && $relativePath !== 'frontend.blade.php') {
            throw new RuntimeException('Frontend view must be frontend.blade.php');
        }

        $suffixes = self::REQUIRED_ASSETS + self::OPTIONAL_ASSETS;
        if (! str_ends_with(strtolower($relativePath), $suffixes[$key])) {
            throw new RuntimeException('Invalid asset extension: '.$key);
        }

        $absolutePath = realpath($directory.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativePath));
        if ($absolutePath === false || ! is_file($absolutePath)) {
            throw new RuntimeException('Missing required asset file: '.$key);
        }

        $directoryPrefix = rtrim($directory, '\\/').DIRECTORY_SEPARATOR;
        if (! str_starts_with($absolutePath, $directoryPrefix)) {
            throw new RuntimeException('Asset path must stay inside the module directory: '.$key);
        }

        return $absolutePath;
    }

    private function normalizeCapabilities(mixed $capabilities): array
    {
        if (! is_array($capabilities)) {
            throw new RuntimeException('Module capabilities must be an array');
        }

        $normalized = [];
        foreach ($capabilities as $capability) {
            if (! is_string($capability) || ! preg_match('/^[a-z][a-z0-9-]*$/', $capability)) {
                throw new RuntimeException('Invalid module capability');
            }

            $normalized[$capability] = true;
        }

        return array_keys($normalized);
    }

    private function addIssue(string $directory, string $reason): void
    {
        $rootPrefix = rtrim(str_replace('\\', '/', $this->root), '/').'/';
        $normalizedDirectory = str_replace('\\', '/', $directory);
        $folder = str_starts_with($normalizedDirectory.'/', $rootPrefix)
            ? substr($normalizedDirectory, strlen($rootPrefix))
            : basename($normalizedDirectory);

        $this->issues[] = [
            'folder' => trim($folder, '/'),
            'reason' => $reason,
        ];
    }
}
