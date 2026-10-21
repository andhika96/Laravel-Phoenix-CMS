<?php

namespace Tests\Feature\Article;

use App\Http\Controllers\Web\Manage_Article\ManageArticleTemplateController;
use App\Models\Article\Article;
use Illuminate\Http\Request;

class FrontendLinkTest extends ArticleTemplatePreviewControllerTest
{
    public function test_frontend_link_only_selects_published_public_articles(): void
    {
        $controller = app(ManageArticleTemplateController::class);
        $request = Request::create('/manage_article/templates');
        $data = $controller->index($request)->getData();
        $this->assertNull($data['frontendDetailUrl']);
        $this->assertSame(route('cms.core.article'), $data['frontendArchiveUrl']);
        foreach ([['private', 'publish', now()], ['password_protected', 'publish', now()], ['public', 'draft', now()], ['public', 'publish', now()->addDay()]] as $index => [$visibility, $status, $date]) {
            Article::create(['uri' => 'excluded-'.$index, 'title' => 'Excluded', 'content' => '', 'visibility' => $visibility, 'status' => $status, 'created_at' => $date]);
        }
        $this->assertNull($controller->index($request)->getData()['frontendDetailUrl']);
        Article::create(['uri' => 'public-reader', 'title' => 'Public', 'content' => '', 'visibility' => 'public', 'status' => 'publish', 'created_at' => now()->subHour()]);
        $this->assertSame(route('cms.core.article.detail', 'public-reader'), $controller->index($request)->getData()['frontendDetailUrl']);
    }
}
