<?php

namespace Tests\Unit;

use App\Support\FrontendMenuBuilder;
use PHPUnit\Framework\TestCase;

class FrontendMenuBuilderTest extends TestCase
{
	public function test_it_keeps_only_safe_icon_class_markup(): void
	{
		$this->assertSame(
			'<i class="fas fa-home text-primary"></i>',
			FrontendMenuBuilder::safeIconHtml('<i class="fas fa-home text-primary" onclick="alert(1)"></i>')
		);
	}

	public function test_it_rejects_custom_icon_markup_without_safe_class(): void
	{
		$this->assertSame('', FrontendMenuBuilder::safeIconHtml('<script>alert(1)</script>'));
		$this->assertSame('', FrontendMenuBuilder::safeIconHtml('<i onclick="alert(1)"></i>'));
	}
}
