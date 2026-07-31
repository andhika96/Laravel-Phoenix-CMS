<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementor\ImageRenditionResolver;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PageBuilderElementorImageRenditionResolverTest extends TestCase
{
    private string $testRoot;

    private string $sourceRoot;

    private string $outputRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testRoot = storage_path('framework/testing/image-box-renditions-'.bin2hex(random_bytes(5)));
        $this->sourceRoot = $this->testRoot.'/source';
        $this->outputRoot = $this->testRoot.'/output';
        File::ensureDirectoryExists($this->sourceRoot);
        $this->createPng($this->sourceRoot.'/landscape.png', 400, 200);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->testRoot);

        parent::tearDown();
    }

    public function test_it_exposes_elementor_named_sizes_and_creates_deterministic_renditions(): void
    {
        $resolver = $this->resolver();

        $this->assertSame([
            'thumbnail' => 150,
            'medium' => 300,
            'medium_large' => 768,
            'large' => 1024,
            '1536x1536' => 1536,
            '2048x2048' => 2048,
            'full' => null,
        ], $resolver->sizes());

        $first = $resolver->resolve('/test-media/landscape.png', 'medium');
        $second = $resolver->resolve('/test-media/landscape.png', 'medium');

        $this->assertSame($first, $second);
        $this->assertMatchesRegularExpression('#^/test-renditions/[a-f0-9]{40}-medium\.png$#', $first);
        $generatedPath = $this->outputRoot.'/'.basename($first);
        $this->assertFileExists($generatedPath);
        $this->assertSame([300, 150], array_slice(getimagesize($generatedPath), 0, 2));
    }

    public function test_it_preserves_aspect_ratio_and_never_upscales(): void
    {
        $resolver = $this->resolver();
        $url = $resolver->resolve('/test-media/landscape.png', '2048x2048');
        $generatedPath = $this->outputRoot.'/'.basename($url);

        $this->assertFileExists($generatedPath);
        $this->assertSame([400, 200], array_slice(getimagesize($generatedPath), 0, 2));
    }

    public function test_it_fails_closed_to_original_url_for_unsafe_or_unsupported_sources(): void
    {
        $resolver = $this->resolver();
        File::put($this->sourceRoot.'/notes.txt', 'not an image');
        File::put($this->testRoot.'/outside.png', file_get_contents($this->sourceRoot.'/landscape.png'));

        $cases = [
            ['/test-media/landscape.png', 'full'],
            ['/test-media/landscape.png', 'unknown'],
            ['https://remote.test/image.png', 'medium'],
            ['//remote.test/image.png', 'medium'],
            ['/test-media/../outside.png', 'medium'],
            ['/test-media/missing.png', 'medium'],
            ['/test-media/notes.txt', 'medium'],
        ];

        foreach ($cases as [$url, $size]) {
            $this->assertSame($url, $resolver->resolve($url, $size));
        }
    }

    private function resolver(): ImageRenditionResolver
    {
        return new ImageRenditionResolver(
            sourceRoots: ['test-media' => $this->sourceRoot],
            outputRoot: $this->outputRoot,
            outputUrlPrefix: '/test-renditions',
        );
    }

    private function createPng(string $path, int $width, int $height): void
    {
        $image = imagecreatetruecolor($width, $height);
        $background = imagecolorallocate($image, 72, 99, 160);
        imagefilledrectangle($image, 0, 0, $width, $height, $background);
        imagepng($image, $path);
        imagedestroy($image);
    }
}
