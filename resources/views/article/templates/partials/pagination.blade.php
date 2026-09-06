@if ($articles->hasPages())
    @php
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
    @endphp
    <nav class="article-pagination article-pagination--ssr article-pagination--model-{{ $paginationType }} article-pagination--position-{{ $paginationPosition }} {{ $paginationShowTotal ? 'article-pagination--with-total' : 'article-pagination--without-total' }} {{ $paginationFrame ? 'article-pagination--with-frame' : 'article-pagination--without-frame' }} {{ !empty($paginationPadding['enabled']) ? 'article-pagination--padding-enabled' : 'article-pagination--padding-default' }} {{ !empty($paginationMargin['enabled']) ? 'article-pagination--margin-enabled' : 'article-pagination--margin-default' }}" data-pagination-type="{{ $paginationType }}" data-pagination-range-desktop="{{ $paginationRangeDesktop }}" data-pagination-range-tablet="{{ $paginationRangeTablet }}" data-pagination-range-mobile="{{ $paginationRangeMobile }}" data-pagination-range-active="{{ $paginationRenderRange }}" style="{{ $paginationStyle }}" aria-label="{{ t('Article pagination') }}">
        <div class="article-pagination__layout">
        @if($paginationShowTotal)<p class="article-pagination__summary article-pagination__context"><strong>{{ t('Total Data') }}: {{ $articles->total() }}</strong><span>{{ t('Showing') }} {{ $articles->firstItem() }}–{{ $articles->lastItem() }} {{ t('of') }} {{ $articles->total() }} {{ t('articles') }}</span></p>@endif
        <div class="article-pagination__pager"><ul class="pagination ph-pagination mb-0">
            <li class="page-item {{ $articles->onFirstPage() ? 'disabled' : '' }}">
                @if ($articles->onFirstPage())
                    <span class="page-link" aria-disabled="true"><i class="{{ $previousIcon }}" aria-hidden="true"></i><span class="visually-hidden">{{ t('Previous') }}</span></span>
                @else
                    <a class="page-link" href="{{ $articles->previousPageUrl() }}" rel="prev" aria-label="{{ t('Previous') }}" data-article-pagination-link><i class="{{ $previousIcon }}" aria-hidden="true"></i><span class="visually-hidden">{{ t('Previous') }}</span></a>
                @endif
            </li>

            <li class="page-item {{ $currentPage === 1 ? 'active' : '' }}">
                @if ($currentPage === 1)
                    <span class="page-link" aria-current="page">1</span>
                @else
                    <a class="page-link" href="{{ $articles->url(1) }}" aria-label="{{ t('First page') }}" data-article-pagination-link>1</a>
                @endif
            </li>

            @if ($startPage > 2)<li class="page-item disabled article-pagination__ellipsis"><span class="page-link" aria-hidden="true">…</span></li>@endif

            @if ($windowStart <= $windowEnd)
                @foreach (range($windowStart, $windowEnd) as $page)
                    <li class="page-item {{ $page === $currentPage ? 'active' : '' }}">
                        @if ($page === $currentPage)
                            <span class="page-link" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="page-link" href="{{ $articles->url($page) }}" aria-label="{{ t('Page') }} {{ $page }}" data-article-pagination-link>{{ $page }}</a>
                        @endif
                    </li>
                @endforeach
            @endif

            @if ($endPage < $lastPage - 1)<li class="page-item disabled article-pagination__ellipsis"><span class="page-link" aria-hidden="true">…</span></li>@endif

            @if ($lastPage > 1)
                <li class="page-item {{ $currentPage === $lastPage ? 'active' : '' }}">
                    @if ($currentPage === $lastPage)
                        <span class="page-link" aria-current="page">{{ $lastPage }}</span>
                    @else
                        <a class="page-link" href="{{ $articles->url($lastPage) }}" aria-label="{{ t('Last page') }}" data-article-pagination-link>{{ $lastPage }}</a>
                    @endif
                </li>
            @endif

            <li class="page-item {{ $articles->hasMorePages() ? '' : 'disabled' }}">
                @if ($articles->hasMorePages())
                    <a class="page-link" href="{{ $articles->nextPageUrl() }}" rel="next" aria-label="{{ t('Next') }}" data-article-pagination-link><i class="{{ $nextIcon }}" aria-hidden="true"></i><span class="visually-hidden">{{ t('Next') }}</span></a>
                @else
                    <span class="page-link" aria-disabled="true"><i class="{{ $nextIcon }}" aria-hidden="true"></i><span class="visually-hidden">{{ t('Next') }}</span></span>
                @endif
            </li>
        </ul></div>
        </div>
    </nav>
@endif
