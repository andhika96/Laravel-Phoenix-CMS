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
        (require database_path('migrations/2026_08_25_000009_add_template_options_to_article_template_settings_table.php'))->up();
    }

    public function test_administrator_can_save_only_allowlisted_global_article_templates(): void
    {
        $admin = Account::create(['email' => 'article-template-admin@example.test', 'username' => 'article-template-admin', 'fullname' => 'Article Template Admin']);

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_article.templates.update'), [
            'archive_template' => 'mosaic-magazine',
            'detail_template' => 'editorial-feature',
            'archive_per_page' => 18,
            'archive_template_options' => [
                'editorial-journal' => [
                    'toolbar' => ['search' => ['enabled' => true, 'position' => 'center']],
                    'grid' => ['desktop' => 4, 'tablet' => 3, 'mobile' => 2],
                ],
            ],
            'detail_template_options' => [
                'editorial-feature' => [
                    'header' => ['description' => ['enabled' => true, 'mode' => 'custom', 'text' => 'Feature copy']],
                ],
            ],
        ])->assertOk()->assertJsonPath('success', true);

        $settings = ArticleTemplateSetting::current();
        $this->assertSame('mosaic-magazine', $settings->archive_template);
        $this->assertSame('editorial-feature', $settings->detail_template);
        $this->assertSame(18, $settings->archive_per_page);
        $this->assertSame($admin->id, $settings->updated_by);
        $this->assertSame('center', data_get($settings->archive_template_options, 'editorial-journal.toolbar.search.position'));
        $this->assertSame(4, data_get($settings->archive_template_options, 'editorial-journal.grid.desktop'));
        $this->assertSame('custom', data_get($settings->detail_template_options, 'editorial-feature.header.description.mode'));
        $this->assertSame('Feature copy', data_get($settings->detail_template_options, 'editorial-feature.header.description.text'));

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_article.templates.update'), [
            'archive_template' => '../../unsafe-view',
            'detail_template' => 'focused-reader',
            'archive_per_page' => 12,
        ])->assertStatus(422);
    }

    public function test_administrator_save_persists_only_normalized_structured_style_options(): void
    {
        $admin = Account::create(['email' => 'article-template-style-admin@example.test', 'username' => 'article-template-style-admin', 'fullname' => 'Article Template Style Admin']);

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_article.templates.update'), [
            'archive_template' => 'balanced-card-grid',
            'detail_template' => 'knowledge-toc',
            'archive_per_page' => 12,
            'archive_template_options' => [
                'balanced-card-grid' => [
                    'thumbnail' => [
                        'mode' => 'asset',
                        'fit' => 'contain',
                        'background_color' => 'rgba(12, 34, 56, 0.5)',
                        'frame' => ['enabled' => true, 'border_color' => '#123456', 'border_width' => '2pt', 'radius' => '8%'],
                    ],
                    'pagination' => [
                        'show_total' => false,
                        'position' => 'center',
                        'frame' => ['enabled' => false, 'border_width' => '20%'],
                        'padding' => ['enabled' => true, 'desktop' => ['top' => '1.25rem', 'right' => '8px', 'bottom' => '4%', 'left' => '12pt']],
                    ],
                    'article_title' => ['tag' => 'h2'],
                    'shell' => ['frame' => ['enabled' => true, 'border_width' => '1em', 'radius' => '1.5rem', 'background_color' => 'hsl(210, 40%, 50%)']],
                    'custom_css' => 'body{display:none}',
                ],
            ],
            'detail_template_options' => [
                'knowledge-toc' => [
                    'shell' => ['padding' => ['enabled' => true, 'mobile' => ['top' => '1rem', 'right' => '2%', 'bottom' => '4pt', 'left' => '8px']]],
                ],
            ],
        ])->assertOk()->assertJsonPath('success', true);

        $settings = ArticleTemplateSetting::current();
        $archive = data_get($settings->archive_template_options, 'balanced-card-grid');
        $detail = data_get($settings->detail_template_options, 'knowledge-toc');

        $this->assertSame('asset', data_get($archive, 'thumbnail.mode'));
        $this->assertSame('contain', data_get($archive, 'thumbnail.fit'));
        $this->assertSame('rgba(12, 34, 56, 0.5)', data_get($archive, 'thumbnail.background_color'));
        $this->assertSame('2pt', data_get($archive, 'thumbnail.frame.border_width'));
        $this->assertFalse(data_get($archive, 'pagination.show_total'));
        $this->assertSame('center', data_get($archive, 'pagination.position'));
        $this->assertSame('1px', data_get($archive, 'pagination.frame.border_width'));
        $this->assertSame('1.25rem', data_get($archive, 'pagination.padding.desktop.top'));
        $this->assertSame('h2', data_get($archive, 'article_title.tag'));
        $this->assertSame('hsl(210, 40%, 50%)', data_get($archive, 'shell.frame.background_color'));
        $this->assertNull(data_get($archive, 'custom_css'));
        $this->assertSame('1rem', data_get($detail, 'shell.padding.mobile.top'));
        $this->assertSame('2%', data_get($detail, 'shell.padding.mobile.right'));
    }

}
