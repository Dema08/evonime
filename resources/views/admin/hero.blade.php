<x-admin-layout title="Pengaturan Hero Banner - EVONIME Admin">

    <div class="max-w-[1400px] mx-auto space-y-8 text-[#F5F0E6]">
        
        <!-- Header Panel -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#1A1A1A] border-2 border-white shadow-[6px_6px_0px_#FFFFFF] p-6 rounded-2xl">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase bg-[#E63946] text-white px-2 py-0.5 border-2 border-white rounded-md">PAGE // HERO BANNER MANAGER</span>
                    <span class="text-xs font-mono text-zinc-400">REALTIME SYNC // RATING FILTER ≥ 9.5</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight" style="font-family: 'Anton', sans-serif;">
                    ★ PENGATURAN HERO CAROUSEL BANNER
                </h1>
                <p class="text-xs md:text-sm text-zinc-300 font-bold">Pilih anime dari rating mingguan terbaik (≥ 9.5), aktifkan banner hero, dan kustomisasi foto landscape & deskripsinya</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.saveHeroSettings()" class="manga-button-primary px-5 py-2.5 text-xs font-black rounded-xl text-white">
                    ✓ SIMPAN HERO BANNER
                </button>
                <button type="button" onclick="window.resetHeroSettings()" class="manga-button px-4 py-2.5 text-xs font-black rounded-xl text-zinc-300">
                    RESET
                </button>
            </div>
        </div>

        <!-- Filter Bar: Rating >= 9.5 filter -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#141414] border-2 border-white p-4 rounded-xl">
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide">
                <span class="text-xs font-black uppercase text-zinc-400 mr-2">FILTER ALUR:</span>
                <button type="button" onclick="window.filterHeroGrid('top')" id="filter-btn-top" class="hero-filter-btn manga-button-primary px-4 py-2 text-xs font-black rounded-xl text-white">
                    🏆 RATING MINGGUAN TERTINGGI (≥ 9.5)
                </button>
                <button type="button" onclick="window.filterHeroGrid('active')" id="filter-btn-active" class="hero-filter-btn manga-button px-4 py-2 text-xs font-black rounded-xl text-white">
                    ★ HANYA AKTIF HERO
                </button>
                <button type="button" onclick="window.filterHeroGrid('all')" id="filter-btn-all" class="hero-filter-btn manga-button px-4 py-2 text-xs font-black rounded-xl text-white">
                    📋 SEMUA ANIME
                </button>
            </div>

            <div class="flex items-center gap-2 text-xs font-black text-emerald-400">
                <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>HERO BANNER AKTIF: <span id="hero-active-count" class="text-[#E63946] font-mono text-sm">{{ count($heroItems) }}</span> SLIDES</span>
            </div>
        </div>

        <!-- Hero Cards Grid -->
        <div id="hero-cards-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($animeList as $anime)
                @php
                    $isHero = !empty($anime['trending_rank']);
                    $rankNum = $anime['trending_rank'] ?? 99;
                    $isTopRated = (isset($anime['rating']) && $anime['rating'] >= 8.5) || !empty($anime['trending_rank']);
                @endphp
                <div class="hero-card-box flex flex-col justify-between p-4 bg-[#1A1A1A] border-2 border-white shadow-[4px_4px_0px_#FFFFFF] rounded-2xl relative transition-all group space-y-3" 
                     data-slug="{{ $anime['slug'] }}"
                     data-rating="{{ $anime['rating'] }}"
                     data-top-rated="{{ $isTopRated ? '1' : '0' }}">
                    
                    <!-- Top Badge & Rating -->
                    <div class="flex items-center justify-between border-b border-zinc-800 pb-2">
                        @if($isTopRated)
                            <span class="px-2 py-0.5 bg-amber-400 text-[#0D0D0D] text-[10px] font-black border border-white rounded shadow-[1px_1px_0px_#FFFFFF]">
                                🏆 TOP WEEKLY RATING
                            </span>
                        @else
                            <span class="px-2 py-0.5 bg-[#141414] text-zinc-400 text-[10px] font-black border border-white rounded">
                                STANDARD RATING
                            </span>
                        @endif

                        <span class="text-amber-400 text-xs font-black">★ {{ number_format($anime['rating'], 1) }}</span>
                    </div>

                    <!-- Poster & Details -->
                    <div class="flex items-start gap-3">
                        <img src="{{ $anime['poster'] }}" alt="{{ $anime['title'] }}" class="w-16 h-24 object-cover border-2 border-white rounded-xl flex-shrink-0 shadow-[2px_2px_0px_#FFFFFF]">
                        
                        <div class="flex-grow min-w-0 space-y-1">
                            <h4 class="text-sm font-black text-white truncate">{{ $anime['title'] }}</h4>
                            <p class="text-[11px] text-zinc-400 font-bold line-clamp-2">{{ $anime['synopsis'] }}</p>
                            
                            <div class="flex items-center gap-2 text-[10px] text-zinc-400 pt-1 font-bold">
                                <span class="px-1.5 py-0.5 bg-[#141414] border border-white text-white rounded">{{ $anime['type'] }}</span>
                                <span>{{ $anime['year'] }}</span>
                                <span>{{ $anime['episodes'] }} Eps</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions & Toggle -->
                    <div class="flex items-center justify-between border-t-2 border-zinc-800 pt-3">
                        <!-- Toggle Checkbox -->
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" 
                                   class="hero-toggle-checkbox w-5 h-5 accent-[#E63946] rounded cursor-pointer"
                                   data-slug="{{ $anime['slug'] }}"
                                   {{ $isHero ? 'checked' : '' }}
                                   onchange="window.updateHeroState(this)">
                            <span class="text-xs font-black text-white">AKTIFKAN HERO</span>
                        </label>

                        <!-- Edit Landscape Banner & Synopsis Button -->
                        <button type="button" 
                                onclick="window.openHeroEditModal('{{ $anime['slug'] }}', '{{ addslashes($anime['title']) }}', '{{ addslashes($anime['banner']) }}', '{{ addslashes($anime['synopsis']) }}', '{{ addslashes($anime['trailer_url'] ?? '') }}')" 
                                class="px-3 py-1.5 bg-[#141414] hover:bg-[#E63946] text-white border-2 border-white text-[11px] font-black rounded-xl transition-all shadow-[2px_2px_0px_#FFFFFF]">
                            ✏️ FOTO, VIDEO & DESKRIPSI
                        </button>
                    </div>

                </div>
            @endforeach
        </div>

    </div>


    <!-- EDIT FOTO LANDSCAPE, VIDEO TRAILER & DESKRIPSI MODAL -->
    <div id="hero-edit-modal" class="fixed inset-0 z-50 hidden overflow-y-auto p-4 md:p-6 bg-black/85 backdrop-blur-md flex items-center justify-center">
        <div class="relative w-full max-w-2xl bg-[#1A1A1A] border-2 border-white rounded-2xl shadow-[8px_8px_0px_#FFFFFF] p-6 space-y-4 text-[#F5F0E6] my-auto max-h-[90vh] flex flex-col">
            
            <!-- Sticky Header -->
            <div class="flex items-center justify-between border-b-2 border-white pb-3 flex-shrink-0">
                <h3 class="text-xl font-black text-white flex items-center gap-2" style="font-family: 'Anton', sans-serif;">
                    <span>✏️</span> EDIT FOTO BANNER, VIDEO TRAILER & DESKRIPSI
                </h3>
                <button type="button" onclick="window.closeHeroEditModal()" class="px-2.5 py-1 bg-[#141414] border-2 border-white text-white font-black text-xs rounded-lg hover:bg-[#E63946]">
                    ✕
                </button>
            </div>

            <!-- Scrollable Modal Body Form -->
            <form onsubmit="window.saveHeroModalEdit(event)" class="space-y-4 overflow-y-auto flex-grow pr-1 max-h-[calc(90vh-120px)] scrollbar-hide">
                <input type="hidden" id="modal-anime-slug">

                <div>
                    <label class="block text-xs font-black uppercase text-zinc-400 mb-1">Judul Anime</label>
                    <input type="text" id="modal-anime-title" readonly class="w-full bg-[#141414] border-2 border-white text-zinc-400 font-bold text-sm rounded-xl px-4 py-2.5 focus:outline-none">
                </div>

                <!-- Choice: Photo vs Video Trailer -->
                <div class="p-3.5 bg-[#141414] border-2 border-white rounded-xl space-y-2">
                    <label class="block text-xs font-black uppercase text-[#E63946]">TIPE MEDIA HERO BANNER (PILIH SALAH SATU)</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-2.5 bg-[#1A1A1A] border-2 border-white rounded-lg cursor-pointer hover:bg-zinc-800 transition-colors">
                            <input type="radio" name="modal_media_type" value="image" id="media-type-image" onchange="window.switchModalMediaType('image')" class="w-4 h-4 accent-[#E63946]" checked>
                            <span class="text-xs font-black text-white">🖼️ FOTO LANDSCAPE</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 bg-[#1A1A1A] border-2 border-white rounded-lg cursor-pointer hover:bg-zinc-800 transition-colors">
                            <input type="radio" name="modal_media_type" value="video" id="media-type-video" onchange="window.switchModalMediaType('video')" class="w-4 h-4 accent-amber-400">
                            <span class="text-xs font-black text-amber-400">🎬 VIDEO TRAILER</span>
                        </label>
                    </div>
                </div>

                <!-- Input Foto (Shown when Image selected) -->
                <div id="modal-photo-container">
                    <label class="block text-xs font-black uppercase text-white mb-1">URL Foto Landscape Banner (16:9 Wallpaper)</label>
                    <input type="text" id="modal-banner-url" placeholder="https://..." class="w-full bg-[#141414] border-2 border-white text-white font-bold text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#E63946]">
                    <p class="text-[11px] text-zinc-400 mt-1 font-bold">Gunakan URL foto anime berukuran landscape horizontal yang tajam.</p>
                </div>

                <!-- Input Video (Shown when Video selected) -->
                <div id="modal-video-container" class="hidden">
                    <label class="block text-xs font-black uppercase text-amber-400 mb-1">URL Trailer Video (YouTube Embed / Direct MP4 Video)</label>
                    <input type="text" id="modal-trailer-url" placeholder="https://www.youtube.com/watch?v=94vOIbO8Zio..." class="w-full bg-[#141414] border-2 border-white text-white font-bold text-sm rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400">
                    <p class="text-[11px] text-zinc-400 mt-1 font-bold">Masukkan URL Embed YouTube (contoh: <code class="text-amber-400">https://www.youtube.com/watch?v=VIDEO_ID</code>) atau URL direct file video .mp4.</p>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase text-white mb-1">Deskripsi Hero Carousel (Headline/Synopsis)</label>
                    <textarea id="modal-synopsis" rows="3" required class="w-full bg-[#141414] border-2 border-white text-white font-bold text-xs rounded-xl p-3 focus:outline-none focus:border-[#E63946] leading-relaxed"></textarea>
                </div>

                <!-- Preview Banner Box -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-400 mb-1">PREVIEW BANNER LANDSCAPE</label>
                    <div class="w-full aspect-video rounded-xl overflow-hidden border-2 border-white bg-black max-h-52">
                        <img id="modal-banner-preview" src="" alt="Banner Preview" class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Sticky Footer Buttons -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t-2 border-white sticky bottom-0 bg-[#1A1A1A] z-20">
                    <button type="button" onclick="window.closeHeroEditModal()" class="manga-button px-5 py-2.5 text-xs font-black rounded-xl text-white">
                        BATAL
                    </button>
                    <button type="submit" class="manga-button-primary px-6 py-2.5 text-xs font-black rounded-xl text-white">
                        ✓ SIMPAN KUSTOMISASI HERO
                    </button>
                </div>
            </form>

        </div>
    </div>


    <!-- Script for Hero Page Filter & Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initHeroPage();
            filterHeroGrid('top'); // Default to top weekly rating >= 9.5
        });

        // Media Type Switcher
        window.switchModalMediaType = function(type) {
            const photoContainer = document.getElementById('modal-photo-container');
            const videoContainer = document.getElementById('modal-video-container');
            if (type === 'video') {
                photoContainer.classList.add('hidden');
                videoContainer.classList.remove('hidden');
            } else {
                photoContainer.classList.remove('hidden');
                videoContainer.classList.add('hidden');
            }
        };

        // 1. Grid Filtering Logic
        window.filterHeroGrid = function(mode) {
            const btns = ['top', 'active', 'all'];
            btns.forEach(b => {
                const btn = document.getElementById(`filter-btn-${b}`);
                if (btn) {
                    if (b === mode) {
                        btn.className = 'hero-filter-btn manga-button-primary px-4 py-2 text-xs font-black rounded-xl text-white';
                    } else {
                        btn.className = 'hero-filter-btn manga-button px-4 py-2 text-xs font-black rounded-xl text-white';
                    }
                }
            });

            const cards = document.querySelectorAll('.hero-card-box');
            let visibleCount = 0;
            cards.forEach(card => {
                const isTop = card.getAttribute('data-top-rated') === '1';
                const chk = card.querySelector('.hero-toggle-checkbox');
                const isActive = chk ? chk.checked : false;

                if (mode === 'top' && isTop) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else if (mode === 'active' && isActive) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else if (mode === 'all') {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            // Fallback: If top filter mode results in 0 visible cards, automatically fallback to showing all cards!
            if (mode === 'top' && visibleCount === 0) {
                cards.forEach(card => card.classList.remove('hidden'));
            }
        };

        // 2. Modal Edit Landscape, Trailer & Synopsis
        window.openHeroEditModal = function(slug, title, bannerUrl, synopsis, trailerUrl) {
            const modal = document.getElementById('hero-edit-modal');
            const customDetails = JSON.parse(localStorage.getItem('evonime_custom_hero_details') || '{}');

            document.getElementById('modal-anime-slug').value = slug;
            document.getElementById('modal-anime-title').value = title;

            const finalBanner = customDetails[slug]?.banner || bannerUrl;
            const finalSynopsis = customDetails[slug]?.synopsis || synopsis;
            const finalTrailer = customDetails[slug]?.trailer || trailerUrl || '';
            const finalMediaType = customDetails[slug]?.media_type || (finalTrailer ? 'video' : 'image');

            document.getElementById('modal-banner-url').value = finalBanner;
            document.getElementById('modal-synopsis').value = finalSynopsis;
            document.getElementById('modal-trailer-url').value = finalTrailer;
            document.getElementById('modal-banner-preview').src = finalBanner;

            if (finalMediaType === 'video') {
                document.getElementById('media-type-video').checked = true;
                window.switchModalMediaType('video');
            } else {
                document.getElementById('media-type-image').checked = true;
                window.switchModalMediaType('image');
            }

            modal.classList.remove('hidden');

            document.getElementById('modal-banner-url').oninput = function(e) {
                document.getElementById('modal-banner-preview').src = e.target.value;
            };
        };

        window.closeHeroEditModal = function() {
            document.getElementById('hero-edit-modal').classList.add('hidden');
        };

        window.saveHeroModalEdit = function(e) {
            e.preventDefault();
            const slug = document.getElementById('modal-anime-slug').value;
            const banner = document.getElementById('modal-banner-url').value;
            const synopsis = document.getElementById('modal-synopsis').value;
            const trailer = document.getElementById('modal-trailer-url').value;
            const mediaType = document.querySelector('input[name="modal_media_type"]:checked')?.value || 'image';

            const customDetails = JSON.parse(localStorage.getItem('evonime_custom_hero_details') || '{}');
            customDetails[slug] = { media_type: mediaType, banner, synopsis, trailer };
            localStorage.setItem('evonime_custom_hero_details', JSON.stringify(customDetails));

            // Also enable hero checkbox for this anime if not checked
            const chk = document.querySelector(`.hero-toggle-checkbox[data-slug="${slug}"]`);
            if (chk) chk.checked = true;

            window.saveHeroSettings();
            window.closeHeroEditModal();
            if (window.showToast) window.showToast(`Hero Media (${mediaType.toUpperCase()}) untuk "${slug}" berhasil disimpan!`);
        };

        // 3. Save Hero Settings
        window.updateHeroState = function(chk) {
            window.saveHeroSettings();
        };

        window.saveHeroSettings = function() {
            const checkboxes = document.querySelectorAll('.hero-toggle-checkbox');
            const heroState = {};
            let count = 0;

            checkboxes.forEach((chk, i) => {
                const slug = chk.getAttribute('data-slug');
                if (chk.checked) {
                    heroState[slug] = count + 1;
                    count++;
                }
            });

            localStorage.setItem('evonime_custom_hero', JSON.stringify(heroState));

            const badgeCount = document.getElementById('hero-active-count');
            if (badgeCount) badgeCount.textContent = count;

            if (window.showToast) window.showToast('Pengaturan Hero Banner berhasil disimpan!');
        };

        window.resetHeroSettings = function() {
            localStorage.removeItem('evonime_custom_hero');
            localStorage.removeItem('evonime_custom_hero_details');
            if (window.showToast) window.showToast('Hero Banner di-reset ke default');
            setTimeout(() => window.location.reload(), 500);
        };

        function initHeroPage() {
            const savedHero = JSON.parse(localStorage.getItem('evonime_custom_hero') || 'null');
            if (savedHero) {
                let count = 0;
                document.querySelectorAll('.hero-toggle-checkbox').forEach(chk => {
                    const slug = chk.getAttribute('data-slug');
                    if (savedHero.hasOwnProperty(slug)) {
                        chk.checked = true;
                        count++;
                    } else {
                        chk.checked = false;
                    }
                });
                const badgeCount = document.getElementById('hero-active-count');
                if (badgeCount) badgeCount.textContent = count;
            }
        }
    </script>

</x-admin-layout>
