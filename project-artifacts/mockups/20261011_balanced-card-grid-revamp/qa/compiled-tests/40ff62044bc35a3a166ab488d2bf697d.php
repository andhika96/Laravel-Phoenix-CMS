<?php
    $outline = [];
    $outlinedContent = preg_replace_callback('/<h([2-3])([^>]*)>(.*?)<\/h\1>/is', function (array $match) use (&$outline) {
        $title = trim(strip_tags($match[3]));
        $id = 'article-section-'.(count($outline) + 1).'-'.\Illuminate\Support\Str::slug($title);
        $outline[] = ['id' => $id, 'title' => $title, 'level' => (int) $match[1]];

        return '<h'.$match[1].$match[2].' id="'.$id.'">'.$match[3].'</h'.$match[1].'>';
    }, (string) $article->content) ?? (string) $article->content;
    $coverUrl = $article->thumb_l && Storage::disk('public')->exists($article->thumb_l) ? Storage::url($article->thumb_l) : asset('assets/images/article/article-image-placeholder.svg');
?>
<article class="article-detail article-detail--knowledge">
    <div class="article-detail__shell article-detail__shell--knowledge" <?php echo $__env->make('article.templates.partials.template-shell-attributes', ['templateOptions' => $templateOptions ?? []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>>
        <div class="article-detail__main">
            <a href="<?php echo e(route('cms.core.article')); ?>" class="article-back-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> <?php echo e(t('Back to articles')); ?></a>
            <header class="article-detail__header">
                <?php echo $__env->make('article.templates.partials.detail-header-copy', ['defaultEyebrow' => $article->category?->name ?? t('Uncategorized'), 'defaultDescription' => \Illuminate\Support\Str::limit(trim(strip_tags((string) $article->content)), 180), 'metaText' => optional($article->created_at)->format('d M Y').' · '.t('By').' '.($article->author?->fullname ?: $article->author?->username)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </header>
            <img src="<?php echo e($coverUrl); ?>" alt="<?php echo e($article->title); ?>" class="article-detail__cover article-media-frame">
            <div class="article-rich-content"><?php echo $outlinedContent; ?></div>
            <?php echo $__env->make('article.templates.partials.detail-navigation', ['previousArticle' => $previousArticle ?? null, 'nextArticle' => $nextArticle ?? null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <?php if(count($outline)): ?>
            <aside class="article-toc"><strong><?php echo e(t('On this page')); ?></strong><ol><?php $__currentLoopData = $outline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li class="level-<?php echo e($item['level']); ?>"><a href="#<?php echo e($item['id']); ?>"><?php echo e($item['title']); ?></a></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ol></aside>
        <?php endif; ?>
    </div>
</article>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/detail/knowledge-toc.blade.php ENDPATH**/ ?>