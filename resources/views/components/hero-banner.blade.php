@props(['items' => []])

<div id="hero-carousel" class="relative w-full h-screen max-h-[760px] overflow-hidden bg-[#0D0D0D] border-b-2 border-[#F5F0E6] group speed-lines">
    
    <!-- Hero Slides Wrapper -->
    <div id="hero-slides" class="relative w-full h-full">
        @foreach($items as $index => $anime)
            <div data-slide="{{ $index }}" data-slug="{{ $anime['slug'] }}" class="hero-slide absolute inset-0 w-full h-full transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}">
                
                <!-- Background Image & Gradients (Dark Manga Spread Style) -->
                <div class="absolute inset-0 w-full h-full overflow-hidden">
                    <img src="{{ $anime['banner'] }}" onerror="this.src='https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=1600&auto=format&fit=crop'" alt="{{ $anime['title'] }}" class="hero-banner-img w-full h-full object-cover object-center transform scale-105 transition-transform duration-10000 ease-linear filter contrast-125 brightness-90">
                    
                    <!-- Manga Panel Gradients -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0D0D0D] via-[#0D0D0D]/70 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#0D0D0D]/95 via-[#0D0D0D]/40 to-transparent"></div>
                    
                    <!-- Halftone Overlay -->
                    <div class="absolute inset-0 halftone-bg opacity-30 pointer-events-none"></div>
                </div>

                <!-- Content Info -->
                <div class="relative z-20 max-w-[1400px] mx-auto h-full px-6 flex items-center">
                    <div class="max-w-2xl space-y-4">
                        
                        <!-- Trending Rank Badge -->
                        @if(!empty($anime['trending_rank']))
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] text-xs font-black tracking-widest uppercase transform -rotate-1">
                                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                CHAPTER 0{{ $anime['trending_rank'] }} / FEATURED SPREAD
                            </div>
                        @endif

                        <!-- Anime Title (Manga Font) -->
                        <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-[#F5F0E6] tracking-tight leading-none drop-shadow-[3px_3px_0px_#111111]" style="font-family: 'Anton', sans-serif;">
                            {{ $anime['title'] }}
                        </h1>

                        <!-- Anime Metadata -->
                        <div class="flex flex-wrap items-center gap-3 text-xs md:text-sm font-bold text-zinc-300">
                            <span class="flex items-center gap-1 font-black text-[#0D0D0D] bg-amber-400 px-3 py-1 border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6]">
                                ★ {{ number_format($anime['rating'], 1) }}
                            </span>
                            <span class="bg-[#1A1A1A] text-[#F5F0E6] px-2.5 py-1 border-2 border-[#F5F0E6] text-xs font-bold">{{ $anime['year'] }}</span>
                            <span class="bg-[#E63946] text-white px-2.5 py-1 border-2 border-[#F5F0E6] text-xs font-bold">{{ $anime['type'] }}</span>
                            <span class="bg-[#1A1A1A] text-[#F5F0E6] px-2.5 py-1 border-2 border-[#F5F0E6] text-xs font-bold">{{ $anime['episodes'] }} EPISODES</span>
                        </div>

                        <!-- Genre Tags -->
                        <div class="flex flex-wrap gap-2 pt-1">
                            @foreach($anime['genres'] as $genre)
                                <span class="text-xs font-extrabold px-3 py-1 bg-[#1A1A1A] text-[#F5F0E6] border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6]">
                                    #{{ strtoupper($genre) }}
                                </span>
                            @endforeach
                        </div>

                        <!-- Synopsis -->
                        <p class="hero-synopsis text-sm md:text-base text-zinc-300 line-clamp-2 md:line-clamp-3 leading-relaxed max-w-xl font-medium bg-[#141414]/90 p-3 border-l-4 border-[#E63946] border-2 border-[#F5F0E6] backdrop-blur-sm shadow-[3px_3px_0px_#F5F0E6]">
                            {{ $anime['synopsis'] }}
                        </p>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center gap-4 pt-4">
                            <a href="/watch/{{ $anime['slug'] }}/1" class="manga-button-primary px-8 py-3.5 text-sm font-extrabold rounded-xl flex items-center gap-2.5 text-white">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                READ & WATCH NOW
                            </a>
                            <button type="button" data-slug="{{ $anime['slug'] }}" onclick="window.toggleWatchlist('{{ $anime['slug'] }}', this)" class="manga-button px-6 py-3.5 text-sm font-extrabold rounded-xl text-white flex items-center gap-2.5">
                                <svg class="w-4.5 h-4.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5">
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
    <div class="absolute bottom-6 left-0 right-0 z-30">
        <div class="max-w-[1400px] mx-auto px-6 flex items-center justify-between">
            
            <!-- Indicators -->
            <div id="hero-dots" class="flex items-center gap-2">
                @foreach($items as $index => $anime)
                    <button type="button" data-dot="{{ $index }}" aria-label="Slide {{ $index + 1 }}" class="hero-dot h-3 border-2 border-[#F5F0E6] transition-all duration-300 {{ $index === 0 ? 'w-10 bg-[#E63946] shadow-[2px_2px_0px_#F5F0E6]' : 'w-3 bg-[#1A1A1A] hover:bg-zinc-700' }}"></button>
                @endforeach
            </div>

            <!-- Arrow Navigation -->
            <div class="flex items-center gap-2">
                <button id="hero-prev" type="button" aria-label="Previous slide" class="p-2.5 text-[#F5F0E6] bg-[#1A1A1A] hover:bg-[#E63946] hover:text-white border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button id="hero-next" type="button" aria-label="Next slide" class="p-2.5 text-[#F5F0E6] bg-[#1A1A1A] hover:bg-[#E63946] hover:text-white border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>

</div>
