@if ($paginator->hasPages())
    <div class="text-center mt-3">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="btn btn-secondary btn-sm disabled me-2">« Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-primary btn-sm me-2">« Previous</a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="btn btn-secondary btn-sm disabled me-1">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="btn btn-info btn-sm me-1">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="btn btn-outline-primary btn-sm me-1">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-primary btn-sm ms-2">Next »</a>
        @else
            <span class="btn btn-secondary btn-sm disabled ms-2">Next »</span>
        @endif
    </div>
@endif
