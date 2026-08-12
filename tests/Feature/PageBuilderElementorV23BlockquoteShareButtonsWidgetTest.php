<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23BlockquoteShareButtonsWidgetTest extends TestCase
{
    public function test_blockquote_is_registered_and_renders_safe_tweet_markup(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.blockquote');

        $this->assertSame('Blockquote', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertFileExists(public_path($module['definition'] ?? 'missing'));

        $html = view($module['view'], [
            'node' => [
                'id' => 'blockquote-test',
                'type' => 'blockquote',
                'settings' => [
                    'skin' => 'quotation',
                    'alignment' => 'center',
                    'content' => '<script>alert(1)</script>',
                    'author' => 'Ada Lovelace',
                    'tweetButton' => true,
                    'tweetView' => 'icon_text',
                    'tweetSkin' => 'bubble',
                    'tweetLabel' => 'Tweet',
                    'tweetTarget' => 'custom',
                    'tweetUrl' => 'https://example.com/article',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('data-pro-widget="blockquote"', $html);
        $this->assertStringContainsString('data-blockquote', $html);
        $this->assertStringContainsString('data-blockquote-tweet', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('https://twitter.com/intent/tweet', $html);
    }

    public function test_share_buttons_are_registered_and_render_allow_listed_network_actions(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.share_buttons');

        $this->assertSame('Share Buttons', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertFileExists(public_path($module['definition'] ?? 'missing'));

        $html = view($module['view'], [
            'node' => [
                'id' => 'share-buttons-test',
                'type' => 'share_buttons',
                'settings' => [
                    'items' => [
                        ['id' => 'x', 'network' => 'x', 'customLabel' => 'X'],
                        ['id' => 'threads', 'network' => 'threads', 'customLabel' => 'Threads'],
                        ['id' => 'copy', 'network' => 'copy', 'customLabel' => 'Copy Link'],
                        ['id' => 'bad', 'network' => 'javascript', 'customLabel' => '<script>'],
                    ],
                    'view' => 'icon_text',
                    'showLabel' => true,
                    'skin' => 'flat',
                    'shape' => 'rounded',
                    'columns' => '3',
                    'alignment' => 'center',
                    'targetUrl' => 'custom',
                    'customUrl' => 'https://example.com/article',
                    'colorMode' => 'custom',
                    'primaryColor' => '#111827',
                    'secondaryColor' => '#ffffff',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('data-pro-widget="share_buttons"', $html);
        $this->assertStringContainsString('data-share-buttons', $html);
        $this->assertStringContainsString('data-share-network="x"', $html);
        $this->assertStringContainsString('data-share-network="threads"', $html);
        $this->assertStringContainsString('data-share-action="copy"', $html);
        $this->assertStringNotContainsString('data-share-network="javascript"', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('columns-3', $html);
    }
}
