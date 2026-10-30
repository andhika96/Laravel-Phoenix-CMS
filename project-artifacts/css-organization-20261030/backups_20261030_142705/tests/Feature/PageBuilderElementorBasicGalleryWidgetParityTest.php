<?php

namespace Tests\Feature;

use App\Support\PageBuilderElementor\ImageRenditionResolver;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PageBuilderElementorBasicGalleryWidgetParityTest extends TestCase
{
    public function test_basic_gallery_is_registered_as_a_general_complex_widget(): void
    {
        $module = config('pagebuilder_elementor_widgets.basic_gallery');

        $this->assertIsArray($module);
        $this->assertSame('basic_gallery', $module['type']);
        $this->assertSame('Basic Gallery', $module['label']);
        $this->assertSame('general', $module['category']);
        $this->assertTrue($module['toolbox']);
        $this->assertFileExists(public_path($module['definition']));
        $this->assertFileExists(public_path($module['canvas']));
        $this->assertFileExists(public_path($module['settings']));
        $this->assertTrue(view()->exists($module['view']));
    }

    public function test_editor_state_reuses_gallery_media_helpers_without_carousel_state(): void
    {
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $definition = file_get_contents(public_path('js/pagebuilder_elementor/widgets/general/basic-gallery/definition.js'));

        foreach ([
            "type: 'basic_gallery'",
            "category: 'general'",
            'PageBuilderElementorComplexWidgetRuntime?.basic_gallery',
        ] as $marker) {
            $this->assertStringContainsString($marker, $definition);
        }

        foreach ([
            'function basicGalleryWidgetDefaults()',
            'function normalizeBasicGallerySettings(settings)',
            "basic_gallery: {",
            "if (c.type === 'basic_gallery')",
            "images: []",
            "imageResolution: 'thumbnail'",
            "columns: '4'",
            "columnsTablet: '2'",
            "columnsMobile: '1'",
            "captionType: 'caption'",
            "linkType: 'media'",
            "lightbox: 'default'",
            "orderBy: 'default'",
            "gapMode: 'default'",
            "imageBorderType: 'default'",
            '...widgetAdvancedDefaults()',
        ] as $marker) {
            $this->assertStringContainsString($marker, $app);
        }

        foreach ([
            'chooseMediaGallery,',
            'removeMediaGalleryItem,',
            'moveMediaGalleryItem,',
        ] as $sharedHelper) {
            $this->assertStringContainsString($sharedHelper, $app);
        }
    }

    public function test_settings_map_confirmed_elementor_content_style_and_advanced_controls(): void
    {
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/general/basic-gallery/Settings.vue'));

        foreach ([
            'Content', 'Style', 'Advanced', 'Basic Gallery', 'Images',
            'Image Resolution', 'Columns', 'Caption', 'Link', 'Lightbox',
            'Order By', 'Gap', 'Border Type', 'Border Width', 'Border Color',
            'Border Radius', 'Alignment', 'Text Color', 'Typography',
            'Text Shadow', 'Spacing',
        ] as $label) {
            $this->assertStringContainsString($label, $settings);
        }

        foreach ([
            "node.settings.imageResolution === 'custom'",
            "node.settings.linkType === 'media'",
            "node.settings.captionType !== 'none'",
            "node.settings.gapMode === 'custom'",
            "node.settings.imageBorderType",
            "editor.chooseMediaGallery(node.settings, 'images')",
            "editor.removeMediaGalleryItem(node.settings, 'images'",
            "editor.moveMediaGalleryItem(node.settings, 'images'",
			':show-display-conditions="false"',
			':show-cache-settings="false"',
        ] as $conditional) {
            $this->assertStringContainsString($conditional, $settings);
        }

        foreach ([
            'Slides to Show', 'Slides to Scroll', 'Navigation', 'Autoplay',
            'Infinite Loop', 'Animation Speed', 'Pagination',
        ] as $carouselOnlyLabel) {
            $this->assertStringNotContainsString($carouselOnlyLabel, $settings);
        }
    }

    public function test_settings_keep_the_gallery_picker_actions_compact_and_keyboard_addressable(): void
    {
        $settings = file_get_contents(public_path('js/pagebuilder_elementor/widgets/general/basic-gallery/Settings.vue'));

        foreach ([
            'pb-basic-gallery-images-control',
            'aria-label="Add images"',
            'aria-label="Move image up"',
            'aria-label="Move image down"',
            'aria-label="Remove image"',
            '.pb-basic-gallery-picker__item:hover .pb-basic-gallery-picker__actions',
            '.pb-basic-gallery-picker__item:focus-within .pb-basic-gallery-picker__actions',
        ] as $marker) {
            $this->assertStringContainsString($marker, $settings);
        }
    }

    public function test_canvas_and_frontend_render_a_responsive_static_grid(): void
    {
        $canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/general/basic-gallery/Canvas.vue'));
        $runtime = file_get_contents(public_path('js/pagebuilder_elementor/frontend-runtime.js'));
        $frontendCss = file_get_contents(public_path('assets/css/frontend_elementor.css'));

        foreach ([
            "name: 'GeneralBasicGallery'",
            'pb-basic-gallery__grid',
            'pb-basic-gallery__item',
            'pb-basic-gallery__caption',
            'grid-template-columns',
            'resolvedColumns',
            'resolvedImages',
            'responsiveValue',
            'Add images to your gallery',
        ] as $marker) {
            $this->assertStringContainsString($marker, $canvas);
        }

        foreach ([
            'function bindBasicGallery(root)',
            '[data-basic-gallery]',
            '[data-basic-gallery-lightbox]',
            'openImageLightbox',
        ] as $marker) {
            $this->assertStringContainsString($marker, $runtime);
        }

        foreach ([
            '.pb-basic-gallery',
            '.pb-basic-gallery__grid',
            '.pb-basic-gallery__item',
            '.pb-basic-gallery__caption',
        ] as $selector) {
            $this->assertStringContainsString($selector, $frontendCss);
        }
    }

    public function test_image_resolution_keeps_intrinsic_rendition_dimensions_visible_in_canvas_and_frontend(): void
    {
        $canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/general/basic-gallery/Canvas.vue'));
        $frontendCss = file_get_contents(public_path('assets/css/frontend_elementor.css'));

        $expectedImageRule = '.pb-basic-gallery__item img{display:block;width:auto;height:auto;max-width:100%;margin-inline:auto}';

        $this->assertStringContainsString($expectedImageRule, $canvas);
        $this->assertStringContainsString($expectedImageRule, $frontendCss);
    }

    public function test_frontend_renderer_outputs_safe_gallery_markup_and_metadata(): void
    {
        $html = view('pagebuilder_elementor.partials.render_basic_gallery', [
            'node' => [
                'id' => 'gallery-parity',
                'type' => 'basic_gallery',
                'settings' => [
                    'images' => [
                        ['id' => 'safe', 'url' => '/storage/gallery/one.jpg', 'alt' => 'Safe image', 'caption' => 'First caption'],
                        ['id' => 'safe-two', 'url' => '/storage/gallery/two.jpg', 'alt' => 'Second image', 'caption' => 'Second caption'],
                        ['id' => 'unsafe', 'url' => 'javascript:alert(1)', 'alt' => 'Unsafe image'],
                    ],
                    'columns' => '4',
                    'columnsTablet' => '2',
                    'columnsMobile' => '1',
                    'captionType' => 'caption',
                    'linkType' => 'media',
                    'lightbox' => 'yes',
                    'cssId' => 'featured-gallery',
                    'cssClass' => 'marketing-gallery',
                ],
            ],
        ])->render();

        $compact = preg_replace('/\s+/', ' ', $html);
        $this->assertStringContainsString('id="featured-gallery"', $compact);
        $this->assertStringContainsString('data-basic-gallery', $compact);
        $this->assertStringContainsString('pb-basic-gallery__grid', $compact);
        $this->assertStringContainsString('pb-basic-gallery__item', $compact);
        $this->assertStringContainsString('pb-basic-gallery__caption', $compact);
        $this->assertStringContainsString('data-basic-gallery-lightbox', $compact);
        $this->assertStringContainsString('First caption', $compact);
        $this->assertStringNotContainsString('javascript:', $compact);
        $this->assertSame(2, substr_count($compact, 'pb-basic-gallery__item'));
        $this->assertStringContainsString('--pb-basic-gallery-columns:4', $compact);
        $this->assertStringContainsString('--pb-basic-gallery-columns-tablet:2', $compact);
        $this->assertStringContainsString('--pb-basic-gallery-columns-mobile:1', $compact);
    }

    public function test_named_image_resolution_changes_the_actual_canvas_and_frontend_source(): void
    {
        $testRoot = storage_path('framework/testing/basic-gallery-rendition-'.bin2hex(random_bytes(5)));
        $sourceRoot = $testRoot.DIRECTORY_SEPARATOR.'media';
        $outputRoot = $testRoot.DIRECTORY_SEPARATOR.'renditions';
        File::ensureDirectoryExists($sourceRoot);
        $sourceImage = imagecreatetruecolor(1200, 800);
        imagefill($sourceImage, 0, 0, imagecolorallocate($sourceImage, 32, 91, 68));
        imagepng($sourceImage, $sourceRoot.DIRECTORY_SEPARATOR.'source.png');
        imagedestroy($sourceImage);
        $this->app->instance(ImageRenditionResolver::class, new ImageRenditionResolver(
            sourceRoots: ['media' => $sourceRoot], outputRoot: $outputRoot,
            outputUrlPrefix: '/test-basic-gallery-renditions',
        ));

        try {
            foreach ([
                'thumbnail' => [150, 100], 'medium' => [300, 200], 'medium_large' => [768, 512],
                'large' => [1024, 683], '1536x1536' => [1200, 800], '2048x2048' => [1200, 800],
            ] as $size => [$expectedWidth, $expectedHeight]) {
                $response = $this->getJson(route('cms.core.pagebuilder_elementor.image_rendition', [
                    'url' => '/media/source.png', 'size' => $size,
                ]));
                $response->assertOk()->assertJsonPath('size', $size);
                $url = (string) $response->json('url');
                $this->assertMatchesRegularExpression(
                    '#^/test-basic-gallery-renditions/[a-f0-9]{40}-'.preg_quote($size, '#').'\.png$#', $url
                );
                [$actualWidth, $actualHeight] = getimagesize($outputRoot.DIRECTORY_SEPARATOR.basename($url));
                $this->assertSame($expectedWidth, $actualWidth, "Unexpected width for {$size}");
                $this->assertSame($expectedHeight, $actualHeight, "Unexpected height for {$size}");
            }

            $customResponse = $this->getJson(route('cms.core.pagebuilder_elementor.image_rendition', [
                'url' => '/media/source.png', 'size' => 'custom', 'width' => 320, 'height' => 180,
            ]));
            $customResponse->assertOk()->assertJsonPath('size', 'custom');
            $customUrl = (string) $customResponse->json('url');
            $this->assertMatchesRegularExpression(
                '#^/test-basic-gallery-renditions/[a-f0-9]{40}-custom-320x180\.png$#', $customUrl
            );
            [$customWidth, $customHeight] = getimagesize($outputRoot.DIRECTORY_SEPARATOR.basename($customUrl));
            $this->assertSame(320, $customWidth);
            $this->assertSame(180, $customHeight);

            $this->getJson(route('cms.core.pagebuilder_elementor.image_rendition', [
                'url' => '/media/source.png', 'size' => 'custom', 'width' => 150, 'height' => 5500000,
            ]))->assertUnprocessable()->assertJsonValidationErrors(['height']);

            $html = view('pagebuilder_elementor.partials.render_basic_gallery', ['node' => [
                'id' => 'gallery-resolution', 'type' => 'basic_gallery', 'settings' => [
                    'images' => [['id' => 'source', 'url' => '/media/source.png', 'alt' => 'Source']],
                    'imageResolution' => 'thumbnail', 'captionType' => 'none', 'linkType' => 'none',
                ],
            ]])->render();
            $this->assertMatchesRegularExpression(
                '#src="/test-basic-gallery-renditions/[a-f0-9]{40}-thumbnail\.png"#', $html
            );
            $canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/general/basic-gallery/Canvas.vue'));
            $this->assertStringContainsString("'settings.imageResolution'(){this.scheduleImageRenditions();}", $canvas);
            $this->assertStringContainsString('customResolutionIsValid()', $canvas);
            $this->assertStringContainsString('resolutionPreviewStyle()', $canvas);
            $this->assertStringNotContainsString('renditionTimer', $canvas);
            $this->assertStringNotContainsString('setTimeout(', $canvas);
            $this->assertStringContainsString('url:this.safeUrl(response.data?.url)||base.url', $canvas);
        } finally {
            File::deleteDirectory($testRoot);
        }
    }

    public function test_media_and_attachment_links_keep_distinct_targets_after_normalization(): void
    {
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $canvas = file_get_contents(public_path('js/pagebuilder_elementor/widgets/general/basic-gallery/Canvas.vue'));
        $this->assertStringContainsString("attachmentUrl: String(source.attachmentUrl || '')", $app);
        $this->assertStringContainsString("if(this.settings.linkType==='media')return this.safeUrl(image.url)", $canvas);
        $this->assertStringContainsString("if(this.settings.linkType==='attachment')return this.safeUrl(image.attachmentUrl||image.url)", $canvas);

        $node = ['id' => 'gallery-links', 'type' => 'basic_gallery', 'settings' => [
            'images' => [[
                'id' => 'linked-image', 'url' => '/storage/gallery/media.jpg',
                'attachmentUrl' => '/media/attachment/42', 'alt' => 'Linked image',
            ]],
            'imageResolution' => 'full', 'captionType' => 'none', 'lightbox' => 'no',
        ]];
        $node['settings']['linkType'] = 'media';
        $mediaHtml = view('pagebuilder_elementor.partials.render_basic_gallery', compact('node'))->render();
        $this->assertStringContainsString('href="/storage/gallery/media.jpg"', $mediaHtml);
        $this->assertStringNotContainsString('href="/media/attachment/42"', $mediaHtml);
        $node['settings']['linkType'] = 'attachment';
        $attachmentHtml = view('pagebuilder_elementor.partials.render_basic_gallery', compact('node'))->render();
        $this->assertStringContainsString('href="/media/attachment/42"', $attachmentHtml);
    }

    public function test_responsive_content_and_style_controls_are_rendered_on_the_frontend(): void
    {
        $html = view('pagebuilder_elementor.partials.render_basic_gallery', ['node' => [
            'id' => 'gallery-style-audit', 'type' => 'basic_gallery', 'settings' => [
                'images' => [['id' => 'styled', 'url' => '/storage/gallery/styled.jpg', 'caption' => 'Styled caption']],
                'imageResolution' => 'full',
                'columns' => '3', 'columnsTablet' => '2', 'columnsMobile' => '1',
                'captionType' => 'caption', 'linkType' => 'none',
                'gapMode' => 'custom', 'gap' => '18px', 'gapTablet' => '12px', 'gapMobile' => '6px',
                'imageBorderType' => 'solid',
                'imageBorderWidthTop' => '1px', 'imageBorderWidthRight' => '2px',
                'imageBorderWidthBottom' => '3px', 'imageBorderWidthLeft' => '4px',
                'imageBorderColor' => '#123456',
                'imageBorderRadiusTop' => '8px', 'imageBorderRadiusRight' => '9px',
                'imageBorderRadiusBottom' => '10px', 'imageBorderRadiusLeft' => '11px',
                'imageBorderRadiusTopTablet' => '6px', 'imageBorderRadiusRightTablet' => '7px',
                'imageBorderRadiusBottomTablet' => '8px', 'imageBorderRadiusLeftTablet' => '9px',
                'imageBorderRadiusTopMobile' => '2px', 'imageBorderRadiusRightMobile' => '3px',
                'imageBorderRadiusBottomMobile' => '4px', 'imageBorderRadiusLeftMobile' => '5px',
                'captionColor' => '#654321',
                'captionAlignment' => 'left', 'captionAlignmentTablet' => 'center', 'captionAlignmentMobile' => 'right',
                'captionFontSize' => '18px', 'captionFontSizeTablet' => '16px', 'captionFontSizeMobile' => '14px',
                'captionLineHeight' => '1.7em', 'captionLineHeightTablet' => '1.5em', 'captionLineHeightMobile' => '1.3em',
                'captionLetterSpacing' => '2px', 'captionLetterSpacingTablet' => '1px', 'captionLetterSpacingMobile' => '0px',
                'captionWordSpacing' => '4px', 'captionWordSpacingTablet' => '3px', 'captionWordSpacingMobile' => '2px',
                'captionSpacing' => '10px', 'captionSpacingTablet' => '8px', 'captionSpacingMobile' => '6px',
            ],
        ]])->render();
        $compact = preg_replace('/\s+/', '', $html);
        foreach ([
            '--pb-basic-gallery-columns:3', '--pb-basic-gallery-columns-tablet:2', '--pb-basic-gallery-columns-mobile:1',
            '--pb-basic-gallery-gap:18px', '--pb-basic-gallery-gap-tablet:12px', '--pb-basic-gallery-gap-mobile:6px',
            'border-style:solid', 'border-width:1px2px3px4px', 'border-color:#123456',
            'border-radius:8px9px10px11px', 'img{border-radius:6px7px8px9px}', 'img{border-radius:2px3px4px5px}',
            'color:#654321',
            'text-align:center;font-size:16px;line-height:1.5em;letter-spacing:1px;word-spacing:3px;margin-top:8px',
            'text-align:right;font-size:14px;line-height:1.3em;letter-spacing:0px;word-spacing:2px;margin-top:6px',
        ] as $expected) {
            $this->assertStringContainsString($expected, $compact);
        }
    }
}
