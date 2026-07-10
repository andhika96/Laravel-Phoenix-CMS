<?php

namespace App\Support;

use App\Models\Awesome_Admin\Header_Navigation_Setting;

class FrontendHeaderNavigationConfigNormalizer
{
	protected const UNITS = ['px', '%', 'em', 'rem', 'pt'];
	protected const DEVICES = ['desktop', 'tablet', 'mobile'];
	protected const POSITIONS = ['left', 'center', 'right'];
	protected const BEHAVIORS = ['stay', 'sticky', 'fixed'];
	protected const CONTAINERS = ['container', 'fluid'];
	protected const LINK_SHAPES = ['default', 'leaf'];
	protected const LEAF_DIRECTIONS = ['forward', 'reverse'];
	protected const TRANSPARENT_COLOR_MODES = ['auto', 'custom'];

	public static function defaultConfig(): array
	{
		return [
			'source' => '/awesome_admin/menu/fe/listdata/parentmenu',
			'colors' =>
			[
				'header_background' => '#ffffff',
				'scrolled_background' => '#ffffff',
				'header_text' => '#101828',
				'link' => '#273142',
				'link_hover' => '#e01d24',
				'link_hover_text' => '#ffffff',
				'link_hover_border' => '#e01d24',
				'link_hover_border_linked' => true,
				'link_focus' => '#c4121a',
				'link_focus_text' => '#ffffff',
				'link_focus_border' => '#c4121a',
				'link_focus_border_linked' => true,
				'link_active' => '#e01d24',
				'link_active_text' => '#ffffff',
				'link_active_border' => '#e01d24',
				'link_active_border_linked' => true,
				'transparent' =>
				[
					'mode' => 'auto',
					'header_text' => '#ffffff',
					'link' => '#ffffff',
					'hover' => '#e01d24',
					'hover_text' => '#ffffff',
					'hover_border' => '#e01d24',
					'focus' => '#c4121a',
					'focus_text' => '#ffffff',
					'focus_border' => '#c4121a',
					'active' => '#e01d24',
					'active_text' => '#ffffff',
					'active_border' => '#e01d24'
				]
			],
			'layout' =>
			[
				'logo_position' => 'left',
				'menu_position' => 'left',
				'container' => 'container',
				'background_follows_container' => false,
				'logo_between_menu' => false
			],
			'behavior' =>
			[
				'position' => 'stay',
				'transparent_before_scroll' => false,
				'transparent_color_mode' => 'auto',
				'animate_on_scroll' => false,
				'uses_scrolled_background' => false
			],
			'effects' =>
			[
				'link_shadow' =>
				[
					'enabled' => false,
					'x' => '0px',
					'y' => '8px',
					'blur' => '18px',
					'spread' => '0px',
					'color' => '#e01d242e',
					'inset' => false,
					'value' => 'none'
				]
			],
			'sizing' =>
			[
				'height' => '76px',
				'header_radius' => self::defaultBox(['18', '18', '18', '18'], true),
				'header_padding' => self::defaultBox(['10', '24', '10', '24'], false),
				'link_shape' => 'default',
				'leaf_direction' => null,
				'link_radius' => self::defaultBox(['0', '0', '0', '0'], true),
				'container_margin' => self::defaultBox(['0', '0', '0', '0'], true)
			]
		];
	}

	public static function current(string $menuPage = 'awesome_admin'): array
	{
		$setting = Header_Navigation_Setting::where('menu_page', $menuPage)->first();

		return [
			'is_active' => $setting?->is_active ?? true,
			'config_json' => self::normalize($setting?->config_json ?? [])
		];
	}

	public static function normalize(array $config): array
	{
		$defaults = self::defaultConfig();
		$colors = is_array($config['colors'] ?? null) ? $config['colors'] : [];
		$transparent = is_array($colors['transparent'] ?? null) ? $colors['transparent'] : [];
		$layout = is_array($config['layout'] ?? null) ? $config['layout'] : [];
		$behavior = is_array($config['behavior'] ?? null) ? $config['behavior'] : [];
		$effects = is_array($config['effects'] ?? null) ? $config['effects'] : [];
		$shadow = is_array($effects['link_shadow'] ?? null) ? $effects['link_shadow'] : [];
		$sizing = is_array($config['sizing'] ?? null) ? $config['sizing'] : [];

		$source = is_string($config['source'] ?? null) ? trim($config['source']) : $defaults['source'];
		if ($source === '' || strlen($source) > 255 || ! preg_match('/^(\/|https?:\/\/)/i', $source))
		{
			$source = $defaults['source'];
		}

		$linkShape = self::choice($sizing['link_shape'] ?? null, self::LINK_SHAPES, 'default');
		$leafDirection = self::choice($sizing['leaf_direction'] ?? null, self::LEAF_DIRECTIONS, 'forward');
		$transparentMode = self::choice($transparent['mode'] ?? ($behavior['transparent_color_mode'] ?? null), self::TRANSPARENT_COLOR_MODES, 'auto');

		$normalized = [
			'source' => $source,
			'colors' =>
			[
				'header_background' => self::color($colors['header_background'] ?? null, '#ffffff'),
				'scrolled_background' => self::color($colors['scrolled_background'] ?? null, '#ffffff'),
				'header_text' => self::color($colors['header_text'] ?? null, '#101828'),
				'link' => self::color($colors['link'] ?? null, '#273142'),
				'link_hover' => self::color($colors['link_hover'] ?? null, '#e01d24'),
				'link_hover_text' => self::color($colors['link_hover_text'] ?? null, '#ffffff'),
				'link_hover_border' => self::color($colors['link_hover_border'] ?? null, '#e01d24'),
				'link_hover_border_linked' => self::boolean($colors['link_hover_border_linked'] ?? true, true),
				'link_focus' => self::color($colors['link_focus'] ?? null, '#c4121a'),
				'link_focus_text' => self::color($colors['link_focus_text'] ?? null, '#ffffff'),
				'link_focus_border' => self::color($colors['link_focus_border'] ?? null, '#c4121a'),
				'link_focus_border_linked' => self::boolean($colors['link_focus_border_linked'] ?? true, true),
				'link_active' => self::color($colors['link_active'] ?? null, '#e01d24'),
				'link_active_text' => self::color($colors['link_active_text'] ?? null, '#ffffff'),
				'link_active_border' => self::color($colors['link_active_border'] ?? null, '#e01d24'),
				'link_active_border_linked' => self::boolean($colors['link_active_border_linked'] ?? true, true),
				'transparent' =>
				[
					'mode' => $transparentMode,
					'header_text' => self::color($transparent['header_text'] ?? null, '#ffffff'),
					'link' => self::color($transparent['link'] ?? null, '#ffffff'),
					'hover' => self::color($transparent['hover'] ?? null, '#e01d24'),
					'hover_text' => self::color($transparent['hover_text'] ?? null, '#ffffff'),
					'hover_border' => self::color($transparent['hover_border'] ?? null, '#e01d24'),
					'focus' => self::color($transparent['focus'] ?? null, '#c4121a'),
					'focus_text' => self::color($transparent['focus_text'] ?? null, '#ffffff'),
					'focus_border' => self::color($transparent['focus_border'] ?? null, '#c4121a'),
					'active' => self::color($transparent['active'] ?? null, '#e01d24'),
					'active_text' => self::color($transparent['active_text'] ?? null, '#ffffff'),
					'active_border' => self::color($transparent['active_border'] ?? null, '#e01d24')
				]
			],
			'layout' =>
			[
				'logo_position' => self::choice($layout['logo_position'] ?? null, self::POSITIONS, 'left'),
				'menu_position' => self::choice($layout['menu_position'] ?? null, self::POSITIONS, 'left'),
				'container' => self::choice($layout['container'] ?? null, self::CONTAINERS, 'container'),
				'background_follows_container' => self::boolean($layout['background_follows_container'] ?? false),
				'logo_between_menu' => self::boolean($layout['logo_between_menu'] ?? false)
			],
			'behavior' =>
			[
				'position' => self::choice($behavior['position'] ?? null, self::BEHAVIORS, 'stay'),
				'transparent_before_scroll' => self::boolean($behavior['transparent_before_scroll'] ?? false),
				'transparent_color_mode' => $transparentMode,
				'animate_on_scroll' => self::boolean($behavior['animate_on_scroll'] ?? false),
				'uses_scrolled_background' => self::boolean($behavior['uses_scrolled_background'] ?? false)
			],
			'effects' =>
			[
				'link_shadow' => self::shadow($shadow)
			],
			'sizing' =>
			[
				'height' => self::dimension($sizing['height'] ?? null, '76px', 32, 240),
				'header_radius' => self::box($sizing['header_radius'] ?? [], ['18', '18', '18', '18'], true),
				'header_padding' => self::box($sizing['header_padding'] ?? [], ['10', '24', '10', '24'], false),
				'link_shape' => $linkShape,
				'leaf_direction' => $linkShape == 'leaf' ? $leafDirection : null,
				'link_radius' => self::box($sizing['link_radius'] ?? [], ['0', '0', '0', '0'], true),
				'container_margin' => self::box($sizing['container_margin'] ?? [], ['0', '0', '0', '0'], true)
			]
		];

		return $normalized;
	}

	public static function cssVariables(array $config, string $device = 'desktop'): array
	{
		$config = self::normalize($config);
		$colors = $config['colors'];
		$transparent = $colors['transparent'];
		$shadow = $config['effects']['link_shadow'];
		$containerMargin = self::boxProfile($config['sizing']['container_margin'], $device);

		return [
			'--ph-fe-header-background' => $colors['header_background'],
			'--ph-fe-scrolled-background' => $colors['scrolled_background'],
			'--ph-fe-header-text' => $colors['header_text'],
			'--ph-fe-link-color' => $colors['link'],
			'--ph-fe-link-hover' => $colors['link_hover'],
			'--ph-fe-link-hover-text' => $colors['link_hover_text'],
			'--ph-fe-link-hover-border' => $colors['link_hover_border'],
			'--ph-fe-link-focus' => $colors['link_focus'],
			'--ph-fe-link-focus-text' => $colors['link_focus_text'],
			'--ph-fe-link-focus-border' => $colors['link_focus_border'],
			'--ph-fe-link-active' => $colors['link_active'],
			'--ph-fe-link-active-text' => $colors['link_active_text'],
			'--ph-fe-link-active-border' => $colors['link_active_border'],
			'--ph-fe-transparent-header-text' => $transparent['header_text'],
			'--ph-fe-transparent-link-color' => $transparent['link'],
			'--ph-fe-transparent-hover' => $transparent['hover'],
			'--ph-fe-transparent-hover-text' => $transparent['hover_text'],
			'--ph-fe-transparent-hover-border' => $transparent['hover_border'],
			'--ph-fe-transparent-focus' => $transparent['focus'],
			'--ph-fe-transparent-focus-text' => $transparent['focus_text'],
			'--ph-fe-transparent-focus-border' => $transparent['focus_border'],
			'--ph-fe-transparent-active' => $transparent['active'],
			'--ph-fe-transparent-active-text' => $transparent['active_text'],
			'--ph-fe-transparent-active-border' => $transparent['active_border'],
			'--ph-fe-header-height' => $config['sizing']['height'],
			'--ph-fe-header-radius' => self::boxCss($config['sizing']['header_radius'], $device),
			'--ph-fe-header-padding' => self::boxCss($config['sizing']['header_padding'], $device),
			'--ph-fe-link-radius' => self::boxCss($config['sizing']['link_radius'], $device),
			'--ph-fe-container-margin-top' => $containerMargin['top'],
			'--ph-fe-container-margin-right' => $containerMargin['right'],
			'--ph-fe-container-margin-bottom' => $containerMargin['bottom'],
			'--ph-fe-container-margin-left' => $containerMargin['left'],
			'--ph-fe-link-shadow' => $shadow['value']
		];
	}

	protected static function defaultBox(array $values, bool $linked): array
	{
		$profile = self::profileFromValues($values, 'px', $linked);

		return array_merge($profile,
		[
			'mode' => 'desktop',
			'preview_device' => 'desktop',
			'responsive' =>
			[
				'all' => $profile,
				'desktop' => null,
				'tablet' => null,
				'mobile' => null
			]
		]);
	}

	protected static function box(array $box, array $defaults, bool $linked): array
	{
		$base = self::profile($box, $defaults, $linked);
		$responsive = is_array($box['responsive'] ?? null) ? $box['responsive'] : [];
		$all = self::profile(is_array($responsive['all'] ?? null) ? $responsive['all'] : $box, $defaults, $linked);
		$profiles = ['all' => $all];

		foreach (self::DEVICES as $device)
		{
			$profiles[$device] = is_array($responsive[$device] ?? null)
				? self::profile($responsive[$device], $defaults, $linked)
				: null;
		}

		return array_merge($base,
		[
			'mode' => self::choice($box['mode'] ?? null, self::DEVICES, 'desktop'),
			'preview_device' => self::choice($box['preview_device'] ?? null, self::DEVICES, 'desktop'),
			'responsive' => $profiles
		]);
	}

	protected static function profile(array $profile, array $defaults, bool $linked): array
	{
		$unit = self::choice($profile['unit'] ?? null, self::UNITS, 'px');
		$values = [];

		foreach (['top', 'right', 'bottom', 'left'] as $index => $side)
		{
			$values[] = self::dimension($profile[$side] ?? null, $defaults[$index].$unit, -500, 1000, $unit);
		}

		return [
			'top' => $values[0],
			'right' => $values[1],
			'bottom' => $values[2],
			'left' => $values[3],
			'unit' => $unit,
			'linked' => self::boolean($profile['linked'] ?? $linked, $linked)
		];
	}

	protected static function profileFromValues(array $values, string $unit, bool $linked): array
	{
		return [
			'top' => $values[0].$unit,
			'right' => $values[1].$unit,
			'bottom' => $values[2].$unit,
			'left' => $values[3].$unit,
			'unit' => $unit,
			'linked' => $linked
		];
	}

	protected static function shadow(array $shadow): array
	{
		$enabled = self::boolean($shadow['enabled'] ?? false);
		$x = self::dimension($shadow['x'] ?? null, '0px', -500, 500);
		$y = self::dimension($shadow['y'] ?? null, '8px', -500, 500);
		$blur = self::dimension($shadow['blur'] ?? null, '18px', 0, 500);
		$spread = self::dimension($shadow['spread'] ?? null, '0px', -500, 500);
		$color = self::color($shadow['color'] ?? null, '#e01d242e');
		$inset = self::boolean($shadow['inset'] ?? false);
		$value = $enabled ? ($inset ? 'inset ' : '').$x.' '.$y.' '.$blur.' '.$spread.' '.$color : 'none';

		return compact('enabled', 'x', 'y', 'blur', 'spread', 'color', 'inset', 'value');
	}

	protected static function boxCss(array $box, string $device): string
	{
		$profile = self::boxProfile($box, $device);

		return implode(' ', [$profile['top'], $profile['right'], $profile['bottom'], $profile['left']]);
	}

	protected static function boxProfile(array $box, string $device): array
	{
		$device = self::choice($device, self::DEVICES, 'desktop');

		return $box['responsive'][$device] ?? $box['responsive']['all'] ?? $box;
	}

	protected static function color($value, string $default): string
	{
		if ( ! is_string($value))
		{
			return $default;
		}

		$value = trim($value);
		if (strtolower($value) === 'transparent' || preg_match('/^#([0-9a-f]{6}|[0-9a-f]{8})$/i', $value))
		{
			return $value;
		}

		if (preg_match('/^(rgba?|hsla?)\([0-9\s.,%\/+-]+\)$/i', $value))
		{
			return $value;
		}

		return $default;
	}

	protected static function dimension($value, string $default, float $min, float $max, ?string $forcedUnit = null): string
	{
		$value = is_string($value) || is_numeric($value) ? trim((string) $value) : $default;

		if ( ! preg_match('/^(-?\d+(?:\.\d+)?)(px|%|em|rem|pt)$/i', $value, $matches))
		{
			if ($forcedUnit !== null && preg_match('/^-?\d+(?:\.\d+)?$/', $value))
			{
				$matches = [null, $value, $forcedUnit];
			}
			else
			{
				preg_match('/^(-?\d+(?:\.\d+)?)(px|%|em|rem|pt)$/i', $default, $matches);
			}
		}

		$number = max($min, min($max, (float) $matches[1]));
		$unit = $forcedUnit ?? strtolower($matches[2]);
		$formatted = rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');

		return $formatted.$unit;
	}

	protected static function boolean($value, bool $default = false): bool
	{
		$normalized = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

		return $normalized ?? $default;
	}

	protected static function choice($value, array $allowed, string $default): string
	{
		return is_string($value) && in_array($value, $allowed, true) ? $value : $default;
	}
}
