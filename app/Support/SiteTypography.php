<?php

namespace App\Support;

use App\Models\Awesome_Admin\Site_Config;
use Illuminate\Support\Facades\Storage;

final class SiteTypography
{
    private const UNITS = ['px', 'em', 'rem'];

    public function resolve(Site_Config $config): array
    {
        $fontFamilyCode = strtolower(str_replace([' ', '-'], '_', trim((string) ($config->font_family ?? 'nunito'))));
        $fontFamilyCode = preg_match('/\A[a-z0-9_]+\z/D', $fontFamilyCode) === 1
            && Storage::exists('public/fonts/'.$fontFamilyCode.'/fonts.css')
                ? $fontFamilyCode
                : 'nunito';

        $fontSizeUnit = in_array($config->font_size_unit ?? 'px', self::UNITS, true)
            ? $config->font_size_unit
            : 'px';
        $fontSizeMin = $fontSizeUnit === 'px' ? 8 : .5;
        $fontSizeMax = $fontSizeUnit === 'px' ? 72 : 4.5;
        $fontSizeValue = is_numeric($config->font_size ?? null)
            ? (float) $config->font_size
            : ($fontSizeUnit === 'px' ? 14 : .875);
        $fontSizeValue = min($fontSizeMax, max($fontSizeMin, $fontSizeValue));

        return [
            'fontFamilyCode' => $fontFamilyCode,
            'fontFamilyName' => ucwords(str_replace('_', ' ', $fontFamilyCode)),
            'fontSize' => rtrim(rtrim(number_format($fontSizeValue, 3, '.', ''), '0'), '.').$fontSizeUnit,
        ];
    }
}
