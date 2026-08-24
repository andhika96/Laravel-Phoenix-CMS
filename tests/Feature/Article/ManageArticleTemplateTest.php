<?php

namespace Tests\Feature\Article;

use App\Models\Article\ArticleTemplateSetting;
use App\Models\Awesome_Admin\Account;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ManageArticleTemplateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('fullname')->nullable();
            $table->string('password')->nullable();
            $table->tinyInteger('status')->default(2);
            $table->timestamps();
        });
        Schema::create('language', function (Blueprint $table): void {
            $table->id();
            $table->string('lang', 12);
            $table->string('lang_from', 255);
            $table->string('lang_to', 255);
        });

        $migration = require database_path('migrations/2026_08_24_000007_create_article_template_settings_table.php');
        $migration->up();
    }

    public function test_administrator_can_save_only_allowlisted_global_article_templates(): void
    {
        $admin = Account::create(['email' => 'article-template-admin@example.test', 'username' => 'article-template-admin', 'fullname' => 'Article Template Admin']);

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_article.templates.update'), [
            'archive_template' => 'mosaic-magazine',
            'detail_template' => 'editorial-feature',
            'archive_per_page' => 18,
        ])->assertOk()->assertJsonPath('success', true);

        $settings = ArticleTemplateSetting::current();
        $this->assertSame('mosaic-magazine', $settings->archive_template);
        $this->assertSame('editorial-feature', $settings->detail_template);
        $this->assertSame(18, $settings->archive_per_page);
        $this->assertSame($admin->id, $settings->updated_by);

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_article.templates.update'), [
            'archive_template' => '../../unsafe-view',
            'detail_template' => 'focused-reader',
            'archive_per_page' => 12,
        ])->assertStatus(422);
    }
}
