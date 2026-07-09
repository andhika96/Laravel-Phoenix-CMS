<?php

namespace App\Support;

class FrontendMenuDropdownConfigNormalizer
{
	protected static array $dropdownTypes = ['none', 'bootstrap', 'mega'];

	protected static array $megaLayouts = ['columns', 'cards', 'featured', 'category_grid'];

	protected static array $alignments = ['start', 'center', 'end'];

	protected static array $widthModes = ['container', 'full', 'custom'];

	protected static array $positions = ['left', 'center', 'right'];

	public static function defaultConfig(): array
	{
		return [
			'dropdown_type' => 'none',
			'mega_layout' => 'columns',
			'config_json' =>
			[
				'dropdown' =>
				[
					'show_arrow' => true,
					'margin_top' => 12,
					'arrow_size' => 12,
					'width' => 760,
					'width_mode' => 'container',
					'align' => 'center',
				],
				'bootstrap' =>
				[
					'width' => 260,
					'align' => 'start',
				],
				'mega' =>
				[
					'columns' => 3,
					'max_items' => 12,
					'image_position' => 'left',
					'title_position' => 'left',
					'description_position' => 'left',
					'show_images' => true,
					'show_description' => true,
					'featured_index' => 0,
				],
			],
		];
	}

	public static function normalize(array $input): array
	{
		$default = self::defaultConfig();
		$configJson = self::configJsonToArray($input['config_json'] ?? []);

		return [
			'dropdown_type' => self::allowedString($input['dropdown_type'] ?? $default['dropdown_type'], self::$dropdownTypes, $default['dropdown_type']),
			'mega_layout' => self::allowedString($input['mega_layout'] ?? $default['mega_layout'], self::$megaLayouts, $default['mega_layout']),
			'config_json' =>
			[
				'dropdown' =>
				[
					'show_arrow' => self::booleanValue($configJson['dropdown']['show_arrow'] ?? $default['config_json']['dropdown']['show_arrow'], $default['config_json']['dropdown']['show_arrow']),
					'margin_top' => self::integerRange($configJson['dropdown']['margin_top'] ?? $default['config_json']['dropdown']['margin_top'], 0, 80, $default['config_json']['dropdown']['margin_top']),
					'arrow_size' => self::integerRange($configJson['dropdown']['arrow_size'] ?? $default['config_json']['dropdown']['arrow_size'], 0, 32, $default['config_json']['dropdown']['arrow_size']),
					'width' => self::integerRange($configJson['dropdown']['width'] ?? $default['config_json']['dropdown']['width'], 240, 1440, $default['config_json']['dropdown']['width']),
					'width_mode' => self::allowedString($configJson['dropdown']['width_mode'] ?? $default['config_json']['dropdown']['width_mode'], self::$widthModes, $default['config_json']['dropdown']['width_mode']),
					'align' => self::allowedString($configJson['dropdown']['align'] ?? $default['config_json']['dropdown']['align'], self::$alignments, $default['config_json']['dropdown']['align']),
				],
				'bootstrap' =>
				[
					'width' => self::integerRange($configJson['bootstrap']['width'] ?? $default['config_json']['bootstrap']['width'], 160, 520, $default['config_json']['bootstrap']['width']),
					'align' => self::allowedString($configJson['bootstrap']['align'] ?? $default['config_json']['bootstrap']['align'], self::$alignments, $default['config_json']['bootstrap']['align']),
				],
				'mega' =>
				[
					'columns' => self::integerRange($configJson['mega']['columns'] ?? $default['config_json']['mega']['columns'], 1, 6, $default['config_json']['mega']['columns']),
					'max_items' => self::integerRange($configJson['mega']['max_items'] ?? $default['config_json']['mega']['max_items'], 1, 24, $default['config_json']['mega']['max_items']),
					'image_position' => self::allowedString($configJson['mega']['image_position'] ?? $default['config_json']['mega']['image_position'], self::$positions, $default['config_json']['mega']['image_position']),
					'title_position' => self::allowedString($configJson['mega']['title_position'] ?? $default['config_json']['mega']['title_position'], self::$positions, $default['config_json']['mega']['title_position']),
					'description_position' => self::allowedString($configJson['mega']['description_position'] ?? $default['config_json']['mega']['description_position'], self::$positions, $default['config_json']['mega']['description_position']),
					'show_images' => self::booleanValue($configJson['mega']['show_images'] ?? $default['config_json']['mega']['show_images'], $default['config_json']['mega']['show_images']),
					'show_description' => self::booleanValue($configJson['mega']['show_description'] ?? $default['config_json']['mega']['show_description'], $default['config_json']['mega']['show_description']),
					'featured_index' => self::integerRange($configJson['mega']['featured_index'] ?? $default['config_json']['mega']['featured_index'], 0, 23, $default['config_json']['mega']['featured_index']),
				],
			],
		];
	}

	protected static function configJsonToArray(mixed $value): array
	{
		if (is_string($value))
		{
			$decoded = json_decode($value, true);

			return is_array($decoded) ? $decoded : [];
		}

		return is_array($value) ? $value : [];
	}

	protected static function allowedString(mixed $value, array $allowed, string $fallback): string
	{
		if ( ! is_string($value))
		{
			return $fallback;
		}

		return in_array($value, $allowed, true) ? $value : $fallback;
	}

	protected static function integerRange(mixed $value, int $min, int $max, int $fallback): int
	{
		if ( ! is_numeric($value))
		{
			return $fallback;
		}

		return min($max, max($min, (int) $value));
	}

	protected static function booleanValue(mixed $value, bool $fallback): bool
	{
		if (is_bool($value))
		{
			return $value;
		}

		if ($value === 1 || $value === '1' || $value === 'true' || $value === 'on')
		{
			return true;
		}

		if ($value === 0 || $value === '0' || $value === 'false' || $value === 'off')
		{
			return false;
		}

		return $fallback;
	}
}
