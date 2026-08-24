<?php

namespace Database\Seeders;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class ArticleLoadTestSeeder extends Seeder
{
    public int $count = 5000;

    public int $categoryCount = 7;

    public string $prefix = 'load-test-20260825';

    public array $result = [];

    private const CATEGORY_DEFINITIONS = [
        ['name' => 'Load Test Technology', 'code' => 'load-test-tech', 'primary' => '#5b5bd6', 'accent' => '#35c1d4'],
        ['name' => 'Load Test Business', 'code' => 'load-test-business', 'primary' => '#0f766e', 'accent' => '#34d399'],
        ['name' => 'Load Test Design', 'code' => 'load-test-design', 'primary' => '#be185d', 'accent' => '#f9a8d4'],
        ['name' => 'Load Test Science', 'code' => 'load-test-science', 'primary' => '#1d4ed8', 'accent' => '#93c5fd'],
        ['name' => 'Load Test Culture', 'code' => 'load-test-culture', 'primary' => '#a16207', 'accent' => '#fcd34d'],
        ['name' => 'Load Test Health', 'code' => 'load-test-health', 'primary' => '#c2410c', 'accent' => '#fdba74'],
        ['name' => 'Load Test Travel', 'code' => 'load-test-travel', 'primary' => '#0369a1', 'accent' => '#67e8f9'],
    ];

    public function run(): void
    {
        if ($this->count < 1 || $this->count > 10000) {
            throw new InvalidArgumentException('Article load-test count must be between 1 and 10000.');
        }

        if ($this->categoryCount < 1 || $this->categoryCount > count(self::CATEGORY_DEFINITIONS)) {
            throw new InvalidArgumentException('Article load-test category count must be between 1 and 7.');
        }

        $prefix = Str::slug($this->prefix);
        if ($prefix === '') {
            throw new InvalidArgumentException('Article load-test prefix must contain letters or numbers.');
        }

        $authorId = DB::table('accounts')->orderBy('id')->value('id');
        if (! $authorId) {
            throw new RuntimeException('Article load-test data requires at least one account.');
        }

        $definitions = array_slice(self::CATEGORY_DEFINITIONS, 0, $this->categoryCount);
        $now = CarbonImmutable::now('Asia/Jakarta')->startOfSecond();
        $categories = $this->synchronizeCategories($definitions, $now);
        $thumbnails = $this->synchronizeThumbnails($definitions, $prefix);
        $existingUris = DB::table('articles')
            ->where('uri', 'like', $prefix.'-%')
            ->pluck('uri')
            ->flip();

        $rows = [];
        $skipped = 0;
        for ($number = 1; $number <= $this->count; $number++) {
            $uri = $prefix.'-'.str_pad((string) $number, 5, '0', STR_PAD_LEFT);
            if ($existingUris->has($uri)) {
                $skipped++;
                continue;
            }

            $definition = $definitions[($number - 1) % count($definitions)];
            $createdAt = $now->subMinutes($number * 131);
            $title = sprintf('%s Performance Insight #%05d', $definition['name'], $number);

            $rows[] = [
                'uri' => $uri,
                'user_id' => $authorId,
                'category_id' => $categories[$definition['code']],
                'subcategory_id' => 0,
                'title' => $title,
                'content' => $this->articleContent($title, $definition['name'], $number),
                'tags' => implode(',', ['load-test', 'performance', $definition['code'], 'batch-20260825']),
                'thumb_s' => $thumbnails[$definition['code']]['thumb_s'],
                'thumb_l' => $thumbnails[$definition['code']]['thumb_l'],
                'visibility' => 'public',
                'password_protected' => null,
                'status' => 'publish',
                'scheduled' => 'false',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('articles')->insert($chunk);
        }

        $this->result = [
            'prefix' => $prefix,
            'created' => count($rows),
            'skipped' => $skipped,
            'categories' => count($definitions),
            'thumbnail_assets' => count($thumbnails) * 2,
        ];

        if ($this->command) {
            $this->command->info(sprintf(
                'Article load-test seed complete: %d created, %d existing skipped, %d categories, %d thumbnail assets.',
                $this->result['created'],
                $this->result['skipped'],
                $this->result['categories'],
                $this->result['thumbnail_assets'],
            ));
        }
    }

    private function synchronizeCategories(array $definitions, CarbonImmutable $now): array
    {
        $categories = [];

        foreach ($definitions as $definition) {
            $categoryId = DB::table('article_categories')->where('code', $definition['code'])->value('id');
            if (! $categoryId) {
                $categoryId = DB::table('article_categories')->insertGetId([
                    'name' => $definition['name'],
                    'code' => $definition['code'],
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $categories[$definition['code']] = $categoryId;
        }

        return $categories;
    }

    private function synchronizeThumbnails(array $definitions, string $prefix): array
    {
        $disk = Storage::disk('public');
        $directory = 'articles/load-test/'.$prefix;
        $thumbnails = [];

        foreach ($definitions as $definition) {
            $large = $directory.'/'.$definition['code'].'-large.svg';
            $small = $directory.'/'.$definition['code'].'-small.svg';

            if (! $disk->exists($large)) {
                $disk->put($large, $this->thumbnailSvg($definition, 1440, 810));
            }
            if (! $disk->exists($small)) {
                $disk->put($small, $this->thumbnailSvg($definition, 720, 405));
            }

            $thumbnails[$definition['code']] = [
                'thumb_l' => $large,
                'thumb_s' => $small,
            ];
        }

        return $thumbnails;
    }

    private function articleContent(string $title, string $categoryName, int $number): string
    {
        $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $escapedCategory = htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8');

        return <<<HTML
<p><strong>{$escapedTitle}</strong> is generated record {$number} in the Load-test dataset. It exercises Article queries, filters, pagination, rich-content rendering, and thumbnail delivery with realistic field sizes.</p>
<h2>Dataset profile</h2>
<p>This {$escapedCategory} article is public and published, uses category-specific local thumbnail assets, and has deterministic metadata so it can be seeded again without creating duplicate URIs.</p>
<p>Use this content only for local performance, pagination, browser, and regression testing.</p>
HTML;
    }

    private function thumbnailSvg(array $definition, int $width, int $height): string
    {
        $label = htmlspecialchars($definition['name'], ENT_QUOTES, 'UTF-8');
        $primary = $definition['primary'];
        $accent = $definition['accent'];
        $titleSize = max(24, (int) round($width / 28));
        $bodySize = max(16, (int) round($width / 48));
        $artPath = sprintf(
            'M0 %dL%d %dL%d %dL%d %dL%d %dV%dH0Z',
            $height,
            (int) round($width * .28),
            (int) round($height * .42),
            (int) round($width * .5),
            (int) round($height * .7),
            (int) round($width * .72),
            (int) round($height * .28),
            $width,
            (int) round($height * .62),
            $height,
        );
        $innerWidth = $width - 144;
        $innerHeight = $height - 144;
        $titleY = (int) round($height * .48);
        $bodyY = (int) round($height * .62);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$width} {$height}" role="img" aria-label="{$label} load-test thumbnail">
  <defs>
    <linearGradient id="background" x1="0" x2="1" y1="0" y2="1"><stop stop-color="{$primary}"/><stop offset="1" stop-color="{$accent}"/></linearGradient>
  </defs>
  <rect width="{$width}" height="{$height}" fill="url(#background)"/>
  <circle cx="{$width}" cy="0" r="{$height}" fill="#ffffff" fill-opacity=".12"/>
  <path d="{$artPath}" fill="#ffffff" fill-opacity=".16"/>
  <rect x="72" y="72" width="{$innerWidth}" height="{$innerHeight}" rx="36" fill="#0f172a" fill-opacity=".2"/>
  <text x="120" y="{$titleY}" fill="#ffffff" font-family="Arial, sans-serif" font-size="{$titleSize}" font-weight="700">{$label}</text>
  <text x="120" y="{$bodyY}" fill="#ffffff" fill-opacity=".86" font-family="Arial, sans-serif" font-size="{$bodySize}">Article performance dataset</text>
</svg>
SVG;
    }
}
