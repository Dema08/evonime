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
            <div class="lg:col-span-3 space-y-5">
                
                <!-- CUSTOM VIDEO PLAYER CONTAINER -->
                <div id="video-player-container" class="relative w-full aspect-video bg-[#0D0D0D] rounded-xl overflow-hidden border-2 border-[#F5F0E6] shadow-[6px_6px_0px_#F5F0E6] group select-none">
                    
                    <!-- Iframe Embed Mode (Otakudesu) -->
                    <iframe id="player-iframe" 
                            src="" 
                            allowfullscreen
                            allow="autoplay; encrypted-media; picture-in-picture"
                            class="absolute inset-0 w-full h-full border-0 hidden">
                    </iframe>

                    <!-- HTML5 Video Mode (fallback for m3u8) -->
                    <video id="player-video" 
                           class="absolute inset-0 w-full h-full hidden" 
                           playsinline></video>

                    <!-- Poster Backdrop (shown when paused/standby) -->
                    <div id="player-backdrop-layer" class="relative w-full h-full flex items-center justify-center overflow-hidden bg-[#0D0D0D]">
                        <img id="player-backdrop" src="{{ $anime['banner'] }}" referrerpolicy="no-referrer" alt="Player Backdrop" class="absolute inset-0 w-full h-full object-cover filter brightness-70 contrast-125">
                        <div class="absolute inset-0 bg-[#0D0D0D]/50 halftone-bg"></div>
                        
                        <!-- Animated Central Play Button when Paused -->
                        <button id="big-play-btn" type="button" onclick="window.togglePlayState()" class="relative z-10 w-20 h-20 bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] flex items-center justify-center hover:bg-red-700 transform hover:scale-110 transition-all duration-300">
                            <svg class="w-10 h-10 fill-current ml-1" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>

                        <!-- Live Status Overlay Banner -->
                        <div class="absolute top-4 left-4 z-10 px-3 py-1 bg-[#1A1A1A] text-[#F5F0E6] border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] text-xs font-black flex items-center gap-2">
                            <span class="px-1.5 py-0.5 bg-[#E63946] text-white rounded text-[10px]">OTAKUDESU</span>
                            <span>EPISODE {{ sprintf('%02d', $episodeNum) }}</span>
                        </div>
                    </div>

                    <!-- Video Custom Controls Bar -->
                    <div class="absolute bottom-0 left-0 right-0 z-20 p-4 bg-[#0D0D0D]/95 border-t-2 border-[#F5F0E6] opacity-95 group-hover:opacity-100 transition-opacity">
                        
                        <!-- Progress Bar Seekbar -->
                        <div class="relative w-full h-2 bg-[#141414] border border-[#F5F0E6] hover:h-3 cursor-pointer transition-all mb-3" id="player-seekbar" onclick="window.seekPlayer(event)">
                            <div id="player-progress-bar" class="h-full bg-[#E63946] relative" style="width: 0%;">
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
                                    <span id="current-time">00:00</span> / <span id="total-time">00:00</span>
                                </span>
                            </div>

                            <!-- Right Controls -->
                            <div class="flex items-center gap-3 text-xs font-bold">
                                <button type="button" onclick="window.toggleMute()" class="px-2.5 py-1 bg-[#1A1A1A] text-[#F5F0E6] border border-[#F5F0E6] rounded">
                                    Mute
                                </button>
                                <button type="button" onclick="window.toggleFullscreen()" class="px-2.5 py-1 bg-[#1A1A1A] text-[#F5F0E6] border border-[#F5F0E6] rounded">
                                    Fullscreen
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

                    <!-- Server Selector (single Otakudesu server) -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-black text-zinc-400 mr-1">SERVER:</span>
                            <button type="button" class="px-3 py-1.5 bg-[#E63946] text-white text-xs font-black border border-[#F5F0E6] shadow-[1px_1px_0px_#F5F0E6]">Otakudesu</button>
                        </div>
                    </div>

                                        <!-- Player note: quality is controlled inside the iframe; downloads below -->
                    <div class="player-note flex items-center gap-2 mt-3 text-[11px] text-zinc-500">
                        <svg class="w-3.5 h-3.5 text-[#E63946]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                        </svg>
                        <span>💡 Ganti kualitas video di dalam player (ikon gear ⚙️). Tombol download tersedia di bawah.</span>
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
            <div class="lg:col-span-1 space-y-4 bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-4 max-h-[720px] overflow-y-auto">
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
            const playIcon = document.getElementById('play-icon');
            const seekbar = document.getElementById('player-seekbar');
            const progressBar = document.getElementById('player-progress-bar');
            const currentTimeEl = document.getElementById('current-time');
            const totalTimeEl = document.getElementById('total-time');

            let isPlaying = false;
            let isIframeMode = false;
            let progressInterval = null;
            let sources = [];
            let downloadUrls = {};

            function formatTime(seconds) {
                if (!seconds || isNaN(seconds)) return '00:00';
                const m = Math.floor(seconds / 60);
                const s = Math.floor(seconds % 60);
                return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            }

            function slugToEpisodeNumber(slug) {
                if (!slug) return null;
                const match = slug.match(/episode-(\d+)/i);
                return match ? match[1] : null;
            }

            function showIframe(url) {
                isIframeMode = true;
                iframe.src = url;
                iframe.classList.remove('hidden');
                video.classList.add('hidden');
                backdrop.classList.add('hidden');
                playBtn.classList.add('hidden');
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

            function togglePlayState() {
                if (isIframeMode) {
                    if (iframe.contentWindow) {
                        iframe.contentWindow.postMessage({ action: 'toggle' }, '*');
                    }
                    if (!isPlaying) {
                        backdrop.classList.add('hidden');
                        playBtn.classList.add('hidden');
                    } else {
                        showBackdrop();
                    }
                    isPlaying = !isPlaying;
                    updatePlayIcon();
                    return;
                }

                if (video.paused) {
                    video.play();
                } else {
                    video.pause();
                }
            }

            function updatePlayIcon() {
                if (isPlaying) {
                    playIcon.innerHTML = '<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>';
                } else {
                    playIcon.innerHTML = '<path d="M8 5v14l11-7z"/>';
                }
            }

            function toggleMute() {
                if (isIframeMode) {
                    if (iframe.contentWindow) {
                        iframe.contentWindow.postMessage({ action: 'mute' }, '*');
                    }
                    return;
                }
                video.muted = !video.muted;
            }

            function toggleFullscreen() {
                if (isIframeMode) {
                    if (iframe.requestFullscreen) {
                        iframe.requestFullscreen();
                    }
                    return;
                }
                if (video.requestFullscreen) {
                    video.requestFullscreen();
                }
            }

            function seekPlayer(e) {
                if (isIframeMode) {
                    return;
                }
                const rect = seekbar.getBoundingClientRect();
                const pct = Math.min(1, Math.max(0, (e.clientX - rect.left) / rect.width));
                if (video.duration) {
                    video.currentTime = pct * video.duration;
                }
            }

            function startProgressTracking() {
                if (progressInterval) clearInterval(progressInterval);
                progressInterval = setInterval(() => {
                    if (isIframeMode || video.paused || !video.duration) return;
                    const pct = (video.currentTime / video.duration) * 100;
                    progressBar.style.width = pct + '%';
                    currentTimeEl.textContent = formatTime(video.currentTime);
                    totalTimeEl.textContent = formatTime(video.duration);
                }, 500);
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
                        downloadUrls = data.data.download_urls || {};
                        const nav = data.data.navigation || {};
                        updateNavButtons(nav);
                        renderDownloadSection();

                        if (sources.length === 0) {
                            console.warn('No sources returned.');
                            return;
                        }
                        const src = sources[0];
                        if (src.is_embed) {
                            showIframe(src.url);
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
                isPlaying = true;
                updatePlayIcon();
                backdrop.classList.add('hidden');
                playBtn.classList.add('hidden');
                startProgressTracking();
            });

            video.addEventListener('pause', () => {
                isPlaying = false;
                updatePlayIcon();
                showBackdrop();
                if (progressInterval) clearInterval(progressInterval);
            });

            video.addEventListener('timeupdate', () => {
                if (video.duration) {
                    const pct = (video.currentTime / video.duration) * 100;
                    progressBar.style.width = pct + '%';
                    currentTimeEl.textContent = formatTime(video.currentTime);
                    totalTimeEl.textContent = formatTime(video.duration);
                }
            });

            video.addEventListener('loadedmetadata', () => {
                totalTimeEl.textContent = formatTime(video.duration);
            });

            loadSources();
        })();
    </script>
</x-app-layout>
