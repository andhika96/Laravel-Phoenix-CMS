<?php
namespace Tests\Feature\Article;

use App\Models\Article\Article;
use App\Models\Awesome_Admin\Account;
use App\Support\Article\ArticleTemplateOptions;
use App\Support\Article\ArticleTemplatePreviewFixture;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ClassicLetsreadTest extends TestCase
{
    public function test_new_layout_defaults_and_validation_are_scoped_to_classic(): void
    {
        $normalizer = app(ArticleTemplateOptions::class);
        $options = $normalizer->archive('mosaic-classic')['mosaic_classic'];
        $this->assertSame('headline-first', $options['lead_layout'] ?? null);
        $this->assertSame('right', $options['side_image_position']);
        $this->assertSame(2, $options['highlight_count']);
        $this->assertTrue($options['show_reading_time']);
        $this->assertTrue($options['show_author']);
        $custom = $normalizer->archive('mosaic-classic', ['mosaic_classic' => [
            'lead_layout' => 'bad', 'side_image_position' => 'bad', 'highlight_count' => 999,
            'highlight_columns' => ['desktop' => 8, 'tablet' => 0, 'mobile' => 4],
            'highlight_height' => ['desktop' => 'url(unsafe)'], 'latest_title' => '<b>Recent stories</b>',
        ]]);
        $this->assertSame(24, $custom['mosaic_classic']['highlight_count']);
        $this->assertSame(['desktop' => 4, 'tablet' => 1, 'mobile' => 2], $custom['mosaic_classic']['highlight_columns']);
        $this->assertSame('16rem', $custom['mosaic_classic']['highlight_height']['desktop']);
        $this->assertSame('Recent stories', $custom['mosaic_classic']['latest_title']);
        $this->assertSame($custom, $normalizer->archive('mosaic-classic', $custom));
        $this->assertArrayNotHasKey('highlight_count', $normalizer->archive('balanced-card-grid')['balanced_grid']);
    }

    public function test_all_regions_partition_posts_once_even_with_zero_or_excess_counts(): void
    {
        foreach ([[3,2],[0,2],[3,0],[0,0],[24,24],[1,24]] as [$side, $highlights]) {
            $xpath = $this->xpath($this->render(12, ['side_count'=>$side, 'highlight_count'=>$highlights]));
            $this->assertSame(12, $xpath->query('//article')->length);
            $this->assertSame(min(11, $side), $xpath->query('//*[@data-classic-region="side"]/article')->length);
            $this->assertSame(min(max(0,11-$side),$highlights), $xpath->query('//*[@data-classic-region="highlights"]/article')->length);
            $this->assertSame(max(0,11-$side-$highlights), $xpath->query('//*[@data-classic-region="grid"]/article')->length);
            $titles=[];
            foreach($xpath->query('//article//*[contains(@class,"article-title-clamp")]/a') as $title) $titles[]=$title->textContent;
            $this->assertSame(array_map(fn($id)=>"Story $id",range(1,12)),$titles);
        }
    }

    public function test_headline_first_metadata_highlights_and_custom_title_reach_render(): void
    {
        $html = $this->render(12, ['latest_title'=>'Latest from our journal']);
        $xpath = $this->xpath($html);
        $lead=$xpath->query('//*[@data-classic-region="lead"]')->item(0);
        $this->assertStringContainsString('article-classic-lead__body', $lead->firstElementChild->getAttribute('class'));
        $this->assertStringContainsString('Latest from our journal', $html);
        $this->assertStringContainsString('Aruna Editorial', $html);
        $this->assertStringContainsString('min read', $html);
        $this->assertStringContainsString('--classic-highlight-height-desktop:16rem', $html);
        $this->assertSame(2, $xpath->query('//*[@data-classic-region="highlights"]/article')->length);
        $without = $this->render(2, ['lead_layout'=>'image-first','show_author'=>false,'show_reading_time'=>false]);
        $this->assertStringNotContainsString('Aruna Editorial', $without);
        $this->assertStringNotContainsString('min read', $without);
        $this->assertStringNotContainsString('data-classic-region="highlights"', $this->render(1));
    }

    public function test_filtered_results_keep_all_posts_in_the_regular_grid(): void
    {
        $html=$this->render(4,[],['search'=>'story']);
        $xpath=$this->xpath($html);
        $this->assertSame(0,$xpath->query('//*[@data-classic-region="lead" or @data-classic-region="side" or @data-classic-region="highlights"]')->length);
        $this->assertSame(4,$xpath->query('//*[@data-classic-region="grid"]/article')->length);
        $this->assertStringContainsString('No articles found', $this->render(0));
    }

    public function test_expanded_preview_has_twelve_unique_sample_stories_and_keeps_other_defaults(): void
    {
        $fixture=app(ArticleTemplatePreviewFixture::class);
        $this->assertCount(6,$fixture->archivePaginator()->items());
        $expanded=$fixture->archivePaginator(1,'/preview',12);
        $this->assertCount(12,$expanded->items());
        $this->assertCount(12,$expanded->getCollection()->unique('uri'));
        $this->assertTrue($expanded->getCollection()->every(fn($article)=>!$article->exists));
    }

    private function render(int $count, array $custom=[], array $query=[]): string
    {
        $this->app->instance('request', Request::create('/article','GET',$query));
        $items=collect();
        for($i=1;$i<=$count;$i++) {
            $article=new Article(['uri'=>"story-$i",'title'=>"Story $i",'content'=>'<p>Readable sample article.</p>','created_at'=>now()]);
            $article->setRelation('category',null);
            $article->setRelation('author',new Account(['fullname'=>'Aruna Editorial']));
            $items->push($article);
        }
        return view('article.templates.archive.mosaic-classic',[
            'articles'=>new LengthAwarePaginator($items,$count,12,1,['path'=>route('cms.core.article')]),
            'templateOptions'=>app(ArticleTemplateOptions::class)->archive('mosaic-classic',['mosaic_classic'=>$custom]),
            'articleCategories'=>collect(), 'templateSettings'=>null,
        ])->render();
    }
    private function xpath(string $html): \DOMXPath { $dom=new \DOMDocument(); @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$html); return new \DOMXPath($dom); }
}
