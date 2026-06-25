<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageBuilderElementorVideoWidgetContentParityTest extends TestCase
{
    public function test_editor_exposes_elementor_like_video_content_controls(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertIsString($appJs);
        $this->assertStringContainsString('const videoSourceOptions = [', $appJs);
        $this->assertStringContainsString("{ value: 'youtube', label: 'YouTube' }", $appJs);
        $this->assertStringContainsString("{ value: 'vimeo', label: 'Vimeo' }", $appJs);
        $this->assertStringContainsString("{ value: 'dailymotion', label: 'Dailymotion' }", $appJs);
        $this->assertStringContainsString("{ value: 'self_hosted', label: 'Self Hosted' }", $appJs);
        $this->assertStringContainsString("{ value: 'videopress', label: 'VideoPress' }", $appJs);
        $this->assertStringContainsString('<label class="pb-form-label">Start Time</label>', $appJs);
        $this->assertStringContainsString('<label class="pb-form-label">End Time</label>', $appJs);
        $this->assertStringContainsString('Suggested Videos', $appJs);
        $this->assertStringContainsString('Privacy Mode', $appJs);
        $this->assertStringContainsString('Intro Title', $appJs);
        $this->assertStringContainsString('Video Info', $appJs);
        $this->assertStringContainsString('Download Button', $appJs);
        $this->assertStringContainsString('Preload', $appJs);
        $this->assertStringContainsString('Poster', $appJs);
        $this->assertStringContainsString('Image Overlay', $appJs);
    }

    public function test_editor_uses_ckfinder_media_picker_for_hosted_video_sources(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertIsString($appJs);
        $this->assertStringContainsString('videoUsesHostedPicker(selectedNode)', $appJs);
        $this->assertStringContainsString('External URL', $appJs);
        $this->assertStringContainsString("@click=\"chooseMedia(selectedNode.settings, 'fileUrl', 'Paste video URL')\"", $appJs);
        $this->assertStringContainsString("@click=\"chooseMedia(selectedNode.settings, 'poster', 'Paste image URL')\"", $appJs);
        $this->assertStringContainsString("@click=\"chooseMedia(selectedNode.settings, 'overlayImage', 'Paste image URL')\"", $appJs);
    }

    public function test_video_widget_preview_supports_iframe_and_hosted_sources(): void
    {
        $videoVue = file_get_contents(public_path('js/pagebuilder_elementor/widgets/basic/Video.vue'));

        $this->assertIsString($videoVue);
        $this->assertStringContainsString('isIframeSource() {', $videoVue);
        $this->assertStringContainsString("return source === 'youtube' || source === 'vimeo' || source === 'dailymotion';", $videoVue);
        $this->assertStringContainsString("if (source === 'vimeo') {", $videoVue);
        $this->assertStringContainsString("if (source === 'dailymotion') {", $videoVue);
        $this->assertStringContainsString("if (source === 'youtube' || source === 'vimeo' || source === 'dailymotion' || source === 'self_hosted' || source === 'videopress') {", $videoVue);
        $this->assertStringContainsString('dismissOverlay()', $videoVue);
    }

    #[DataProvider('frontendVideoSourceProvider')]
    public function test_frontend_renderer_emits_source_specific_video_markup(
        array $node,
        array $expectedFragments
    ): void {
        $html = view('pagebuilder_elementor.partials.render_node', ['node' => $node])->render();

        foreach ($expectedFragments as $fragment) {
            $this->assertStringContainsString($fragment, $html);
        }
    }

    public static function frontendVideoSourceProvider(): array
    {
        return [
            'youtube_privacy_and_params' => [
                [
                    'id' => 'video-youtube-content',
                    'type' => 'video',
                    'settings' => [
                        'sourceType' => 'youtube',
                        'youtubeUrl' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'youtubeEmbed' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                        'startTime' => 12,
                        'endTime' => 48,
                        'autoplay' => true,
                        'mute' => true,
                        'loop' => true,
                        'playerControls' => false,
                        'captions' => true,
                        'privacyMode' => true,
                        'lazyLoad' => true,
                        'suggestedVideos' => 'current_channel',
                        'ratio' => '16/9',
                    ],
                ],
                [
                    'youtube-nocookie.com/embed/dQw4w9WgXcQ',
                    'autoplay=1',
                    'mute=1',
                    'loop=1',
                    'playlist=dQw4w9WgXcQ',
                    'controls=0',
                    'cc_load_policy=1',
                    'start=12',
                    'end=48',
                    'rel=0',
                    'loading="lazy"',
                ],
            ],
            'self_hosted_ckfinder_video' => [
                [
                    'id' => 'video-self-hosted',
                    'type' => 'video',
                    'settings' => [
                        'sourceType' => 'self_hosted',
                        'fileUrl' => 'https://cdn.example.com/videos/launch.mp4',
                        'startTime' => 5,
                        'endTime' => 19,
                        'autoplay' => true,
                        'mute' => true,
                        'loop' => true,
                        'playerControls' => false,
                        'downloadButton' => false,
                        'preload' => 'none',
                        'poster' => 'https://cdn.example.com/videos/launch-poster.jpg',
                        'imageOverlay' => true,
                        'overlayImage' => 'https://cdn.example.com/videos/launch-overlay.jpg',
                        'ratio' => '16/9',
                    ],
                ],
                [
                    'data-video-html=',
                    'launch.mp4#t=5,19',
                    'autoplay',
                    'muted',
                    'loop',
                    'preload=&amp;quot;none&amp;quot;',
                    'poster=&amp;quot;https://cdn.example.com/videos/launch-poster.jpg&amp;quot;',
                    'controlslist=&amp;quot;nodownload&amp;quot;',
                    'el-video-overlay',
                    'launch-overlay.jpg',
                ],
            ],
            'vimeo_embed' => [
                [
                    'id' => 'video-vimeo-content',
                    'type' => 'video',
                    'settings' => [
                        'sourceType' => 'vimeo',
                        'vimeoUrl' => 'https://vimeo.com/235215203',
                        'startTime' => 9,
                        'autoplay' => true,
                        'mute' => true,
                        'loop' => true,
                        'privacyMode' => true,
                        'introTitle' => false,
                        'introPortrait' => false,
                        'introByline' => false,
                        'controlsColor' => '#ff3366',
                        'ratio' => '16/9',
                    ],
                ],
                [
                    'player.vimeo.com/video/235215203',
                    'autoplay=1',
                    'muted=1',
                    'loop=1',
                    'dnt=1',
                    'title=0',
                    'portrait=0',
                    'byline=0',
                    'color=ff3366',
                    '#t=9s',
                ],
            ],
            'videopress_hosted' => [
                [
                    'id' => 'video-videopress-content',
                    'type' => 'video',
                    'settings' => [
                        'sourceType' => 'videopress',
                        'fileUrl' => 'https://cdn.example.com/videos/videopress.mp4',
                        'startTime' => 8,
                        'autoplay' => false,
                        'mute' => false,
                        'loop' => true,
                        'playerControls' => true,
                        'ratio' => '16/9',
                    ],
                ],
                [
                    '<video src="https://cdn.example.com/videos/videopress.mp4#t=8"',
                    'playsinline',
                    'controls',
                    'loop',
                ],
            ],
        ];
    }
}
