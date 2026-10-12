<?php ($articleTypography = app(\App\Support\SiteTypography::class)->resolve(site_config())); ?>

<?php $__env->startSection('title'); ?><?php echo e(t('Private article')); ?><?php $__env->stopSection(); ?>

<?php $__env->startPush('meta'); ?><meta name="robots" content="noindex,nofollow"><?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <main class="article-access-page article-access-page--private">
        <section class="article-access-state" aria-labelledby="article-private-title">
            <a href="<?php echo e(route('cms.core.article')); ?>" class="article-back-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> <?php echo e(t('Back to articles')); ?></a>
            <div class="article-access-state__content">
                <span class="article-access-state__icon" aria-hidden="true"><i class="fas fa-lock"></i></span>
                <h1 id="article-private-title"><?php echo e(t('This article is private')); ?></h1>
                <p><?php echo e(t('This content is only available to its author and permitted readers.')); ?></p>
                <a href="<?php echo e(route('cms.core.article')); ?>" class="article-text-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> <?php echo e(t('Back to articles')); ?></a>
            </div>
        </section>
    </main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
    <link href="<?php echo e(asset('storage/fonts/'.$articleTypography['fontFamilyCode'].'/fonts.css?v=').time()); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/theme-responsive-typography.css?v=').time()); ?>" rel="stylesheet">
    <style>:root{--ph-font-family:'<?php echo e($articleTypography['fontFamilyName']); ?>',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;--ph-font-size:<?php echo e($articleTypography['fontSize']); ?>;}</style>
    <link href="<?php echo e(url('assets/css/article/article-frontend-2026.css?v=').time()); ?>" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('js'); ?><script src="<?php echo e(url('assets/js/article/article-theme-color-sync-2026.js?v=').time()); ?>"></script><?php $__env->stopPush(); ?>

<?php echo $__env->make('themes.'.custom_theme('frontend'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/private.blade.php ENDPATH**/ ?>