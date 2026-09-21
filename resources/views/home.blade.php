<x-app-layout title="EVONIME - Dark Manga Paper Anime Universe">

    <!-- 1. HERO CAROUSEL SECTION -->
    <x-hero-banner :items="$heroItems" />

    <div class="max-w-[1400px] mx-auto px-4 md:px-6 space-y-8 sm:space-y-16 mt-6 sm:mt-8 text-[#F5F0E6]">

        <!-- 2. CONTINUE WATCHING SECTION -->
        @if(count($continueWatching) > 0)
            <section id="continue-watching" class="space-y-4">
                <div class="flex items-center justify-between border-b-2 border-[#F5F0E6] pb-2">
                    <h2 class="text-xl md:text-2xl font-black text-[#F5F0E6] flex items-center gap-2" style="font-family: 'Anton', sans-serif;">
                        <span class="w-2.5 h-5 bg-[#E63946] border border-[#F5F0E6] inline-block"></span>
                        LANJUT TONTON
                    </h2>
                    <span class="text-xs font-black bg-[#1A1A1A] text-[#F5F0E6] px-2.5 py-1 border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6]">RESUME CHAPTER</span>
                </div>

                <div class="flex items-stretch gap-4 overflow-x-auto scrollbar-hide py-2 px-0.5">
                    @foreach($continueWatching as $cw)
                        <div class="flex-none w-52 sm:w-64 md:w-72 bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] sm:shadow-[4px_4px_0px_#F5F0E6] hover:shadow-[5px_5px_0px_#F5F0E6] hover:-translate-y-1 transition-all duration-200 overflow-hidden group text-[#F5F0E6]">
                            <a href="/watch/{{ $cw['slug'] }}/{{ $cw['continue_ep'] }}" class="block relative aspect-video bg-zinc-900 overflow-hidden border-b-2 border-[#F5F0E6]">
                                <img referrerpolicy="no-referrer" src="{{ $cw['banner'] }}" alt="{{ $cw['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                
                                <!-- Play Button Overlay -->
                                <div class="absolute inset-0 bg-[#0D0D0D]/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center halftone-bg">
                                    <div class="w-10 h-10 bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] flex items-center justify-center transform group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 fill-current ml-0.5" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Progress Bar at bottom -->
                                <div class="absolute bottom-0 left-0 right-0 h-2 bg-[#141414] border-t-2 border-[#F5F0E6]">
                                    <div class="h-full bg-[#E63946]" style="width: {{ $cw['continue_progress'] }}%"></div>
                                </div>
                            </a>

                            <div class="p-3 bg-[#1A1A1A]">
                                <h3 class="text-xs md:text-sm font-black text-[#F5F0E6] group-hover:text-[#E63946] transition-colors truncate">
                                    {{ $cw['title'] }}
                                </h3>
                                <div class="flex items-center justify-between mt-1.5 text-[11px] text-zinc-400 font-extrabold border-t border-zinc-800 pt-1.5">
                                    <span>Episode {{ $cw['continue_ep'] }}</span>
                                    <span class="text-[#E63946] font-black">
                                        {{ $cw['continue_progress'] > 0 ? $cw['continue_progress'] . '% DONE' : 'LANJUT ▶' }}
                                    </span>
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
            subtitle="#1 to #5 Most Popular Weekly Releases"
            id="carousel-trending"
            :items="$trendingNow" 
            :showRank="true" 
        />


        <!-- 4. LATEST EPISODES SECTION -->
        <section id="latest-episodes" class="space-y-4">
            <div class="flex items-center justify-between border-b-2 border-[#F5F0E6] pb-2">
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-[#F5F0E6] flex items-center gap-2" style="font-family: 'Anton', sans-serif;">
                        <span class="w-2.5 h-5 bg-[#E63946] border border-[#F5F0E6] inline-block"></span>
                        LATEST CHAPTERS & EPISODES
                    </h2>
                    <p class="text-xs text-zinc-400 font-bold mt-0.5 ml-4.5">Fresh releases updated hourly on dark manga paper</p>
                </div>
                <a href="/anime?filter=latest" class="manga-button px-4 py-1.5 text-xs rounded-lg flex items-center gap-1">
                    VIEW ALL
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <!-- 6-column Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2.5 sm:gap-4">
                @foreach($latestEpisodes as $anime)
                    <div class="group relative flex flex-col bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] sm:shadow-[4px_4px_0px_#F5F0E6] hover:shadow-[5px_5px_0px_#F5F0E6] hover:-translate-y-1 transition-all duration-200 overflow-hidden text-[#F5F0E6]">
                        <a href="/watch/{{ $anime['slug'] }}/{{ $anime['episodes'] }}" class="relative aspect-[2/3] overflow-hidden bg-zinc-900 block border-b-2 border-[#F5F0E6]">
                            <img referrerpolicy="no-referrer" src="{{ $anime['poster'] }}" alt="{{ $anime['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            
                            <!-- Badges -->
                            <div class="absolute top-2 left-2 right-2 flex items-center justify-between">
                                <span class="px-1.5 py-0.5 bg-[#E63946] text-white text-[10px] font-black border border-[#F5F0E6] shadow-[1px_1px_0px_#F5F0E6]">
                                    {{ $anime['latest_ep'] }}
                                </span>
                                <span class="px-1.5 py-0.5 bg-amber-400 text-[#0D0D0D] text-[10px] font-black border border-[#F5F0E6] shadow-[1px_1px_0px_#F5F0E6]">
                                    ★ {{ number_format($anime['rating'], 1) }}
                                </span>
                            </div>

                            <!-- Play overlay -->
                            <div class="absolute inset-0 bg-[#0D0D0D]/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center halftone-bg">
                                <div class="w-10 h-10 bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] flex items-center justify-center transform scale-75 group-hover:scale-100 transition-transform">
                                    <svg class="w-5 h-5 fill-current ml-0.5" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <div class="p-2.5 bg-[#1A1A1A]">
                            <a href="/anime/{{ $anime['slug'] }}">
                                <h3 class="text-xs font-black text-[#F5F0E6] group-hover:text-[#E63946] transition-colors line-clamp-1">
                                    {{ $anime['title'] }}
                                </h3>
                            </a>
                            <div class="flex items-center justify-between mt-1.5 text-[10px] text-zinc-400 font-extrabold border-t border-zinc-800 pt-1">
                                <span class="bg-[#141414] px-1 border border-[#F5F0E6]">SUB • HD</span>
                                <span>{{ $anime['latest_date'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>


        <!-- 5. POPULAR ANIME GRID -->
        <section id="popular-anime" class="space-y-4">
            <div class="flex items-center justify-between border-b-2 border-[#F5F0E6] pb-2">
                <div>
                    <h2 class="text-xl md:text-2xl font-black text-[#F5F0E6] flex items-center gap-2" style="font-family: 'Anton', sans-serif;">
                        <span class="w-2.5 h-5 bg-[#E63946] border border-[#F5F0E6] inline-block"></span>
                        POPULAR ANIME MASTERPIECES
                    </h2>
                    <p class="text-xs text-zinc-400 font-bold mt-0.5 ml-4.5">All time classic legendary series</p>
                </div>
                <a href="/anime" class="manga-button px-4 py-1.5 text-xs rounded-lg flex items-center gap-1">
                    EXPLORE ALL
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2.5 sm:gap-4">
                @foreach($popularAnime as $anime)
                    <x-anime-card :anime="$anime" />
                @endforeach
            </div>
        </section>


        <!-- 6. RECOMMENDED (You May Also Like) -->
        <x-anime-carousel 
            title="You May Also Like" 
            subtitle="Curated picks based on manga reader preferences"
            id="carousel-recommended"
            :items="$recommended" 
        />


        <!-- 7. EXPLORE GENRES SECTION -->
        <section id="genres" class="space-y-4 py-4 sm:py-6 bg-[#141414] p-3.5 sm:p-6 border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] sm:shadow-[4px_4px_0px_#F5F0E6]">
            <div class="border-b-2 border-[#F5F0E6] pb-2">
                <h2 class="text-xl md:text-2xl font-black text-[#F5F0E6] flex items-center gap-2" style="font-family: 'Anton', sans-serif;">
                    <span class="w-2.5 h-5 bg-[#E63946] border border-[#F5F0E6] inline-block"></span>
                    EXPLORE GENRES & THEMES
                </h2>
                <p class="text-xs text-zinc-400 font-bold mt-0.5 ml-4.5">Select a category to jump straight into manga panels</p>
            </div>

            <div class="flex flex-wrap gap-2.5 pt-2">
                @foreach($genres as $genre)
                    <a href="/anime?genre={{ urlencode($genre) }}" class="manga-button px-4 py-2 text-xs font-black uppercase text-[#F5F0E6] rounded-lg bg-[#1A1A1A] hover:bg-[#E63946] hover:text-white transition-all">
                        #{{ $genre }}
                    </a>
                @endforeach
            </div>
        </section>


        <!-- 8. ANIME SCHEDULE SECTION -->
        <section id="schedule" class="space-y-4 py-4 sm:py-6 bg-[#141414] p-3.5 sm:p-6 border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] sm:shadow-[4px_4px_0px_#F5F0E6]">
            <div class="border-b-2 border-[#F5F0E6] pb-2">
                <h2 class="text-xl md:text-2xl font-black text-[#F5F0E6] flex items-center gap-2" style="font-family: 'Anton', sans-serif;">
                    <span class="w-2.5 h-5 bg-[#E63946] border border-[#F5F0E6] inline-block"></span>
                    ANIME RELEASE TIMETABLE
                </h2>
                <p class="text-xs text-zinc-400 font-bold mt-0.5 ml-4.5">Weekly episode broadcast schedule JST</p>
            </div>

            <!-- Day Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide border-b-2 border-[#F5F0E6] pb-3 pt-2">
                @foreach($scheduleDays as $index => $day)
                    <button type="button" 
                            data-schedule-tab="{{ $day }}"
                            onclick="window.switchScheduleTab('{{ $day }}')"
                            class="schedule-tab-btn px-5 py-2.5 text-xs md:text-sm font-black uppercase rounded-lg transition-all duration-150 {{ $index === 5 ? 'bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6]' : 'bg-[#1A1A1A] text-[#F5F0E6] border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] hover:bg-zinc-800' }}">
                        {{ $day }}
                    </button>
                @endforeach
            </div>

            <!-- Schedule Content Grid -->
            <div id="schedule-content" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-4">
                @foreach($animeList as $anime)
                    <div data-schedule-day="{{ $anime['schedule_day'] }}" class="schedule-item-card flex items-center gap-3 p-3 bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] hover:shadow-[5px_5px_0px_#F5F0E6] transition-all text-[#F5F0E6]">
                        <img referrerpolicy="no-referrer" src="{{ $anime['poster'] }}" alt="{{ $anime['title'] }}" class="w-14 h-18 object-cover border-2 border-[#F5F0E6] flex-shrink-0">
                        <div class="flex-grow min-w-0">
                            <h4 class="text-sm font-black text-[#F5F0E6] truncate">{{ $anime['title'] }}</h4>
                            <p class="text-xs text-zinc-400 font-bold mt-0.5">{{ $anime['latest_ep'] ?? 'Episode Release' }}</p>
                            <span class="inline-block text-[10px] text-white font-black bg-[#E63946] px-2 py-0.5 border border-[#F5F0E6] mt-1 shadow-[1px_1px_0px_#F5F0E6]">
                                JST {{ $anime['schedule_time'] }}
                            </span>
                        </div>
                        <a href="/anime/{{ $anime['slug'] }}" class="p-2.5 bg-[#141414] hover:bg-[#E63946] text-[#F5F0E6] hover:text-white border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>

    </div>

</x-app-layout>
