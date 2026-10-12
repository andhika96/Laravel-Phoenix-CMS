<?php ($articleTypography = app(\App\Support\SiteTypography::class)->resolve(site_config())); ?>
<?php $__env->startSection('title'); ?>
    <?php echo e(t('Articles')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <main id="ph-app-article-frontend" data-list-url="<?php echo e(route('cms.core.article.listdata')); ?>" data-template-options="<?php echo e(json_encode($templateOptions)); ?>" data-pagination-prev="<i class='<?php echo e(in_array(data_get($templateOptions ?? [], 'pagination.previous_icon'), ['fas fa-chevron-left', 'fas fa-angle-left', 'fas fa-arrow-left'], true) ? data_get($templateOptions ?? [], 'pagination.previous_icon') : 'fas fa-chevron-left'); ?>' aria-hidden='true'></i><span class='visually-hidden'><?php echo e(t('Previous')); ?></span>" data-pagination-next="<i class='<?php echo e(in_array(data_get($templateOptions ?? [], 'pagination.next_icon'), ['fas fa-chevron-right', 'fas fa-angle-right', 'fas fa-arrow-right'], true) ? data_get($templateOptions ?? [], 'pagination.next_icon') : 'fas fa-chevron-right'); ?>' aria-hidden='true'></i><span class='visually-hidden'><?php echo e(t('Next')); ?></span>">
        <div class="article-frontend-app" aria-live="polite" v-on:submit.prevent="handleSubmit" v-on:click="handleClick" v-on:input="syncFilterInput" v-on:change="syncFilterInput" v-bind:class="{ 'is-hydrated': isHydrated, 'is-loading-list': isLoading }" v-bind:aria-busy="isLoading ? 'true' : 'false'">
            <div class="article-frontend-runtime">
                <div data-article-ssr v-once>
                    <?php echo $__env->make($archiveView, ['articles' => $articles, 'templateSettings' => $templateSettings, 'articleCategories' => $articleCategories, 'templateOptions' => $templateOptions], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
                <p class="article-frontend-runtime__error" v-if="error" role="alert" v-text="error"></p>
            </div>
            <Teleport v-if="isLoading" to="[data-article-vue-list-state-slot]">
                <div class="article-vue-list-loading text-center p-5" role="status"><div class="spinner-border text-primary mb-2" aria-hidden="true"></div><div class="h6 m-0"><?php echo e(t('Loading')); ?> ...</div></div>
            </Teleport>
            <Teleport v-if="isHydrated && !isLoading && totalPage > 1" to="[data-article-vue-control-slot]">
                <div class="article-pagination article-pagination--vue" :class="paginationClasses" :style="paginationStyle">
                    <div class="article-pagination__layout">
                        <div v-if="paginationOptions.show_total" class="article-pagination__context"><strong><?php echo e(t('Total Data')); ?>: {{ totalLabel }}</strong><span><?php echo e(t('Showing')); ?> {{ firstItem }}–{{ lastItem }} <?php echo e(t('of')); ?> {{ totalLabel }} <?php echo e(t('articles')); ?></span></div>
                        <div class="article-pagination__pager"><paginate v-model="currentPage" v-bind:page-count="totalPage" v-bind:force-page="currentPage" v-bind:page-range="paginationRange" v-bind:margin-pages="1" v-bind:click-handler="goToPage" v-bind:prev-text="paginationCopy.prev" v-bind:next-text="paginationCopy.next" container-class="pagination ph-pagination m-0 font-size-inherit" page-class="page-item" page-link-class="page-link" prev-class="page-item" prev-link-class="page-link" next-class="page-item" next-link-class="page-link" active-class="active" disabled-class="disabled"></paginate></div>
                    </div>
                </div>
            </Teleport>
        </div>
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
    <script src="<?php echo e(url('assets/plugins/vue/plugins/vuejs-paginate-next/js/vuejs-paginate-next.1.0.2.umd.js')); ?>"></script>
    <script src="<?php echo e(url('assets/js/vue3/article/vueV3-article-frontend-2026.js?v=').time()); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('themes.'.custom_theme('frontend'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/archive.blade.php ENDPATH**/ ?>