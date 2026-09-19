<x-app-layout :title="'Watch ' . $anime['title'] . ' Episode ' . $episodeNum . ' - EVONIME'">

    <div class="max-w-[1400px] mx-auto px-4 md:px-6 space-y-6 mt-2 text-[#F5F0E6]">
        
        <!-- Breadcrumb & Title Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 text-xs font-bold text-zinc-400">
            <div class="flex items-center gap-2">
                <a href="/" class="hover:text-[#E63946] transition-colors">HOME</a>
                <span>/</span>
                <a href="/anime/{{ $anime['slug'] }}" class="hover:text-[#E63946] transition-colors">{{ strtoupper($anime['title']) }}</a>
                <span>/</span>
                <span class="text-[#E63946] font-black">EPISODE {{ sprintf('%02d', $episodeNum) }}</span>
            </div>

            <!-- Light/Dark Server badge -->
            <div class="flex items-center gap-2 bg-[#1A1A1A] px-3 py-1 border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6]">
                <span class="w-2 h-2 rounded-full bg-[#E63946] animate-ping"></span>
                <span class="text-[#F5F0E6] font-black text-[11px]">CDN STREAMING ACTIVE</span>
            </div>
        </div>


        <!-- Desktop 2-Column Watch Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
            
            <!-- Left Main Column (3 Cols) -->
            <div class="lg:col-span-3 space-y-5">
                
                <!-- CUSTOM VIDEO PLAYER CONTAINER -->
                <div id="video-player-container" class="relative w-full aspect-video bg-[#0D0D0D] rounded-xl overflow-hidden border-2 border-[#F5F0E6] shadow-[6px_6px_0px_#F5F0E6] group select-none">
                    
                    <!-- Dummy Video Screen with Canvas / Poster Backdrop -->
                    <div class="relative w-full h-full flex items-center justify-center overflow-hidden bg-[#0D0D0D]">
                        <img id="player-backdrop" src="{{ $anime['banner'] }}" alt="Player Backdrop" class="absolute inset-0 w-full h-full object-cover filter brightness-70 contrast-125">
                        <div class="absolute inset-0 bg-[#0D0D0D]/50 halftone-bg"></div>
                        
                        <!-- Animated Central Play Button when Paused -->
                        <button id="big-play-btn" type="button" onclick="window.togglePlayState()" class="relative z-10 w-20 h-20 bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] flex items-center justify-center hover:bg-red-700 transform hover:scale-110 transition-all duration-300">
                            <svg class="w-10 h-10 fill-current ml-1" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>

                        <!-- Live Status Overlay Banner -->
                        <div class="absolute top-4 left-4 z-10 px-3 py-1 bg-[#1A1A1A] text-[#F5F0E6] border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] text-xs font-black flex items-center gap-2">
                            <span class="px-1.5 py-0.5 bg-[#E63946] text-white rounded text-[10px]">1080p HD</span>
                            <span>EPISODE {{ sprintf('%02d', $episodeNum) }}</span>
                        </div>
                    </div>

                    <!-- Video Custom Controls Bar -->
                    <div class="absolute bottom-0 left-0 right-0 z-20 p-4 bg-[#0D0D0D]/95 border-t-2 border-[#F5F0E6] opacity-95 group-hover:opacity-100 transition-opacity">
                        
                        <!-- Progress Bar Seekbar -->
                        <div class="relative w-full h-2 bg-[#141414] border border-[#F5F0E6] hover:h-3 cursor-pointer transition-all mb-3" id="player-seekbar" onclick="window.seekPlayer(event)">
                            <div id="player-progress-bar" class="h-full bg-[#E63946] relative" style="width: 42%;">
                                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3.5 h-3.5 bg-[#F5F0E6] border border-[#0D0D0D]"></div>
                            </div>
                        </div>

                        <!-- Control Icons & Buttons -->
                        <div class="flex items-center justify-between text-[#F5F0E6]">
                            
                            <!-- Left Controls -->
                            <div class="flex items-center gap-4">
                                <button type="button" onclick="window.togglePlayState()" aria-label="Play/Pause" class="hover:text-[#E63946] transition-colors">
                                    <svg id="play-icon" class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                    </svg>
                                </button>

                                <!-- Time Display -->
                                <span class="text-xs font-mono font-bold text-zinc-300">
                                    <span id="current-time">10:14</span> / <span id="total-time">24:00</span>
                                </span>
                            </div>

                            <!-- Right Controls -->
                            <div class="flex items-center gap-3 text-xs font-bold">
                                <button type="button" onclick="window.showToast('Server 1 HD Active')" class="px-2.5 py-1 bg-[#1A1A1A] text-[#F5F0E6] border border-[#F5F0E6] rounded">
                                    Server 1
                                </button>
                                <button type="button" onclick="window.showToast('1080p Full HD')" class="px-2.5 py-1 bg-[#E63946] text-white border border-[#F5F0E6] rounded">
                                    1080p
                                </button>
                            </div>

                        </div>

                    </div>
                </div>

                <!-- VIDEO PLAYER CONTROLS & SERVER SELECTOR -->
                <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-5 space-y-4">
                    
                    <!-- Player Header & Prev/Next Buttons -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-800 pb-4">
                        <div>
                            <h2 class="text-lg md:text-xl font-black text-[#F5F0E6]" style="font-family: 'Anton', sans-serif;">
                                {{ strtoupper($anime['title']) }} - EPISODE {{ sprintf('%02d', $episodeNum) }}
                            </h2>
                            <p class="text-xs text-zinc-400 font-bold mt-0.5">Full HD 1080p • Indonesian & English Subtitles</p>
                        </div>

                        <!-- Prev / Next Episode Buttons -->
                        <div class="flex items-center gap-2">
                            @if($episodeNum > 1)
                                <a href="/watch/{{ $anime['slug'] }}/{{ $episodeNum - 1 }}" class="manga-button px-4 py-2 text-xs font-black rounded-lg bg-[#141414] text-[#F5F0E6] flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    PREV EP
                                </a>
                            @endif

                            @if($episodeNum < $anime['episodes'])
                                <a href="/watch/{{ $anime['slug'] }}/{{ $episodeNum + 1 }}" class="manga-button-primary px-5 py-2 text-xs font-black rounded-lg text-white flex items-center gap-1.5">
                                    NEXT EP
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Server Selector & Toggles Row -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-black text-zinc-400 mr-1">SERVER:</span>
                            <button type="button" class="px-3 py-1.5 bg-[#E63946] text-white text-xs font-black border border-[#F5F0E6] shadow-[1px_1px_0px_#F5F0E6]">Server 1</button>
                            <button type="button" class="px-3 py-1.5 bg-[#141414] text-zinc-300 text-xs font-black border border-[#F5F0E6]">Server 2</button>
                        </div>
                    </div>

                </div>

                <!-- Anime Metadata & Synopsis below video -->
                <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-black text-[#F5F0E6]" style="font-family: 'Anton', sans-serif;">ABOUT {{ strtoupper($anime['title']) }}</h3>
                        <x-rating-badge :rating="$anime['rating']" />
                    </div>
                    <p class="text-xs md:text-sm text-zinc-300 font-medium leading-relaxed">
                        {{ $anime['synopsis'] }}
                    </p>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="lg:col-span-1 space-y-4 bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-4 max-h-[720px] overflow-y-auto">
                <div class="flex items-center justify-between border-b-2 border-[#F5F0E6] pb-3">
                    <h3 class="text-sm font-black text-[#F5F0E6] flex items-center gap-2" style="font-family: 'Anton', sans-serif;">
                        <span class="w-2 h-4 bg-[#E63946] border border-[#F5F0E6] inline-block"></span>
                        CHAPTERS & EPISODES
                    </h3>
                    <span class="text-[11px] text-zinc-400 font-mono font-bold">{{ count($anime['episodes_list'] ?? []) }} EPS</span>
                </div>

                <!-- Episode Items List -->
                <div class="space-y-2">
                    @foreach($anime['episodes_list'] as $ep)
                        <a href="/watch/{{ $anime['slug'] }}/{{ $ep['number'] }}" 
                           class="flex items-center justify-between p-2.5 rounded-lg border-2 transition-all text-xs font-black {{ $ep['number'] == $episodeNum ? 'bg-[#E63946] text-white border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6]' : 'bg-[#141414] hover:bg-zinc-800 border-[#F5F0E6] text-[#F5F0E6]' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-7 h-7 flex items-center justify-center font-mono text-[11px] bg-[#0D0D0D] border border-[#F5F0E6] text-[#F5F0E6]">
                                    {{ sprintf('%02d', $ep['number']) }}
                                </span>
                                <span class="truncate">{{ $ep['title'] }}</span>
                            </div>
                            @if($ep['number'] == $episodeNum)
                                <span class="w-2 h-2 bg-white animate-pulse flex-shrink-0"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</x-app-layout>
