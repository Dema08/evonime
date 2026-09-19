@props([
    'anime',
    'compact' => false,
    'showRank' => false,
    'rank' => null
])

<div class="group relative flex flex-col w-full h-full bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] hover:shadow-[6px_6px_0px_#F5F0E6] hover:-translate-y-1 transition-all duration-200 overflow-hidden text-[#F5F0E6]">
    
    <!-- Image Wrapper with Aspect Ratio -->
    <a href="/anime/{{ $anime['slug'] }}" class="relative w-full aspect-[2/3] overflow-hidden bg-zinc-900 block border-b-2 border-[#F5F0E6]">
        
        <!-- Poster Image with Hover Zoom -->
        <img src="{{ $anime['poster'] }}" 
             alt="{{ $anime['title'] }}" 
             loading="lazy" 
             class="w-full h-full object-cover object-center transform group-hover:scale-[1.05] transition-transform duration-300 ease-out">

        <!-- Top Badges Overlay -->
        <div class="absolute top-2 left-2 right-2 flex items-center justify-between pointer-events-none z-10">
            <!-- HD & SUB Badge -->
            <div class="flex items-center gap-1">
                @if(!empty($anime['quality']))
                    <span class="px-1.5 py-0.5 bg-[#E63946] text-white text-[10px] font-black border border-[#F5F0E6] shadow-[1px_1px_0px_#F5F0E6]">
                        {{ $anime['quality'] }}
                    </span>
                @endif
                @if(!empty($anime['sub']))
                    <span class="px-1.5 py-0.5 bg-[#1A1A1A] text-[#F5F0E6] text-[10px] font-black border border-[#F5F0E6] shadow-[1px_1px_0px_#F5F0E6]">
                        SUB
                    </span>
                @endif
            </div>

            <!-- Rating Tag -->
            <div class="px-2 py-0.5 bg-amber-400 text-[#0D0D0D] text-xs font-black border border-[#F5F0E6] shadow-[1px_1px_0px_#F5F0E6] flex items-center gap-1">
                <span>★ {{ number_format($anime['rating'], 1) }}</span>
            </div>
        </div>

        <!-- Hover Overlay with Play Icon -->
        <div class="absolute inset-0 bg-[#0D0D0D]/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center halftone-bg">
            <div class="w-12 h-12 bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] flex items-center justify-center transform scale-75 group-hover:scale-100 transition-transform duration-200">
                <svg class="w-6 h-6 fill-current ml-0.5" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </div>
        </div>

        <!-- Optional Big Subtle Rank Number (Trending Carousel) -->
        @if($showRank && $rank)
            <div class="absolute bottom-1 right-2 text-6xl font-black text-white/85 group-hover:text-[#E63946] transition-colors pointer-events-none font-mono leading-none drop-shadow-[2px_2px_0px_#0D0D0D]" style="font-family: 'Anton', sans-serif;">
                {{ sprintf('%02d', $rank) }}
            </div>
        @endif

    </a>

    <!-- Card Content Info -->
    <div class="p-3 flex flex-col justify-between flex-grow bg-[#1A1A1A]">
        <a href="/anime/{{ $anime['slug'] }}" class="block">
            <h3 class="text-xs md:text-sm font-black text-[#F5F0E6] group-hover:text-[#E63946] transition-colors line-clamp-1 leading-snug">
                {{ $anime['title'] }}
            </h3>
        </a>

        <div class="flex items-center justify-between pt-2 text-[10px] md:text-[11px] text-zinc-400 font-extrabold border-t border-zinc-800 mt-2">
            <span class="bg-[#141414] px-1.5 py-0.5 border border-[#F5F0E6] text-[#F5F0E6]">{{ $anime['latest_ep'] ?? ($anime['episodes'] . ' Ep') }}</span>
            <span>{{ $anime['year'] }}</span>
        </div>
    </div>

</div>
