<div id="search-modal" class="fixed inset-0 z-50 hidden flex items-start justify-center pt-16 md:pt-24 px-4 bg-black/80 backdrop-blur-md transition-opacity duration-300">
    
    <!-- Modal Container -->
    <div class="relative w-full max-w-2xl bg-[#101010] border border-zinc-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[80vh] transform transition-all scale-95 opacity-0 duration-300" id="search-modal-content">
        
        <!-- Search Input Bar -->
        <div class="relative p-4 border-b border-zinc-800/80 flex items-center gap-3">
            <svg class="w-5 h-5 text-violet-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input id="search-input" 
                   type="text" 
                   placeholder="Search anime title, genre, studio..." 
                   autocomplete="off"
                   class="w-full bg-transparent text-white placeholder-zinc-500 text-sm md:text-base font-medium focus:outline-none">
            <button id="search-close-btn" type="button" class="px-2 py-1 bg-zinc-900 hover:bg-zinc-800 text-zinc-400 text-xs rounded border border-zinc-800">
                ESC
            </button>
        </div>

        <!-- Scrollable Search Body -->
        <div class="p-5 overflow-y-auto flex-grow space-y-6">
            
            <!-- Popular Searches Suggestions -->
            <div id="popular-searches-section">
                <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Popular Searches
                </h4>
                <div class="flex flex-wrap gap-2">
                    @foreach(['Solo Leveling', 'One Piece', 'Jujutsu Kaisen', 'Demon Slayer', 'Frieren', 'Bleach', 'Naruto'] as $pop)
                        <button type="button" 
                                onclick="window.setSearchQuery('{{ $pop }}')" 
                                class="px-3 py-1.5 bg-[#1A1A1A] hover:bg-violet-600/30 border border-zinc-800 hover:border-violet-500/50 text-zinc-300 hover:text-white text-xs font-semibold rounded-lg transition-all">
                            {{ $pop }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Live Search Results List -->
            <div id="search-results-section">
                <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-3 flex items-center justify-between">
                    <span>Results</span>
                    <span id="results-count" class="text-violet-400 font-normal"></span>
                </h4>
                <div id="search-results-list" class="space-y-2">
                    <!-- Dynamic JS Items will be injected here -->
                    <div class="text-center py-8 text-zinc-500 text-sm">
                        Type anime name to search live...
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Footer Hint -->
        <div class="px-5 py-3 bg-[#0B0B0B] border-t border-zinc-800/60 flex items-center justify-between text-[11px] text-zinc-500">
            <span>Press <kbd class="px-1.5 py-0.5 bg-zinc-800 text-zinc-300 rounded text-[10px]">Ctrl + K</kbd> to search anytime</span>
            <span>EVONIME Realtime Frontend Search</span>
        </div>

    </div>

</div>
