@if ($articles->hasPages())
    @php
        $currentPage = $articles->currentPage();
        $lastPage = $articles->lastPage();
        $startPage = max(1, $currentPage - 1);
        $endPage = min($lastPage, $currentPage + 1);
        $windowStart = max(2, $startPage);
        $windowEnd = min($lastPage - 1, $endPage);
    @endphp
    <nav class="article-pagination" aria-label="{{ t('Article pagination') }}">
        <p class="article-pagination__summary">{{ t('Showing') }} {{ $articles->firstItem() }}–{{ $articles->lastItem() }} {{ t('of') }} {{ $articles->total() }} {{ t('articles') }}</p>
        <ul class="pagination ph-pagination mb-0">
            <li class="page-item {{ $articles->onFirstPage() ? 'disabled' : '' }}">
                @if ($articles->onFirstPage())
                    <span class="page-link" aria-disabled="true"><i class="far fa-chevron-left" aria-hidden="true"></i><span class="visually-hidden">{{ t('Previous') }}</span></span>
                @else
                    <a class="page-link" href="{{ $articles->previousPageUrl() }}" rel="prev" aria-label="{{ t('Previous') }}" data-article-pagination-link><i class="far fa-chevron-left" aria-hidden="true"></i><span class="visually-hidden">{{ t('Previous') }}</span></a>
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
                    <a class="page-link" href="{{ $articles->nextPageUrl() }}" rel="next" aria-label="{{ t('Next') }}" data-article-pagination-link><i class="far fa-chevron-right" aria-hidden="true"></i><span class="visually-hidden">{{ t('Next') }}</span></a>
                @else
                    <span class="page-link" aria-disabled="true"><i class="far fa-chevron-right" aria-hidden="true"></i><span class="visually-hidden">{{ t('Next') }}</span></span>
                @endif
            </li>
        </ul>
    </nav>
@endif
