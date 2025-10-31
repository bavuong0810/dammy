@if ($paginator->hasPages())
    <!-- Pagination -->
    <nav aria-label="...">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <a class="page-link" href="javascript:;" tabindex="-1" aria-disabled="true">
                        <i class="bx bx-chevrons-left"></i>
                    </a>
                </li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}"><i class="bx bx-chevrons-left"></i></a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <a class="page-link" href="javascript:;">{{ $page }} <span class="visually-hidden">(current)</span></a>
                            </li>
                        @elseif (($page == $paginator->currentPage() + 1 || $page == $paginator->currentPage() + 2) || $page == $paginator->lastPage())
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @elseif ($page == $paginator->lastPage() - 1)
                            <li class="page-item disabled">
                                <a class="page-link" href="javascript:;">
                                    ...
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}"><i class="bx bx-chevrons-right"></i></a>
                </li>
            @else
                <li class="page-item disabled"><a class="page-link" href="javascript:;"><i class="bx bx-chevrons-right"></i></a>
                </li>
            @endif
        </ul>
    </nav>
    <!-- Pagination -->
@endif
