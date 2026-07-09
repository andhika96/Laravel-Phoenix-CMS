<?php

namespace Tests\Unit;

use App\Support\FrontendMenuDropdownConfigNormalizer;
use PHPUnit\Framework\TestCase;

class FrontendMenuDropdownConfigNormalizerTest extends TestCase
{
	public function test_it_builds_secure_defaults_without_manual_arrow_position(): void
	{
		$config = FrontendMenuDropdownConfigNormalizer::defaultConfig();

		$this->assertSame('none', $config['dropdown_type']);
		$this->assertSame('columns', $config['mega_layout']);
		$this->assertTrue($config['config_json']['dropdown']['show_arrow']);
		$this->assertSame(12, $config['config_json']['dropdown']['arrow_size']);
		$this->assertSame('container', $config['config_json']['dropdown']['width_mode']);
		$this->assertSame('', $config['config_json']['dropdown']['toggle_icon_type']);
		$this->assertSame('', $config['config_json']['dropdown']['toggle_icon_path']);
		$this->assertSame('', $config['config_json']['dropdown']['toggle_icon_url']);
		$this->assertSame('', $config['config_json']['dropdown']['toggle_icon_custom']);
		$this->assertArrayNotHasKey('arrow_position', $config['config_json']['dropdown']);
	}

	public function test_it_normalizes_dropdown_toggle_icon_configuration(): void
	{
		$config = FrontendMenuDropdownConfigNormalizer::normalize(
		[
			'config_json' =>
			[
				'dropdown' =>
				[
					'toggle_icon_type' => 'custom_input',
					'toggle_icon_path' => 'dropdown_toggle_123.png',
					'toggle_icon_url' => 'https://example.test/storage/icons/dropdown_toggle/dropdown_toggle_123.png',
					'toggle_icon_custom' => '<i class="fas fa-angle-down"></i>',
				],
			],
		]);

		$this->assertSame('custom_input', $config['config_json']['dropdown']['toggle_icon_type']);
		$this->assertSame('dropdown_toggle_123.png', $config['config_json']['dropdown']['toggle_icon_path']);
		$this->assertSame('https://example.test/storage/icons/dropdown_toggle/dropdown_toggle_123.png', $config['config_json']['dropdown']['toggle_icon_url']);
		$this->assertSame('<i class="fas fa-angle-down"></i>', $config['config_json']['dropdown']['toggle_icon_custom']);
	}

	public function test_it_normalizes_dropdown_type_layout_positions_and_numeric_bounds(): void
	{
		$config = FrontendMenuDropdownConfigNormalizer::normalize(
		[
			'dropdown_type' => 'mega',
			'mega_layout' => 'featured',
			'config_json' =>
			[
				'dropdown' =>
				[
					'show_arrow' => '1',
					'margin_top' => 120,
					'arrow_size' => -5,
					'width' => 3000,
					'width_mode' => 'full',
					'align' => 'invalid',
					'arrow_position' => 'right',
				],
				'bootstrap' =>
				[
					'width' => 40,
					'align' => 'end',
				],
				'mega' =>
				[
					'columns' => 12,
					'max_items' => 60,
					'image_position' => 'right',
					'title_position' => 'center',
					'description_position' => 'invalid',
					'show_images' => 'true',
					'show_description' => '0',
					'featured_index' => -9,
				],
			],
		]);

		$this->assertSame('mega', $config['dropdown_type']);
		$this->assertSame('featured', $config['mega_layout']);
		$this->assertSame(80, $config['config_json']['dropdown']['margin_top']);
		$this->assertSame(0, $config['config_json']['dropdown']['arrow_size']);
		$this->assertSame(1440, $config['config_json']['dropdown']['width']);
		$this->assertSame('full', $config['config_json']['dropdown']['width_mode']);
		$this->assertSame('center', $config['config_json']['dropdown']['align']);
		$this->assertArrayNotHasKey('arrow_position', $config['config_json']['dropdown']);
		$this->assertSame(160, $config['config_json']['bootstrap']['width']);
		$this->assertSame('end', $config['config_json']['bootstrap']['align']);
		$this->assertSame(6, $config['config_json']['mega']['columns']);
		$this->assertSame(24, $config['config_json']['mega']['max_items']);
		$this->assertSame('right', $config['config_json']['mega']['image_position']);
		$this->assertSame('center', $config['config_json']['mega']['title_position']);
		$this->assertSame('left', $config['config_json']['mega']['description_position']);
		$this->assertTrue($config['config_json']['mega']['show_images']);
		$this->assertFalse($config['config_json']['mega']['show_description']);
		$this->assertSame(0, $config['config_json']['mega']['featured_index']);
	}

	public function test_it_falls_back_to_whitelisted_values(): void
	{
		$config = FrontendMenuDropdownConfigNormalizer::normalize(
		[
			'dropdown_type' => '<script>alert(1)</script>',
			'mega_layout' => 'unknown-layout',
			'config_json' =>
			[
				'dropdown' =>
				[
					'show_arrow' => 'not-a-bool',
					'width_mode' => 'javascript:alert(1)',
				],
				'mega' =>
				[
					'image_position' => 'javascript:alert(1)',
					'title_position' => '<b>right</b>',
				],
			],
		]);

		$this->assertSame('none', $config['dropdown_type']);
		$this->assertSame('columns', $config['mega_layout']);
		$this->assertTrue($config['config_json']['dropdown']['show_arrow']);
		$this->assertSame('container', $config['config_json']['dropdown']['width_mode']);
		$this->assertSame('left', $config['config_json']['mega']['image_position']);
		$this->assertSame('left', $config['config_json']['mega']['title_position']);
	}
}
