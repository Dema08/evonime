@props([
    'title' => '',
    'subtitle' => null,
    'id' => 'carousel-' . uniqid(),
    'items' => [],
    'showRank' => false
])

<div class="space-y-4 my-8">
    <!-- Header with Title & Controls -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-white flex items-center gap-2">
                <span class="w-1.5 h-5 bg-violet-600 rounded-full inline-block"></span>
                {{ $title }}
            </h2>
            @if($subtitle)
                <p class="text-xs text-zinc-400 mt-0.5 ml-3.5">{{ $subtitle }}</p>
            @endif
        </div>

        <!-- Scroll Arrows -->
        <div class="flex items-center gap-2">
            <button type="button" 
                    onclick="document.getElementById('{{ $id }}').scrollBy({left: -600, behavior: 'smooth'})"
                    aria-label="Scroll Left"
                    class="p-2 text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <button type="button" 
                    onclick="document.getElementById('{{ $id }}').scrollBy({left: 600, behavior: 'smooth'})"
                    aria-label="Scroll Right"
                    class="p-2 text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Scrollable Items Track -->
    <div id="{{ $id }}" class="flex items-stretch gap-4 overflow-x-auto scrollbar-hide py-2 px-0.5 scroll-smooth">
        @foreach($items as $index => $anime)
            <div class="flex-none w-[160px] sm:w-[185px] md:w-[210px]">
                <x-anime-card :anime="$anime" :showRank="$showRank" :rank="$index + 1" />
            </div>
        @endforeach
    </div>
</div>
