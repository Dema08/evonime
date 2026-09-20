<x-admin-layout title="Pengaturan Rating Anime - EVONIME Admin">

    <div class="max-w-[1400px] mx-auto space-y-8 text-[#F5F0E6]">
        
        <!-- Header Panel -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#1A1A1A] border-2 border-white shadow-[6px_6px_0px_#FFFFFF] p-6 rounded-2xl">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase bg-amber-400 text-[#0D0D0D] px-2 py-0.5 border-2 border-white rounded-md">PAGE // TOP RATED MANAGER</span>
                    <span class="text-xs font-mono text-zinc-400">REALTIME RATING SYSTEM // HIGH TIER</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight" style="font-family: 'Anton', sans-serif;">
                    🏆 PENGATURAN ANIME RATING TERTINGGI
                </h1>
                <p class="text-xs md:text-sm text-zinc-300 font-bold">Ubah skor rating anime untuk mengatur urutan dan daftar anime rating tertinggi (Top Rated) secara realtime</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.saveRatingSettings()" class="manga-button-primary px-5 py-2.5 text-xs font-black rounded-xl text-white">
                    ✓ SIMPAN RATING
                </button>
                <button type="button" onclick="window.resetRatingSettings()" class="manga-button px-4 py-2.5 text-xs font-black rounded-xl text-zinc-300">
                    RESET
                </button>
            </div>
        </div>

        <!-- Live Status Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 bg-[#141414] border-2 border-white p-4 rounded-xl text-xs font-black">
            <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full bg-amber-400 animate-pulse"></span>
                <span>TOTAL ANIME RATING TERTINGGI (≥9.5): <span id="top-rated-active-count" class="text-amber-400 font-mono text-sm">{{ count($topRatedItems) }}</span> TITLES</span>
            </div>
            <div class="text-zinc-400 text-[11px]">
                Tip: Klik tombol preset rating (9.9, 9.8, 9.7) atau isi nilai skor manual pada kotak input.
            </div>
        </div>

        <!-- Rating Editor List Rows -->
        <div class="space-y-4">
            @foreach($animeList as $anime)
                <div class="rating-item-card flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 bg-[#1A1A1A] border-2 border-white shadow-[4px_4px_0px_#FFFFFF] rounded-2xl group transition-all" data-slug="{{ $anime['slug'] }}">
                    
                    <!-- Anime Info -->
                    <div class="flex items-center gap-4 min-w-0">
                        <img referrerpolicy="no-referrer" src="{{ $anime['poster'] }}" alt="{{ $anime['title'] }}" class="w-16 h-22 object-cover border-2 border-white rounded-xl flex-shrink-0 shadow-[2px_2px_0px_#FFFFFF]">
                        <div class="space-y-1.5 min-w-0">
                            <h4 class="text-base font-black text-white truncate">{{ $anime['title'] }}</h4>
                            <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-zinc-400">
                                <span class="px-2 py-0.5 bg-[#141414] border border-white text-white rounded text-[10px]">{{ $anime['type'] }}</span>
                                <span>•</span>
                                <span>{{ $anime['year'] }}</span>
                                <span>•</span>
                                <span>{{ $anime['episodes'] }} Eps</span>
                                <span>•</span>
                                <span class="text-zinc-300 font-extrabold">{{ implode(', ', array_slice($anime['genres'], 0, 3)) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Editor Buttons & Inputs -->
                    <div class="flex flex-wrap items-center gap-3 self-end md:self-auto border-t-2 md:border-t-0 border-zinc-800 pt-3 md:pt-0">
                        <span class="text-xs font-black uppercase text-zinc-400">UBAH SKOR:</span>
                        
                        <!-- Quick Preset Buttons -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach([9.9, 9.8, 9.7, 9.5, 9.2] as $preset)
                                <button type="button" 
                                        onclick="window.setPresetRating('{{ $anime['slug'] }}', {{ $preset }})"
                                        class="px-3 py-1.5 text-xs font-black rounded-xl border-2 border-white transition-all {{ number_format($anime['rating'], 1) == number_format($preset, 1) ? 'bg-amber-400 text-[#0D0D0D] shadow-[2px_2px_0px_#FFFFFF]' : 'bg-[#141414] text-white hover:bg-amber-400 hover:text-[#0D0D0D]' }}">
                                    ★ {{ number_format($preset, 1) }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Manual Input Box -->
                        <div class="flex items-center gap-1.5 bg-[#141414] border-2 border-white px-3 py-1 rounded-xl shadow-[2px_2px_0px_#FFFFFF]">
                            <span class="text-amber-400 text-sm font-black">★</span>
                            <input type="number" 
                                   step="0.1" 
                                   min="1.0" 
                                   max="10.0" 
                                   value="{{ number_format($anime['rating'], 1) }}" 
                                   class="rating-input-box w-16 bg-transparent text-center text-amber-400 text-sm font-black focus:outline-none"
                                   data-slug="{{ $anime['slug'] }}"
                                   onchange="window.saveRatingSettings()">
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

    </div>

    <!-- Script for Top Rated Page -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initTopRatedPage();
        });

        window.setPresetRating = function(slug, val) {
            const inp = document.querySelector(`.rating-input-box[data-slug="${slug}"]`);
            if (inp) {
                inp.value = parseFloat(val).toFixed(1);
                window.saveRatingSettings();
            }
        };

        window.saveRatingSettings = function() {
            const inputs = document.querySelectorAll('.rating-input-box');
            const ratingState = {};
            let topCount = 0;

            inputs.forEach(inp => {
                const slug = inp.getAttribute('data-slug');
                const val = parseFloat(inp.value) || 9.0;
                ratingState[slug] = val;
                if (val >= 9.5) topCount++;
            });

            localStorage.setItem('evonime_custom_ratings', JSON.stringify(ratingState));

            const badgeCount = document.getElementById('top-rated-active-count');
            if (badgeCount) badgeCount.textContent = topCount;

            if (window.showToast) window.showToast('Rating Anime berhasil diperbarui!');
        };

        window.resetRatingSettings = function() {
            localStorage.removeItem('evonime_custom_ratings');
            if (window.showToast) window.showToast('Rating Anime di-reset ke default');
            setTimeout(() => window.location.reload(), 500);
        };

        function initTopRatedPage() {
            const savedRatings = JSON.parse(localStorage.getItem('evonime_custom_ratings') || 'null');
            if (savedRatings) {
                let topCount = 0;
                document.querySelectorAll('.rating-input-box').forEach(inp => {
                    const slug = inp.getAttribute('data-slug');
                    if (savedRatings.hasOwnProperty(slug)) {
                        const val = parseFloat(savedRatings[slug]);
                        inp.value = val.toFixed(1);
                        if (val >= 9.5) topCount++;
                    }
                });
                const badgeCount = document.getElementById('top-rated-active-count');
                if (badgeCount) badgeCount.textContent = topCount;
            }
        }
    </script>

</x-admin-layout>
