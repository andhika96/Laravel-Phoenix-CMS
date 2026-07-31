<?php

namespace App\Support\PageBuilderElementor;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

final class ImageRenditionResolver
{
    private const SIZES = [
        'thumbnail' => 150,
        'medium' => 300,
        'medium_large' => 768,
        'large' => 1024,
        '1536x1536' => 1536,
        '2048x2048' => 2048,
        'full' => null,
    ];

    private array $sourceRoots;

    private string $outputRoot;

    private string $outputUrlPrefix;

    public function __construct(?array $sourceRoots = null, ?string $outputRoot = null, ?string $outputUrlPrefix = null)
    {
        $this->sourceRoots = $sourceRoots ?? [
            'storage' => storage_path('app/public'),
            '' => public_path(),
        ];
        uksort($this->sourceRoots, static fn (string $left, string $right): int => strlen($right) <=> strlen($left));

        $this->outputRoot = $outputRoot ?? public_path('assets/pagebuilder_elementor/renditions');
        $this->outputUrlPrefix = '/'.trim($outputUrlPrefix ?? '/assets/pagebuilder_elementor/renditions', '/');
    }

    public function sizes(): array
    {
        return self::SIZES;
    }

    public function resolve(string $url, string $size): string
    {
        if (! array_key_exists($size, self::SIZES) || self::SIZES[$size] === null) {
            return $url;
        }

        $sourcePath = $this->sourcePath($url);
        if ($sourcePath === null || @getimagesize($sourcePath) === false) {
            return $url;
        }

        $extension = strtolower((string) pathinfo($sourcePath, PATHINFO_EXTENSION));
        $extension = $extension === 'jpeg' ? 'jpg' : $extension;
        if (! in_array($extension, ['jpg', 'png', 'gif', 'webp'], true)) {
            return $url;
        }

        try {
            $this->ensureOutputDirectory();
            $width = self::SIZES[$size];
            $fingerprint = implode('|', [
                $sourcePath,
                (string) filemtime($sourcePath),
                (string) filesize($sourcePath),
                $size,
                (string) $width,
            ]);
            $filename = sha1($fingerprint).'-'.$size.'.'.$extension;
            $destination = rtrim($this->outputRoot, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;

            if (! is_file($destination)) {
                $temporary = $destination.'.tmp';
                $manager = new ImageManager(new Driver());
                $encoded = $manager->read($sourcePath)
                    ->scaleDown(width: $width)
                    ->encodeByExtension($extension);
                $encoded->save($temporary);

                if (! @rename($temporary, $destination)) {
                    @unlink($temporary);

                    return $url;
                }
            }

            return $this->outputUrlPrefix.'/'.$filename;
        } catch (Throwable) {
            if (isset($temporary) && is_file($temporary)) {
                @unlink($temporary);
            }

            return $url;
        }
    }

    private function sourcePath(string $url): ?string
    {
        $raw = trim($url);
        if ($raw === '' || str_contains($raw, "\0") || str_contains($raw, '\\') || str_starts_with($raw, '//')) {
            return null;
        }

        $parts = parse_url($raw);
        if ($parts === false || isset($parts['scheme']) || isset($parts['host'])) {
            return null;
        }

        $path = rawurldecode((string) ($parts['path'] ?? ''));
        $segments = array_values(array_filter(explode('/', str_replace('\\', '/', $path)), static fn (string $segment): bool => $segment !== ''));
        if ($segments === [] || in_array('..', $segments, true) || in_array('.', $segments, true)) {
            return null;
        }

        $relative = implode(DIRECTORY_SEPARATOR, $segments);

        foreach ($this->sourceRoots as $prefix => $root) {
            $normalizedPrefix = trim(str_replace('\\', '/', (string) $prefix), '/');
            $normalizedPath = implode('/', $segments);
            if ($normalizedPrefix !== '' && $normalizedPath !== $normalizedPrefix && ! str_starts_with($normalizedPath, $normalizedPrefix.'/')) {
                continue;
            }

            $suffix = $normalizedPrefix === '' ? $normalizedPath : ltrim(substr($normalizedPath, strlen($normalizedPrefix)), '/');
            $rootPath = realpath((string) $root);
            $candidate = $rootPath === false ? false : realpath($rootPath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $suffix));

            if ($rootPath !== false && $candidate !== false && is_file($candidate) && $this->isContained($candidate, $rootPath)) {
                return $candidate;
            }
        }

        return null;
    }

    private function isContained(string $candidate, string $root): bool
    {
        $candidatePath = rtrim(str_replace('\\', '/', $candidate), '/').'/';
        $rootPath = rtrim(str_replace('\\', '/', $root), '/').'/';

        if (DIRECTORY_SEPARATOR === '\\') {
            return str_starts_with(strtolower($candidatePath), strtolower($rootPath));
        }

        return str_starts_with($candidatePath, $rootPath);
    }

    private function ensureOutputDirectory(): void
    {
        if (is_dir($this->outputRoot)) {
            return;
        }

        if (! @mkdir($this->outputRoot, 0755, true) && ! is_dir($this->outputRoot)) {
            throw new \RuntimeException('Unable to create the pagebuilder image rendition directory.');
        }
    }
}
