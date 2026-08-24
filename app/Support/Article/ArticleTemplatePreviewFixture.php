<?php

namespace App\Support\Article;

use App\Models\Article\Article;
use App\Models\Article\Article_Categories;
use App\Models\Awesome_Admin\Account;
use Carbon\CarbonImmutable;
use Illuminate\Pagination\LengthAwarePaginator;

final class ArticleTemplatePreviewFixture
{
    private const MEDIA_DIRECTORY = 'articles/template-preview-20260825';

    public function archivePaginator(int $page = 1, string $path = '/article'): LengthAwarePaginator
    {
        $articles = $this->articles();
        $perPage = 6;
        $page = max(1, min(8, $page));
        $offset = ($page - 1) * $perPage;
        $items = collect(range(0, $perPage - 1))
            ->map(fn (int $index): Article => clone $articles[($offset + $index) % count($articles)]);

        return new LengthAwarePaginator($items, 48, $perPage, $page, [
            'path' => $path,
            'pageName' => 'page',
        ]);
    }

    public function detailArticle(): Article
    {
        return $this->articles()[0];
    }

    /** @return array{previous: Article, next: Article} */
    public function neighbors(): array
    {
        $articles = $this->articles();

        return [
            'previous' => $articles[5],
            'next' => $articles[1],
        ];
    }

    /** @return array<int, Article> */
    private function articles(): array
    {
        $author = new Account([
            'id' => 9001,
            'fullname' => 'Aruna Systems Editorial',
            'username' => 'aruna-editorial',
        ]);
        $baseDate = CarbonImmutable::parse('2026-08-24 09:00:00', 'Asia/Jakarta');
        $definitions = [
            ['uri' => 'preview-resilient-systems', 'category' => 'Technology', 'code' => 'technology', 'title' => 'Building Resilient Systems at Scale', 'excerpt' => 'A practical guide to the habits, tooling, and decisions that make modern digital systems dependable.', 'media' => 'alpine-lake.png'],
            ['uri' => 'preview-designs-that-last', 'category' => 'Design', 'code' => 'design', 'title' => 'Designing Spaces That Last Beyond a Trend Cycle', 'excerpt' => 'How calmer visual systems help teams make more confident product decisions.', 'media' => 'design-studio.png'],
            ['uri' => 'preview-slow-coastal-travel', 'category' => 'Travel', 'code' => 'travel', 'title' => 'Slow Adventures Along the Coastal Road', 'excerpt' => 'A quieter way to build memorable journeys around place, pace, and perspective.', 'media' => 'coastal-road.png'],
            ['uri' => 'preview-hybrid-work', 'category' => 'Workplace', 'code' => 'workplace', 'title' => 'What Hybrid Teams Need to Do Their Best Work', 'excerpt' => 'Small operating principles that make collaboration more deliberate and humane.', 'media' => 'team-meeting.png'],
            ['uri' => 'preview-wellbeing', 'category' => 'Wellbeing', 'code' => 'wellbeing', 'title' => 'The Rituals That Give Busy Days More Room', 'excerpt' => 'A grounded perspective on attention, recovery, and everyday wellbeing.', 'media' => 'wellness-room.png'],
            ['uri' => 'preview-patterns-and-principles', 'category' => 'Knowledge', 'code' => 'knowledge', 'title' => 'Patterns, Principles, and the Practice of Better Decisions', 'excerpt' => 'A structured reading experience for material that benefits from a clear outline.', 'media' => 'canyon-abstract.png'],
        ];

        return array_map(function (array $definition, int $index) use ($author, $baseDate): Article {
            $article = new Article([
                'id' => 9100 + $index,
                'uri' => $definition['uri'],
                'user_id' => $author->id,
                'category_id' => 9200 + $index,
                'title' => $definition['title'],
                'content' => $index === 0 ? $this->longFormContent() : '<p>'.$definition['excerpt'].'</p>',
                'tags' => strtolower($definition['category']).',editorial,preview',
                'thumb_s' => self::MEDIA_DIRECTORY.'/'.$definition['media'],
                'thumb_l' => self::MEDIA_DIRECTORY.'/'.$definition['media'],
                'visibility' => 'public',
                'status' => 'publish',
                'scheduled' => 'false',
                'created_at' => $baseDate->subDays($index * 2),
                'updated_at' => $baseDate->subDays($index * 2),
            ]);
            $article->setRelation('category', new Article_Categories([
                'id' => 9200 + $index,
                'name' => $definition['category'],
                'code' => $definition['code'],
            ]));
            $article->setRelation('author', clone $author);

            return $article;
        }, $definitions, array_keys($definitions));
    }

    private function longFormContent(): string
    {
        return <<<'HTML'
<p class="article-lead">Resilience is not a feature added at the end of delivery. It is the accumulated result of clear priorities, observability, and teams that make room for learning.</p>
<blockquote>Reliable experiences are built in small, deliberate decisions—long before the moment they are tested.</blockquote>
<h2>Define realistic scenarios</h2>
<p>Start with the situations that matter most to readers and customers. A useful system makes those scenarios visible, measurable, and easier to improve over time.</p>
<h2>Make reliability visible</h2>
<p>Use a calm visual hierarchy to surface what needs attention. The aim is not more dashboards; it is a shared understanding of where energy should go next.</p>
<h2>Design for recovery</h2>
<p>Teams work better when recovery is planned into the experience. Clear paths, predictable interfaces, and useful context turn a difficult moment into a manageable one.</p>
HTML;
    }
}
