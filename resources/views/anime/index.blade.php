<x-app-layout title="Explore Anime - EVONIME">

    <div class="max-w-[1400px] mx-auto px-4 md:px-6 space-y-8 mt-4">
        
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-800 pb-6">
            <div>
                <h1 class="text-2xl md:text-4xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    <span class="w-2 h-7 bg-red-600 rounded-full inline-block"></span>
                    Explore Anime
                </h1>
                <p class="text-xs md:text-sm text-zinc-400 mt-1">Browse and filter thousands of anime series and movies</p>
            </div>

            <!-- Realtime Explore Search Bar -->
            <div class="relative w-full md:w-80">
                <input id="explore-search-input" 
                       type="text" 
                       placeholder="Search anime..." 
                       class="w-full bg-[#151515] border border-zinc-800 focus:border-red-500 text-white placeholder-zinc-500 text-sm rounded-xl pl-10 pr-4 py-2.5 focus:outline-none transition-colors">
                <svg class="w-4 h-4 text-zinc-500 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Filter Bar & Dropdowns -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-[#101010] p-4 rounded-2xl border border-zinc-800/80">
            
            <!-- Quick Filter Buttons -->
            <div class="flex items-center gap-2.5 overflow-x-auto scrollbar-hide">
                <button type="button" onclick="window.filterExplore('all')" class="explore-tab-btn manga-button-primary px-4 py-2 text-xs font-black rounded-xl" data-filter="all">All</button>
                <button type="button" onclick="window.filterExplore('popular')" class="explore-tab-btn manga-button px-4 py-2 text-xs font-black rounded-xl" data-filter="popular">Popular</button>
                <button type="button" onclick="window.filterExplore('latest')" class="explore-tab-btn manga-button px-4 py-2 text-xs font-black rounded-xl" data-filter="latest">Latest</button>
                <button type="button" onclick="window.filterExplore('rating')" class="explore-tab-btn manga-button px-4 py-2 text-xs font-black rounded-xl" data-filter="rating">Top Rated</button>
            </div>

            <!-- Dropdown Filters -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <!-- Genre Dropdown -->
                <select id="filter-genre" onchange="window.applyDropdownFilters()" class="bg-[#1A1A1A] border border-zinc-800 text-zinc-300 text-xs font-medium rounded-xl px-3 py-2 focus:outline-none focus:border-red-500">
                    <option value="">All Genres</option>
                    @foreach($genres as $genre)
                        <option value="{{ strtolower($genre) }}">{{ $genre }}</option>
                    @endforeach
                </select>

                <!-- Year Dropdown -->
                <select id="filter-year" onchange="window.applyDropdownFilters()" class="bg-[#1A1A1A] border border-zinc-800 text-zinc-300 text-xs font-medium rounded-xl px-3 py-2 focus:outline-none focus:border-red-500">
                    <option value="">All Years</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                    <option value="2017">2017</option>
                    <option value="2015">2015</option>
                    <option value="2011">2011</option>
                    <option value="2007">2007</option>
                    <option value="1999">1999</option>
                </select>

                <!-- Status Dropdown -->
                <select id="filter-status" onchange="window.applyDropdownFilters()" class="bg-[#1A1A1A] border border-zinc-800 text-zinc-300 text-xs font-medium rounded-xl px-3 py-2 focus:outline-none focus:border-red-500">
                    <option value="">All Status</option>
                    <option value="Ongoing">Ongoing</option>
                    <option value="Completed">Completed</option>
                </select>

                <!-- Type Dropdown -->
                <select id="filter-type" onchange="window.applyDropdownFilters()" class="bg-[#1A1A1A] border border-zinc-800 text-zinc-300 text-xs font-medium rounded-xl px-3 py-2 focus:outline-none focus:border-red-500">
                    <option value="">All Types</option>
                    <option value="TV">TV Series</option>
                    <option value="Movie">Movie</option>
                </select>
            </div>

        </div>

        <!-- Anime Grid Container (6 cols Desktop / 4 cols Tablet / 2 cols Mobile) -->
        <div id="explore-anime-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($animeList as $anime)
                <div class="explore-card-item" 
                     data-title="{{ strtolower($anime['title']) }}"
                     data-genres="{{ strtolower(implode(',', $anime['genres'])) }}"
                     data-year="{{ $anime['year'] }}"
                     data-status="{{ $anime['status'] }}"
                     data-type="{{ $anime['type'] }}"
                     data-rating="{{ $anime['rating'] }}">
                    <x-anime-card :anime="$anime" />
                </div>
            @endforeach
        </div>

        <!-- Empty state placeholder -->
        <div id="no-results-msg" class="hidden text-center py-16 bg-[#151515] border border-zinc-800 rounded-2xl space-y-3">
            <svg class="w-12 h-12 text-zinc-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-base font-bold text-zinc-300">No anime found</h3>
            <p class="text-xs text-zinc-500">Try adjusting your filters or search keywords.</p>
        </div>

    </div>

</x-app-layout>
