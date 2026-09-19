<x-app-layout title="My Watchlist - EVONIME">

    <div class="max-w-[1400px] mx-auto px-4 md:px-6 space-y-8 mt-4 text-[#F5F0E6]">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-[#F5F0E6] pb-6">
            <div>
                <h1 class="text-2xl md:text-4xl font-black text-[#F5F0E6] tracking-tight flex items-center gap-3" style="font-family: 'Anton', sans-serif;">
                    <span class="w-2.5 h-7 bg-[#E63946] border border-[#F5F0E6] inline-block"></span>
                    MY MANGA WATCHLIST
                </h1>
                <p class="text-xs md:text-sm text-zinc-400 font-bold mt-1">Your bookmarked anime series and movies</p>
            </div>

            <!-- Clear Watchlist Button -->
            <button type="button" onclick="window.clearWatchlist()" class="manga-button px-4 py-2 text-xs font-black rounded-lg bg-[#1A1A1A] hover:bg-[#E63946] hover:text-white transition-all self-start sm:self-auto">
                CLEAR ALL WATCHLIST
            </button>
        </div>

        <!-- Watchlist Anime Grid -->
        <div id="watchlist-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($animeList as $index => $anime)
                <!-- Demo default items if localStorage is empty -->
                <div class="watchlist-card-demo flex flex-col bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] hover:shadow-[6px_6px_0px_#F5F0E6] transition-all overflow-hidden group text-[#F5F0E6]">
                    <a href="/anime/{{ $anime['slug'] }}" class="relative aspect-[2/3] bg-zinc-900 block overflow-hidden border-b-2 border-[#F5F0E6]">
                        <img src="{{ $anime['poster'] }}" alt="{{ $anime['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-2 right-2 px-2 py-0.5 bg-amber-400 text-[#0D0D0D] text-xs font-black border border-[#F5F0E6] shadow-[1px_1px_0px_#F5F0E6]">
                            ★ {{ number_format($anime['rating'], 1) }}
                        </div>
                    </a>
                    <div class="p-3 flex flex-col justify-between flex-grow bg-[#1A1A1A]">
                        <h3 class="text-xs font-black text-[#F5F0E6] group-hover:text-[#E63946] transition-colors line-clamp-1">
                            {{ $anime['title'] }}
                        </h3>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-zinc-800">
                            <a href="/watch/{{ $anime['slug'] }}/1" class="text-[11px] font-black text-[#E63946] hover:underline">WATCH NOW</a>
                            <button type="button" onclick="window.toggleWatchlist('{{ $anime['slug'] }}', this)" class="text-[11px] font-bold text-zinc-400 hover:text-[#E63946]">REMOVE</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty state placeholder -->
        <div id="watchlist-empty-msg" class="hidden text-center py-20 bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] rounded-2xl space-y-3">
            <svg class="w-16 h-16 text-zinc-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
            </svg>
            <h3 class="text-base font-black text-[#F5F0E6]">Your Watchlist is empty</h3>
            <p class="text-xs text-zinc-400 font-bold">Explore anime and click "+ Add to List" to save items here.</p>
            <a href="/anime" class="inline-block px-6 py-2.5 bg-[#E63946] text-white text-xs font-black border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] rounded-lg hover:bg-red-700 transition-colors mt-2">
                EXPLORE ANIME NOW
            </a>
        </div>

    </div>

</x-app-layout>
