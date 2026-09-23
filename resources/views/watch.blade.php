<x-app-layout :title="'Watch ' . $anime['title'] . ' Episode ' . $episodeNum . ' - EVONIME'">

    <div class="max-w-[1400px] mx-auto px-4 md:px-6 space-y-6 mt-2 pt-24 md:pt-28 text-[#F5F0E6]">
        
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
            <div id="main-player-col" class="lg:col-span-3 space-y-5 transition-all duration-300">
                
                <!-- AMBIENT GLOW BACKDROP FOR 1080p VIP PLAYER -->
                <div class="relative group">
                    <div id="player-ambient-glow" class="hidden absolute -inset-1.5 bg-gradient-to-r from-amber-500 via-yellow-500 to-amber-600 rounded-2xl blur-xl opacity-60 transition-all duration-700 pointer-events-none animate-pulse-slow"></div>

                    <!-- CUSTOM VIDEO PLAYER CONTAINER -->
                    <div id="video-player-container" class="relative w-full aspect-video bg-[#0D0D0D] rounded-xl overflow-hidden border-2 border-[#F5F0E6] shadow-[6px_6px_0px_#F5F0E6] select-none transition-all duration-500 z-10">
                        
                        <!-- Floating Watermark VIP Badge for 1080p (Google Drive Direct) -->
                        <div id="vip-1080p-watermark" class="hidden absolute top-3 right-3 z-30 pointer-events-none flex items-center gap-2 px-3 py-1.5 bg-black/85 backdrop-blur-md border border-amber-400/80 rounded-lg shadow-[0_0_20px_rgba(245,158,11,0.6)] transition-all duration-300">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                            </span>
                            <span class="text-[11px] font-black tracking-wider text-amber-300 uppercase flex items-center gap-1.5">
                                🌟 1080p ULTRA HD • GOOGLE DRIVE STORAGE
                            </span>
                        </div>

                        <!-- Iframe Embed Mode (Otakudesu) -->
                        <iframe id="player-iframe" 
                                src="" 
                                allowfullscreen
                                allow="autoplay; encrypted-media; fullscreen; picture-in-picture"
                                referrerpolicy="no-referrer"
                                class="absolute inset-0 w-full h-full border-0 hidden">
                        </iframe>

                        <!-- Fallback Card UI saat embed diblokir (X-Frame-Options / CSP) -->
                        <div id="iframe-fallback" class="hidden absolute inset-0 z-30 flex items-center justify-center p-4 bg-[#0D0D0D]/90 backdrop-blur-md select-text">
                            <div class="relative w-full max-w-lg bg-[#141414] border-2 border-[#E63946] rounded-2xl p-6 md:p-7 shadow-[0_10px_40px_rgba(230,57,70,0.35)] text-center space-y-4">
                                
                                <!-- Header Pill Badge -->
                                <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-[#E63946] text-white text-xs font-black rounded-full border border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] uppercase tracking-wider">
                                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                    <span>🎬 PEMBERITAHUAN PEMUTARAN</span>
                                </div>

                                <!-- Card Title & Clean User Message -->
                                <div class="space-y-1.5">
                                    <h3 class="text-lg md:text-xl font-black text-[#F5F0E6]" style="font-family: 'Anton', sans-serif;">
                                        PUTAR VIDEO DI TAB BARU
                                    </h3>
                                    <p id="iframe-fallback-msg" class="text-xs md:text-sm font-bold text-zinc-300 leading-relaxed max-w-md mx-auto">
                                        Server streaming membatasi pemutaran langsung di dalam iframe. Silakan tonton video ini secara lancar dengan kualitas yang dipilih melalui tab baru.
                                    </p>
                                </div>

                                <!-- Selected Quality & Server Info Box -->
                                <div id="iframe-fallback-quality-badge" class="bg-[#1A1A1A] border-2 border-[#F5F0E6] rounded-xl p-3 shadow-[3px_3px_0px_#F5F0E6] flex items-center justify-center gap-3 text-xs font-mono font-bold">
                                    <div class="flex items-center gap-1.5 text-zinc-300">
                                        <span class="text-zinc-400">Kualitas:</span>
                                        <span id="fallback-quality-label" class="px-2 py-0.5 bg-[#E63946] text-white rounded font-black text-xs">720p</span>
                                    </div>
                                    <span class="text-zinc-600">•</span>
                                    <div class="flex items-center gap-1.5 text-zinc-300">
                                        <span class="text-zinc-400">Server:</span>
                                        <span id="fallback-server-label" class="text-amber-400 font-bold">Otakudesu</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-1">
                                    <a id="iframe-open-new-tab" href="#" target="_blank" rel="noopener noreferrer"
                                       class="w-full sm:w-auto px-6 py-3 bg-[#E63946] hover:bg-red-700 text-white text-xs font-black rounded-xl border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                                        <span id="iframe-open-btn-text">BUKA DI TAB BARU</span>
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                            <path d="M14 3h7v7h-2V6.41l-9.29 9.3-1.42-1.42L17.59 5H14V3zM5 5h6v2H5v12h12v-6h2v8H3V5h2z"/>
                                        </svg>
                                    </a>
                                    <button id="iframe-switch-gdrive-btn" type="button" onclick="window.switchProviderTab('google_drive')"
                                       class="hidden w-full sm:w-auto px-6 py-3 bg-amber-500 hover:bg-amber-600 text-black text-xs font-black rounded-xl border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                                        <span>🌟 PUTAR 1080p DI GOOGLE DRIVE</span>
                                    </button>
                                    <button id="iframe-switch-otaku-btn" type="button" onclick="window.switchProviderTab('otakudesu')"
                                       class="hidden w-full sm:w-auto px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-[#F5F0E6] text-xs font-black rounded-xl border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                                        <span>⚡ BERALIH KE OTAKUDESU (720p)</span>
                                    </button>
                                </div>

                            </div>
                        </div>

                        <!-- HTML5 Video Mode (Localhost Streaming Proxy 1080p & Direct MP4) -->
                        <video id="player-video" 
                               class="absolute inset-0 w-full h-full hidden z-20 bg-black cursor-pointer object-contain transition-all duration-300" 
                               playsinline
                               crossorigin="anonymous"
                               preload="metadata"></video>

                        <!-- Custom HTML5 Video Player Overlay Controls (for 1080p Google Drive Mode) -->
                        <div id="custom-video-controls" class="hidden absolute bottom-0 left-0 right-0 w-full z-30 bg-gradient-to-t from-black/95 via-black/85 to-transparent px-3 sm:px-5 pb-3 pt-8 space-y-2 select-none opacity-100 transition-opacity duration-300 box-border">
                            
                            <!-- Full-Width Progress Seek Track -->
                            <div id="seek-container" class="relative group cursor-pointer w-full py-1 flex items-center">
                                <!-- Time Tooltip preview on seek hover -->
                                <div id="seek-tooltip" class="absolute -top-9 hidden -translate-x-1/2 px-2.5 py-1 bg-black/95 text-amber-300 border-2 border-amber-400 rounded-lg text-xs font-mono font-bold shadow-2xl pointer-events-none z-40">
                                    00:00
                                </div>
                                <!-- Progress Track Background -->
                                <div class="w-full h-2.5 group-hover:h-3.5 bg-zinc-800/90 rounded-full overflow-hidden relative transition-all duration-200 border border-zinc-700/80 shadow-inner">
                                    <!-- Buffered Bar -->
                                    <div id="seek-buffer-bar" class="absolute top-0 bottom-0 left-0 bg-zinc-600/80 rounded-full w-0 transition-all"></div>
                                    <!-- Played Progress Bar -->
                                    <div id="seek-progress-bar" class="absolute top-0 bottom-0 left-0 bg-gradient-to-r from-red-600 via-amber-500 to-yellow-400 rounded-full w-0 shadow-[0_0_15px_rgba(245,158,11,1)]"></div>
                                </div>
                                <!-- Seek Thumb Pin -->
                                <div id="seek-thumb" class="absolute left-0 -ml-3 w-6 h-6 bg-gradient-to-r from-amber-400 to-yellow-300 border-2 border-white rounded-full shadow-[0_0_15px_rgba(245,158,11,1)] opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none scale-105"></div>
                            </div>

                            <!-- Bottom Controls Bar (Single Row, No Scroll, Always Visible) -->
                            <div class="flex items-center justify-between gap-1 sm:gap-2 text-white w-full">
                                
                                <!-- Left Section: Play/Pause, -10s, +10s, Volume, Timestamp -->
                                <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                                    
                                    <!-- Play / Pause Button with Text & Icon -->
                                    <button id="ctrl-play-btn" type="button" class="px-3 py-1.5 bg-gradient-to-r from-amber-500 to-yellow-400 hover:from-amber-400 hover:to-yellow-300 text-black text-xs sm:text-sm font-black rounded-lg shadow-[0_0_15px_rgba(245,158,11,0.8)] hover:scale-105 active:scale-95 transition-all cursor-pointer focus:outline-none flex items-center gap-1 flex-shrink-0" title="Play / Pause (Space)">
                                        <svg id="ctrl-play-icon" class="w-4 h-4 fill-current ml-0.5" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        <svg id="ctrl-pause-icon" class="w-4 h-4 fill-current hidden" viewBox="0 0 24 24">
                                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                        </svg>
                                        <span id="ctrl-play-text">Play</span>
                                    </button>

                                    <!-- Quick Rewind (-10s) -->
                                    <button id="ctrl-rewind-btn" type="button" class="px-2 sm:px-2.5 py-1.5 bg-black/70 hover:bg-zinc-800 border border-zinc-700/90 rounded-lg text-xs font-mono font-bold text-white hover:text-amber-300 transition-all cursor-pointer flex-shrink-0" title="Mundur 10 detik">
                                        -10s
                                    </button>

                                    <!-- Quick Forward (+10s) -->
                                    <button id="ctrl-forward-btn" type="button" class="px-2 sm:px-2.5 py-1.5 bg-black/70 hover:bg-zinc-800 border border-zinc-700/90 rounded-lg text-xs font-mono font-bold text-white hover:text-amber-300 transition-all cursor-pointer flex-shrink-0" title="Maju 10 detik">
                                        +10s
                                    </button>

                                    <!-- Volume Group -->
                                    <div id="ctrl-volume-group" class="relative group/vol flex items-center gap-1 bg-black/70 px-2 py-1.5 border border-zinc-800 rounded-lg flex-shrink-0">
                                        <button id="ctrl-volume-btn" type="button" class="text-zinc-200 hover:text-amber-300 transition-all cursor-pointer focus:outline-none flex items-center" title="Mute/Unmute (M)">
                                            <svg id="vol-high-icon" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                                            </svg>
                                            <svg id="vol-mute-icon" class="w-4 h-4 fill-current hidden text-red-500" viewBox="0 0 24 24">
                                                <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73 4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                                            </svg>
                                        </button>
                                        <input id="ctrl-volume-slider" type="range" min="0" max="1" step="0.05" value="1"
                                               class="w-10 sm:w-16 h-1.5 bg-zinc-700 accent-amber-400 rounded-lg cursor-pointer opacity-90 group-hover/vol:opacity-100 transition-opacity">
                                    </div>

                                    <!-- Timestamp Badge -->
                                    <div class="px-2 py-1.5 bg-black/80 border border-zinc-800 rounded-lg text-xs font-mono font-bold flex-shrink-0">
                                        <span id="ctrl-current-time" class="text-white">00:00</span>
                                        <span class="text-zinc-500 mx-0.5">/</span>
                                        <span id="ctrl-duration" class="text-zinc-300">00:00</span>
                                    </div>

                                </div>

                                <!-- Right Section: Fit, Teater, Speed, PiP, Fullscreen -->
                                <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                                    
                                    <!-- Video Fit Toggle Button -->
                                    <button id="ctrl-fit-btn" type="button" onclick="window.toggleVideoFit()" class="px-2 sm:px-2.5 py-1.5 text-xs font-mono font-extrabold text-cyan-300 hover:text-white bg-black/70 hover:bg-black border border-cyan-500/50 rounded-lg transition-all cursor-pointer flex-shrink-0" title="Ubah Ukuran Video (Fit / Cover)">
                                        <span id="fit-mode-label">Fit</span>
                                    </button>

                                    <!-- Mode Teater Toggle Button -->
                                    <button id="ctrl-theater-btn" type="button" onclick="window.toggleTheaterMode()" class="px-2 sm:px-2.5 py-1.5 text-xs font-mono font-extrabold text-amber-300 hover:text-white bg-black/70 hover:bg-black border border-amber-500/50 rounded-lg transition-all cursor-pointer flex-shrink-0" title="Mode Teater / Layar Lebar (T)">
                                        <span id="theater-mode-label">Teater</span>
                                    </button>

                                    <!-- Speed Toggle Button -->
                                    <button id="ctrl-speed-btn" type="button" class="px-2 sm:px-2.5 py-1.5 text-xs font-mono font-extrabold text-amber-300 hover:text-white bg-black/70 hover:bg-black border border-amber-500/50 rounded-lg transition-all cursor-pointer flex-shrink-0" title="Kecepatan Pemutaran">
                                        <span id="speed-label">1.0x</span>
                                    </button>

                                    <!-- Picture-in-Picture Button -->
                                    <button id="ctrl-pip-btn" type="button" class="p-1.5 bg-black/70 hover:bg-zinc-800 border border-zinc-800 rounded-lg text-zinc-200 hover:text-white transition-all cursor-pointer focus:outline-none flex-shrink-0" title="Picture in Picture">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                            <path d="M19 7h-8v6h8V7zm2-4H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14z"/>
                                        </svg>
                                    </button>

                                    <!-- Fullscreen Button -->
                                    <button id="ctrl-fullscreen-btn" type="button" class="p-1.5 bg-amber-500/20 hover:bg-amber-500 border border-amber-500/60 rounded-lg text-amber-300 hover:text-black transition-all cursor-pointer focus:outline-none flex-shrink-0" title="Layar Penuh (F)">
                                        <svg id="fullscreen-expand-icon" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                            <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                                        </svg>
                                        <svg id="fullscreen-compress-icon" class="w-4 h-4 fill-current hidden" viewBox="0 0 24 24">
                                            <path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/>
                                        </svg>
                                    </button>

                                </div>

                            </div>
                        </div>

                        <!-- Center Flash Play/Pause Overlay Animation Container -->
                        <div id="custom-center-play-overlay" class="hidden absolute inset-0 z-25 flex items-center justify-center pointer-events-none">
                            <div id="center-flash-icon" class="w-16 h-16 sm:w-20 sm:h-20 bg-black/85 border-2 border-amber-400 text-amber-400 rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(245,158,11,0.8)] transform scale-75 opacity-0 transition-all duration-300">
                                <svg id="center-flash-svg-play" class="w-8 h-8 sm:w-10 sm:h-10 fill-current ml-1" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                <svg id="center-flash-svg-pause" class="w-8 h-8 sm:w-10 sm:h-10 fill-current hidden" viewBox="0 0 24 24">
                                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Poster Backdrop (shown when paused/standby) -->
                        <div id="player-backdrop-layer" class="relative w-full h-full flex items-center justify-center overflow-hidden bg-[#0D0D0D]">
                            <img id="player-backdrop" src="{{ $anime['banner'] }}" referrerpolicy="no-referrer" alt="Player Backdrop" class="absolute inset-0 w-full h-full object-cover filter brightness-70 contrast-125">
                            <div class="absolute inset-0 bg-[#0D0D0D]/50 halftone-bg"></div>
                            
                            <!-- Animated Central Play Button when Paused -->
                            <button id="big-play-btn" type="button" onclick="window.togglePlayState()" class="relative z-10 px-6 py-3.5 bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] flex items-center justify-center gap-3 hover:bg-red-700 transform hover:scale-105 transition-all duration-300 cursor-pointer rounded-xl">
                                <svg class="w-8 h-8 fill-current ml-0.5" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                <span class="font-black text-sm md:text-base tracking-wider uppercase">PUTAR EPISODE {{ sprintf('%02d', $episodeNum) }} (1080p)</span>
                            </button>

                            <!-- Live Status Overlay Banner -->
                            <div class="absolute top-4 left-4 z-10 px-3 py-1 bg-[#1A1A1A] text-[#F5F0E6] border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] text-xs font-black flex items-center gap-2">
                                <span id="player-status-badge" class="px-1.5 py-0.5 bg-[#E63946] text-white rounded text-[10px] font-black">OTAKUDESU</span>
                                <span>EPISODE {{ sprintf('%02d', $episodeNum) }}</span>
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
                            <p class="text-xs text-zinc-400 font-bold mt-0.5">Otakudesu • Hardsub Indonesia</p>
                        </div>

                        <!-- Prev / Next Episode Buttons (dynamic from navigation data) -->
                        <div class="flex items-center gap-2">
                            <button id="nav-prev-btn" type="button" disabled
                                    class="manga-button px-4 py-2 text-xs font-black rounded-lg bg-[#141414] text-zinc-500 border border-[#F5F0E6] cursor-not-allowed opacity-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                EP SEBELUMNYA
                            </button>
                            <button id="nav-next-btn" type="button" disabled
                                    class="manga-button-primary px-5 py-2 text-xs font-black rounded-lg text-white flex items-center gap-1.5 opacity-50 cursor-not-allowed">
                                EP SELANJUTNYA
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Dual-Mode Provider Switcher (Google Drive 1080p vs Otakudesu) -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 bg-[#141414] p-3.5 rounded-xl border border-zinc-800">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-black text-zinc-400 mr-1 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#E63946]" fill="currentColor" viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12z"/></svg>
                                SUMBER STREAM:
                            </span>
                            <button id="provider-tab-gdrive" type="button" onclick="window.switchProviderTab('google_drive')"
                                    class="px-3.5 py-2 rounded-lg text-xs font-black border transition-all flex items-center gap-2 bg-[#E63946] border-[#F5F0E6] text-white shadow-[2px_2px_0px_#F5F0E6] cursor-pointer">
                                <span style="color: #ffffff !important;" class="text-white font-extrabold">🌟 Google Drive</span>
                                <span id="gdrive-badge" style="color: #ffffff !important;" class="px-1.5 py-0.5 bg-black/40 text-white text-[10px] rounded font-mono font-black border border-white/40">1080p FHD</span>
                            </button>
                            <button id="provider-tab-otaku" type="button" onclick="window.switchProviderTab('otakudesu')"
                                    class="px-3.5 py-2 rounded-lg text-xs font-black border transition-all flex items-center gap-2 bg-[#E63946] border-[#F5F0E6] text-white shadow-[2px_2px_0px_#F5F0E6] cursor-pointer">
                                <span>⚡ Otakudesu</span>
                                <span class="px-1.5 py-0.5 bg-red-950 text-white text-[10px] rounded font-mono font-bold">360p-720p</span>
                            </button>
                        </div>

                        <!-- Current Server Badge -->
                        <div class="flex flex-wrap items-center gap-2">
                            <button id="current-server-badge" type="button" class="px-3 py-1.5 bg-[#1A1A1A] text-zinc-300 text-xs font-black border border-zinc-700 rounded-lg">Memuat...</button>
                        </div>
                    </div>

                    <!-- Pilihan Resolusi (360p, 480p, 720p, 1080p FHD) -->
                    <div class="border-t border-zinc-800 pt-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-zinc-300 flex items-center gap-1.5 uppercase tracking-wider">
                                <svg class="w-4 h-4 text-[#E63946]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                PILIH RESOLUSI:
                            </span>
                            <span id="player-mode-badge" class="text-[11px] font-mono text-emerald-400 font-bold bg-emerald-950/40 px-2.5 py-1 rounded border border-emerald-500/40">
                                Localhost Proxy • 1080p
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2.5" id="unified-quality-selector">
                            <button type="button" onclick="window.selectQualityDirect('360p')" class="quality-btn px-3.5 py-1.5 rounded-lg text-xs font-black border transition-all bg-[#141414] border-zinc-700 text-zinc-400 hover:text-white cursor-pointer" data-quality="360p">360p</button>
                            <button type="button" onclick="window.selectQualityDirect('480p')" class="quality-btn px-3.5 py-1.5 rounded-lg text-xs font-black border transition-all bg-[#141414] border-zinc-700 text-zinc-400 hover:text-white cursor-pointer" data-quality="480p">480p</button>
                            <button type="button" onclick="window.selectQualityDirect('720p')" class="quality-btn px-3.5 py-1.5 rounded-lg text-xs font-black border transition-all bg-[#141414] border-zinc-700 text-zinc-400 hover:text-white cursor-pointer" data-quality="720p">720p</button>
                            <button type="button" onclick="window.selectQualityDirect('1080p')" class="quality-btn px-4 py-1.5 rounded-lg text-xs font-black border transition-all bg-amber-950/40 border-amber-500/50 text-amber-300 hover:bg-amber-900/60 shadow-[2px_2px_0px_#f59e0b] cursor-pointer flex items-center gap-1.5" data-quality="1080p">
                                <span>🌟 1080p FHD</span>
                                <span class="px-1.5 py-0.2 bg-amber-500 text-black text-[9px] rounded font-mono font-black uppercase">Drive Storage</span>
                            </button>
                        </div>

                        <!-- 1080p VIP Special Banner Feature Info Box -->
                        <div id="gdrive-1080p-info-box" class="hidden p-3.5 rounded-xl bg-gradient-to-r from-amber-950/50 via-zinc-900 to-amber-950/50 border-2 border-amber-500/50 text-amber-200 shadow-[0_4px_25px_rgba(245,158,11,0.2)] space-y-2.5 transition-all duration-300">
                            <div class="flex items-center justify-between border-b border-amber-500/20 pb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">👑</span>
                                    <span class="text-xs font-black uppercase tracking-wider text-amber-300">PEMUTARAN SPESIAL 1080p ULTRA HD (GOOGLE DRIVE)</span>
                                </div>
                                <span class="px-2 py-0.5 bg-amber-500/20 text-amber-300 text-[10px] font-mono font-bold rounded border border-amber-500/40 animate-pulse">
                                    GOOGLE DRIVE SERVER
                                </span>
                            </div>
                            <p class="text-xs text-amber-100/90 leading-relaxed font-medium">
                                Video ini diputar khusus secara langsung dari server <strong class="text-amber-300 font-bold">Google Drive EVONIME</strong> menggunakan pemutar internal HTML5 (Streaming Proxy). Tampilan lebih bersih tanpa iklan iframe, kualitas 1080p Full HD jernih, bitrate tinggi, & dukungan subtitle Indonesia.
                            </p>
                            <div class="flex flex-wrap items-center gap-2 pt-0.5 text-[11px] font-bold">
                                <span class="px-2 py-0.5 bg-black/60 text-amber-300 rounded border border-amber-500/30 flex items-center gap-1">✨ Full HD 1080p Original</span>
                                <span class="px-2 py-0.5 bg-black/60 text-emerald-400 rounded border border-emerald-500/30 flex items-center gap-1">🛡️ Anti Ads & Iframe Popups</span>
                                <span class="px-2 py-0.5 bg-black/60 text-cyan-300 rounded border border-cyan-500/30 flex items-center gap-1">🔊 High Bitrate Audio</span>
                                <span class="px-2 py-0.5 bg-black/60 text-purple-300 rounded border border-purple-500/30 flex items-center gap-1">💬 Subtitle Indonesia</span>
                            </div>
                        </div>
                    </div>

                    <!-- Mirror Selectors: Kualitas x Server (Otakudesu embed) -->
                    <div id="mirror-selectors" class="hidden download-section border-t border-zinc-800 pt-4">
                        <div class="selector-group">
                            <span class="selector-label">Kualitas:</span>
                            <div class="selector-buttons" id="quality-selector"></div>
                        </div>
                        <div class="selector-group">
                            <span class="selector-label">Server:</span>
                            <div class="selector-buttons" id="server-selector"></div>
                        </div>
                        <div id="player-loading" class="hidden items-center gap-2 text-xs font-bold text-zinc-400 pt-2">
                            <span class="w-3 h-3 border-2 border-[#E63946] border-t-transparent rounded-full animate-spin inline-block"></span>
                            Memuat server...
                        </div>
                    </div>
                    <!-- Download Section -->
                    <div id="download-section" class="download-section border-t border-zinc-800 pt-4 hidden">
                        <h4 class="text-sm font-black text-[#E63946] mb-2">📥 Unduh Episode Ini</h4>
                        <div id="download-providers" class="flex flex-col gap-3"></div>
                        <p id="download-empty" class="text-xs text-zinc-500 hidden">Tidak ada link download tersedia.</p>
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
            <div id="sidebar-col" class="lg:col-span-1 space-y-4 bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-4 max-h-[720px] overflow-y-auto transition-all duration-300">
                <div class="flex items-center justify-between border-b-2 border-[#F5F0E6] pb-3">
                    <h3 class="text-sm font-black text-[#F5F0E6] flex items-center gap-2" style="font-family: 'Anton', sans-serif;">
                        <span class="w-2 h-4 bg-[#E63946] border border-[#F5F0E6] inline-block"></span>
                        CHAPTERS & EPISODES
                    </h3>
                    <span class="text-[11px] text-zinc-400 font-mono font-bold">{{ $episodes->count() }} EPS</span>
                </div>

                <!-- Episode Items List -->
                <div class="space-y-2 max-h-[580px] overflow-y-auto pr-1">
                    @foreach($episodes as $ep)
                        @php
                            $epNum = is_array($ep) ? ($ep['episode_number'] ?? $ep['number'] ?? 1) : ($ep->episode_number ?? $ep->number ?? 1);
                            $epTitle = is_array($ep) ? ($ep['title'] ?? ('Episode ' . $epNum)) : ($ep->title ?? ('Episode ' . $epNum));
                        @endphp
                        <a href="/watch/{{ $anime['slug'] }}/{{ $epNum }}" 
                           class="flex items-center justify-between p-2.5 rounded-lg border-2 transition-all text-xs font-black {{ $epNum == $episodeNum ? 'bg-[#E63946] text-white border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6]' : 'bg-[#141414] hover:bg-zinc-800 border-[#F5F0E6] text-[#F5F0E6]' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-7 h-7 flex items-center justify-center font-mono text-[11px] bg-[#0D0D0D] border border-[#F5F0E6] text-[#F5F0E6]">
                                    {{ sprintf('%02d', $epNum) }}
                                </span>
                                <span class="truncate">{{ $epTitle }}</span>
                            </div>
                            @if($epNum == $episodeNum)
                                <span class="w-2 h-2 bg-white animate-pulse flex-shrink-0"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

        </div>

    </div>


    <!-- Native Subtitle Custom Styling for Evonime Web Player -->
    <style>
        video::cue {
            background-color: rgba(10, 10, 10, 0.82) !important;
            color: #FFFFFF !important;
            font-family: 'Inter', -apple-system, sans-serif !important;
            font-size: 1.15rem !important;
            font-weight: 700 !important;
            line-height: 1.4 !important;
            text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.95), 0 0 2px #000 !important;
            padding: 2px 8px !important;
            border-radius: 4px !important;
        }
    </style>

    <!-- Player JavaScript -->
    <script>
        (function () {
            const episodeId = {{ $episodeId ?? 'null' }};
            const animeSlug = '{{ $anime['slug'] }}';
            const playerContainer = document.getElementById('video-player-container');
            const iframe = document.getElementById('player-iframe');
            const video = document.getElementById('player-video');
            const backdrop = document.getElementById('player-backdrop-layer');
            const playBtn = document.getElementById('big-play-btn');

            let isIframeMode = false;
            let sources = [];
            let providersData = { google_drive: [], otakudesu: [], local: [] };
            let hasGoogleDrive = false;
            let hasOtakudesu = false;
            let currentProvider = 'google_drive';
            let currentSubtitles = [];
            let downloadUrls = {};
            let allEmbedSources = [];
            let currentQuality = 'auto';
            let currentStreamUrl = '';
            let currentServerName = null;

            // CUSTOM HTML5 VIDEO CONTROLS LOGIC FOR 1080P GOOGLE DRIVE
            const customControls = document.getElementById('custom-video-controls');
            const centerOverlay = document.getElementById('custom-center-play-overlay');
            const centerFlashIcon = document.getElementById('center-flash-icon');
            const centerSvgPlay = document.getElementById('center-flash-svg-play');
            const centerSvgPause = document.getElementById('center-flash-svg-pause');

            const ctrlPlayBtn = document.getElementById('ctrl-play-btn');
            const ctrlPlayIcon = document.getElementById('ctrl-play-icon');
            const ctrlPauseIcon = document.getElementById('ctrl-pause-icon');

            const ctrlRewindBtn = document.getElementById('ctrl-rewind-btn');
            const ctrlForwardBtn = document.getElementById('ctrl-forward-btn');

            const ctrlVolumeBtn = document.getElementById('ctrl-volume-btn');
            const volHighIcon = document.getElementById('vol-high-icon');
            const volMuteIcon = document.getElementById('vol-mute-icon');
            const ctrlVolumeSlider = document.getElementById('ctrl-volume-slider');

            const ctrlCurrentTime = document.getElementById('ctrl-current-time');
            const ctrlDuration = document.getElementById('ctrl-duration');

            const seekContainer = document.getElementById('seek-container');
            const seekTooltip = document.getElementById('seek-tooltip');
            const seekBufferBar = document.getElementById('seek-buffer-bar');
            const seekProgressBar = document.getElementById('seek-progress-bar');
            const seekThumb = document.getElementById('seek-thumb');

            const ctrlSpeedBtn = document.getElementById('ctrl-speed-btn');
            const speedLabel = document.getElementById('speed-label');

            const ctrlPipBtn = document.getElementById('ctrl-pip-btn');
            const ctrlFullscreenBtn = document.getElementById('ctrl-fullscreen-btn');
            const fsExpandIcon = document.getElementById('fullscreen-expand-icon');
            const fsCompressIcon = document.getElementById('fullscreen-compress-icon');

            let controlsTimeout = null;
            let playbackSpeeds = [1.0, 1.25, 1.5, 2.0];
            let speedIndex = 0;

            function formatTime(seconds) {
                if (isNaN(seconds) || seconds === Infinity) return '00:00';
                const s = Math.floor(seconds % 60);
                const m = Math.floor((seconds / 60) % 60);
                const h = Math.floor(seconds / 3600);
                if (h > 0) {
                    return `${h}:${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
                }
                return `${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
            }

            function triggerCenterFlash(isPlay) {
                if (!centerOverlay || !centerFlashIcon) return;
                centerOverlay.classList.remove('hidden');
                if (isPlay) {
                    if (centerSvgPlay) centerSvgPlay.classList.remove('hidden');
                    if (centerSvgPause) centerSvgPause.classList.add('hidden');
                } else {
                    if (centerSvgPlay) centerSvgPlay.classList.add('hidden');
                    if (centerSvgPause) centerSvgPause.classList.remove('hidden');
                }
                centerFlashIcon.className = 'w-16 h-16 sm:w-20 sm:h-20 bg-black/85 border-2 border-amber-400 text-amber-400 rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(245,158,11,0.8)] transform scale-100 opacity-100 transition-all duration-300';
                setTimeout(() => {
                    centerFlashIcon.className = 'w-16 h-16 sm:w-20 sm:h-20 bg-black/85 border-2 border-amber-400 text-amber-400 rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(245,158,11,0.8)] transform scale-125 opacity-0 transition-all duration-300';
                    setTimeout(() => centerOverlay.classList.add('hidden'), 300);
                }, 400);
            }

            function toggleVideoPlay() {
                if (video.paused || video.ended) {
                    video.play().then(() => {
                        triggerCenterFlash(true);
                    }).catch(e => console.log('Play error:', e));
                } else {
                    video.pause();
                    triggerCenterFlash(false);
                }
            }

            window.togglePlayState = function () {
                toggleVideoPlay();
            };

            // Play/Pause Events
            if (ctrlPlayBtn) ctrlPlayBtn.addEventListener('click', toggleVideoPlay);

            video.addEventListener('play', () => {
                if (ctrlPlayIcon) ctrlPlayIcon.classList.add('hidden');
                if (ctrlPauseIcon) ctrlPauseIcon.classList.remove('hidden');
                const playText = document.getElementById('ctrl-play-text');
                if (playText) playText.textContent = 'Pause';
            });

            video.addEventListener('pause', () => {
                if (ctrlPlayIcon) ctrlPlayIcon.classList.remove('hidden');
                if (ctrlPauseIcon) ctrlPauseIcon.classList.add('hidden');
                const playText = document.getElementById('ctrl-play-text');
                if (playText) playText.textContent = 'Play';
            });

            // Quick Seek -10s / +10s
            if (ctrlRewindBtn) {
                ctrlRewindBtn.addEventListener('click', () => {
                    video.currentTime = Math.max(0, video.currentTime - 10);
                });
            }
            if (ctrlForwardBtn) {
                ctrlForwardBtn.addEventListener('click', () => {
                    video.currentTime = Math.min(video.duration || 0, video.currentTime + 10);
                });
            }

            // Time & Progress Update
            video.addEventListener('timeupdate', () => {
                if (ctrlCurrentTime) ctrlCurrentTime.textContent = formatTime(video.currentTime);
                if (ctrlDuration && video.duration) ctrlDuration.textContent = formatTime(video.duration);

                if (video.duration) {
                    const pct = (video.currentTime / video.duration) * 100;
                    if (seekProgressBar) seekProgressBar.style.width = pct + '%';
                    if (seekThumb) seekThumb.style.left = pct + '%';
                }
            });

            video.addEventListener('loadedmetadata', () => {
                if (ctrlDuration) ctrlDuration.textContent = formatTime(video.duration);
            });

            // Buffer Progress Bar
            video.addEventListener('progress', () => {
                if (video.buffered.length > 0 && video.duration) {
                    const bufEnd = video.buffered.end(video.buffered.length - 1);
                    const pct = (bufEnd / video.duration) * 100;
                    if (seekBufferBar) seekBufferBar.style.width = pct + '%';
                }
            });

            // Seek Bar Interactions
            let isSeeking = false;
            function updateSeekFromEvent(e) {
                if (!seekContainer || !video.duration) return;
                const rect = seekContainer.getBoundingClientRect();
                const pos = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                video.currentTime = pos * video.duration;
            }

            if (seekContainer) {
                seekContainer.addEventListener('mousedown', (e) => {
                    isSeeking = true;
                    updateSeekFromEvent(e);
                });

                seekContainer.addEventListener('mousemove', (e) => {
                    const rect = seekContainer.getBoundingClientRect();
                    const pos = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                    if (seekTooltip && video.duration) {
                        seekTooltip.textContent = formatTime(pos * video.duration);
                        seekTooltip.style.left = (pos * 100) + '%';
                        seekTooltip.classList.remove('hidden');
                    }
                    if (isSeeking) updateSeekFromEvent(e);
                });

                seekContainer.addEventListener('mouseleave', () => {
                    if (seekTooltip) seekTooltip.classList.add('hidden');
                    isSeeking = false;
                });

                window.addEventListener('mouseup', () => {
                    isSeeking = false;
                });
            }

            // Volume Controls
            function updateVolumeUI() {
                if (video.muted || video.volume === 0) {
                    if (volHighIcon) volHighIcon.classList.add('hidden');
                    if (volMuteIcon) volMuteIcon.classList.remove('hidden');
                    if (ctrlVolumeSlider) ctrlVolumeSlider.value = 0;
                } else {
                    if (volHighIcon) volHighIcon.classList.remove('hidden');
                    if (volMuteIcon) volMuteIcon.classList.add('hidden');
                    if (ctrlVolumeSlider) ctrlVolumeSlider.value = video.volume;
                }
            }

            if (ctrlVolumeBtn) {
                ctrlVolumeBtn.addEventListener('click', () => {
                    video.muted = !video.muted;
                    updateVolumeUI();
                });
            }

            if (ctrlVolumeSlider) {
                ctrlVolumeSlider.addEventListener('input', (e) => {
                    video.volume = parseFloat(e.target.value);
                    video.muted = (video.volume === 0);
                    updateVolumeUI();
                });
            }

            // Playback Speed Toggle
            if (ctrlSpeedBtn) {
                ctrlSpeedBtn.addEventListener('click', () => {
                    speedIndex = (speedIndex + 1) % playbackSpeeds.length;
                    const spd = playbackSpeeds[speedIndex];
                    video.playbackRate = spd;
                    if (speedLabel) speedLabel.textContent = spd.toFixed(1) + 'x';
                    if (window.showToast) {
                        window.showToast(`Kecepatan pemutaran: ${spd}x`, 'info', null, 2000);
                    }
                });
            }

            // Picture-in-Picture
            if (ctrlPipBtn) {
                ctrlPipBtn.addEventListener('click', async () => {
                    try {
                        if (document.pictureInPictureElement) {
                            await document.exitPictureInPicture();
                        } else if (document.pictureInPictureEnabled) {
                            await video.requestPictureInPicture();
                        }
                    } catch (err) {
                        console.error('PiP Error:', err);
                    }
                });
            }

            // Fullscreen Handler
            function toggleFullscreen() {
                const container = document.getElementById('video-player-container');
                if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                    if (container.requestFullscreen) {
                        container.requestFullscreen();
                    } else if (container.webkitRequestFullscreen) {
                        container.webkitRequestFullscreen();
                    }
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    }
                }
            }

            if (ctrlFullscreenBtn) ctrlFullscreenBtn.addEventListener('click', toggleFullscreen);

            document.addEventListener('fullscreenchange', updateFullscreenUI);
            document.addEventListener('webkitfullscreenchange', updateFullscreenUI);

            function updateFullscreenUI() {
                const isFS = Boolean(document.fullscreenElement || document.webkitFullscreenElement);
                if (fsExpandIcon && fsCompressIcon) {
                    if (isFS) {
                        fsExpandIcon.classList.add('hidden');
                        fsCompressIcon.classList.remove('hidden');
                    } else {
                        fsExpandIcon.classList.remove('hidden');
                        fsCompressIcon.classList.add('hidden');
                    }
                }
            }

            // Theater Mode Toggle (Cinema Full-Width View)
            let isTheaterMode = false;
            window.toggleTheaterMode = function () {
                const mainCol = document.getElementById('main-player-col');
                const sidebarCol = document.getElementById('sidebar-col');
                const theaterBtnLabel = document.getElementById('theater-mode-label');

                isTheaterMode = !isTheaterMode;
                if (isTheaterMode) {
                    if (mainCol) {
                        mainCol.classList.remove('lg:col-span-3');
                        mainCol.classList.add('lg:col-span-4');
                    }
                    if (sidebarCol) {
                        sidebarCol.classList.remove('lg:col-span-1');
                        sidebarCol.classList.add('lg:col-span-4');
                    }
                    if (theaterBtnLabel) theaterBtnLabel.textContent = 'Normal';
                    if (window.showToast) window.showToast('Mode Teater Aktif (Layar Lebih Lebar)', 'info');
                } else {
                    if (mainCol) {
                        mainCol.classList.remove('lg:col-span-4');
                        mainCol.classList.add('lg:col-span-3');
                    }
                    if (sidebarCol) {
                        sidebarCol.classList.remove('lg:col-span-4');
                        sidebarCol.classList.add('lg:col-span-1');
                    }
                    if (theaterBtnLabel) theaterBtnLabel.textContent = 'Teater';
                    if (window.showToast) window.showToast('Mode Normal Aktif', 'info');
                }
            };

            // Video Fit Mode Toggle (Fit Normal vs Fill Penuh)
            let isVideoCover = false;
            window.toggleVideoFit = function () {
                const v = document.getElementById('player-video');
                const fitLabel = document.getElementById('fit-mode-label');
                isVideoCover = !isVideoCover;
                if (isVideoCover) {
                    v.classList.remove('object-contain');
                    v.classList.add('object-cover');
                    if (fitLabel) fitLabel.textContent = 'Penuh';
                    if (window.showToast) window.showToast('Mode Video: Memenuhi Layar (Fill)', 'info');
                } else {
                    v.classList.remove('object-cover');
                    v.classList.add('object-contain');
                    if (fitLabel) fitLabel.textContent = 'Fit';
                    if (window.showToast) window.showToast('Mode Video: Proporsional (Fit)', 'info');
                }
            };

            // Video Area Click & Mouse Move Auto-Hide Controls
            if (playerContainer) {
                playerContainer.addEventListener('mousemove', showControlsWithTimeout);
                playerContainer.addEventListener('mouseleave', () => {
                    if (!video.paused && customControls) {
                        customControls.classList.add('opacity-0');
                    }
                });
            }

            function showControlsWithTimeout() {
                if (!customControls) return;
                customControls.classList.remove('opacity-0');
                if (controlsTimeout) clearTimeout(controlsTimeout);
                if (!video.paused) {
                    controlsTimeout = setTimeout(() => {
                        customControls.classList.add('opacity-0');
                    }, 3500);
                }
            }

            video.addEventListener('click', (e) => {
                toggleVideoPlay();
            });

            // Keyboard Shortcuts when player is active
            document.addEventListener('keydown', (e) => {
                if (document.activeElement && (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA')) return;
                if (video.classList.contains('hidden')) return;

                if (e.code === 'Space' || e.code === 'KeyK') {
                    e.preventDefault();
                    toggleVideoPlay();
                } else if (e.code === 'KeyF') {
                    e.preventDefault();
                    toggleFullscreen();
                } else if (e.code === 'KeyT') {
                    e.preventDefault();
                    window.toggleTheaterMode();
                } else if (e.code === 'KeyM') {
                    e.preventDefault();
                    video.muted = !video.muted;
                    updateVolumeUI();
                } else if (e.code === 'ArrowLeft') {
                    e.preventDefault();
                    video.currentTime = Math.max(0, video.currentTime - 5);
                } else if (e.code === 'ArrowRight') {
                    e.preventDefault();
                    video.currentTime = Math.min(video.duration || 0, video.currentTime + 5);
                }
            });

            window.playLocalProxy1080p = function () {
                if (!hasGoogleDrive || !providersData.google_drive || providersData.google_drive.length === 0) {
                    if (window.showToast) {
                        window.showToast('Sumber Google Drive 1080p belum tersedia untuk episode ini. Memutar via Otakudesu.', 'warning');
                    }
                    return;
                }
                const gdrive = providersData.google_drive[0];
                const proxyUrl = gdrive.proxy_url || ('/stream/' + animeSlug + '/' + episodeNumber + '/1080p');

                currentProvider = 'google_drive';
                currentQuality = '1080p';
                currentServerName = 'Localhost Proxy (1080p FHD)';
                currentStreamUrl = proxyUrl;

                updateProviderTabUI('google_drive');
                updateQualityButtonUI('1080p');
                updateServerBadge();

                // Sembunyikan mirror Otakudesu & iframe
                const mirrorBox = document.getElementById('mirror-selectors');
                if (mirrorBox) mirrorBox.classList.add('hidden');
                iframe.classList.add('hidden');
                hideIframeFallback();

                // Tampilkan HTML5 Video Player & Custom Controls
                video.classList.remove('hidden');
                video.controls = false;
                if (customControls) customControls.classList.remove('hidden');
                backdrop.classList.add('hidden');
                playBtn.classList.add('hidden');

                // Muat URL localhost streaming proxy ke HTML5 video
                const fullProxyUrl = proxyUrl.startsWith('http') ? proxyUrl : (window.location.origin + proxyUrl);
                if (video.src !== fullProxyUrl && video.src !== proxyUrl) {
                    video.src = proxyUrl;
                    video.load();
                }

                // Pasang subtitle WebVTT ke HTML5 video jika tersedia
                const oldTracks = video.querySelectorAll('track');
                oldTracks.forEach(t => t.remove());

                if (currentSubtitles && currentSubtitles.length > 0) {
                    currentSubtitles.forEach(sub => {
                        const track = document.createElement('track');
                        track.kind = 'subtitles';
                        track.label = sub.label || 'Indonesia';
                        track.srclang = sub.language || 'id';
                        track.src = sub.url;
                        if (sub.is_default) {
                            track.default = true;
                        }
                        video.appendChild(track);
                    });
                }

                video.play().catch(e => console.log('Autoplay deferred until user interaction:', e));

                const modeBadge = document.getElementById('player-mode-badge');
                if (modeBadge) {
                    modeBadge.textContent = 'Localhost Proxy • 1080p FHD';
                    modeBadge.className = 'text-[11px] font-mono text-emerald-400 font-bold bg-emerald-950/40 px-2.5 py-1 rounded border border-emerald-500/40';
                }

                if (window.showToast) {
                    window.showToast('Memutar 1080p FHD via Localhost Streaming Proxy (Google Drive Storage)', 'success');
                }
            };

            window.selectQualityDirect = function (quality) {
                if (quality === '1080p') {
                    window.playLocalProxy1080p();
                } else {
                    // Hentikan HTML5 video & sembunyikan kontrol custom jika sedang main
                    video.pause();
                    video.classList.add('hidden');
                    if (customControls) customControls.classList.add('hidden');

                    currentProvider = 'otakudesu';
                    updateProviderTabUI('otakudesu');
                    updateQualityButtonUI(quality);

                    const modeBadge = document.getElementById('player-mode-badge');
                    if (modeBadge) {
                        modeBadge.textContent = `Otakudesu CDN (${quality})`;
                        modeBadge.className = 'text-[11px] font-mono text-zinc-400 font-bold bg-[#141414] px-2.5 py-1 rounded border border-zinc-800';
                    }

                    // Tampilkan selector Otakudesu dan pilih kualitas
                    const mirrorBox = document.getElementById('mirror-selectors');
                    if (mirrorBox) mirrorBox.classList.remove('hidden');

                    selectQuality(quality);
                }
            };

            function updateQualityButtonUI(activeQuality) {
                const container = document.getElementById('video-player-container');
                const ambientGlow = document.getElementById('player-ambient-glow');
                const vipWatermark = document.getElementById('vip-1080p-watermark');
                const gdriveInfoBox = document.getElementById('gdrive-1080p-info-box');
                const statusBadge = document.getElementById('player-status-badge');

                document.querySelectorAll('.quality-btn').forEach(btn => {
                    const q = btn.getAttribute('data-quality');
                    if (q === '1080p') {
                        if (activeQuality === '1080p') {
                            btn.className = 'quality-btn px-4 py-1.5 rounded-lg text-xs font-black border transition-all bg-[#E63946] border-[#F5F0E6] text-white shadow-[3px_3px_0px_#F5F0E6] scale-105 cursor-pointer flex items-center gap-1.5';
                        } else {
                            btn.className = 'quality-btn px-4 py-1.5 rounded-lg text-xs font-black border transition-all bg-amber-950/40 border-amber-500/50 text-amber-300 hover:bg-amber-900/60 shadow-[2px_2px_0px_#f59e0b] cursor-pointer flex items-center gap-1.5';
                        }
                    } else {
                        if (q === activeQuality) {
                            btn.className = 'quality-btn px-3.5 py-1.5 rounded-lg text-xs font-black border transition-all bg-[#E63946] border-[#F5F0E6] text-white shadow-[2px_2px_0px_#F5F0E6] cursor-pointer';
                        } else {
                            btn.className = 'quality-btn px-3 py-1.5 rounded-lg text-xs font-black border transition-all bg-[#141414] border-zinc-700 text-zinc-400 hover:text-white cursor-pointer';
                        }
                    }
                });

                // TAMPILAN SPESIAL JIKA RESOLUSI 1080p (GOOGLE DRIVE STORAGE)
                if (activeQuality === '1080p') {
                    if (container) {
                        container.className = 'relative w-full aspect-video bg-[#0D0D0D] rounded-xl overflow-hidden border-2 border-amber-400 shadow-[0_0_35px_rgba(245,158,11,0.55),6px_6px_0px_#F59E0B] select-none transition-all duration-500 z-10';
                    }
                    if (ambientGlow) ambientGlow.classList.remove('hidden');
                    if (vipWatermark) vipWatermark.classList.remove('hidden');
                    if (gdriveInfoBox) gdriveInfoBox.classList.remove('hidden');
                    if (statusBadge) {
                        statusBadge.textContent = 'GOOGLE DRIVE 1080p';
                        statusBadge.className = 'px-1.5 py-0.5 bg-amber-500 text-black rounded text-[10px] font-black';
                    }
                } else {
                    if (container) {
                        container.className = 'relative w-full aspect-video bg-[#0D0D0D] rounded-xl overflow-hidden border-2 border-[#F5F0E6] shadow-[6px_6px_0px_#F5F0E6] select-none transition-all duration-500 z-10';
                    }
                    if (ambientGlow) ambientGlow.classList.add('hidden');
                    if (vipWatermark) vipWatermark.classList.add('hidden');
                    if (gdriveInfoBox) gdriveInfoBox.classList.add('hidden');
                    if (statusBadge) {
                        statusBadge.textContent = 'OTAKUDESU';
                        statusBadge.className = 'px-1.5 py-0.5 bg-[#E63946] text-white rounded text-[10px] font-black';
                    }
                }
            }

            window.switchProviderTab = function (provider) {
                if (provider === 'google_drive') {
                    window.playLocalProxy1080p();
                } else {
                    window.selectQualityDirect('720p');
                }
            };

            function updateProviderTabUI(activeProvider) {
                const tabGDrive = document.getElementById('provider-tab-gdrive');
                const tabOtaku = document.getElementById('provider-tab-otaku');
                const gdriveBadge = document.getElementById('gdrive-badge');

                if (tabGDrive) {
                    if (activeProvider === 'google_drive') {
                        tabGDrive.className = 'px-3.5 py-2 rounded-lg text-xs font-black border transition-all flex items-center gap-2 bg-[#E63946] border-[#F5F0E6] text-white shadow-[3px_3px_0px_#F5F0E6] cursor-pointer scale-105';
                        if (gdriveBadge) {
                            gdriveBadge.className = 'px-1.5 py-0.5 bg-black/40 text-white text-[10px] rounded font-mono font-black border border-white/40';
                        }
                    } else {
                        tabGDrive.className = 'px-3.5 py-2 rounded-lg text-xs font-black border transition-all flex items-center gap-2 bg-[#1A1A1A] border-amber-400 text-white font-bold hover:text-amber-200 cursor-pointer';
                        if (gdriveBadge) {
                            gdriveBadge.className = 'px-1.5 py-0.5 bg-amber-500/20 text-white text-[10px] rounded font-mono font-black border border-amber-400/50';
                        }
                    }
                }

                if (tabOtaku) {
                    if (activeProvider === 'otakudesu') {
                        tabOtaku.className = 'px-3.5 py-2 rounded-lg text-xs font-black border transition-all flex items-center gap-2 bg-[#E63946] border-[#F5F0E6] text-white shadow-[2px_2px_0px_#F5F0E6] cursor-pointer';
                    } else {
                        tabOtaku.className = 'px-3.5 py-2 rounded-lg text-xs font-black border transition-all flex items-center gap-2 bg-[#1A1A1A] border-zinc-700 text-zinc-400 hover:text-white cursor-pointer';
                    }
                }
            }

            function updateServerBadge() {
                const badge = document.getElementById('current-server-badge');
                if (badge) {
                    const parts = [];
                    if (currentServerName) parts.push(currentServerName);
                    if (currentQuality && currentQuality !== 'auto') parts.push(currentQuality);
                    badge.textContent = parts.length ? parts.join(' • ') : 'Memuat...';
                }
            }
            function markIframeLoaded() {
                hideIframeFallback();
            }
            function hideIframeFallback() {
                const fallback = document.getElementById('iframe-fallback');
                if (fallback) fallback.classList.add('hidden');
            }
            /**
             * Deteksi iframe embed yang diblokir (X-Frame-Options / CSP frame-ancestors).
             * Jika frame diblokir, browser menampilkan error-document kosong yang masih
             * bisa diakses (about:blank) → itulah penanda "blocked".
             * Akses cross-origin yang normal akan melempar SecurityError → dianggap OK.
             */
            function detectIframeBlocked() {
                try {
                    const doc = iframe.contentDocument;
                    if (!doc) return false; // tidak bisa diakses = cross-origin normal
                    const url = doc.URL || '';
                    if (url === 'about:blank' || url === '') return true;
                    if (doc.body && doc.body.innerHTML.trim() === '') return false; // masih loading
                    return false;
                } catch (e) {
                    return false;
                }
            }
            function showIframeFallback(reason) {
                // Tampilkan hanya jika embed diblokir (X-Frame-Options/CSP).
                if (!isIframeMode) return;
                const fallback = document.getElementById('iframe-fallback');
                const msg = document.getElementById('iframe-fallback-msg');
                const openBtn = document.getElementById('iframe-open-new-tab');
                const btnTextSpan = document.getElementById('iframe-open-btn-text');
                const qLabel = document.getElementById('fallback-quality-label');
                const sLabel = document.getElementById('fallback-server-label');

                const cleanUserMsg = 'Server streaming membatasi pemutaran langsung di dalam iframe. Silakan buka di tab baru untuk menonton video dengan kualitas terbaik secara lancar.';
                
                if (msg) {
                    msg.textContent = cleanUserMsg;
                }
                const displayQuality = currentQuality && currentQuality !== 'auto' ? currentQuality : 'Auto';
                if (qLabel) qLabel.textContent = displayQuality;
                if (sLabel) sLabel.textContent = currentServerName || 'Otakudesu';

                if (openBtn && currentStreamUrl) {
                    openBtn.href = currentStreamUrl;
                    if (btnTextSpan) {
                        btnTextSpan.textContent = `BUKA [${displayQuality}] DI TAB BARU`;
                    }
                }
                // Atur tombol fallback switch antar provider (Otakudesu <-> Google Drive)
                const switchGdriveBtn = document.getElementById('iframe-switch-gdrive-btn');
                const switchOtakuBtn = document.getElementById('iframe-switch-otaku-btn');

                if (switchGdriveBtn) {
                    if (currentProvider !== 'google_drive' && hasGoogleDrive) {
                        switchGdriveBtn.classList.remove('hidden');
                    } else {
                        switchGdriveBtn.classList.add('hidden');
                    }
                }

                if (switchOtakuBtn) {
                    if (currentProvider === 'google_drive' && hasOtakudesu) {
                        switchOtakuBtn.classList.remove('hidden');
                    } else {
                        switchOtakuBtn.classList.add('hidden');
                    }
                }

                if (fallback) fallback.classList.remove('hidden');

                // TAMPILKAN NOTIFIKASI PEMBERITAHUAN MEMBUKA TAB BARU KUALITAS TERPILIH
                if (window.showToast && currentStreamUrl) {
                    window.showToast(
                        `Putar video kualitas ${displayQuality} (${currentServerName || 'Server'}) di tab baru`,
                        'tab-prompt',
                        {
                            text: `BUKA ${displayQuality} ↗`,
                            url: currentStreamUrl,
                            target: '_blank'
                        },
                        7500
                    );
                }
            }
            function initSelectors(embedSources) {
                allEmbedSources = embedSources || [];
                const qualities = [...new Set(allEmbedSources.map(s => s.quality).filter(Boolean))];
                qualities.sort((a, b) => {
                    if (a === 'auto') return -1;
                    if (b === 'auto') return 1;
                    return parseInt(a) - parseInt(b);
                });
                const qContainer = document.getElementById('quality-selector');
                if (!qContainer) return;
                qContainer.innerHTML = '';
                qualities.forEach(q => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'selector-btn' + (q === 'auto' ? ' active' : '');
                    btn.textContent = q === 'auto' ? 'Auto' : q;
                    btn.onclick = () => selectQuality(q);
                    qContainer.appendChild(btn);
                });
                currentQuality = qualities.length > 0 ? qualities[0] : 'auto';
                renderServers();
            }
            function selectQuality(quality) {
                currentQuality = quality;
                document.querySelectorAll('#quality-selector .selector-btn').forEach(btn => {
                    btn.classList.toggle('active', btn.textContent === (quality === 'auto' ? 'Auto' : quality));
                });
                if (window.showToast) {
                    window.showToast(`Kualitas video diubah ke ${quality === 'auto' ? 'Auto Quality' : quality}`, 'info');
                }
                renderServers();
            }
            function renderServers() {
                const serversForQuality = allEmbedSources.filter(s => s.quality === currentQuality);
                const sContainer = document.getElementById('server-selector');
                if (!sContainer) return;
                sContainer.innerHTML = '';
                if (serversForQuality.length === 0) {
                    sContainer.innerHTML = '<span class="text-xs text-zinc-500">Tidak ada server untuk kualitas ini</span>';
                    return;
                }
                serversForQuality.forEach((source, i) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'selector-btn' + (i === 0 ? ' active' : '');
                    btn.textContent = source.server_name || ('Server ' + (i + 1));
                    btn.onclick = () => selectServer(source, btn);
                    sContainer.appendChild(btn);
                });
                selectServer(serversForQuality[0], sContainer.firstElementChild);
            }
            async function selectServer(source, buttonEl) {
                document.querySelectorAll('#server-selector .selector-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                if (buttonEl) buttonEl.classList.add('active');
                currentServerName = source.server_name || ('Server ' + currentQuality);
                if (source.quality) currentQuality = source.quality;
                updateServerBadge();
                const loadingEl = document.getElementById('player-loading');
                if (source.needs_resolve && source.data_content) {
                    if (loadingEl) { loadingEl.classList.remove('hidden'); loadingEl.classList.add('flex'); }
                    try {
                        const res = await fetch('/api/v1/stream/resolve-mirror', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').content) || '',
                            },
                            body: JSON.stringify({ data_content: source.data_content }),
                        });
                        const data = await res.json();
                        if (data.success && data.url) {
                            const isBlocked = data.embeddable === false;
                            showIframe(data.url, isBlocked ? (data.reason || null) : null);

                            // Notifikasi Pemberitahuan Buka di Tab Baru dengan Kualitas yang Dipilih
                            if (window.showToast) {
                                window.showToast(
                                    `Video ${currentQuality} (${currentServerName}) siap diputar. Tonton di tab baru tanpa kendala.`,
                                    isBlocked ? 'warning' : 'tab-prompt',
                                    {
                                        text: `BUKA ${currentQuality} ↗`,
                                        url: data.url,
                                        target: '_blank'
                                    },
                                    6500
                                );
                            }
                        } else {
                            const errMsg = data.message || 'Server streaming tidak merespons. Coba server lain.';
                            if (window.showToast) {
                                window.showToast(errMsg, 'error', null, 5000);
                            } else {
                                alert(errMsg);
                            }
                        }
                    } catch (e) {
                        console.error(e);
                        if (window.showToast) {
                            window.showToast('Gagal memuat server stream. Coba lagi.', 'error', null, 5000);
                        } else {
                            alert('Gagal memuat server. Coba lagi.');
                        }
                    } finally {
                        if (loadingEl) { loadingEl.classList.add('hidden'); loadingEl.classList.remove('flex'); }
                    }
                } else if (source.url) {
                    const isBlocked = source.embeddable === false;
                    showIframe(
                        source.url,
                        isBlocked ? (source.embed_block_reason || null) : null
                    );

                    if (window.showToast) {
                        window.showToast(
                            `Video kualitas ${currentQuality} (${currentServerName}) siap. Klik untuk membuka di tab baru.`,
                            isBlocked ? 'warning' : 'tab-prompt',
                            {
                                text: `BUKA ${currentQuality} ↗`,
                                url: source.url,
                                target: '_blank'
                            },
                            6500
                        );
                    }
                }
            }

            function slugToEpisodeNumber(slug) {
                if (!slug) return null;
                const match = slug.match(/episode-(\d+)/i);
                return match ? match[1] : null;
            }

            function reloadIframe() {
                if (!currentStreamUrl) return;
                const url = currentStreamUrl;
                hideIframeFallback();
                iframe.setAttribute('src', 'about:blank');
                window.setTimeout(() => showIframe(url, null), 60);
            }

            function showIframe(url, blockReason) {
                isIframeMode = true;
                currentStreamUrl = url;
                updateServerBadge();
                iframe.removeAttribute('srcdoc');
                iframe.setAttribute('src', url);
                iframe.setAttribute('referrerpolicy', 'no-referrer');
                iframe.setAttribute(
                    'allow',
                    'autoplay; encrypted-media; fullscreen; picture-in-picture'
                );
                iframe.style.width = '100%';
                iframe.style.height = '100%';
                iframe.classList.remove('hidden');
                video.classList.add('hidden');
                backdrop.classList.add('hidden');
                playBtn.classList.add('hidden');
                hideIframeFallback();
                // Server sudah diketahui menolak embed (X-Frame-Options / CSP
                // frame-ancestors) → tampilkan fallback + tombol buka tab baru.
                if (blockReason) {
                    showIframeFallback(blockReason);
                    return;
                }
                // Deteksi blokir embed: iframe yang diblokir X-Frame-Options / CSP
                // frame-ancestors memuat error-document kosong (about:blank).
                if (showIframe._timer) clearTimeout(showIframe._timer);
                showIframe._timer = setTimeout(() => {
                    if (!isIframeMode || !currentStreamUrl) return;
                    if (detectIframeBlocked()) showIframeFallback(null);
                }, 8000);
            }

            function showVideo() {
                isIframeMode = false;
                iframe.classList.add('hidden');
                video.classList.remove('hidden');
            }

            function showBackdrop() {
                backdrop.classList.remove('hidden');
                playBtn.classList.remove('hidden');
            }

            function renderDownloadSection() {
                const downloadSection = document.getElementById('download-section');
                const downloadProviders = document.getElementById('download-providers');
                const downloadEmpty = document.getElementById('download-empty');
                downloadProviders.innerHTML = '';

                const hasUrls = downloadUrls
                    && Object.keys(downloadUrls).length > 0
                    && Object.values(downloadUrls).some(res => Array.isArray(res) && res.length > 0);

                if (!hasUrls) {
                    downloadSection.classList.add('hidden');
                    if (downloadEmpty) downloadEmpty.classList.remove('hidden');
                    return;
                }

                if (downloadEmpty) downloadEmpty.classList.add('hidden');
                downloadSection.classList.remove('hidden');

                Object.entries(downloadUrls).forEach(([format, resolutions]) => {
                    if (!Array.isArray(resolutions)) return;

                    resolutions.forEach(res => {
                        const group = document.createElement('div');
                        group.className = 'resolution-group';

                        const label = document.createElement('span');
                        label.className = 'res-label';
                        label.textContent = res.resolution || format;
                        group.appendChild(label);

                        const btns = document.createElement('div');
                        btns.className = 'provider-buttons';

                        (res.urls || []).forEach(urlItem => {
                            const btn = document.createElement('a');
                            btn.href = urlItem.url;
                            btn.target = '_blank';
                            btn.rel = 'noopener noreferrer';
                            btn.className = 'provider-btn';
                            btn.textContent = urlItem.provider || 'Download';
                            btns.appendChild(btn);
                        });

                        group.appendChild(btns);
                        downloadProviders.appendChild(group);
                    });
                });
            }
            function loadSources() {
                if (!episodeId) {
                    console.warn('No episodeId provided for player.');
                    return;
                }

                fetch(`/api/v1/stream/sources/${episodeId}`)
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success || !data.data) {
                            console.error('Failed to load sources:', data);
                            return;
                        }
                        sources = data.data.sources || [];
                        providersData = data.data.providers || { google_drive: [], otakudesu: [], local: [] };
                        currentSubtitles = data.data.subtitles || [];
                        downloadUrls = data.data.download_urls || {};
                        const nav = data.data.navigation || {};
                        updateNavButtons(nav);
                        renderDownloadSection();

                        hasGoogleDrive = Boolean(data.data.has_gdrive && providersData.google_drive && providersData.google_drive.length > 0);
                        hasOtakudesu = Boolean(data.data.has_otakudesu || (providersData.otakudesu && providersData.otakudesu.length > 0));

                        // Perbarui status dan tampilan tombol tab Google Drive
                        const tabGDrive = document.getElementById('provider-tab-gdrive');
                        const gdriveBadge = document.getElementById('gdrive-badge');
                        if (tabGDrive) {
                            if (hasGoogleDrive) {
                                tabGDrive.classList.remove('opacity-40', 'cursor-not-allowed');
                                tabGDrive.classList.add('cursor-pointer');
                                if (gdriveBadge) {
                                    gdriveBadge.textContent = '1080p FHD';
                                    gdriveBadge.className = 'px-1.5 py-0.5 bg-amber-500/20 text-amber-300 text-[10px] rounded font-mono font-black border border-amber-500/30';
                                }
                            } else {
                                tabGDrive.classList.add('opacity-40', 'cursor-not-allowed');
                                tabGDrive.classList.remove('cursor-pointer');
                                if (gdriveBadge) {
                                    gdriveBadge.textContent = 'Belum Ada';
                                    gdriveBadge.className = 'px-1.5 py-0.5 bg-zinc-800 text-zinc-500 text-[10px] rounded font-mono font-bold';
                                }
                            }
                        }

                        // JIKA ADA GOOGLE DRIVE 1080p: JADIKAN SUMBER UTAMA DEFAULT!
                        if (hasGoogleDrive) {
                            window.switchProviderTab('google_drive');
                        } else if (hasOtakudesu) {
                            window.switchProviderTab('otakudesu');
                        } else if (sources.length > 0) {
                            const src = sources[0];
                            currentServerName = src.server_name || 'Server';
                            currentQuality = src.quality || 'auto';
                            updateServerBadge();
                            if (src.is_embed && src.url) {
                                showIframe(src.url, src.embeddable === false ? (src.embed_block_reason || null) : null);
                            } else if (src.is_m3u8) {
                                showVideo();
                                video.src = src.url;
                                if (window.Hls && window.Hls.isSupported()) {
                                    const hls = new window.Hls();
                                    hls.loadSource(src.url);
                                    hls.attachMedia(video);
                                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                                    video.src = src.url;
                                }
                            } else {
                                showVideo();
                                video.src = src.url;
                            }
                        } else {
                            console.warn('Tidak ada sumber streaming untuk episode ini.');
                        }
                    })
                    .catch(err => {
                        console.error('Error loading sources:', err);
                    });
            }

            function updateNavButtons(nav) {
                const prevBtn = document.getElementById('nav-prev-btn');
                const nextBtn = document.getElementById('nav-next-btn');

                if (nav.has_previous && nav.previous_slug) {
                    const epNum = slugToEpisodeNumber(nav.previous_slug);
                    if (epNum) {
                        prevBtn.disabled = false;
                        prevBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'text-zinc-500');
                        prevBtn.classList.add('text-[#F5F0E6]', 'hover:bg-zinc-800');
                        prevBtn.onclick = () => {
                            window.location.href = '/watch/' + animeSlug + '/' + epNum;
                        };
                    }
                }

                if (nav.has_next && nav.next_slug) {
                    const epNum = slugToEpisodeNumber(nav.next_slug);
                    if (epNum) {
                        nextBtn.disabled = false;
                        nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        nextBtn.classList.add('hover:bg-red-700');
                        nextBtn.onclick = () => {
                            window.location.href = '/watch/' + animeSlug + '/' + epNum;
                        };
                    }
                }
            }

            video.addEventListener('play', () => {
                backdrop.classList.add('hidden');
                playBtn.classList.add('hidden');
            });

            video.addEventListener('pause', () => {
                showBackdrop();
            });

            loadSources();
        })();
    </script>

    {{-- Continue Watching (Lanjut Tonton): tracking level-EPISODE --}}
    {{-- Player memakai iframe pihak ketiga (cross-origin) sehingga --}}
    {{-- video.currentTime tidak bisa dibaca → progres tidak dikirim dari browser. --}}
    @push('scripts')
    <script>
        (function () {
            const episodeId = {{ $episodeId ?? 'null' }};
            const animeSlug = @json($anime['slug'] ?? '');
            const episodeNumber = {{ (int) ($episodeNum ?? 0) }};
            const animeTitle = @json($anime['title'] ?? '');
            const posterUrl = @json($anime['poster'] ?? $anime['banner'] ?? '');

            if (!episodeId) return;

            @auth
            // User login: catat tontonan lewat route web (session + CSRF).
            // Endpoint API /api/v1/watch/record tetap ada untuk klien Bearer token;
            // grup middleware "api" tidak menjalankan sesi browser.
            fetch(@json(route('watch.record')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token()),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ episode_id: episodeId }),
                credentials: 'same-origin',
            })
                .then(response => {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .catch(error => console.error('Record watch failed:', error));
            @else
            // Guest: simpan riwayat lokal saja (tanpa API)
            try {
                const key = 'evonime_watch_history';
                let history = JSON.parse(localStorage.getItem(key) || '[]');
                history = history.filter(h => h.episode_id !== episodeId);
                history.unshift({
                    episode_id: episodeId,
                    anime_slug: animeSlug,
                    anime_title: animeTitle,
                    episode_number: episodeNumber,
                    poster_url: posterUrl,
                    last_watched_at: new Date().toISOString(),
                });
                history = history.slice(0, 50);
                localStorage.setItem(key, JSON.stringify(history));
            } catch (e) {
                console.error('LocalStorage error:', e);
            }
            @endauth
        })();
    </script>
    @endpush
</x-app-layout>
