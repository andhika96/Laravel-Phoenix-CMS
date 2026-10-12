<?php
    $titleOptions = $templateOptions ?? $options ?? [];
    $titleTag = data_get($titleOptions, 'article_title.tag', 'h4');
    $titleTag = in_array($titleTag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true) ? $titleTag : 'h4';
?>

<?php switch($titleTag):
    case ('h1'): ?>
        <h1 class="article-title-clamp"><a href="<?php echo e(route('cms.core.article.detail', $article->uri)); ?>"><?php echo e($article->title); ?></a></h1>
        <?php break; ?>
    <?php case ('h2'): ?>
        <h2 class="article-title-clamp"><a href="<?php echo e(route('cms.core.article.detail', $article->uri)); ?>"><?php echo e($article->title); ?></a></h2>
        <?php break; ?>
    <?php case ('h3'): ?>
        <h3 class="article-title-clamp"><a href="<?php echo e(route('cms.core.article.detail', $article->uri)); ?>"><?php echo e($article->title); ?></a></h3>
        <?php break; ?>
    <?php case ('h5'): ?>
        <h5 class="article-title-clamp"><a href="<?php echo e(route('cms.core.article.detail', $article->uri)); ?>"><?php echo e($article->title); ?></a></h5>
        <?php break; ?>
    <?php case ('h6'): ?>
        <h6 class="article-title-clamp"><a href="<?php echo e(route('cms.core.article.detail', $article->uri)); ?>"><?php echo e($article->title); ?></a></h6>
        <?php break; ?>
    <?php default: ?>
        <h4 class="article-title-clamp"><a href="<?php echo e(route('cms.core.article.detail', $article->uri)); ?>"><?php echo e($article->title); ?></a></h4>
<?php endswitch; ?>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/partials/archive-title.blade.php ENDPATH**/ ?>