<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementorV24\FormDatasetNormalizer;
use Tests\TestCase;

class PageBuilderElementorV24FormDatasetNormalizerTest extends TestCase
{
    public function test_it_normalizes_nodes_and_preserves_parent_links(): void
    {
        $normalizer = app(FormDatasetNormalizer::class);

        $result = $normalizer->normalize([
            'schemaVersion' => 1,
            'nodes' => [
                ['id' => 'country-id', 'parentId' => null, 'label' => 'Indonesia', 'code' => 'ID'],
                ['id' => 'province-jb', 'parentId' => 'country-id', 'label' => 'Jawa Barat', 'value' => 'ID-JB'],
            ],
        ]);

        $this->assertSame(1, $result['schemaVersion']);
        $this->assertSame('Indonesia', $result['nodes'][0]['name']);
        $this->assertSame('ID', $result['nodes'][0]['value']);
        $this->assertSame('province-jb', $result['nodes'][1]['id']);
        $this->assertSame('country-id', $result['nodes'][1]['parentId']);
        $this->assertTrue($normalizer->validate($result)['valid']);
    }

    public function test_it_rejects_duplicate_missing_parent_and_circular_nodes(): void
    {
        $normalizer = app(FormDatasetNormalizer::class);

        $result = $normalizer->validate([
            'nodes' => [
                ['id' => 'same', 'parentId' => 'missing', 'label' => 'One'],
                ['id' => 'same', 'parentId' => 'same', 'label' => 'Two'],
            ],
        ]);

        $this->assertFalse($result['valid']);
        $codes = array_column($result['errors'], 'code');
        $this->assertContains('duplicate-id', $codes);
        $this->assertContains('missing-parent', $codes);
        $this->assertContains('cycle', $codes);
    }

    public function test_it_normalizes_product_media_metadata_and_rejects_unsafe_urls(): void
    {
        $normalizer = app(FormDatasetNormalizer::class);

        $dataset = $normalizer->normalize([
            'nodes' => [[
                'id' => 'mgs5ev',
                'label' => 'MGS5 EV',
                'meta' => [
                    'thumbnailSource' => 'url',
                    'thumbnailUrl' => 'https://cdn.example.com/mgs5ev-thumb.webp',
                    'thumbnailAlt' => ' MGS5 EV thumbnail ',
                    'imageSource' => 'ckfinder',
                    'imageUrl' => '/assets/mgs5ev.webp',
                    'imageAlt' => 'MGS5 EV',
                    'description' => ' Because Everyone Matters ',
                    'detailUrl' => 'javascript:alert(1)',
                    'detailLabel' => ' Learn More ',
                    'unknown' => 'discard me',
                ],
            ]],
        ]);

        $this->assertSame([
            'thumbnailSource' => 'url',
            'thumbnailUrl' => 'https://cdn.example.com/mgs5ev-thumb.webp',
            'thumbnailAlt' => 'MGS5 EV thumbnail',
            'imageSource' => 'ckfinder',
            'imageUrl' => '/assets/mgs5ev.webp',
            'imageAlt' => 'MGS5 EV',
            'description' => 'Because Everyone Matters',
            'detailUrl' => '',
            'detailLabel' => 'Learn More',
            'unknown' => 'discard me',
        ], $dataset['nodes'][0]['meta']);
    }

    public function test_it_rejects_duplicate_product_codes_at_the_same_depth(): void
    {
        $normalizer = app(FormDatasetNormalizer::class);

        $result = $normalizer->validate([
            'nodes' => [
                ['id' => 'model-a', 'parentId' => null, 'label' => 'Model A', 'code' => 'same-code'],
                ['id' => 'model-b', 'parentId' => null, 'label' => 'Model B', 'code' => 'SAME-CODE'],
                ['id' => 'type-a', 'parentId' => 'model-a', 'label' => 'Type A', 'code' => 'same-code'],
            ],
        ]);

        $this->assertFalse($result['valid']);
        $this->assertContains('duplicate-code', array_column($result['errors'], 'code'));
    }
}
