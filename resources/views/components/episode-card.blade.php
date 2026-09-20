@props([
    'animeSlug' => 'solo-leveling',
    'episode' => []
])

<a href="/watch/{{ $animeSlug }}/{{ $episode['number'] }}" class="group flex items-center gap-3 p-2.5 bg-[#151515] hover:bg-[#1A1A1A] border border-zinc-800/80 hover:border-red-500/50 rounded-xl transition-all duration-200">
    
    <!-- Episode Thumbnail -->
    <div class="relative w-28 md:w-32 aspect-video rounded-lg overflow-hidden bg-zinc-900 flex-shrink-0">
        <img referrerpolicy="no-referrer" src="{{ $episode['thumbnail'] ?? 'https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=400&auto=format&fit=crop' }}" 
             alt="{{ $episode['title'] }}" 
             loading="lazy" 
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        
        <!-- Play overlay -->
        <div class="absolute inset-0 bg-black/40 group-hover:bg-red-600/30 transition-colors flex items-center justify-center">
            <div class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center shadow-md transform group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4 fill-current ml-0.5" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </div>
        </div>

        @if(!empty($episode['watched']))
            <div class="absolute top-1.5 left-1.5 px-1.5 py-0.5 bg-red-600 text-white text-[9px] font-bold rounded flex items-center gap-1 shadow-sm">
                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>WATCHED</span>
            </div>
        @endif
    </div>

    <!-- Episode Details -->
    <div class="flex-grow min-w-0">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-bold text-red-400">EP {{ sprintf('%02d', $episode['number']) }}</span>
            <span class="text-[11px] text-zinc-500">{{ $episode['duration'] ?? '24 min' }}</span>
        </div>
        <h4 class="text-sm font-semibold text-zinc-200 group-hover:text-white transition-colors truncate mt-0.5">
            {{ $episode['title'] }}
        </h4>
        <div class="flex items-center gap-2 mt-1.5 text-[10px] text-zinc-400">
            <span class="px-1.5 py-0.5 bg-zinc-900 border border-zinc-800 rounded font-semibold">SUB</span>
            <span class="px-1.5 py-0.5 bg-zinc-900 border border-zinc-800 rounded font-semibold text-red-300">HD 1080p</span>
        </div>
    </div>

</a>
