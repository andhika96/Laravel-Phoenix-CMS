<?php

namespace Tests\Unit;

use App\Support\FrontendHeaderNavigationConfigNormalizer;
use PHPUnit\Framework\TestCase;

class FrontendHeaderNavigationConfigNormalizerTest extends TestCase
{
	public function test_it_builds_safe_header_navigation_defaults(): void
	{
		$config = FrontendHeaderNavigationConfigNormalizer::defaultConfig();

		$this->assertSame('/awesome_admin/header-navigation/preview-data', $config['source']);
		$this->assertSame('left', $config['layout']['logo_position']);
		$this->assertSame('stay', $config['behavior']['position']);
		$this->assertSame('76px', $config['sizing']['height']);
		$this->assertSame('18px', $config['sizing']['header_radius']['top']);
		$this->assertFalse($config['effects']['link_shadow']['enabled']);
	}

	public function test_it_whitelists_layout_behavior_colors_and_dimensions(): void
	{
		$config = FrontendHeaderNavigationConfigNormalizer::normalize(
		[
			'source' => 'javascript:alert(1)',
			'colors' =>
			[
				'header_background' => 'red;display:none',
				'link_hover' => 'rgba(10, 20, 30, .5)'
			],
			'layout' =>
			[
				'logo_position' => '<script>',
				'menu_position' => 'right',
				'container' => 'fluid'
			],
			'behavior' =>
			[
				'position' => 'absolute'
			],
			'sizing' =>
			[
				'height' => '9999px'
			]
		]);

		$this->assertSame('/awesome_admin/header-navigation/preview-data', $config['source']);
		$this->assertSame('#ffffff', $config['colors']['header_background']);
		$this->assertSame('rgba(10, 20, 30, .5)', $config['colors']['link_hover']);
		$this->assertSame('left', $config['layout']['logo_position']);
		$this->assertSame('right', $config['layout']['menu_position']);
		$this->assertSame('fluid', $config['layout']['container']);
		$this->assertSame('stay', $config['behavior']['position']);
		$this->assertSame('240px', $config['sizing']['height']);
	}

	public function test_it_uses_responsive_overrides_for_frontend_css_variables(): void
	{
		$config = FrontendHeaderNavigationConfigNormalizer::normalize(
		[
			'sizing' =>
			[
				'header_padding' =>
				[
					'responsive' =>
					[
						'all' => ['top' => '10px', 'right' => '24px', 'bottom' => '10px', 'left' => '24px', 'unit' => 'px'],
						'mobile' => ['top' => '8px', 'right' => '12px', 'bottom' => '8px', 'left' => '12px', 'unit' => 'px']
					]
				]
			]
		]);

		$desktop = FrontendHeaderNavigationConfigNormalizer::cssVariables($config, 'desktop');
		$mobile = FrontendHeaderNavigationConfigNormalizer::cssVariables($config, 'mobile');

		$this->assertSame('10px 24px 10px 24px', $desktop['--ph-fe-header-padding']);
		$this->assertSame('8px 12px 8px 12px', $mobile['--ph-fe-header-padding']);
	}
}
