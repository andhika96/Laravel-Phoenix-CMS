<?php ($articleTypography = app(\App\Support\SiteTypography::class)->resolve(site_config())); ?>

<?php $__env->startSection('title'); ?>
    <?php echo e($article->title); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <main id="ph-article-detail">
        <?php echo $__env->make($detailView, ['article' => $article, 'templateSettings' => $templateSettings, 'templateOptions' => $templateOptions, 'previousArticle' => $previousArticle ?? null, 'nextArticle' => $nextArticle ?? null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
    <link href="<?php echo e(asset('storage/fonts/'.$articleTypography['fontFamilyCode'].'/fonts.css?v=').time()); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/theme-responsive-typography.css?v=').time()); ?>" rel="stylesheet">
    <style>:root{--ph-font-family:'<?php echo e($articleTypography['fontFamilyName']); ?>',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;--ph-font-size:<?php echo e($articleTypography['fontSize']); ?>;}</style>
    <link href="<?php echo e(url('assets/css/article/article-frontend-2026.css?v=').time()); ?>" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('js'); ?>
    <script src="<?php echo e(url('assets/js/article/article-theme-color-sync-2026.js?v=').time()); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('themes.'.custom_theme('frontend'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/detail.blade.php ENDPATH**/ ?>