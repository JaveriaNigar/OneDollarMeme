@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link btn btn-secondary disabled">Previous</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link btn btn-primary" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item ms-2">
                    <a class="page-link btn btn-primary" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
                </li>
            @else
                <li class="page-item disabled ms-2" aria-disabled="true">
                    <span class="page-link btn btn-secondary disabled">Next</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
