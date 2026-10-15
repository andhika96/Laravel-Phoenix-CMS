<?php
    $header = $templateOptions['header'] ?? [];
    $eyebrow = data_get($header, 'eyebrow', ['enabled' => true, 'mode' => 'dynamic', 'text' => '']);
    $title = data_get($header, 'title', ['enabled' => true]);
    $description = data_get($header, 'description', ['enabled' => true, 'mode' => 'dynamic', 'text' => '']);
    $eyebrowText = data_get($eyebrow, 'mode') === 'custom' && data_get($eyebrow, 'text') !== '' ? data_get($eyebrow, 'text') : $defaultEyebrow;
    $descriptionText = data_get($description, 'mode') === 'custom' && data_get($description, 'text') !== '' ? data_get($description, 'text') : $defaultDescription;
?>
<?php if(data_get($eyebrow, 'enabled') && $eyebrowText): ?><span class="article-chip"><?php echo e($eyebrowText); ?></span><?php endif; ?>
<?php
    $metaParts = [];
    if (data_get($templateOptions, 'display.date', true) && $article->created_at) $metaParts[] = $article->created_at->format('d M Y');
    $authorName = $article->author?->fullname ?: $article->author?->username;
    if (data_get($templateOptions, 'display.author', true) && $authorName) $metaParts[] = t('By').' '.$authorName;
    $metaText = implode(' · ', $metaParts);
?>
<?php if($metaText): ?><p class="article-detail__meta"><?php echo e($metaText); ?></p><?php endif; ?>
<?php if(data_get($title, 'enabled')): ?><h1><?php echo e($article->title); ?></h1><?php endif; ?>
<?php if(data_get($description, 'enabled') && $descriptionText): ?><p class="article-detail__dek"><?php echo e($descriptionText); ?></p><?php endif; ?>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/partials/detail-header-copy.blade.php ENDPATH**/ ?>