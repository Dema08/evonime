<x-app-layout :title="$anime['title'] . ' - EVONIME'">

    <!-- Detail Hero Section with Blurred Artwork Background -->
    <div class="relative w-full min-h-[480px] bg-[#070707] overflow-hidden">
        
        <!-- Blurred Background Image -->
        <div class="absolute inset-0 w-full h-full overflow-hidden">
            <img src="{{ $anime['banner'] }}" referrerpolicy="no-referrer" alt="{{ $anime['title'] }}" class="w-full h-full object-cover filter blur-2xl opacity-30 transform scale-110">
            <div class="absolute inset-0 bg-gradient-to-t from-[#070707] via-[#070707]/80 to-[#070707]/60"></div>
        </div>

        <!-- Detail Content Container -->
        <div class="relative z-10 max-w-[1400px] mx-auto px-4 md:px-6 pt-24 md:pt-28 pb-12">
            <div class="flex flex-col md:flex-row items-start gap-8">
                
                <!-- Poster Image Card -->
                <div class="w-48 sm:w-56 md:w-64 aspect-[2/3] rounded-2xl overflow-hidden shadow-2xl border border-zinc-700/60 flex-shrink-0 mx-auto md:mx-0">
                    <img src="{{ $anime['poster'] }}" referrerpolicy="no-referrer" alt="{{ $anime['title'] }}" class="w-full h-full object-cover">
                </div>

                <!-- Info Details -->
                <div class="flex-grow space-y-4 text-center md:text-left">
                    
                    <!-- Japanese Title Subheading -->
                    @if(!empty($anime['japanese_title']))
                        <p class="text-xs font-semibold text-red-400 tracking-wider uppercase">{{ $anime['japanese_title'] }}</p>
                    @endif

                    <!-- Title -->
                    <h1 class="text-3xl md:text-5xl font-extrabold text-white tracking-tight">
                        {{ $anime['title'] }}
                    </h1>

                    <!-- Rating & Meta stats -->
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 text-sm font-medium text-zinc-300">
                        <x-rating-badge :rating="$anime['rating']" />
                        <span>•</span>
                        <span>{{ $anime['year'] }}</span>
                        <span>•</span>
                        <span class="px-2 py-0.5 bg-zinc-800 border border-zinc-700 text-xs font-semibold rounded">{{ $anime['type'] }}</span>
                        <span>•</span>
                        <span>{{ $anime['episodes'] }} Episodes</span>
                        <span>•</span>
                        <span class="text-emerald-400 font-semibold">{{ $anime['status'] }}</span>
                    </div>

                    <!-- Genre chips -->
                    <div class="flex flex-wrap justify-center md:justify-start gap-2 pt-1">
                        @foreach($anime['genres'] as $genre)
                            <x-genre-chip :name="$genre" />
                        @endforeach
                    </div>

                    <!-- Synopsis -->
                    <div class="pt-2 max-w-3xl">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-1">Synopsis</h3>
                        <p class="text-sm md:text-base text-zinc-300 leading-relaxed">
                            {{ $anime['synopsis'] }}
                        </p>
                    </div>

                    <!-- Extra Info Metadata -->
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-6 text-xs text-zinc-400 pt-2 border-t border-zinc-800/80">
                        <div><span class="text-zinc-500">Studio:</span> <span class="text-zinc-200 font-semibold">{{ $anime['studio'] ?? 'A-1 Pictures' }}</span></div>
                        <div><span class="text-zinc-500">Quality:</span> <span class="text-red-400 font-semibold">HD 1080p</span></div>
                        <div><span class="text-zinc-500">Audio:</span> <span class="text-zinc-200 font-semibold">Japanese (Original)</span></div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 pt-4">
                        <a href="/watch/{{ $anime['slug'] }}/1" class="manga-button-primary px-8 py-3.5 text-sm font-extrabold rounded-xl flex items-center gap-2.5 text-white">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            <span>READ & WATCH NOW</span>
                        </a>
                        <button type="button" onclick="window.toggleWatchlist('{{ $anime['slug'] }}', this)" class="manga-button px-6 py-3.5 text-sm font-extrabold rounded-xl flex items-center gap-2.5 text-white">
                            <svg class="w-4.5 h-4.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>ADD TO LIST</span>
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>


    <!-- EPISODES SECTION -->
    <div class="max-w-[1400px] mx-auto px-4 md:px-6 space-y-6 mt-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-800 pb-4">
            <div>
                <h2 class="text-xl md:text-2xl font-extrabold text-white flex items-center gap-2">
                    <span class="w-1.5 h-5 bg-red-600 rounded-full inline-block"></span>
                    Episodes ({{ is_countable($anime['episodes'] ?? null) ? count($anime['episodes']) : ($anime['episodes'] ?? 0) }})
                </h2>
                <p class="text-xs text-zinc-400 mt-0.5">Select an episode to start streaming</p>
            </div>

            <!-- Search Episode Input -->
            <div class="relative w-full sm:w-64">
                <input id="episode-search-input" 
                       type="text" 
                       placeholder="Search episode..." 
                       onkeyup="window.filterEpisodes(this.value)"
                       class="w-full bg-[#151515] border border-zinc-800 text-white placeholder-zinc-500 text-xs rounded-xl pl-9 pr-3 py-2 focus:outline-none focus:border-red-500">
                <svg class="w-3.5 h-3.5 text-zinc-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Episode Cards Grid -->
        <div id="episode-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($episodesList as $ep)
                <div class="episode-item-wrapper" data-title="{{ strtolower($ep['title']) }}" data-ep="{{ $ep['number'] }}">
                    <x-episode-card :animeSlug="$anime['slug']" :episode="$ep" />
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-zinc-500 text-sm font-semibold">
                    Belum ada episode untuk anime ini.
                </div>
            @endforelse
        </div>

        <!-- YOU MAY ALSO LIKE SECTION -->
        <div class="pt-8">
            <x-anime-carousel 
                title="You May Also Like" 
                subtitle="Similar titles recommended for fans of {{ $anime['title'] }}"
                id="detail-recommended"
                :items="$recommended" 
            />
        </div>

    </div>

</x-app-layout>
