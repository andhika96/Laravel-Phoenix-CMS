<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementor\ImageRenditionResolver;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PageBuilderElementorImageRenditionResolverSameOriginUrlTest extends TestCase
{
    private string $testRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testRoot = storage_path('framework/testing/basic-gallery-same-origin-'.bin2hex(random_bytes(5)));
        File::ensureDirectoryExists($this->testRoot.'/source');
        $image = imagecreatetruecolor(400, 200);
        imagepng($image, $this->testRoot.'/source/gallery.png');
        imagedestroy($image);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->testRoot);

        parent::tearDown();
    }

    public function test_it_resolves_an_absolute_url_from_the_current_cms_origin(): void
    {
        config()->set('app.url', 'https://laravel-13-phoenix.aruna');
        $resolver = new ImageRenditionResolver(
            sourceRoots: ['storage/ckfinder/userfiles' => $this->testRoot.'/source'],
            outputRoot: $this->testRoot.'/renditions',
            outputUrlPrefix: '/test-renditions',
        );

        $url = $resolver->resolve(
            'https://laravel-13-phoenix.aruna/storage/ckfinder/userfiles/gallery.png',
            'thumbnail',
        );

        $this->assertMatchesRegularExpression('#^/test-renditions/[a-f0-9]{40}-thumbnail\.png$#', $url);
        $this->assertFileExists($this->testRoot.'/renditions/'.basename($url));
    }
}
