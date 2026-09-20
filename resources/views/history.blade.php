<x-app-layout title="Watch History - EVONIME">

    <div class="max-w-[1400px] mx-auto px-4 md:px-6 space-y-8 mt-4 pt-24 md:pt-28 text-[#F5F0E6]">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-[#F5F0E6] pb-6">
            <div>
                <h1 class="text-2xl md:text-4xl font-black text-[#F5F0E6] tracking-tight flex items-center gap-3" style="font-family: 'Anton', sans-serif;">
                    <span class="w-2.5 h-7 bg-[#E63946] border border-[#F5F0E6] inline-block"></span>
                    WATCH HISTORY CHAPTERS
                </h1>
                <p class="text-xs md:text-sm text-zinc-400 font-bold mt-1">Episodes you recently started or finished watching</p>
            </div>

            <!-- Clear History Button -->
            <button type="button" onclick="window.clearHistory()" class="manga-button px-4 py-2 text-xs font-black rounded-lg bg-[#1A1A1A] hover:bg-[#E63946] hover:text-white transition-all self-start sm:self-auto">
                CLEAR HISTORY
            </button>
        </div>

        <!-- History List Grid -->
        <div id="history-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach(array_slice($animeList, 0, 4) as $anime)
                <div class="flex items-center gap-4 p-3 bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] hover:shadow-[5px_5px_0px_#F5F0E6] transition-all group">
                    <a href="/watch/{{ $anime['slug'] }}/{{ $anime['continue_ep'] ?? 1 }}" class="relative w-32 aspect-video bg-zinc-900 border border-[#F5F0E6] overflow-hidden flex-shrink-0">
                        <img referrerpolicy="no-referrer" src="{{ $anime['banner'] }}" alt="{{ $anime['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-[#0D0D0D]/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center halftone-bg">
                            <div class="w-8 h-8 bg-[#E63946] text-white border border-[#F5F0E6] flex items-center justify-center shadow">
                                <svg class="w-4 h-4 fill-current ml-0.5" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <div class="flex-grow min-w-0 space-y-1.5">
                        <h3 class="text-sm font-black text-[#F5F0E6] group-hover:text-[#E63946] transition-colors truncate">
                            {{ $anime['title'] }}
                        </h3>
                        <p class="text-xs text-zinc-400 font-bold">Episode {{ $anime['continue_ep'] ?? 1 }} • Recent</p>

                        <!-- Progress Line -->
                        <div class="w-full h-2 bg-[#141414] border border-[#F5F0E6] overflow-hidden">
                            <div class="h-full bg-[#E63946]" style="width: {{ $anime['continue_progress'] ?? 60 }}%"></div>
                        </div>

                        <div class="flex items-center justify-between pt-1 text-[11px]">
                            <a href="/watch/{{ $anime['slug'] }}/{{ $anime['continue_ep'] ?? 1 }}" class="font-black text-[#E63946] hover:underline">
                                CONTINUE ▶
                            </a>
                            <span class="text-zinc-400 font-mono font-bold">{{ $anime['continue_progress'] ?? 60 }}%</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

</x-app-layout>
