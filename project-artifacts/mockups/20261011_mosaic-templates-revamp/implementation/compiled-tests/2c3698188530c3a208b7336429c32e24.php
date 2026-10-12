<?php
    $thumbnailOptions = $templateOptions['thumbnail'] ?? [];
    $thumbnailMode = data_get($thumbnailOptions, 'mode', 'background');
    $thumbnailMode = in_array($thumbnailMode, ['background', 'asset'], true) ? $thumbnailMode : 'background';
    $thumbnailFit = data_get($thumbnailOptions, 'fit', 'cover');
    $thumbnailFit = in_array($thumbnailFit, ['cover', 'contain'], true) ? $thumbnailFit : 'cover';
    $thumbnailHeightStyle = $thumbnailMode === 'background' ? '--article-thumbnail-height:'.data_get($thumbnailOptions, 'height', '9.3rem').';' : '';
    $thumbnailFrame = $thumbnailOptions['frame'] ?? [];
    $thumbnailFrameEnabled = !empty($thumbnailFrame['enabled']);
?>
<a href="<?php echo e($href); ?>" class="<?php echo e($class); ?> article-media-frame <?php echo e($thumbnailMode === 'asset' ? 'article-asset-media' : 'article-background-media'); ?> <?php echo e($thumbnailFrameEnabled ? 'article-thumbnail-frame--custom' : ''); ?>" style="--article-media-image:url('<?php echo e($mediaUrl); ?>');--article-thumbnail-fit:<?php echo e($thumbnailFit); ?>;--article-thumbnail-background:<?php echo e(data_get($thumbnailOptions, 'background_color', '#f2f4f7')); ?>;--article-thumbnail-frame-border-color:<?php echo e(data_get($thumbnailFrame, 'border_color', '#e1e6ee')); ?>;--article-thumbnail-frame-border-width:<?php echo e(data_get($thumbnailFrame, 'border_width', '1px')); ?>;--article-thumbnail-frame-radius:<?php echo e(data_get($thumbnailFrame, 'radius', '1rem')); ?>;<?php echo e($thumbnailHeightStyle); ?>" aria-label="<?php echo e($title); ?>"><?php if($thumbnailMode === 'asset'): ?><img src="<?php echo e($mediaUrl); ?>" alt=""><?php else: ?><span class="visually-hidden"><?php echo e($title); ?></span><?php endif; ?></a>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/partials/media-link.blade.php ENDPATH**/ ?>