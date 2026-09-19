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
                    <div class="hero-video-container absolute inset-0 w-full h-full overflow-hidden opacity-0 transition-opacity duration-1000 pointer-events-none z-10">
                        <iframe data-src="{{ !empty($anime['trailer_url']) && (str_contains($anime['trailer_url'], 'youtube.com') || str_contains($anime['trailer_url'], 'youtu.be')) ? $anime['trailer_url'] : '' }}" class="hero-video-iframe w-[160%] h-[160%] absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 object-cover pointer-events-none border-0 {{ !empty($anime['trailer_url']) && (str_contains($anime['trailer_url'], 'youtube.com') || str_contains($anime['trailer_url'], 'youtu.be')) ? '' : 'hidden' }}" allow="autoplay; encrypted-media"></iframe>
                        <video class="hero-video-player w-full h-full object-cover {{ !empty($anime['trailer_url']) && !(str_contains($anime['trailer_url'], 'youtube.com') || str_contains($anime['trailer_url'], 'youtu.be')) ? '' : 'hidden' }}" muted playsinline data-src="{{ $anime['trailer_url'] ?? '' }}"></video>
                    </div>

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

    <!-- Carousel Controls & Indicators (Netflix / IDLIX Style Layout) -->
    <div class="absolute bottom-3 sm:bottom-6 left-0 right-0 z-30">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 flex items-center justify-between">
            
            <!-- Indicators -->
            <div id="hero-dots" class="flex items-center gap-1.5 sm:gap-2">
                @foreach($items as $index => $anime)
                    <button type="button" data-dot="{{ $index }}" aria-label="Slide {{ $index + 1 }}" class="hero-dot h-2.5 sm:h-3 border border-[#F5F0E6] sm:border-2 transition-all duration-300 {{ $index === 0 ? 'w-8 sm:w-10 bg-[#E63946] shadow-[2px_2px_0px_#F5F0E6]' : 'w-2.5 sm:w-3 bg-[#1A1A1A] hover:bg-zinc-700' }}"></button>
                @endforeach
            </div>

            <!-- Right Navigation: Sleek Netflix Mute Button & Arrow Controls -->
            <div class="flex items-center gap-3">
                <!-- Sleek Netflix / IDLIX Mute Button -->
                <button type="button" onclick="window.toggleHeroVideoMute()" aria-label="Toggle trailer audio" class="hero-video-controls-bar w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-[#1A1A1A]/80 hover:bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] sm:shadow-[3px_3px_0px_#F5F0E6] flex items-center justify-center transition-all backdrop-blur-md cursor-pointer hidden">
                    <svg class="hero-muted-icon w-4 h-4 sm:w-5 sm:h-5 fill-current" viewBox="0 0 24 24"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3z"/></svg>
                    <svg class="hero-sound-icon w-4 h-4 sm:w-5 sm:h-5 fill-current hidden" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
                </button>

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

</div>
