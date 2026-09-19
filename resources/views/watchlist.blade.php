<x-app-layout title="My Watchlist - EVONIME">

    <div class="max-w-[1400px] mx-auto px-4 md:px-6 space-y-8 mt-4">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-800 pb-6">
            <div>
                <h1 class="text-2xl md:text-4xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    <span class="w-2 h-7 bg-violet-600 rounded-full inline-block"></span>
                    My Watchlist
                </h1>
                <p class="text-xs md:text-sm text-zinc-400 mt-1">Your bookmarked anime series and movies</p>
            </div>

            <!-- Clear Watchlist Button -->
            <button type="button" onclick="window.clearWatchlist()" class="px-4 py-2 bg-zinc-900 hover:bg-rose-950/40 text-zinc-400 hover:text-rose-400 border border-zinc-800 hover:border-rose-800/50 text-xs font-bold rounded-xl transition-all self-start sm:self-auto">
                Clear All Watchlist
            </button>
        </div>

        <!-- Watchlist Anime Grid -->
        <div id="watchlist-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($animeList as $index => $anime)
                <!-- Demo default items if localStorage is empty -->
                <div class="watchlist-card-demo flex flex-col bg-[#151515] border border-zinc-800 rounded-xl overflow-hidden group">
                    <a href="/anime/{{ $anime['slug'] }}" class="relative aspect-[2/3] bg-zinc-900 block overflow-hidden">
                        <img src="{{ $anime['poster'] }}" alt="{{ $anime['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-2 right-2 px-2 py-0.5 bg-black/80 text-amber-400 text-xs font-bold rounded">
                            ★ {{ number_format($anime['rating'], 1) }}
                        </div>
                    </a>
                    <div class="p-3 flex flex-col justify-between flex-grow">
                        <h3 class="text-xs font-bold text-zinc-100 group-hover:text-violet-400 transition-colors line-clamp-1">
                            {{ $anime['title'] }}
                        </h3>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-zinc-800/80">
                            <a href="/watch/{{ $anime['slug'] }}/1" class="text-[11px] font-bold text-violet-400 hover:underline">Watch Now</a>
                            <button type="button" onclick="window.toggleWatchlist('{{ $anime['slug'] }}', this)" class="text-[11px] text-zinc-500 hover:text-rose-400">Remove</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty state placeholder -->
        <div id="watchlist-empty-msg" class="hidden text-center py-20 bg-[#151515] border border-zinc-800 rounded-2xl space-y-3">
            <svg class="w-16 h-16 text-zinc-700 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
            </svg>
            <h3 class="text-base font-bold text-zinc-300">Your Watchlist is empty</h3>
            <p class="text-xs text-zinc-500">Explore anime and click "+ Add to List" to save items here.</p>
            <a href="/anime" class="inline-block px-6 py-2.5 bg-violet-600 text-white text-xs font-bold rounded-xl hover:bg-violet-500 transition-colors mt-2">
                Explore Anime Now
            </a>
        </div>

    </div>

</x-app-layout>
