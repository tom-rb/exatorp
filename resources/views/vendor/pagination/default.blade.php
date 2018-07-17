@extends('vendor.pagination.simple-default')
@section('pagination-links')
    <ul class="pagination-list">
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li>
                    <span class="pagination-ellipsis">{{ $element }}</span>
                </li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <li>
                        <a href="{{ $url }}" class="pagination-link{{ $page == $paginator->currentPage() ? ' is-current' : '' }}">{{ $page }}</a>
                    </li>
                @endforeach
            @endif
        @endforeach
    </ul>
@stop
