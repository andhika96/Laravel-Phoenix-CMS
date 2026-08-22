<?php

namespace App\Support\PageBuilderElementorV24;

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

    public function resolve(string $url, string $size, ?int $width = null, ?int $height = null): string
    {
        $isCustom = $size === 'custom';
        if (! $isCustom && (! array_key_exists($size, self::SIZES) || self::SIZES[$size] === null)) {
            return $url;
        }

        if ($isCustom) {
            $width = $width === null ? null : max(1, min(4096, $width));
            $height = $height === null ? null : max(1, min(4096, $height));
            if ($width === null && $height === null) {
                return $url;
            }
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
            $width = $isCustom ? $width : self::SIZES[$size];
            $height = $isCustom ? $height : null;
            $fingerprint = implode('|', [
                $sourcePath,
                (string) filemtime($sourcePath),
                (string) filesize($sourcePath),
                $size,
                (string) $width,
                (string) $height,
            ]);
            $dimensionSuffix = $isCustom ? '-'.($width ?? 0).'x'.($height ?? 0) : '';
            $filename = sha1($fingerprint).'-'.$size.$dimensionSuffix.'.'.$extension;
            $destination = rtrim($this->outputRoot, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;

            if (! is_file($destination)) {
                $temporary = $destination.'.tmp';
                $manager = new ImageManager(new Driver());
                $image = $manager->read($sourcePath);
                $scaled = $isCustom
                    ? ($width !== null && $height !== null
                        ? $image->coverDown($width, $height)
                        : $image->scaleDown(width: $width, height: $height))
                    : $image->scaleDown(width: $width);
                $encoded = $scaled->encodeByExtension($extension);
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
        if ($parts === false || ((isset($parts['scheme']) || isset($parts['host'])) && ! $this->isSameOriginUrl($parts))) {
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

    /**
     * CKFinder returns absolute URLs in some browser configurations. They are
     * safe to turn into a local path only when they belong to this CMS; remote
     * URLs stay fail-closed and are never fetched by the rendition endpoint.
     */
    private function isSameOriginUrl(array $parts): bool
    {
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));
        if (! in_array($scheme, ['http', 'https'], true) || $host === '' || isset($parts['user']) || isset($parts['pass'])) {
            return false;
        }

        $configuredHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        $requestHost = app()->bound('request') ? request()->getHost() : null;
        $trustedHosts = array_filter([$configuredHost, $requestHost], static fn ($candidate): bool => is_string($candidate) && $candidate !== '');

        foreach ($trustedHosts as $trustedHost) {
            if ($host === strtolower($trustedHost)) {
                return true;
            }
        }

        return false;
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
