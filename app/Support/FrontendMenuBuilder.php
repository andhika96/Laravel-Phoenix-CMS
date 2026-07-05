<?php

namespace App\Support;

use App\Models\Awesome_Admin\Category_Menu_FE_JSON;
use App\Models\Awesome_Admin\Parent_Menu_FE_JSON;
use App\Models\Awesome_Admin\Parent_Menu_FE_Dropdown_Config;
use App\Models\Awesome_Admin\Sub_Menu_FE_JSON;
use Illuminate\Support\Str;

class FrontendMenuBuilder
{
	public static function items(string $menuPage = 'awesome_admin'): array
	{
		$parentMenu = Parent_Menu_FE_JSON::where('menu_page', $menuPage)->first();
		$parents = self::decodeMenuVars($parentMenu?->menu_vars);
		$categories = self::categories($menuPage);
		$configs = Parent_Menu_FE_Dropdown_Config::where('menu_page', $menuPage)->get()->keyBy('parent_code');
		$submenus = Sub_Menu_FE_JSON::get()->keyBy('parent_code');
		$items = [];

		foreach ($parents as $parent)
		{
			$parentCode = $parent['parent_code'] ?? '';
			$dropdownConfig = isset($configs[$parentCode]) ? FrontendMenuDropdownConfigNormalizer::normalize(
			[
				'dropdown_type' => $configs[$parentCode]->dropdown_type,
				'mega_layout' => $configs[$parentCode]->mega_layout,
				'config_json' => $configs[$parentCode]->config_json,
			]) : FrontendMenuDropdownConfigNormalizer::defaultConfig();

			$items[] = [
				'parent_code' => $parentCode,
				'parent_name' => $parent['parent_name'] ?? '',
				'parent_link' => $parent['parent_link'] ?? '',
				'parent_url' => self::menuUrl($parent['parent_link'] ?? ''),
				'parent_type' => $parent['parent_type'] ?? '',
				'is_for_parent_menu' => $parent['is_for_parent_menu'] ?? 'single',
				'category_code' => $parent['category_code'] ?? '',
				'category_name' => $categories[$parent['category_code'] ?? ''] ?? '',
				'icon_html' => self::safeIconHtml($parent['parent_icon_custom'] ?? ''),
				'icon_url' => self::imageUrl($parent['parent_icon_path'] ?? '', 'icons/parent_menu'),
				'dropdown_config' => $dropdownConfig,
				'submenus' => self::submenusForParent($submenus[$parentCode] ?? null),
			];
		}

		return $items;
	}

	public static function menuUrl(?string $link): string
	{
		$link = trim((string) $link);

		if ($link == '')
		{
			return '#';
		}

		if (Str::startsWith($link, ['http://', 'https://', '#', 'mailto:', 'tel:']))
		{
			return $link;
		}

		return url($link);
	}

	public static function safeIconHtml(?string $html): string
	{
		if ( ! is_string($html) || $html == '')
		{
			return '';
		}

		if ( ! preg_match('/<i[^>]*class=("|\')([^"\']+)("|\')[^>]*>/i', $html, $matches))
		{
			return '';
		}

		$class = trim(preg_replace('/\s+/', ' ', preg_replace('/[^A-Za-z0-9 _-]/', '', $matches[2])));

		if ($class == '')
		{
			return '';
		}

		return '<i class="'.e($class).'"></i>';
	}

	protected static function submenusForParent($submenuRow): array
	{
		$submenus = self::decodeMenuVars($submenuRow?->menu_vars);
		$items = [];

		foreach ($submenus as $submenu)
		{
			$items[] = [
				'submenu_name' => $submenu['submenu_name'] ?? '',
				'submenu_link' => $submenu['submenu_link'] ?? '',
				'submenu_url' => self::menuUrl($submenu['submenu_link'] ?? ''),
				'submenu_type' => $submenu['submenu_type'] ?? '',
				'submenu_description' => $submenu['submenu_description'] ?? ($submenu['description'] ?? ''),
				'icon_html' => self::safeIconHtml($submenu['submenu_icon_custom'] ?? ''),
				'icon_url' => self::imageUrl($submenu['submenu_icon_path'] ?? '', 'icons/sub_menu'),
			];
		}

		return $items;
	}

	protected static function categories(string $menuPage): array
	{
		$categoryMenu = Category_Menu_FE_JSON::where('menu_page', $menuPage)->first();
		$categories = [];

		foreach (self::decodeMenuVars($categoryMenu?->menu_vars) as $category)
		{
			if (isset($category['category_code']))
			{
				$categories[$category['category_code']] = $category['category_name'] ?? '';
			}
		}

		return $categories;
	}

	protected static function decodeMenuVars(?string $menuVars): array
	{
		if ( ! is_string($menuVars) || $menuVars == '' || $menuVars === 'null')
		{
			return [];
		}

		$decoded = json_decode($menuVars, true);

		return is_array($decoded) ? array_values($decoded) : [];
	}

	protected static function imageUrl(?string $path, string $directory): string
	{
		if ( ! is_string($path) || $path == '')
		{
			return '';
		}

		if (function_exists('getImageURL'))
		{
			return url(getImageURL($path, $directory));
		}

		return asset('storage/'.$directory.'/'.$path);
	}
}
