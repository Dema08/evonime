<x-app-layout title="EVONIME - Your Anime, Your Universe.">

    <!-- 1. HERO CAROUSEL SECTION -->
    <x-hero-banner :items="$heroItems" />

    <div class="max-w-[1400px] mx-auto px-4 md:px-6 space-y-12 mt-6">

        <!-- 2. CONTINUE WATCHING SECTION -->
        @if(count($continueWatching) > 0)
            <section id="continue-watching" class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl md:text-2xl font-extrabold text-white flex items-center gap-2">
                        <span class="w-1.5 h-5 bg-violet-600 rounded-full inline-block"></span>
                        Continue Watching
                    </h2>
                    <span class="text-xs text-zinc-500 font-medium">Demo History</span>
                </div>

                <div class="flex items-stretch gap-4 overflow-x-auto scrollbar-hide py-2 px-0.5">
                    @foreach($continueWatching as $cw)
                        <div class="flex-none w-56 md:w-64 bg-[#151515] border border-zinc-800/80 hover:border-violet-500/50 rounded-xl overflow-hidden group transition-all duration-300">
                            <a href="/watch/{{ $cw['slug'] }}/{{ $cw['continue_ep'] }}" class="block relative aspect-video bg-zinc-900 overflow-hidden">
                                <img src="{{ $cw['banner'] }}" alt="{{ $cw['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                
                                <!-- Play Button Overlay -->
                                <div class="absolute inset-0 bg-black/40 group-hover:bg-violet-600/30 transition-colors flex items-center justify-center">
                                    <div class="w-10 h-10 rounded-full bg-violet-600 text-white flex items-center justify-center transform group-hover:scale-110 transition-transform shadow-lg">
                                        <svg class="w-5 h-5 fill-current ml-0.5" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Progress Bar at bottom -->
                                <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-zinc-900/80">
                                    <div class="h-full bg-gradient-to-r from-violet-600 to-purple-400 rounded-r-full" style="width: {{ $cw['continue_progress'] }}%"></div>
                                </div>
                            </a>

                            <div class="p-3">
                                <h3 class="text-sm font-bold text-zinc-100 group-hover:text-violet-400 transition-colors truncate">
                                    {{ $cw['title'] }}
                                </h3>
                                <div class="flex items-center justify-between mt-1 text-[11px] text-zinc-400">
                                    <span>Episode {{ $cw['continue_ep'] }}</span>
                                    <span class="text-violet-400 font-bold">{{ $cw['continue_progress'] }}%</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif


        <!-- 3. TRENDING NOW CAROUSEL -->
        <x-anime-carousel 
            title="Trending Now" 
            subtitle="#1 to #5 Most Popular This Week"
            id="carousel-trending"
            :items="$trendingNow" 
            :showRank="true" 
        />


        <!-- 4. LATEST EPISODES SECTION (Grid 6 desktop / 4 tablet / 2 mobile) -->
        <section id="latest-episodes" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl md:text-2xl font-extrabold text-white flex items-center gap-2">
                        <span class="w-1.5 h-5 bg-violet-600 rounded-full inline-block"></span>
                        Latest Episodes
                    </h2>
                    <p class="text-xs text-zinc-400 mt-0.5 ml-3.5">Fresh releases updated hourly</p>
                </div>
                <a href="/anime?filter=latest" class="text-xs font-bold text-violet-400 hover:text-violet-300 flex items-center gap-1">
                    View All
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <!-- 6-column Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($latestEpisodes as $anime)
                    <div class="group relative flex flex-col bg-[#151515] rounded-xl overflow-hidden border border-zinc-800/80 hover:border-violet-500/50 transition-all duration-300 hover:shadow-[0_10px_25px_-5px_rgba(124,58,237,0.3)]">
                        <a href="/watch/{{ $anime['slug'] }}/{{ $anime['episodes'] }}" class="relative aspect-[2/3] overflow-hidden bg-zinc-900 block">
                            <img src="{{ $anime['poster'] }}" alt="{{ $anime['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            
                            <!-- Badges -->
                            <div class="absolute top-2 left-2 right-2 flex items-center justify-between">
                                <span class="px-1.5 py-0.5 bg-black/80 text-[10px] font-bold text-violet-300 rounded border border-violet-500/30">
                                    {{ $anime['latest_ep'] }}
                                </span>
                                <span class="px-1.5 py-0.5 bg-black/80 text-amber-400 text-[10px] font-bold rounded flex items-center gap-1">
                                    ★ {{ number_format($anime['rating'], 1) }}
                                </span>
                            </div>

                            <!-- Play overlay -->
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <div class="w-10 h-10 rounded-full bg-violet-600 text-white flex items-center justify-center transform scale-75 group-hover:scale-100 transition-transform shadow-lg">
                                    <svg class="w-5 h-5 fill-current ml-0.5" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <div class="p-2.5 bg-[#151515]">
                            <a href="/anime/{{ $anime['slug'] }}">
                                <h3 class="text-xs font-bold text-zinc-100 group-hover:text-violet-400 transition-colors line-clamp-1">
                                    {{ $anime['title'] }}
                                </h3>
                            </a>
                            <div class="flex items-center justify-between mt-1 text-[10px] text-zinc-400">
                                <span>SUB • HD</span>
                                <span>{{ $anime['latest_date'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>


        <!-- 5. POPULAR ANIME GRID -->
        <section id="popular-anime" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl md:text-2xl font-extrabold text-white flex items-center gap-2">
                        <span class="w-1.5 h-5 bg-violet-600 rounded-full inline-block"></span>
                        Popular Anime
                    </h2>
                    <p class="text-xs text-zinc-400 mt-0.5 ml-3.5">All time favorite series</p>
                </div>
                <a href="/anime" class="text-xs font-bold text-violet-400 hover:text-violet-300 flex items-center gap-1">
                    Explore All
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($popularAnime as $anime)
                    <x-anime-card :anime="$anime" />
                @endforeach
            </div>
        </section>


        <!-- 6. RECOMMENDED (You May Also Like) -->
        <x-anime-carousel 
            title="You May Also Like" 
            subtitle="Curated picks based on your taste"
            id="carousel-recommended"
            :items="$recommended" 
        />


        <!-- 7. EXPLORE GENRES SECTION -->
        <section id="genres" class="space-y-4 py-4">
            <div>
                <h2 class="text-xl md:text-2xl font-extrabold text-white flex items-center gap-2">
                    <span class="w-1.5 h-5 bg-violet-600 rounded-full inline-block"></span>
                    Explore Genres
                </h2>
                <p class="text-xs text-zinc-400 mt-0.5 ml-3.5">Find anime by category</p>
            </div>

            <div class="flex flex-wrap gap-2.5">
                @foreach($genres as $genre)
                    <x-genre-chip :name="$genre" />
                @endforeach
            </div>
        </section>


        <!-- 8. ANIME SCHEDULE SECTION -->
        <section id="schedule" class="space-y-4 py-4">
            <div>
                <h2 class="text-xl md:text-2xl font-extrabold text-white flex items-center gap-2">
                    <span class="w-1.5 h-5 bg-violet-600 rounded-full inline-block"></span>
                    Anime Release Schedule
                </h2>
                <p class="text-xs text-zinc-400 mt-0.5 ml-3.5">Weekly episode broadcast timetable</p>
            </div>

            <!-- Day Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide border-b border-zinc-800 pb-3">
                @foreach($scheduleDays as $index => $day)
                    <button type="button" 
                            data-schedule-tab="{{ $day }}"
                            onclick="window.switchScheduleTab('{{ $day }}')"
                            class="schedule-tab-btn px-5 py-2 text-xs md:text-sm font-bold rounded-xl transition-all duration-200 {{ $index === 5 ? 'bg-violet-600 text-white shadow-[0_0_15px_rgba(124,58,237,0.5)]' : 'bg-[#151515] text-zinc-400 hover:text-white hover:bg-zinc-800 border border-zinc-800' }}">
                        {{ $day }}
                    </button>
                @endforeach
            </div>

            <!-- Schedule Content Grid -->
            <div id="schedule-content" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-2">
                @foreach($animeList as $anime)
                    <div data-schedule-day="{{ $anime['schedule_day'] }}" class="schedule-item-card flex items-center gap-3 p-3 bg-[#151515] border border-zinc-800/80 rounded-xl hover:border-violet-500/50 transition-all">
                        <img src="{{ $anime['poster'] }}" alt="{{ $anime['title'] }}" class="w-12 h-16 object-cover rounded-lg flex-shrink-0">
                        <div class="flex-grow min-w-0">
                            <h4 class="text-sm font-bold text-white truncate">{{ $anime['title'] }}</h4>
                            <p class="text-xs text-zinc-400 mt-0.5">{{ $anime['latest_ep'] ?? 'Episode Release' }}</p>
                            <span class="inline-block text-[10px] text-violet-400 font-bold bg-violet-950/60 px-2 py-0.5 rounded mt-1 border border-violet-800/50">
                                Broadcast {{ $anime['schedule_time'] }} JST
                            </span>
                        </div>
                        <a href="/anime/{{ $anime['slug'] }}" class="p-2 bg-zinc-900 hover:bg-violet-600 text-zinc-400 hover:text-white rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>

    </div>

</x-app-layout>
