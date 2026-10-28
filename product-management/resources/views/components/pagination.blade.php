@if ($elements)
    <nav class="flex items-center gap-1">
        {{-- Previous Page Link --}}
        @if ($elements['disabled'] === false)
            <button type="button"
                class="page-link"
                x-data="{}"
                x-on:click="window.location.href = '{{ $elements['prev']['url'] }}'">
                <span class="sr-only">Previous</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>
        @else
            <span class="page-link invisible" aria-disabled="true">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </span>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements['steps'] as $element)
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($url === null)
                        <span class="page-ellipsis" aria-disabled="true">
                            <span class="text-xs select-none">…</span>
                        </span>
                    @else
                        <button type="button"
                            class="page-link {{ $page === $elements['current'] ? 'active' : '' }}"
                            x-data="{}"
                            x-on:click="window.location.href = '{{ $url }}'">
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($elements['disabled'] === false)
            <button type="button"
                class="page-link"
                x-data="{}"
                x-on:click="window.location.href = '{{ $elements['next']['url'] }}'">
                <span class="sr-only">Next</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        @else
            <span class="page-link invisible" aria-disabled="true">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </span>
        @endif
    </nav>
@endif
