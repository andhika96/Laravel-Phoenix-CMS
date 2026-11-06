<?php

namespace App\Support;

use App\Models\Awesome_Admin\Page_Theme_Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthThemeImages
{
    public static function paths(Page_Theme_Setting $setting, string $theme, string $slot): array
    {
        $saved = ($setting->page_theme_options ?? [])[$theme] ?? [];
        $value = in_array($theme, AuthThemeCatalog::LEGACY, true) ? $setting->page_background_image
            : (array_key_exists($slot, $saved) ? $saved[$slot] : (AuthThemeCatalog::fields($theme)[$slot]['default'] ?? null));
        return array_values(array_filter(is_array($value) ? $value : [$value], AuthThemeCatalog::validImagePath(...)));
    }

    public static function store(string $page, string $theme, string $slot, array $files): array
    {
        self::validateSlot($page, $theme, $slot);
        $multiple = (AuthThemeCatalog::fields($theme)[$slot]['type'] ?? '') === 'images';
        if (!$multiple && count($files) !== 1) {
            throw ValidationException::withMessages(['images' => 'Choose one image for this field.']);
        }
        $newPaths = [];
        try {
            foreach ($files as $file) {
                $path = $file->store('images/page_themes/auth/'.$page.'/'.$theme, 'public');
                if (!$path) {
                    throw new \RuntimeException('The image could not be stored.');
                }
                $newPaths[] = $path;
            }
            return DB::transaction(function () use ($page, $theme, $slot, $newPaths, $multiple) {
                $setting = Page_Theme_Setting::where('uri', $page)->lockForUpdate()->firstOrFail();
                $previous = self::paths($setting, $theme, $slot);
                $paths = $multiple ? array_merge($previous, $newPaths) : $newPaths;
                if (count($paths) > 6) {
                    throw ValidationException::withMessages(['images' => 'A maximum of six avatar images is supported.']);
                }
                self::write($setting, $theme, $slot, $paths, $multiple);
                if (!$multiple) {
                    DB::afterCommit(fn () => self::deleteUnreferenced($previous));
                }
                return self::result($paths);
            }, 3);
        } catch (\Throwable $error) {
            self::deleteUnreferenced($newPaths);
            throw $error;
        }
    }

    public static function remove(string $page, string $theme, string $slot, string $path): array
    {
        self::validateSlot($page, $theme, $slot);
        if (!AuthThemeCatalog::validImagePath($path)) {
            throw ValidationException::withMessages(['path' => 'This image does not belong to the selected field.']);
        }
        return DB::transaction(function () use ($page, $theme, $slot, $path) {
            $setting = Page_Theme_Setting::where('uri', $page)->lockForUpdate()->firstOrFail();
            $paths = self::paths($setting, $theme, $slot);
            if (!in_array($path, $paths, true)) {
                throw ValidationException::withMessages(['path' => 'The image has changed. Refresh the settings and try again.']);
            }
            $paths = array_values(array_diff($paths, [$path]));
            $multiple = (AuthThemeCatalog::fields($theme)[$slot]['type'] ?? '') === 'images';
            self::write($setting, $theme, $slot, $paths, $multiple);
            DB::afterCommit(fn () => self::deleteUnreferenced([$path]));
            return self::result($paths);
        }, 3);
    }

    private static function validateSlot(string $page, string $theme, string $slot): void
    {
        if (!in_array($page, AuthThemeCatalog::PAGES, true) || !AuthThemeCatalog::supports($theme)
            || !in_array($slot, AuthThemeCatalog::imageSlots($theme), true)) {
            throw ValidationException::withMessages(['slot' => 'Unsupported authentication image field.']);
        }
    }

    private static function write(Page_Theme_Setting $setting, string $theme, string $slot, array $paths, bool $multiple): void
    {
        $value = $multiple ? $paths : ($paths[0] ?? null);
        if (in_array($theme, AuthThemeCatalog::LEGACY, true)) {
            $setting->update(['page_background_image' => $value]);
            return;
        }
        $options = $setting->page_theme_options ?? [];
        $options[$theme][$slot] = $value;
        $setting->update(['page_theme_options' => $options]);
    }

    private static function result(array $paths): array
    {
        return ['success' => true, 'status' => 'success', 'paths' => $paths, 'urls' => array_map(AuthThemeCatalog::imageUrl(...), $paths)];
    }

    public static function deleteUnreferenced(array $paths): void
    {
        foreach ($paths as $path) {
            // Shared/older assets are retained; only this uploader's owned files may be removed.
            if (!AuthThemeCatalog::validImagePath($path) || !str_starts_with($path, 'images/page_themes/auth/')) {
                continue;
            }
            $referenced = Page_Theme_Setting::get()->contains(function ($setting) use ($path) {
                if ($setting->page_background_image === $path) {
                    return true;
                }
                $values = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($setting->page_theme_options ?? []));
                foreach ($values as $value) {
                    if ($value === $path) {
                        return true;
                    }
                }
                return false;
            });
            if (!$referenced) {
                try {
                    Storage::disk('public')->delete($path);
                } catch (\Throwable $error) {
                    report($error);
                }
            }
        }
    }
}
