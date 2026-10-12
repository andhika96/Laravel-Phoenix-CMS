<?php ($articleTypography = app(\App\Support\SiteTypography::class)->resolve(site_config())); ?>

<?php $__env->startSection('title'); ?><?php echo e(t('Protected article')); ?><?php $__env->stopSection(); ?>

<?php $__env->startPush('meta'); ?><meta name="robots" content="noindex,nofollow"><?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <main id="ph-article-password-gate" class="article-access-page article-access-page--password" data-unlock-url="<?php echo e($unlockUrl); ?>" data-redirect-url="<?php echo e($redirectUrl); ?>" data-initial-error="<?php echo e($errors->first('password')); ?>">
        <section class="article-access-state" aria-labelledby="article-password-title">
            <a href="<?php echo e(route('cms.core.article')); ?>" class="article-back-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> <?php echo e(t('Back to articles')); ?></a>
            <div class="article-access-state__veil" aria-hidden="true"></div>
            <div class="article-password-modal" role="dialog" aria-modal="true" aria-labelledby="article-password-title" aria-describedby="article-password-description">
                <a href="<?php echo e(route('cms.core.article')); ?>" class="article-password-modal__close" aria-label="<?php echo e(t('Close')); ?>"><i class="fas fa-times" aria-hidden="true"></i></a>
                <span class="article-password-modal__icon" aria-hidden="true"><i class="fas fa-lock"></i></span>
                <h1 id="article-password-title"><?php echo e(t('Protected article')); ?></h1>
                <p id="article-password-description"><?php echo e(t('Enter the password set by the author to continue reading.')); ?></p>
                <form action="<?php echo e($unlockUrl); ?>" method="post" v-on:submit.prevent="unlock">
                    <?php echo csrf_field(); ?>
                    <label for="article-password-input"><?php echo e(t('Password')); ?></label>
                    <div class="article-password-modal__input-wrap">
                        <input id="article-password-input" name="password" v-bind:type="showPassword ? 'text' : 'password'" v-model="password" autocomplete="current-password" required maxlength="128" v-bind:aria-invalid="error ? 'true' : 'false'" aria-describedby="article-password-error">
                        <button type="button" class="article-password-modal__toggle" v-on:click="showPassword = !showPassword" v-bind:aria-label="showPassword ? 'Hide password' : 'Show password'"><i v-bind:class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" aria-hidden="true"></i></button>
                    </div>
                    <p id="article-password-error" class="article-password-modal__error" role="alert" v-show="error" v-text="error"><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></p>
                    <div class="article-password-modal__actions">
                        <a href="<?php echo e(route('cms.core.article')); ?>" class="btn ph-btn-theme-outline"><?php echo e(t('Back to articles')); ?></a>
                        <button type="submit" class="btn ph-btn-theme" v-bind:disabled="isSubmitting"><span v-if="!isSubmitting"><?php echo e(t('Unlock article')); ?></span><span v-else><?php echo e(t('Unlocking')); ?>…</span></button>
                    </div>
                </form>
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

<?php $__env->startPush('js'); ?>
    <script src="<?php echo e(url('assets/js/article/article-theme-color-sync-2026.js?v=').time()); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.5.21/dist/vue.global.prod.js" crossorigin="anonymous"></script>
    <script src="<?php echo e(url('assets/js/vue3/article/vueV3-article-frontend-2026.js?v=').time()); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('themes.'.custom_theme('frontend'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/password-protected.blade.php ENDPATH**/ ?>