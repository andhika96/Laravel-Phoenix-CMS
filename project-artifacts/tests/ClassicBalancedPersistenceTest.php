<?php

namespace Tests\Feature\Article;

use App\Models\Article\ArticleTemplateSetting;
use App\Models\Awesome_Admin\Account;

class ClassicBalancedPersistenceTest extends ManageArticleTemplateTest
{
    public function test_custom_classic_and_balanced_settings_survive_save_together(): void
    {
        $admin = Account::create(['email' => 'classic-balanced@example.test', 'username' => 'classic-balanced', 'fullname' => 'Layout Test']);
        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_article.templates.update'), [
            'archive_template' => 'mosaic-classic', 'detail_template' => 'focused-reader', 'archive_per_page' => 12,
            'archive_template_options' => [
                'mosaic-classic' => ['mosaic_classic' => ['side_count' => 0, 'side_position' => 'left', 'lead_height' => ['mobile' => '18rem'], 'show_excerpt' => false]],
                'balanced-card-grid' => ['balanced_grid' => ['card_style' => 'elevated', 'text_align' => 'center', 'gap' => ['desktop' => '3rem'], 'show_read_more' => false]],
                'mosaic-magazine' => ['mosaic_magazine' => ['supporting_count' => 4]],
            ],
        ])->assertOk()->assertJsonPath('success', true);
        $saved = ArticleTemplateSetting::current()->fresh()->archive_template_options;
        $this->assertSame(0, data_get($saved, 'mosaic-classic.mosaic_classic.side_count'));
        $this->assertSame('18rem', data_get($saved, 'mosaic-classic.mosaic_classic.lead_height.mobile'));
        $this->assertFalse(data_get($saved, 'mosaic-classic.mosaic_classic.show_excerpt'));
        $this->assertSame('elevated', data_get($saved, 'balanced-card-grid.balanced_grid.card_style'));
        $this->assertSame('3rem', data_get($saved, 'balanced-card-grid.balanced_grid.gap.desktop'));
        $this->assertFalse(data_get($saved, 'balanced-card-grid.balanced_grid.show_read_more'));
        $this->assertSame(4, data_get($saved, 'mosaic-magazine.mosaic_magazine.supporting_count'));
    }
}
