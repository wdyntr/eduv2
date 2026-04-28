@if ($paginator->hasPages())
<div style="
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 4px;
    font-size: 13px;
    color: var(--text-muted);
    flex-wrap: wrap;
    gap: 12px;
">
    <span>
        Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
        dari {{ $paginator->total() }} data
    </span>

    <div style="display:flex;gap:6px;align-items:center;">
        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <span class="btn-icon" style="opacity:0.3;cursor:not-allowed;">← Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn-icon">← Prev</a>
        @endif

        {{-- Halaman --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="color:var(--text-dim);padding:0 4px;">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="btn-icon" style="
                            border-color:var(--gold);
                            color:var(--gold);
                            cursor:default;
                        ">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="btn-icon">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn-icon">Next →</a>
        @else
            <span class="btn-icon" style="opacity:0.3;cursor:not-allowed;">Next →</span>
        @endif
    </div>
</div>
@endif