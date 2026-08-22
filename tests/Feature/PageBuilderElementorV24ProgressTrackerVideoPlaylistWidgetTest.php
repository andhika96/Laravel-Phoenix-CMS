<?php

namespace Tests\Feature;

use Tests\Concerns\InteractsWithPageBuilderElementorV24Modules;
use Tests\TestCase;

class PageBuilderElementorV24ProgressTrackerVideoPlaylistWidgetTest extends TestCase
{
    use InteractsWithPageBuilderElementorV24Modules;
    public function test_progress_tracker_is_registered_and_renders_horizontal_and_circular_markup(): void
    {
        $module = $this->pageBuilderV24Module('progress_tracker');

        $this->assertIsArray($module);
        $this->assertSame('Progress Tracker', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertFileExists($module['assets']['view'] ?? 'missing');
        $this->assertFileExists($module['assets']['definition'] ?? 'missing');

        $html = $this->pageBuilderV24ModuleView($module, [
            'node' => [
                'id' => 'progress-tracker-test',
                'type' => 'progress_tracker',
                'settings' => [
                    'trackerType' => 'circular',
                    'relativeTo' => 'selector',
                    'selector' => '.article-content',
                    'showPercentage' => true,
                    'indicatorColor' => '#6979f8',
                    'backgroundColor' => '#e4e7ec',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('data-pro-widget="progress_tracker"', $html);
        $this->assertStringContainsString('data-progress-tracker', $html);
        $this->assertStringContainsString('pb-pro-progress-tracker--circular', $html);
        $this->assertStringContainsString('<svg', $html);
        $this->assertStringContainsString('article-content', $html);

        $horizontalHtml = $this->pageBuilderV24ModuleView($module, [
            'node' => [
                'id' => 'progress-tracker-alignment-test',
                'type' => 'progress_tracker',
                'settings' => [
                    'trackerType' => 'horizontal',
                    'direction' => 'center',
                    'indicatorAlignment' => 'right',
                    'showPercentage' => true,
                ],
            ],
        ])->render();
        $this->assertStringContainsString('pb-pro-progress-tracker--horizontal align-center', $horizontalHtml);
        $this->assertStringContainsString('margin-left:auto;margin-right:0', $horizontalHtml);
    }

    public function test_video_playlist_is_registered_and_renders_safe_interactive_markup(): void
    {
        $module = $this->pageBuilderV24Module('video_playlist');

        $this->assertIsArray($module);
        $this->assertSame('Video Playlist', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertFileExists($module['assets']['view'] ?? 'missing');
        $this->assertFileExists($module['assets']['definition'] ?? 'missing');

        $html = $this->pageBuilderV24ModuleView($module, [
            'node' => [
                'id' => 'video-playlist-test',
                'type' => 'video_playlist',
                'settings' => [
                    'playlistName' => 'Course Playlist',
                    'playlistTitleTag' => 'h3',
                    'items' => [[
                        'id' => 'video-1',
                        'type' => 'youtube',
                        'link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'title' => '<script>alert(1)</script> Getting Started',
                        'titleTag' => 'h4',
                        'duration' => '0:16',
                        'thumbnailUrl' => '',
                        'showContentTabs' => true,
                        'contentTabOneTitle' => 'Overview',
                        'contentTabOneContent' => 'Course overview',
                    ], [
                        'id' => 'section-1',
                        'type' => 'section',
                        'title' => 'Advanced lessons',
                        'sectionContent' => 'Advanced lessons',
                    ]],
                    'showVideoCount' => true,
                    'showDuration' => true,
                    'showThumbnails' => true,
                    'indicateWatched' => true,
                    'videoPosition' => 'right',
                    'videoHeight' => '360px',
                    'dropdownAlignment' => 'right',
                    'dropdownIconSource' => 'library',
                    'dropdownIconClass' => 'fas fa-angle-down',
                    'dropdownIconColor' => '#112233',
                    'dropdownIconColorHover' => '#223344',
                    'dropdownIconColorActive' => '#334455',
                    'iconBackground' => '#ddeeff',
                    'iconShadow' => '0 2px 4px rgba(0,0,0,.2)',
                    'sectionBackgroundType' => 'gradient',
                    'sectionGradientColorOne' => '#ffeedd',
                    'sectionGradientColorTwo' => '#ddeeff',
                    'sectionGradientAngle' => 120,
                    'sectionBorderType' => 'dashed',
                    'sectionBoxShadow' => '0 3px 6px rgba(0,0,0,.2)',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('data-pro-widget="video_playlist"', $html);
        $this->assertStringContainsString('data-video-playlist', $html);
        $this->assertStringContainsString('pb-pro-video-playlist', $html);
        $this->assertStringContainsString('data-playlist-index="0"', $html);
        $this->assertStringContainsString('data-playlist-player', $html);
        $this->assertStringContainsString('youtube.com/embed/dQw4w9WgXcQ', $html);
        $this->assertStringContainsString('Course Playlist', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('Advanced lessons', $html);
        $this->assertStringContainsString('data-playlist-dropdown-toggle', $html);
        $this->assertStringContainsString('aria-expanded="true"', $html);
        $this->assertStringContainsString('justify-content:flex-end', $html);
        $this->assertStringContainsString('fas fa-angle-down', $html);
        $this->assertStringContainsString('color:#112233', $html);
        $this->assertStringContainsString('color:#223344', $html);
        $this->assertStringContainsString('color:#334455', $html);
        $this->assertStringContainsString('linear-gradient(120deg, #ffeedd, #ddeeff)', $html);
        $this->assertStringContainsString('border-style:dashed', $html);
        $this->assertStringContainsString('background:#ddeeff', $html);
        $this->assertStringContainsString('box-shadow:0 2px 4px rgba(0,0,0,.2)', $html);
    }
}
