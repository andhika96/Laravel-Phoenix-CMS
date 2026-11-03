<?php

namespace App\Support;

class AuthAppearance
{
	public static function palette(?string $color): array
	{
		$color = is_string($color) && preg_match('/\A#[0-9a-f]{6}\z/i', $color) ? strtoupper($color) : '#1FA675';
		$rgb = array_map('hexdec', str_split(substr($color, 1), 2));
		$hover = self::mix($rgb, 0.9);
		$active = self::mix($rgb, 0.8);

		return [
			'primary' => $color,
			'hover' => $hover,
			'active' => $active,
			'disabled' => self::mix($rgb, 0.3, 255),
			'text' => self::textColor($color),
			'hover_text' => self::textColor($hover),
			'active_text' => self::textColor($active),
			'rgb' => implode(', ', $rgb),
		];
	}

	private static function mix(array $rgb, float $weight, int $target = 0): string
	{
		return sprintf('#%02X%02X%02X', ...array_map(fn ($channel) => (int) round($channel * $weight + $target * (1 - $weight)), $rgb));
	}

	private static function textColor(string $color): string
	{
		$channels = array_map(function ($channel) {
			$value = hexdec($channel) / 255;
			return $value <= 0.04045 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
		}, str_split(substr($color, 1), 2));
		$luminance = $channels[0] * 0.2126 + $channels[1] * 0.7152 + $channels[2] * 0.0722;

		return 1.05 / ($luminance + 0.05) >= ($luminance + 0.05) / 0.05 ? '#FFFFFF' : '#000000';
	}
}
