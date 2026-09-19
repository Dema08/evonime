@props([
    'title' => '',
    'subtitle' => null,
    'id' => 'carousel-' . uniqid(),
    'items' => [],
    'showRank' => false
])

<div class="space-y-4 my-10 bg-[#141414] p-6 border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] relative text-[#F5F0E6]">
    
    <!-- Manga Section Heading Badge -->
    <div class="absolute -top-3.5 left-6 bg-[#F5F0E6] text-[#0D0D0D] px-4 py-1 border-2 border-[#F5F0E6] text-[11px] font-black tracking-widest uppercase shadow-[2px_2px_0px_#E63946]">
        CHAPTER SECTION // CURATED
    </div>

    <!-- Header with Title & Controls -->
    <div class="flex items-center justify-between pt-2">
        <div>
            <h2 class="text-2xl md:text-3xl font-black tracking-tight text-[#F5F0E6] flex items-center gap-2" style="font-family: 'Anton', sans-serif;">
                <span class="w-2.5 h-6 bg-[#E63946] border border-[#F5F0E6] inline-block"></span>
                {{ strtoupper($title) }}
            </h2>
            @if($subtitle)
                <p class="text-xs text-zinc-400 font-bold mt-0.5 ml-4.5">{{ $subtitle }}</p>
            @endif
        </div>

        <!-- Scroll Arrows -->
        <div class="flex items-center gap-2">
            <button type="button" 
                    onclick="document.getElementById('{{ $id }}').scrollBy({left: -600, behavior: 'smooth'})"
                    aria-label="Scroll Left"
                    class="p-2.5 text-[#F5F0E6] bg-[#1A1A1A] hover:bg-[#E63946] hover:text-white border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] rounded-lg transition-colors">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <button type="button" 
                    onclick="document.getElementById('{{ $id }}').scrollBy({left: 600, behavior: 'smooth'})"
                    aria-label="Scroll Right"
                    class="p-2.5 text-[#F5F0E6] bg-[#1A1A1A] hover:bg-[#E63946] hover:text-white border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] rounded-lg transition-colors">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Scrollable Items Track -->
    <div id="{{ $id }}" class="flex items-stretch gap-4 overflow-x-auto scrollbar-hide py-3 px-1 scroll-smooth">
        @foreach($items as $index => $anime)
            <div class="flex-none w-[170px] sm:w-[195px] md:w-[220px]">
                <x-anime-card :anime="$anime" :showRank="$showRank" :rank="$index + 1" />
            </div>
        @endforeach
    </div>
</div>
