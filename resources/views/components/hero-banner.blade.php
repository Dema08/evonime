@props(['items' => []])

<div id="hero-carousel" class="relative w-full h-[520px] md:h-[600px] overflow-hidden bg-[#070707] group">
    
    <!-- Hero Slides Wrapper -->
    <div id="hero-slides" class="relative w-full h-full">
        @foreach($items as $index => $anime)
            <div data-slide="{{ $index }}" class="hero-slide absolute inset-0 w-full h-full transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}">
                
                <!-- Background Image & Gradients -->
                <div class="absolute inset-0 w-full h-full overflow-hidden">
                    <img src="{{ $anime['banner'] }}" alt="{{ $anime['title'] }}" class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-10000 ease-linear">
                    
                    <!-- Multi-directional dark gradients for readability -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#070707] via-[#070707]/60 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#070707] via-[#070707]/80 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-b from-[#070707]/40 via-transparent to-[#070707]"></div>
                </div>

                <!-- Content Info -->
                <div class="relative z-20 max-w-[1400px] mx-auto h-full px-6 flex items-center">
                    <div class="max-w-2xl space-y-4 pt-12 md:pt-0">
                        
                        <!-- Trending Rank Badge -->
                        @if(!empty($anime['trending_rank']))
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-violet-600/30 border border-violet-500/50 backdrop-blur-md rounded-full text-violet-300 text-xs font-bold tracking-wide uppercase">
                                <span class="w-2 h-2 rounded-full bg-violet-400 animate-pulse"></span>
                                #{{ $anime['trending_rank'] }} TRENDING
                            </div>
                        @endif

                        <!-- Anime Title -->
                        <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-none drop-shadow-lg">
                            {{ $anime['title'] }}
                        </h1>

                        <!-- Anime Metadata -->
                        <div class="flex flex-wrap items-center gap-3 text-xs md:text-sm font-medium text-zinc-300">
                            <span class="flex items-center gap-1 font-bold text-amber-400 bg-amber-400/10 px-2.5 py-0.5 rounded border border-amber-400/20">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ number_format($anime['rating'], 1) }}
                            </span>
                            <span>•</span>
                            <span>{{ $anime['year'] }}</span>
                            <span>•</span>
                            <span class="bg-zinc-800/80 px-2 py-0.5 rounded text-xs font-semibold">{{ $anime['type'] }}</span>
                            <span>•</span>
                            <span>{{ $anime['episodes'] }} Episodes</span>
                        </div>

                        <!-- Genre Tags -->
                        <div class="flex flex-wrap gap-2 pt-1">
                            @foreach($anime['genres'] as $genre)
                                <span class="text-xs px-2.5 py-1 bg-zinc-900/80 border border-zinc-800 text-zinc-300 rounded-md">
                                    {{ $genre }}
                                </span>
                            @endforeach
                        </div>

                        <!-- Synopsis -->
                        <p class="text-sm md:text-base text-zinc-400 line-clamp-2 md:line-clamp-3 leading-relaxed max-w-xl">
                            {{ $anime['synopsis'] }}
                        </p>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center gap-4 pt-4">
                            <a href="/watch/{{ $anime['slug'] }}/1" class="px-7 py-3 bg-violet-600 hover:bg-violet-500 text-white text-sm font-bold rounded-full transition-all duration-300 shadow-[0_0_20px_rgba(124,58,237,0.5)] hover:shadow-[0_0_30px_rgba(124,58,237,0.8)] flex items-center gap-2 transform hover:scale-105">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                Watch Now
                            </a>
                            <button type="button" data-slug="{{ $anime['slug'] }}" onclick="window.toggleWatchlist('{{ $anime['slug'] }}', this)" class="px-6 py-3 bg-zinc-900/90 hover:bg-zinc-800 border border-zinc-700/80 text-zinc-200 text-sm font-semibold rounded-full transition-all duration-300 flex items-center gap-2 hover:border-violet-500/50">
                                <svg class="w-4.5 h-4.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Add to List</span>
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        @endforeach
    </div>

    <!-- Carousel Controls & Indicators -->
    <div class="absolute bottom-6 left-0 right-0 z-30">
        <div class="max-w-[1400px] mx-auto px-6 flex items-center justify-between">
            
            <!-- Indicators -->
            <div id="hero-dots" class="flex items-center gap-2">
                @foreach($items as $index => $anime)
                    <button type="button" data-dot="{{ $index }}" aria-label="Slide {{ $index + 1 }}" class="hero-dot h-2 rounded-full transition-all duration-300 {{ $index === 0 ? 'w-8 bg-violet-500 shadow-[0_0_10px_rgba(124,58,237,0.8)]' : 'w-2 bg-zinc-600 hover:bg-zinc-400' }}"></button>
                @endforeach
            </div>

            <!-- Arrow Navigation -->
            <div class="flex items-center gap-2">
                <button id="hero-prev" type="button" aria-label="Previous slide" class="p-2 text-zinc-400 hover:text-white bg-zinc-900/60 hover:bg-zinc-800 border border-zinc-800 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button id="hero-next" type="button" aria-label="Next slide" class="p-2 text-zinc-400 hover:text-white bg-zinc-900/60 hover:bg-zinc-800 border border-zinc-800 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>

</div>
