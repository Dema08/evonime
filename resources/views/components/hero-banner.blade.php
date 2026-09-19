@props(['items' => []])

<div id="hero-carousel" class="relative w-full h-[520px] sm:h-[620px] md:h-screen md:max-h-[760px] overflow-hidden bg-[#0D0D0D] border-b-2 border-[#F5F0E6] group speed-lines">
    
    <!-- Hero Slides Wrapper -->
    <div id="hero-slides" class="relative w-full h-full">
        @foreach($items as $index => $anime)
            <div data-slide="{{ $index }}" data-slug="{{ $anime['slug'] }}" data-trailer="{{ $anime['trailer_url'] ?? '' }}" class="hero-slide absolute inset-0 w-full h-full transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}">
                
                <!-- Background Image & Gradients (Dark Manga Spread Style) -->
                <div class="absolute inset-0 w-full h-full overflow-hidden">
                    <!-- Image Poster -->
                    <img src="{{ $anime['banner'] }}" onerror="this.src='https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=1600&auto=format&fit=crop'" alt="{{ $anime['title'] }}" class="hero-banner-img w-full h-full object-cover object-center transform scale-105 transition-transform duration-10000 ease-linear filter contrast-125 brightness-90">
                    
                    <!-- Background Video Container (YouTube Embed / MP4 Video) -->
                    @if(!empty($anime['trailer_url']))
                        <div class="hero-video-container absolute inset-0 w-full h-full overflow-hidden opacity-0 transition-opacity duration-1000 pointer-events-none z-10">
                            @if(str_contains($anime['trailer_url'], 'youtube.com') || str_contains($anime['trailer_url'], 'youtu.be'))
                                <iframe data-src="{{ $anime['trailer_url'] }}" class="hero-video-iframe w-[160%] h-[160%] absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 object-cover pointer-events-none border-0" allow="autoplay; encrypted-media"></iframe>
                            @else
                                <video class="hero-video-player w-full h-full object-cover" loop muted playsinline data-src="{{ $anime['trailer_url'] }}"></video>
                            @endif
                        </div>
                    @endif

                    <!-- Manga Panel Gradients -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0D0D0D] via-[#0D0D0D]/70 to-transparent z-15"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#0D0D0D]/95 via-[#0D0D0D]/40 to-transparent z-15"></div>
                    
                    <!-- Halftone Overlay -->
                    <div class="absolute inset-0 halftone-bg opacity-30 pointer-events-none z-15"></div>
                </div>

                <!-- Content Info -->
                <div class="relative z-20 max-w-[1400px] mx-auto h-full px-4 sm:px-6 flex items-center pt-10 md:pt-0">
                    <div class="max-w-2xl space-y-2.5 sm:space-y-4">
                        
                        <!-- Trending Rank Badge -->
                        @if(!empty($anime['trending_rank']))
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 bg-[#E63946] text-white border border-[#F5F0E6] sm:border-2 shadow-[2px_2px_0px_#F5F0E6] sm:shadow-[3px_3px_0px_#F5F0E6] text-[10px] sm:text-xs font-black tracking-widest uppercase transform -rotate-1">
                                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-white animate-pulse"></span>
                                CHAPTER 0{{ $anime['trending_rank'] }} / FEATURED SPREAD
                            </div>
                        @endif

                        <!-- Anime Title (Manga Font) -->
                        <h1 class="text-2xl sm:text-4xl md:text-6xl lg:text-7xl font-black text-[#F5F0E6] tracking-tight leading-none drop-shadow-[2px_2px_0px_#111111]" style="font-family: 'Anton', sans-serif;">
                            {{ $anime['title'] }}
                        </h1>

                        <!-- Anime Metadata -->
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-3 text-[10px] sm:text-xs md:text-sm font-bold text-zinc-300">
                            <span class="flex items-center gap-1 font-black text-[#0D0D0D] bg-amber-400 px-2 py-0.5 sm:px-3 sm:py-1 border border-[#F5F0E6] sm:border-2 shadow-[1px_1px_0px_#F5F0E6] sm:shadow-[2px_2px_0px_#F5F0E6]">
                                ★ {{ number_format($anime['rating'], 1) }}
                            </span>
                            <span class="bg-[#1A1A1A] text-[#F5F0E6] px-2 py-0.5 sm:px-2.5 sm:py-1 border border-[#F5F0E6] sm:border-2 text-[10px] sm:text-xs font-bold">{{ $anime['year'] }}</span>
                            <span class="bg-[#E63946] text-white px-2 py-0.5 sm:px-2.5 sm:py-1 border border-[#F5F0E6] sm:border-2 text-[10px] sm:text-xs font-bold">{{ $anime['type'] }}</span>
                            <span class="bg-[#1A1A1A] text-[#F5F0E6] px-2 py-0.5 sm:px-2.5 sm:py-1 border border-[#F5F0E6] sm:border-2 text-[10px] sm:text-xs font-bold">{{ $anime['episodes'] }} EPISODES</span>
                        </div>

                        <!-- Genre Tags -->
                        <div class="flex flex-wrap gap-1.5 sm:gap-2 pt-0.5">
                            @foreach($anime['genres'] as $genre)
                                <span class="text-[10px] sm:text-xs font-extrabold px-2 py-0.5 sm:px-3 sm:py-1 bg-[#1A1A1A] text-[#F5F0E6] border border-[#F5F0E6] sm:border-2 shadow-[1px_1px_0px_#F5F0E6] sm:shadow-[2px_2px_0px_#F5F0E6]">
                                    #{{ strtoupper($genre) }}
                                </span>
                            @endforeach
                        </div>

                        <!-- Synopsis -->
                        <p class="hero-synopsis text-xs sm:text-sm md:text-base text-zinc-300 line-clamp-2 md:line-clamp-3 leading-relaxed max-w-xl font-medium bg-[#141414]/90 p-2.5 sm:p-3 border-l-4 border-[#E63946] border border-[#F5F0E6] sm:border-2 backdrop-blur-sm shadow-[2px_2px_0px_#F5F0E6] sm:shadow-[3px_3px_0px_#F5F0E6]">
                            {{ $anime['synopsis'] }}
                        </p>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center gap-2.5 sm:gap-4 pt-2 sm:pt-4">
                            <a href="/watch/{{ $anime['slug'] }}/1" class="manga-button-primary px-5 py-2.5 sm:px-8 sm:py-3.5 text-xs sm:text-sm font-extrabold rounded-xl flex items-center gap-2 text-white">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                READ & WATCH NOW
                            </a>

                            @if(!empty($anime['trailer_url']))
                                <button type="button" onclick="window.toggleHeroTrailer(this)" class="hero-play-trailer-btn manga-button px-4 py-2.5 sm:px-6 sm:py-3.5 text-xs sm:text-sm font-extrabold rounded-xl text-amber-400 border-amber-400/40 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
                                    </svg>
                                    <span>PLAY TRAILER</span>
                                </button>
                            @endif

                            <button type="button" data-slug="{{ $anime['slug'] }}" onclick="window.toggleWatchlist('{{ $anime['slug'] }}', this)" class="manga-button px-4 py-2.5 sm:px-6 sm:py-3.5 text-xs sm:text-sm font-extrabold rounded-xl text-white flex items-center gap-2">
                                <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>ADD TO LIST</span>
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        @endforeach
    </div>

    <!-- Carousel Controls & Indicators -->
    <div class="absolute bottom-3 sm:bottom-6 left-0 right-0 z-30">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 flex items-center justify-between">
            
            <!-- Indicators -->
            <div id="hero-dots" class="flex items-center gap-1.5 sm:gap-2">
                @foreach($items as $index => $anime)
                    <button type="button" data-dot="{{ $index }}" aria-label="Slide {{ $index + 1 }}" class="hero-dot h-2.5 sm:h-3 border border-[#F5F0E6] sm:border-2 transition-all duration-300 {{ $index === 0 ? 'w-8 sm:w-10 bg-[#E63946] shadow-[2px_2px_0px_#F5F0E6]' : 'w-2.5 sm:w-3 bg-[#1A1A1A] hover:bg-zinc-700' }}"></button>
                @endforeach
            </div>

            <!-- Arrow Navigation -->
            <div class="flex items-center gap-1.5 sm:gap-2">
                <button id="hero-prev" type="button" aria-label="Previous slide" class="p-1.5 sm:p-2.5 text-[#F5F0E6] bg-[#1A1A1A] hover:bg-[#E63946] hover:text-white border border-[#F5F0E6] sm:border-2 shadow-[2px_2px_0px_#F5F0E6] rounded-lg transition-colors">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button id="hero-next" type="button" aria-label="Next slide" class="p-1.5 sm:p-2.5 text-[#F5F0E6] bg-[#1A1A1A] hover:bg-[#E63946] hover:text-white border border-[#F5F0E6] sm:border-2 shadow-[2px_2px_0px_#F5F0E6] rounded-lg transition-colors">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>

</div>
