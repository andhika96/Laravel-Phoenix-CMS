<?php

namespace Tests\Feature\Article;

use App\Models\Article\ArticleTemplateSetting;
use App\Models\Awesome_Admin\Account;

class MosaicMagazinePersistenceTest extends ManageArticleTemplateTest
{
    public function test_mosaic_settings_round_trip_through_save_without_changing_other_templates(): void
    {
        $admin = Account::create(['email' => 'mosaic@example.test', 'username' => 'mosaic', 'fullname' => 'Mosaic Test']);
        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_article.templates.update'), [
            'archive_template' => 'mosaic-magazine', 'detail_template' => 'focused-reader', 'archive_per_page' => 12,
            'archive_template_options' => [
                'mosaic-magazine' => ['grid' => ['desktop' => 3], 'mosaic_magazine' => [
                    'supporting_count' => 3, 'match_grid' => true, 'proportion' => 'asymmetric',
                    'columns' => ['desktop' => 2, 'tablet' => 2, 'mobile' => 1],
                ]],
                'editorial-journal' => ['grid' => ['desktop' => 4]],
            ],
        ])->assertOk()->assertJsonPath('success', true);
        $saved = ArticleTemplateSetting::current()->fresh();
        $this->assertSame(3, data_get($saved->archive_template_options, 'mosaic-magazine.mosaic_magazine.supporting_count'));
        $this->assertTrue(data_get($saved->archive_template_options, 'mosaic-magazine.mosaic_magazine.match_grid'));
        $this->assertSame('asymmetric', data_get($saved->archive_template_options, 'mosaic-magazine.mosaic_magazine.proportion'));
        $this->assertSame(4, data_get($saved->archive_template_options, 'editorial-journal.grid.desktop'));
    }
}
