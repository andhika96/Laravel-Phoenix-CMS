<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23ProgressTrackerVideoPlaylistWidgetTest extends TestCase
{
    public function test_progress_tracker_is_registered_and_renders_horizontal_and_circular_markup(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.progress_tracker');

        $this->assertIsArray($module);
        $this->assertSame('Progress Tracker', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertSame('pagebuilder_elementor_v23.partials.render_pro_widget', $module['view'] ?? null);
        $this->assertFileExists(public_path($module['definition'] ?? 'missing'));

        $html = view($module['view'], [
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
    }

    public function test_video_playlist_is_registered_and_renders_safe_interactive_markup(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.video_playlist');

        $this->assertIsArray($module);
        $this->assertSame('Video Playlist', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertSame('pagebuilder_elementor_v23.partials.render_pro_widget', $module['view'] ?? null);
        $this->assertFileExists(public_path($module['definition'] ?? 'missing'));

        $html = view($module['view'], [
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
    }
}
