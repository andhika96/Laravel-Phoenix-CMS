<?php if($articles->hasPages()): ?>
    <?php
        $currentPage = $articles->currentPage();
        $lastPage = $articles->lastPage();
        $paginationOptions = data_get($templateOptions ?? [], 'pagination', []);
        $paginationType = data_get($paginationOptions, 'type', 'boxed');
        $paginationType = in_array($paginationType, ['underline', 'boxed', 'soft'], true) ? $paginationType : 'boxed';
        $paginationRange = data_get($paginationOptions, 'range', []);
        $paginationRangeDesktop = min(9, max(1, (int) data_get($paginationRange, 'desktop', 3)));
        $paginationRangeTablet = min(9, max(1, (int) data_get($paginationRange, 'tablet', 3)));
        $paginationRangeMobile = min(9, max(1, (int) data_get($paginationRange, 'mobile', 2)));
        $paginationRenderDevice = in_array($previewDevice ?? null, ['desktop', 'tablet', 'mobile'], true) ? $previewDevice : 'desktop';
        $paginationRenderRange = match ($paginationRenderDevice) {
            'tablet' => $paginationRangeTablet,
            'mobile' => $paginationRangeMobile,
            default => $paginationRangeDesktop,
        };
        $paginationWindowRadius = max(1, intdiv($paginationRenderRange, 2));
        $startPage = max(1, $currentPage - $paginationWindowRadius);
        $endPage = min($lastPage, $currentPage + $paginationWindowRadius);
        $windowStart = max(2, $startPage);
        $windowEnd = min($lastPage - 1, $endPage);
        $paginationPosition = data_get($paginationOptions, 'position', 'right');
        $paginationPosition = in_array($paginationPosition, ['left', 'center', 'right'], true) ? $paginationPosition : 'right';
        $paginationShowTotal = data_get($paginationOptions, 'show_total', true) !== false;
        $paginationFrame = data_get($paginationOptions, 'frame.enabled', true) !== false;
        $paginationPadding = data_get($paginationOptions, 'padding', []);
        $paginationMargin = data_get($paginationOptions, 'margin', []);
        $paginationStyle = '';
        foreach (['desktop', 'tablet', 'mobile'] as $device) {
            foreach (['top', 'right', 'bottom', 'left'] as $side) {
                $paginationStyle .= '--article-pagination-padding-'.$device.'-'.$side.':'.data_get($paginationPadding, $device.'.'.$side, '0px').';';
                $paginationStyle .= '--article-pagination-margin-'.$device.'-'.$side.':'.data_get($paginationMargin, $device.'.'.$side, '0px').';';
            }
        }
        $paginationStyle .= '--article-pagination-frame-border-color:'.data_get($paginationOptions, 'frame.border_color', '#e6e9ef').';';
        $paginationStyle .= '--article-pagination-frame-border-width:'.data_get($paginationOptions, 'frame.border_width', '1px').';';
        $paginationStyle .= '--article-pagination-frame-radius:'.data_get($paginationOptions, 'frame.radius', '.75rem').';';
        $paginationStyle .= '--article-pagination-frame-background:'.data_get($paginationOptions, 'frame.background_color', '#ffffff').';';
        $paginationStyle .= '--article-pagination-item-radius:'.data_get($paginationOptions, 'item_radius', '0.45rem').';';
        $paginationStyle .= '--article-pagination-item-gap:'.data_get($paginationOptions, 'item_gap', '0.45rem').';';
        foreach ([
            'item_background_color' => '--article-pagination-item-background',
            'item_text_color' => '--article-pagination-item-text',
            'item_border_color' => '--article-pagination-item-border',
            'item_hover_background_color' => '--article-pagination-item-hover-background',
            'item_hover_text_color' => '--article-pagination-item-hover-text',
            'item_active_background_color' => '--article-pagination-item-active-background',
            'item_active_text_color' => '--article-pagination-item-active-text',
        ] as $key => $property) {
            $color = data_get($paginationOptions, $key);
            if (is_string($color) && preg_match('/^(?:#[0-9a-f]{3,8}|rgba?\([^)]*\)|hsla?\([^)]*\))$/i', $color) === 1) {
                $paginationStyle .= $property.':'.$color.';';
            }
        }
        $previousIcon = data_get($paginationOptions, 'previous_icon', 'fas fa-chevron-left');
        $previousIcon = in_array($previousIcon, ['fas fa-chevron-left', 'fas fa-angle-left', 'fas fa-arrow-left'], true) ? $previousIcon : 'fas fa-chevron-left';
        $nextIcon = data_get($paginationOptions, 'next_icon', 'fas fa-chevron-right');
        $nextIcon = in_array($nextIcon, ['fas fa-chevron-right', 'fas fa-angle-right', 'fas fa-arrow-right'], true) ? $nextIcon : 'fas fa-chevron-right';
    ?>
    <nav class="article-pagination article-pagination--ssr article-pagination--model-<?php echo e($paginationType); ?> article-pagination--position-<?php echo e($paginationPosition); ?> <?php echo e($paginationShowTotal ? 'article-pagination--with-total' : 'article-pagination--without-total'); ?> <?php echo e($paginationFrame ? 'article-pagination--with-frame' : 'article-pagination--without-frame'); ?> <?php echo e(!empty($paginationPadding['enabled']) ? 'article-pagination--padding-enabled' : 'article-pagination--padding-default'); ?> <?php echo e(!empty($paginationMargin['enabled']) ? 'article-pagination--margin-enabled' : 'article-pagination--margin-default'); ?>" data-pagination-type="<?php echo e($paginationType); ?>" data-pagination-range-desktop="<?php echo e($paginationRangeDesktop); ?>" data-pagination-range-tablet="<?php echo e($paginationRangeTablet); ?>" data-pagination-range-mobile="<?php echo e($paginationRangeMobile); ?>" data-pagination-range-active="<?php echo e($paginationRenderRange); ?>" style="<?php echo e($paginationStyle); ?>" aria-label="<?php echo e(t('Article pagination')); ?>">
        <div class="article-pagination__layout">
        <?php if($paginationShowTotal): ?><p class="article-pagination__summary article-pagination__context"><strong><?php echo e(t('Total Data')); ?>: <?php echo e($articles->total()); ?></strong><span><?php echo e(t('Showing')); ?> <?php echo e($articles->firstItem()); ?>–<?php echo e($articles->lastItem()); ?> <?php echo e(t('of')); ?> <?php echo e($articles->total()); ?> <?php echo e(t('articles')); ?></span></p><?php endif; ?>
        <div class="article-pagination__pager"><ul class="pagination ph-pagination mb-0">
            <li class="page-item <?php echo e($articles->onFirstPage() ? 'disabled' : ''); ?>">
                <?php if($articles->onFirstPage()): ?>
                    <span class="page-link" aria-disabled="true"><i class="<?php echo e($previousIcon); ?>" aria-hidden="true"></i><span class="visually-hidden"><?php echo e(t('Previous')); ?></span></span>
                <?php else: ?>
                    <a class="page-link" href="<?php echo e($articles->previousPageUrl()); ?>" rel="prev" aria-label="<?php echo e(t('Previous')); ?>" data-article-pagination-link><i class="<?php echo e($previousIcon); ?>" aria-hidden="true"></i><span class="visually-hidden"><?php echo e(t('Previous')); ?></span></a>
                <?php endif; ?>
            </li>

            <li class="page-item <?php echo e($currentPage === 1 ? 'active' : ''); ?>">
                <?php if($currentPage === 1): ?>
                    <span class="page-link" aria-current="page">1</span>
                <?php else: ?>
                    <a class="page-link" href="<?php echo e($articles->url(1)); ?>" aria-label="<?php echo e(t('First page')); ?>" data-article-pagination-link>1</a>
                <?php endif; ?>
            </li>

            <?php if($startPage > 2): ?><li class="page-item disabled article-pagination__ellipsis"><span class="page-link" aria-hidden="true">…</span></li><?php endif; ?>

            <?php if($windowStart <= $windowEnd): ?>
                <?php $__currentLoopData = range($windowStart, $windowEnd); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="page-item <?php echo e($page === $currentPage ? 'active' : ''); ?>">
                        <?php if($page === $currentPage): ?>
                            <span class="page-link" aria-current="page"><?php echo e($page); ?></span>
                        <?php else: ?>
                            <a class="page-link" href="<?php echo e($articles->url($page)); ?>" aria-label="<?php echo e(t('Page')); ?> <?php echo e($page); ?>" data-article-pagination-link><?php echo e($page); ?></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            <?php if($endPage < $lastPage - 1): ?><li class="page-item disabled article-pagination__ellipsis"><span class="page-link" aria-hidden="true">…</span></li><?php endif; ?>

            <?php if($lastPage > 1): ?>
                <li class="page-item <?php echo e($currentPage === $lastPage ? 'active' : ''); ?>">
                    <?php if($currentPage === $lastPage): ?>
                        <span class="page-link" aria-current="page"><?php echo e($lastPage); ?></span>
                    <?php else: ?>
                        <a class="page-link" href="<?php echo e($articles->url($lastPage)); ?>" aria-label="<?php echo e(t('Last page')); ?>" data-article-pagination-link><?php echo e($lastPage); ?></a>
                    <?php endif; ?>
                </li>
            <?php endif; ?>

            <li class="page-item <?php echo e($articles->hasMorePages() ? '' : 'disabled'); ?>">
                <?php if($articles->hasMorePages()): ?>
                    <a class="page-link" href="<?php echo e($articles->nextPageUrl()); ?>" rel="next" aria-label="<?php echo e(t('Next')); ?>" data-article-pagination-link><i class="<?php echo e($nextIcon); ?>" aria-hidden="true"></i><span class="visually-hidden"><?php echo e(t('Next')); ?></span></a>
                <?php else: ?>
                    <span class="page-link" aria-disabled="true"><i class="<?php echo e($nextIcon); ?>" aria-hidden="true"></i><span class="visually-hidden"><?php echo e(t('Next')); ?></span></span>
                <?php endif; ?>
            </li>
        </ul></div>
        </div>
    </nav>
<?php endif; ?>
<?php /**PATH D:\Laragon\www\laravel-13-phoenix\resources\views/article/templates/partials/pagination.blade.php ENDPATH**/ ?>