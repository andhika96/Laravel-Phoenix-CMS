<?php

namespace Tests\Feature;

use App\Models\Article\Article;
use App\Models\Awesome_Admin\Account;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class ManageArticleListRevampTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        Schema::create('accounts', function (Blueprint $table) {
            $table->id(); $table->string('fullname')->nullable(); $table->timestamps();
        });
        Schema::create('language', function (Blueprint $table) {
            $table->id(); $table->string('lang'); $table->string('lang_from'); $table->string('lang_to');
        });
        Schema::create('article_status', function (Blueprint $table) {
            $table->id(); $table->string('code_name'); $table->string('name'); $table->string('class_name'); $table->integer('is_active')->default(0);
        });
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->timestamps();
        });
        Schema::create('articles', function (Blueprint $table) {
            $table->id(); $table->string('uri'); $table->unsignedBigInteger('user_id')->nullable(); $table->unsignedBigInteger('category_id')->nullable();
            $table->string('title'); $table->text('content'); $table->string('status')->nullable(); $table->string('scheduled')->default('false'); $table->timestamps();
        });
        DB::table('article_status')->insert([
            ['id'=>1,'code_name'=>'publish','name'=>'Publish','class_name'=>'badge text-bg-success','is_active'=>0],
            ['id'=>2,'code_name'=>'draft','name'=>'Draft','class_name'=>'badge text-bg-secondary','is_active'=>0],
            ['id'=>3,'code_name'=>'pending','name'=>'Pending','class_name'=>'badge text-bg-info','is_active'=>0],
        ]);
        DB::table('article_categories')->insert([['id'=>1,'name'=>'News'],['id'=>2,'name'=>'Design']]);
        $admin=Account::create(['fullname'=>'Article Admin']);
        $this->withoutMiddleware()->actingAs($admin,'web');
        for ($i=1;$i<=18;$i++) {
            Article::create(['uri'=>'sample-'.$i,'user_id'=>$admin->id,'category_id'=>$i<=12?1:2,'title'=>sprintf('Article %02d',$i),'content'=>'<p>Safe article summary</p>','status'=>$i%2?'publish':'draft','scheduled'=>$i%3===0?'true':'false','created_at'=>sprintf('2026-10-%02d 09:00:00',$i)]);
        }
    }

    public function test_filters_sort_page_size_and_status_counts_are_computed_on_server(): void
    {
        $result=$this->getJson(route('cms.core.manage_article.listdata',['filter_by_category'=>1,'filter_by_scheduled'=>'true','filter_by_status'=>'draft','sort'=>'title_desc','per_page'=>5]));
        $result->assertOk()->assertJsonPath('total',2)->assertJsonPath('limit',5)->assertJsonPath('status_counts.all',4)
            ->assertJsonPath('status_counts.publish',2)->assertJsonPath('status_counts.draft',2)->assertJsonPath('data.0.title_full','Article 12')
            ->assertJsonPath('data.0.category_name','News')->assertJsonPath('data.0.status_code','draft')->assertJsonPath('data.0.status_name','Draft');
        $this->assertStringNotContainsString('<p>', $result->json('data.0.excerpt'));
        $this->getJson(route('cms.core.manage_article.listdata',['per_page'=>5,'page'=>2,'sort'=>'oldest']))
            ->assertOk()->assertJsonPath('current_page',2)->assertJsonPath('data.0.title_full','Article 06')->assertJsonPath('total',18);
    }

    public function test_search_preserves_other_filters_and_empty_page_is_valid(): void
    {
        $this->getJson(route('cms.core.manage_article.listdata',['search'=>'Article 1','filter_by_category'=>1,'sort'=>'title_asc']))
            ->assertOk()->assertJsonPath('total',3)->assertJsonPath('data.0.title_full','Article 10');
        $this->getJson(route('cms.core.manage_article.listdata',['search'=>'No matching article']))
            ->assertOk()->assertJsonPath('total',0)->assertJsonCount(0,'data')->assertJsonPath('status_counts.all',0);
    }

    public function test_invalid_sort_and_unbounded_page_size_are_rejected(): void
    {
        $this->getJson(route('cms.core.manage_article.listdata',['sort'=>'title;delete','per_page'=>50000]))
            ->assertUnprocessable()->assertJsonValidationErrors(['sort','per_page']);
    }

    public function test_legacy_status_ids_are_displayed_and_filtered_consistently(): void
    {
        Article::findOrFail(1)->update(['status'=>'1']);
        $this->getJson(route('cms.core.manage_article.listdata',['filter_by_status'=>'publish','search'=>'Article 01']))
            ->assertOk()->assertJsonPath('total',1)->assertJsonPath('data.0.status_code','publish')->assertJsonPath('data.0.status_name','Publish');
    }

    public function test_bulk_status_updates_only_selected_articles_and_validates_all_ids_first(): void
    {
        $this->postJson(route('cms.core.manage_article.bulk_update'),['mode'=>'bulk_update','changeStatus'=>'pending','getSelected'=>[1,2]])
            ->assertOk()->assertJsonPath('success',true);
        $this->assertSame('pending',Article::findOrFail(1)->status);
        $this->assertSame('pending',Article::findOrFail(2)->status);
        $this->assertSame('publish',Article::findOrFail(3)->status);
        $this->postJson(route('cms.core.manage_article.bulk_update'),['mode'=>'bulk_update','changeStatus'=>'draft','getSelected'=>[1,999]])
            ->assertUnprocessable();
        $this->assertSame('pending',Article::findOrFail(1)->status);
        $this->postJson(route('cms.core.manage_article.bulk_update'),['mode'=>'bulk_update','changeStatus'=>'pending','getSelected'=>[]])
            ->assertUnprocessable();
    }

    public function test_bulk_failure_rolls_back_changes_to_every_selected_article(): void
    {
        $originalDispatcher = Article::getEventDispatcher();
        $dispatcher = clone $originalDispatcher;
        Article::setEventDispatcher($dispatcher);
        $dispatcher->listen('eloquent.updating: '.Article::class, function (Article $article): void {
            if ($article->id === 2) throw new \RuntimeException('Simulated second update failure');
        });
        try {
            $this->postJson(route('cms.core.manage_article.bulk_update'), ['mode'=>'bulk_update','changeStatus'=>'pending','getSelected'=>[1,2]])
                ->assertServerError();
        } finally {
            Article::setEventDispatcher($originalDispatcher);
        }
        $this->assertSame('publish', Article::findOrFail(1)->status);
        $this->assertSame('draft', Article::findOrFail(2)->status);
    }
}
