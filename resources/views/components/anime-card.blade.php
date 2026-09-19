@props([
    'anime',
    'compact' => false,
    'showRank' => false,
    'rank' => null
])

<div class="group relative flex flex-col w-full h-full bg-[#151515] rounded-xl overflow-hidden border border-zinc-800/60 hover:border-violet-500/50 transition-all duration-300 ease-out hover:shadow-[0_10px_25px_-5px_rgba(124,58,237,0.3)]">
    
    <!-- Image Wrapper with Aspect Ratio -->
    <a href="/anime/{{ $anime['slug'] }}" class="relative w-full aspect-[2/3] overflow-hidden bg-zinc-900 block">
        
        <!-- Poster Image with Hover Zoom -->
        <img src="{{ $anime['poster'] }}" 
             alt="{{ $anime['title'] }}" 
             loading="lazy" 
             class="w-full h-full object-cover object-center transform group-hover:scale-[1.05] transition-transform duration-500 ease-out">

        <!-- Top Badges Overlay -->
        <div class="absolute top-2 left-2 right-2 flex items-center justify-between pointer-events-none z-10">
            <!-- HD & SUB Badge -->
            <div class="flex items-center gap-1">
                @if(!empty($anime['quality']))
                    <span class="px-1.5 py-0.5 bg-black/70 backdrop-blur-md text-[10px] font-extrabold text-violet-300 rounded border border-violet-500/30">
                        {{ $anime['quality'] }}
                    </span>
                @endif
                @if(!empty($anime['sub']))
                    <span class="px-1.5 py-0.5 bg-black/70 backdrop-blur-md text-[10px] font-bold text-zinc-300 rounded border border-zinc-700/50">
                        SUB
                    </span>
                @endif
            </div>

            <!-- Rating Tag -->
            <div class="px-2 py-0.5 bg-black/80 backdrop-blur-md text-amber-400 text-xs font-bold rounded flex items-center gap-1 border border-amber-400/20 shadow-sm">
                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span>{{ number_format($anime['rating'], 1) }}</span>
            </div>
        </div>

        <!-- Hover Overlay with Play Icon -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#070707]/90 via-[#070707]/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
            <div class="w-12 h-12 rounded-full bg-violet-600 text-white flex items-center justify-center transform scale-75 group-hover:scale-100 transition-transform duration-300 shadow-[0_0_20px_rgba(124,58,237,0.8)]">
                <svg class="w-6 h-6 fill-current ml-0.5" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </div>
        </div>

        <!-- Optional Big Subtle Rank Number (Trending Carousel) -->
        @if($showRank && $rank)
            <div class="absolute bottom-1 left-2 text-5xl md:text-6xl font-black text-white/20 group-hover:text-violet-500/30 transition-colors pointer-events-none font-mono leading-none">
                {{ sprintf('%02d', $rank) }}
            </div>
        @endif

    </a>

    <!-- Card Content Info -->
    <div class="p-3 flex flex-col justify-between flex-grow bg-[#151515]">
        <a href="/anime/{{ $anime['slug'] }}" class="block">
            <h3 class="text-sm font-bold text-zinc-100 group-hover:text-violet-400 transition-colors line-clamp-1 leading-snug">
                {{ $anime['title'] }}
            </h3>
        </a>

        <div class="flex items-center justify-between pt-2 text-[11px] text-zinc-400 font-medium">
            <span>{{ $anime['latest_ep'] ?? ($anime['episodes'] . ' Ep') }}</span>
            <span class="text-zinc-500">•</span>
            <span>{{ $anime['year'] }}</span>
        </div>
    </div>

</div>
