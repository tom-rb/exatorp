@if ($paginator->hasPages())
    <nav class="pagination is-centered">
        <a class="pagination-previous" href="{{ $paginator->previousPageUrl() }}" rel="prev" {{ $paginator->onFirstPage() ? 'disabled' : '' }}>@lang('pagination.previous')</a>
        <a class="pagination-next" href="{{ $paginator->nextPageUrl() }}" rel="next" {{ $paginator->hasMorePages() ? '' : 'disabled' }}>@lang('pagination.next')</a>

        @yield('pagination-links')
    </nav>
@endif
