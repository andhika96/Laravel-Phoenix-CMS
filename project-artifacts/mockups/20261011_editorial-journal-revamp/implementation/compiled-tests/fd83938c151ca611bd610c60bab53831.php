<?php
    $classicAuthor = $classic['show_author'] && $article->relationLoaded('author') ? $article->author : null;
    $classicAuthorName = $classicAuthor?->fullname ?: $classicAuthor?->username;
    $classicReadTime = max(1, (int) ceil(str_word_count(strip_tags((string) $article->content)) / 220));
?>
<?php if($classicAuthorName || ($classic['show_date'] && $article->created_at) || $classic['show_reading_time']): ?>
    <div class="article-classic-meta">
        <?php if($classicAuthorName): ?><span class="article-classic-author"><span class="article-classic-avatar" aria-hidden="true"><?php echo e(\Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($classicAuthorName, 0, 1))); ?></span><?php echo e($classicAuthorName); ?></span><?php endif; ?>
        <?php if($classic['show_date'] && $article->created_at): ?><time datetime="<?php echo e($article->created_at->toDateString()); ?>"><?php echo e($article->created_at->format('d M Y')); ?></time><?php endif; ?>
        <?php if($classic['show_reading_time']): ?><span><?php echo e($classicReadTime); ?> <?php echo e(t('min read')); ?></span><?php endif; ?>
    </div>
<?php endif; ?>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/partials/classic-meta.blade.php ENDPATH**/ ?>