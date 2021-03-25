@if ($paginator->hasNext || $paginator->hasPrevious)
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if (! $paginator->hasPrevious)
                <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                    <span class="page-link" aria-hidden="true">&lsaquo; Previous</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator['previousUrl'] ?: }}" rel="prev" aria-label="Previous">&lsaquo; Previous</a>
                </li>
            @endif
            {{-- Next Page Link --}}
            @if ($paginator->hasNext)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator['nextUrl'] ?: }}" rel="next" aria-label="Next">Next &rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="Next">
                    <span class="page-link" aria-hidden="true">Next &rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
