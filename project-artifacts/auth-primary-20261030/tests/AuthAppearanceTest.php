<?php

use App\Support\AuthAppearance;
use PHPUnit\Framework\TestCase;

final class AuthAppearanceTest extends TestCase
{
    public function test_light_primary_uses_dark_text_and_generated_shades(): void
    {
        $palette=AuthAppearance::palette('#ffffff');
        $this->assertSame('#FFFFFF',$palette['primary']);
        $this->assertSame('#000000',$palette['text']);
        $this->assertSame('#E6E6E6',$palette['hover']);
        $this->assertSame('#CCCCCC',$palette['active']);
    }

    public function test_dark_primary_uses_light_text(): void
    {
        $palette=AuthAppearance::palette('#000000');
        $this->assertSame('#FFFFFF',$palette['text']);
        $this->assertSame('#FFFFFF',$palette['hover_text']);
        $this->assertSame('#FFFFFF',$palette['active_text']);
    }

    public function test_current_green_gets_readable_text_and_solid_generated_states(): void
    {
        $palette=AuthAppearance::palette('#1FA675');
        $this->assertSame('#000000',$palette['text']);
        $this->assertSame('#1C9569',$palette['hover']);
        $this->assertSame('#19855E',$palette['active']);
        $this->assertSame('31, 166, 117',$palette['rgb']);
    }

    public function test_invalid_stored_color_cannot_escape_into_style(): void
    {
        $this->assertSame(AuthAppearance::palette('#1FA675'),AuthAppearance::palette('red; background:url(https://example.com)'));
        $this->assertSame(AuthAppearance::palette('#1FA675'),AuthAppearance::palette(null));
        $this->assertSame(AuthAppearance::palette('#1FA675'),AuthAppearance::palette('#12345678'));
    }
}
