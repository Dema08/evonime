<x-app-layout :title="'Watch ' . $anime['title'] . ' Episode ' . $episodeNum . ' - EVONIME'">

    <div class="max-w-[1400px] mx-auto px-4 md:px-6 space-y-6 mt-2">
        
        <!-- Breadcrumb & Title Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 text-xs font-medium text-zinc-400">
            <div class="flex items-center gap-2">
                <a href="/" class="hover:text-white transition-colors">Home</a>
                <span>/</span>
                <a href="/anime/{{ $anime['slug'] }}" class="hover:text-white transition-colors">{{ $anime['title'] }}</a>
                <span>/</span>
                <span class="text-violet-400 font-bold">Episode {{ sprintf('%02d', $episodeNum) }}</span>
            </div>

            <!-- Light/Dark Server badge -->
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span class="text-zinc-300 font-semibold">Streaming Server Active (CDN 1)</span>
            </div>
        </div>


        <!-- Desktop 2-Column Watch Layout (Left: Video Player + Meta, Right: Episodes Sidebar) -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
            
            <!-- Left Main Column (3 Cols) -->
            <div class="lg:col-span-3 space-y-5">
                
                <!-- CUSTOM VIDEO PLAYER CONTAINER -->
                <div id="video-player-container" class="relative w-full aspect-video bg-black rounded-2xl overflow-hidden border border-zinc-800 shadow-2xl group select-none">
                    
                    <!-- Dummy Video Screen with Canvas / Poster Backdrop -->
                    <div class="relative w-full h-full flex items-center justify-center overflow-hidden bg-gradient-to-br from-zinc-950 via-[#0D0D12] to-zinc-900">
                        <img id="player-backdrop" src="{{ $anime['banner'] }}" alt="Player Backdrop" class="absolute inset-0 w-full h-full object-cover filter brightness-50">
                        <div class="absolute inset-0 bg-black/40"></div>
                        
                        <!-- Animated Central Play Button when Paused -->
                        <button id="big-play-btn" type="button" onclick="window.togglePlayState()" class="relative z-10 w-20 h-20 rounded-full bg-violet-600/90 text-white flex items-center justify-center shadow-[0_0_30px_rgba(124,58,237,0.8)] hover:bg-violet-500 transform hover:scale-110 transition-all duration-300">
                            <svg class="w-10 h-10 fill-current ml-1" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>

                        <!-- Live Status Overlay Banner -->
                        <div class="absolute top-4 left-4 z-10 px-3 py-1 bg-black/70 backdrop-blur-md rounded-lg text-xs font-bold text-white flex items-center gap-2 border border-white/10">
                            <span class="px-1.5 py-0.5 bg-violet-600 rounded text-[10px]">1080p HD</span>
                            <span>{{ $anime['title'] }} - Episode {{ sprintf('%02d', $episodeNum) }}</span>
                        </div>
                    </div>

                    <!-- Video Custom Controls Bar -->
                    <div class="absolute bottom-0 left-0 right-0 z-20 p-4 bg-gradient-to-t from-black/90 via-black/60 to-transparent opacity-90 group-hover:opacity-100 transition-opacity">
                        
                        <!-- Progress Bar Seekbar -->
                        <div class="relative w-full h-1.5 bg-zinc-800 hover:h-2.5 rounded-full cursor-pointer transition-all mb-3" id="player-seekbar" onclick="window.seekPlayer(event)">
                            <div id="player-progress-bar" class="h-full bg-gradient-to-r from-violet-600 to-purple-400 rounded-full relative" style="width: 42%;">
                                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3.5 h-3.5 bg-white rounded-full shadow-md"></div>
                            </div>
                        </div>

                        <!-- Control Icons & Buttons -->
                        <div class="flex items-center justify-between text-zinc-200">
                            
                            <!-- Left Controls -->
                            <div class="flex items-center gap-4">
                                <button type="button" onclick="window.togglePlayState()" aria-label="Play/Pause" class="hover:text-violet-400 transition-colors">
                                    <svg id="play-icon" class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                    </svg>
                                </button>

                                <!-- Time Display -->
                                <span class="text-xs font-mono text-zinc-300">
                                    <span id="current-time">10:14</span> / <span id="total-time">24:00</span>
                                </span>

                                <!-- Volume Control -->
                                <div class="hidden sm:flex items-center gap-2 text-zinc-300">
                                    <button type="button" aria-label="Mute" class="hover:text-white">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                            <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z"/>
                                        </svg>
                                    </button>
                                    <input type="range" min="0" max="100" value="80" class="w-16 h-1 bg-zinc-700 rounded-lg appearance-none accent-violet-500 cursor-pointer">
                                </div>
                            </div>

                            <!-- Right Controls (Quality, Subtitle, Fullscreen, Theater Mode) -->
                            <div class="flex items-center gap-3 text-xs">
                                
                                <!-- Server Selector Quick Pill -->
                                <button type="button" onclick="window.showToast('Switched to Server 1 HD')" class="px-2 py-1 bg-zinc-800 hover:bg-zinc-700 rounded font-semibold text-zinc-300 border border-zinc-700">
                                    Server 1
                                </button>

                                <!-- Quality -->
                                <button type="button" onclick="window.showToast('Quality: 1080p Full HD')" class="px-2 py-1 bg-violet-600/30 text-violet-300 rounded font-bold border border-violet-500/40">
                                    1080p
                                </button>

                                <!-- Subtitle -->
                                <button type="button" onclick="window.showToast('Subtitles: Indonesian')" class="hidden sm:inline-block px-2 py-1 bg-zinc-800 rounded font-semibold text-zinc-300">
                                    SUB Indo
                                </button>

                                <!-- Theater Mode -->
                                <button type="button" onclick="window.toggleTheaterMode()" title="Theater Mode" class="hover:text-violet-400 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                    </svg>
                                </button>

                                <!-- Fullscreen -->
                                <button type="button" onclick="window.toggleFullscreen()" title="Fullscreen" class="hover:text-violet-400 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                    </svg>
                                </button>
                            </div>

                        </div>

                    </div>
                </div>

                <!-- VIDEO PLAYER CONTROLS & SERVER SELECTOR -->
                <div class="bg-[#151515] border border-zinc-800 rounded-2xl p-5 space-y-4">
                    
                    <!-- Player Header & Prev/Next Buttons -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-800/80 pb-4">
                        <div>
                            <h2 class="text-lg md:text-xl font-extrabold text-white">
                                {{ $anime['title'] }} - Episode {{ sprintf('%02d', $episodeNum) }}
                            </h2>
                            <p class="text-xs text-zinc-400 mt-0.5">Full HD 1080p • Indonesian & English Subtitles</p>
                        </div>

                        <!-- Prev / Next Episode Buttons -->
                        <div class="flex items-center gap-2">
                            @if($episodeNum > 1)
                                <a href="/watch/{{ $anime['slug'] }}/{{ $episodeNum - 1 }}" class="px-4 py-2 bg-[#1A1A1A] hover:bg-zinc-800 border border-zinc-800 text-zinc-300 hover:text-white text-xs font-bold rounded-xl transition-all flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Prev Ep
                                </a>
                            @endif

                            @if($episodeNum < $anime['episodes'])
                                <a href="/watch/{{ $anime['slug'] }}/{{ $episodeNum + 1 }}" class="px-5 py-2 bg-violet-600 hover:bg-violet-500 text-white text-xs font-bold rounded-xl transition-all shadow-[0_0_15px_rgba(124,58,237,0.5)] flex items-center gap-1.5">
                                    Next Ep
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Server Selector & Toggles Row -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        
                        <!-- Server Selector List -->
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-bold text-zinc-400 mr-1">SERVER:</span>
                            <button type="button" onclick="window.selectServer(1, this)" class="server-btn px-3 py-1.5 bg-violet-600 text-white text-xs font-bold rounded-lg transition-all border border-violet-500 shadow-md">
                                Server 1 (CDN Fast)
                            </button>
                            <button type="button" onclick="window.selectServer(2, this)" class="server-btn px-3 py-1.5 bg-[#1A1A1A] hover:bg-zinc-800 text-zinc-300 text-xs font-bold rounded-lg border border-zinc-800 transition-all">
                                Server 2 (Backup)
                            </button>
                            <button type="button" onclick="window.selectServer(3, this)" class="server-btn px-3 py-1.5 bg-[#1A1A1A] hover:bg-zinc-800 text-zinc-300 text-xs font-bold rounded-lg border border-zinc-800 transition-all">
                                Server 3 (Alpha)
                            </button>
                        </div>

                        <!-- Auto Play & Auto Next Toggles -->
                        <div class="flex items-center gap-5 text-xs text-zinc-300 font-semibold">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 accent-violet-600 rounded">
                                <span>Auto Next</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 accent-violet-600 rounded">
                                <span>Auto Play</span>
                            </label>
                        </div>

                    </div>

                </div>

                <!-- Anime Metadata & Synopsis below video -->
                <div class="bg-[#151515] border border-zinc-800/80 rounded-2xl p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-white">About {{ $anime['title'] }}</h3>
                        <x-rating-badge :rating="$anime['rating']" />
                    </div>
                    <p class="text-xs md:text-sm text-zinc-400 leading-relaxed">
                        {{ $anime['synopsis'] }}
                    </p>
                </div>

            </div>

            <!-- Right Sidebar Column (1 Col: Episodes Selector Grid) -->
            <div class="lg:col-span-1 space-y-4 bg-[#101010] border border-zinc-800/80 rounded-2xl p-4 max-h-[720px] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
                    <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-violet-600 rounded-full inline-block"></span>
                        Episodes List
                    </h3>
                    <span class="text-[11px] text-zinc-500 font-mono">{{ count($anime['episodes_list'] ?? []) }} eps</span>
                </div>

                <!-- Episode Items List -->
                <div class="space-y-2">
                    @foreach($anime['episodes_list'] as $ep)
                        <a href="/watch/{{ $anime['slug'] }}/{{ $ep['number'] }}" 
                           class="flex items-center justify-between p-2.5 rounded-xl border transition-all text-xs font-semibold {{ $ep['number'] == $episodeNum ? 'bg-violet-600/20 border-violet-500 text-violet-300 shadow-md' : 'bg-[#151515] hover:bg-[#1A1A1A] border-zinc-800/80 text-zinc-300 hover:text-white' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center font-mono text-[11px] {{ $ep['number'] == $episodeNum ? 'bg-violet-600 text-white' : 'bg-zinc-900 text-zinc-400' }}">
                                    {{ sprintf('%02d', $ep['number']) }}
                                </span>
                                <span class="truncate">{{ $ep['title'] }}</span>
                            </div>
                            @if($ep['number'] == $episodeNum)
                                <span class="w-2 h-2 rounded-full bg-violet-400 animate-pulse flex-shrink-0"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</x-app-layout>
