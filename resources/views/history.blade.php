<x-app-layout title="Watch History - EVONIME">

    <div class="max-w-[1400px] mx-auto px-4 md:px-6 space-y-8 mt-4">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-800 pb-6">
            <div>
                <h1 class="text-2xl md:text-4xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    <span class="w-2 h-7 bg-violet-600 rounded-full inline-block"></span>
                    Watch History
                </h1>
                <p class="text-xs md:text-sm text-zinc-400 mt-1">Episodes you recently started or finished watching</p>
            </div>

            <!-- Clear History Button -->
            <button type="button" onclick="window.clearHistory()" class="px-4 py-2 bg-zinc-900 hover:bg-rose-950/40 text-zinc-400 hover:text-rose-400 border border-zinc-800 hover:border-rose-800/50 text-xs font-bold rounded-xl transition-all self-start sm:self-auto">
                Clear Watch History
            </button>
        </div>

        <!-- History List Grid -->
        <div id="history-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach(array_slice($animeList, 0, 4) as $anime)
                <div class="flex items-center gap-4 p-3 bg-[#151515] border border-zinc-800/80 rounded-2xl hover:border-violet-500/50 transition-all group">
                    <a href="/watch/{{ $anime['slug'] }}/{{ $anime['continue_ep'] ?? 1 }}" class="relative w-32 aspect-video bg-zinc-900 rounded-xl overflow-hidden flex-shrink-0">
                        <img src="{{ $anime['banner'] }}" alt="{{ $anime['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-black/30 group-hover:bg-violet-600/30 transition-colors flex items-center justify-center">
                            <div class="w-8 h-8 rounded-full bg-violet-600 text-white flex items-center justify-center shadow">
                                <svg class="w-4 h-4 fill-current ml-0.5" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </a>

                    <div class="flex-grow min-w-0 space-y-1.5">
                        <h3 class="text-sm font-bold text-white group-hover:text-violet-400 transition-colors truncate">
                            {{ $anime['title'] }}
                        </h3>
                        <p class="text-xs text-zinc-400">Episode {{ $anime['continue_ep'] ?? 1 }} • Watched 2 hours ago</p>

                        <!-- Progress Line -->
                        <div class="w-full h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-violet-600 rounded-full" style="width: {{ $anime['continue_progress'] ?? 60 }}%"></div>
                        </div>

                        <div class="flex items-center justify-between pt-1 text-[11px]">
                            <a href="/watch/{{ $anime['slug'] }}/{{ $anime['continue_ep'] ?? 1 }}" class="font-bold text-violet-400 hover:underline">
                                Continue ▶
                            </a>
                            <span class="text-zinc-500 font-mono">{{ $anime['continue_progress'] ?? 60 }}%</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

</x-app-layout>
