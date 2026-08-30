<?php

namespace App\Support\PageBuilderElementorV24\CompiledNative;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use ZipArchive;

final class AutomaticCompiledNativeSource
{
    private const MAX_FILES = 5000;

    private const MAX_UNCOMPRESSED_BYTES = 134217728;

    public static function fromUpload(UploadedFile $source, ?string $requestedEntry = null): SourcePackage
    {
        $extension = strtolower((string) ($source->getClientOriginalExtension() ?: pathinfo($source->getClientOriginalName(), PATHINFO_EXTENSION)));
        if (! in_array($extension, ['html', 'htm', 'zip'], true)) {
            throw new InvalidArgumentException('Only HTML, HTM, and ZIP sources are supported.');
        }

        $workspacePath = storage_path('app/pagebuilder-v24-compiled-native/'.bin2hex(random_bytes(16)));
        if (! mkdir($workspacePath, 0770, true) && ! is_dir($workspacePath)) {
            throw new InvalidArgumentException('The temporary source workspace could not be created.');
        }

        try {
            if ($extension !== 'zip') {
                return self::copyHtmlUpload($source, $workspacePath);
            }

            return self::extractZipUpload($source, $workspacePath, $requestedEntry);
        } catch (\Throwable $exception) {
            (new SourcePackage('', '', $workspacePath))->cleanup();
            throw $exception;
        }
    }

    private static function copyHtmlUpload(UploadedFile $source, string $workspacePath): SourcePackage
    {
        $originalName = basename((string) $source->getClientOriginalName());
        $entry = self::safeRelativePath($originalName) ?? 'index.html';
        if (! self::isHtmlPath($entry)) {
            throw new InvalidArgumentException('The uploaded source must have an HTML or HTM extension.');
        }

        $destination = $workspacePath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $entry);
        $directory = dirname($destination);
        if (! is_dir($directory) && ! mkdir($directory, 0770, true) && ! is_dir($directory)) {
            throw new InvalidArgumentException('The HTML source directory could not be created.');
        }
        if (! copy($source->getPathname(), $destination)) {
            throw new InvalidArgumentException('The HTML source could not be copied into the temporary workspace.');
        }

        $html = file_get_contents($destination);
        if ($html === false || trim($html) === '') {
            throw new InvalidArgumentException('The HTML source is empty.');
        }

        return new SourcePackage($html, $entry, $workspacePath, [$entry]);
    }

    private static function extractZipUpload(UploadedFile $source, string $workspacePath, ?string $requestedEntry): SourcePackage
    {
        $zip = new ZipArchive();
        $result = $zip->open($source->getPathname());
        if ($result !== true) {
            throw new InvalidArgumentException('The ZIP source could not be opened.');
        }

        $files = [];
        $diagnostics = [];
        $totalBytes = 0;
        try {
            if ($zip->numFiles > self::MAX_FILES) {
                throw new InvalidArgumentException('The ZIP source contains too many files.');
            }

            for ($index = 0; $index < $zip->numFiles; $index++) {
                $stat = $zip->statIndex($index);
                $rawName = (string) ($stat['name'] ?? '');
                $normalized = str_replace('\\', '/', $rawName);
                if ($normalized === '' || str_ends_with($normalized, '/')) {
                    continue;
                }

                $entry = self::safeRelativePath($normalized);
                if ($entry === null) {
                    $diagnostics[] = [
                        'code' => 'archive-path-rejected',
                        'path' => $rawName,
                        'message' => 'An unsafe archive path was not extracted.',
                    ];
                    continue;
                }

                $size = max(0, (int) ($stat['size'] ?? 0));
                $totalBytes += $size;
                if ($totalBytes > self::MAX_UNCOMPRESSED_BYTES) {
                    throw new InvalidArgumentException('The ZIP source expands beyond the supported size limit.');
                }

                $contents = $zip->getFromIndex($index);
                if ($contents === false) {
                    throw new InvalidArgumentException("The archive entry '{$entry}' could not be read.");
                }

                $destination = $workspacePath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $entry);
                $directory = dirname($destination);
                if (! is_dir($directory) && ! mkdir($directory, 0770, true) && ! is_dir($directory)) {
                    throw new InvalidArgumentException("The archive directory for '{$entry}' could not be created.");
                }
                if (file_put_contents($destination, $contents) === false) {
                    throw new InvalidArgumentException("The archive entry '{$entry}' could not be extracted.");
                }

                $files[$entry] = true;
            }
        } finally {
            $zip->close();
        }

        $fileList = array_keys($files);
        $entry = self::chooseHtmlEntry($fileList, $requestedEntry);
        $htmlPath = $workspacePath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $entry);
        $html = file_get_contents($htmlPath);
        if ($html === false || trim($html) === '') {
            throw new InvalidArgumentException('The selected HTML archive entry is empty.');
        }

        return new SourcePackage($html, $entry, $workspacePath, $fileList, $diagnostics);
    }

    /** @param array<int,string> $files */
    private static function chooseHtmlEntry(array $files, ?string $requestedEntry): string
    {
        $htmlFiles = array_values(array_filter($files, static fn (string $path): bool => self::isHtmlPath($path)));
        if ($htmlFiles === []) {
            throw new InvalidArgumentException('The ZIP source does not contain an HTML or HTM entry.');
        }

        if ($requestedEntry !== null && trim($requestedEntry) !== '') {
            $requested = self::safeRelativePath($requestedEntry);
            if ($requested === null || ! in_array($requested, $htmlFiles, true)) {
                throw new InvalidArgumentException('The requested ZIP HTML entry was not found.');
            }

            return $requested;
        }

        foreach (['home.html', 'index.html'] as $preferred) {
            foreach ($htmlFiles as $candidate) {
                if (strtolower($candidate) === $preferred) {
                    return $candidate;
                }
            }
        }

        if (count($htmlFiles) !== 1) {
            throw new InvalidArgumentException('The ZIP source contains multiple HTML entries; choose an entry explicitly.');
        }

        return $htmlFiles[0];
    }

    private static function isHtmlPath(string $path): bool
    {
        return in_array(strtolower((string) pathinfo($path, PATHINFO_EXTENSION)), ['html', 'htm'], true);
    }

    private static function safeRelativePath(string $path): ?string
    {
        $normalized = str_replace('\\', '/', trim($path));
        if ($normalized === '' || str_contains($normalized, "\0") || str_starts_with($normalized, '/') || preg_match('/^[A-Za-z]:\//', $normalized)) {
            return null;
        }

        $parts = [];
        foreach (explode('/', $normalized) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                return null;
            }
            $parts[] = $part;
        }

        return $parts === [] ? null : implode('/', $parts);
    }
}
