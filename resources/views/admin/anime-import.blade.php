<x-admin-layout title="Import Anime Otakudesu - EVONIME Admin">
    <div class="max-w-[1400px] mx-auto space-y-6 text-[#F5F0E6]" x-data="animeImport()">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[6px_6px_0px_#F5F0E6] p-6">
            <div>
                <h1 class="text-3xl font-black" style="font-family: 'Anton', sans-serif;">IMPORT ANIME // OTAKUDESU</h1>
                <p class="text-xs text-zinc-400 font-bold">Cari dan import anime secara instan dari Otakudesu ke database</p>
            </div>
        </div>

        <!-- Search Box -->
        <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[6px_6px_0px_#F5F0E6] p-6">
            <form @submit.prevent="searchAnime" class="flex flex-col md:flex-row gap-3">
                <input type="text" x-model="query" id="search-anime" placeholder="Ketik judul anime (contoh: One Piece, Naruto)..." class="flex-1 px-4 py-3 bg-[#141414] border-2 border-[#F5F0E6] text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-[#E63946]">
                <button type="submit" class="px-6 py-3 text-xs font-black bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] hover:bg-red-700 transition-all flex items-center justify-center gap-2">
                    <span x-show="!loading">CARI ANIME</span>
                    <span x-show="loading">MENCARI...</span>
                </button>
            </form>
        </div>

        <!-- Progress Box (Active Import) -->
        <div x-show="importing" class="bg-[#1A1A1A] border-2 border-[#E63946] shadow-[6px_6px_0px_#E63946] p-6 space-y-4" style="display: none;">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-black text-amber-400">Sedang Mengimport: <span x-text="activeTitle"></span></h3>
                <span class="text-xs font-mono px-3 py-1 bg-amber-500/20 border border-amber-500 text-amber-300 font-bold uppercase" x-text="importStatus"></span>
            </div>
            <div class="w-full bg-[#141414] border-2 border-[#F5F0E6] h-6 relative overflow-hidden">
                <div class="bg-[#E63946] h-full transition-all duration-300" :style="`width: ${progressPercent}%`"></div>
                <div class="absolute inset-0 flex items-center justify-center text-xs font-black text-white" x-text="`${currentEpisodeCount} / ${totalEpisodeCount} Episode (${progressPercent}%)`"></div>
            </div>
            <p class="text-xs text-zinc-400 font-mono" x-text="currentEpisodeText"></p>
        </div>

        <!-- Search Results -->
        <div class="space-y-4">
            <h2 class="text-xl font-black uppercase tracking-wider" style="font-family: 'Anton', sans-serif;" x-show="results.length > 0">Hasil Pencarian</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="item in results" :key="item.slug">
                    <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] flex flex-col justify-between p-4 space-y-4">
                        <div class="flex gap-4">
                            <img :src="item.poster" class="w-24 h-36 object-cover border-2 border-[#F5F0E6]" alt="Poster">
                            <div class="space-y-2 flex-1 min-w-0">
                                <h3 class="text-sm font-black text-white truncate" x-text="item.title"></h3>
                                <p class="text-xs text-zinc-400" x-text="item.status || 'Ongoing'"></p>
                                <p class="text-xs text-amber-400 font-bold" x-text="item.rating ? '★ ' + item.rating : ''"></p>
                                <template x-if="item.exists">
                                    <span class="inline-block px-2 py-0.5 bg-green-600/30 border border-green-500 text-green-400 text-[10px] font-black uppercase">Sudah Ada</span>
                                </template>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-zinc-800 flex items-center justify-between">
                            <button @click="confirmImport(item)" class="w-full py-2 text-xs font-black bg-[#141414] hover:bg-[#E63946] text-[#F5F0E6] border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] transition-all" x-text="item.exists ? 'RE-IMPORT (UPDATE)' : 'IMPORT ANIME'"></button>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="searched && results.length === 0 && !loading" class="bg-[#1A1A1A] border-2 border-[#F5F0E6] p-8 text-center text-zinc-400 font-bold" style="display: none;">
                Tidak ada anime ditemukan dengan kata kunci tersebut.
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4" style="display: none;">
            <div class="bg-[#1A1A1A] border-4 border-[#F5F0E6] shadow-[8px_8px_0px_#E63946] max-w-md w-full p-6 space-y-4 text-[#F5F0E6]">
                <h3 class="text-2xl font-black" style="font-family: 'Anton', sans-serif;">KONFIRMASI IMPORT</h3>
                <p class="text-sm font-bold text-zinc-300">
                    Import anime <span class="text-amber-400 font-black" x-text="selectedAnime?.title"></span>? Tindakan ini akan mengambil detail lengkap beserta seluruh daftar episodenya ke database.
                </p>
                <div class="flex justify-end gap-3 pt-4">
                    <button @click="showModal = false" class="px-4 py-2 text-xs font-black bg-[#141414] text-zinc-300 border-2 border-[#F5F0E6] hover:bg-zinc-800">BATAL</button>
                    <button @click="startImport" class="px-5 py-2 text-xs font-black bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] hover:bg-red-700">YA, IMPORT</button>
                </div>
            </div>
        </div>

        <!-- ===== SUCCESS NOTIFICATION CARD MODAL ===== -->
        <div x-show="showSuccessCard" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-50 bg-black/85 backdrop-blur-sm flex items-center justify-center p-4" 
             style="display: none;"
             @click.self="showSuccessCard = false">
            <div class="relative bg-[#141414] border-4 border-emerald-500 rounded-2xl shadow-[0_0_60px_rgba(16,185,129,0.4)] max-w-md w-full p-8 text-center space-y-5">

                <!-- Close Button -->
                <button @click="showSuccessCard = false" class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center text-zinc-500 hover:text-white hover:bg-zinc-800 rounded-lg transition-all text-lg">✕</button>

                <!-- Success Icon Ring -->
                <div class="flex items-center justify-center">
                    <div class="w-20 h-20 rounded-full bg-emerald-500/15 border-2 border-emerald-500 flex items-center justify-center shadow-[0_0_30px_rgba(16,185,129,0.3)]">
                        <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>

                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-emerald-500 text-white text-xs font-black rounded-full uppercase tracking-wider shadow-[2px_2px_0px_#F5F0E6] border border-[#F5F0E6]">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    <span>✅ IMPORT BERHASIL</span>
                </div>

                <!-- Title & Message -->
                <div class="space-y-2">
                    <h2 class="text-2xl font-black text-[#F5F0E6]" style="font-family: 'Anton', sans-serif;">DATABASE DIPERBARUI</h2>
                    <p class="text-sm font-bold text-zinc-300 leading-relaxed">
                        Anime <span class="text-amber-400 font-black" x-text="`"${finishedTitle}"`"></span> telah berhasil diimport dan semua episode telah tersimpan ke database.
                    </p>
                </div>

                <!-- Stats Box -->
                <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] rounded-xl p-4 shadow-[3px_3px_0px_#F5F0E6] flex items-center justify-center gap-6 text-xs font-mono font-bold">
                    <div class="text-center">
                        <div class="text-emerald-400 text-xl font-black" x-text="finishedEpisodeCount"></div>
                        <div class="text-zinc-400 text-[10px] uppercase tracking-wider">Episode</div>
                    </div>
                    <div class="w-px h-8 bg-zinc-700"></div>
                    <div class="text-center">
                        <div class="text-[#E63946] text-xl font-black">✓</div>
                        <div class="text-zinc-400 text-[10px] uppercase tracking-wider">Tersimpan</div>
                    </div>
                    <div class="w-px h-8 bg-zinc-700"></div>
                    <div class="text-center">
                        <div class="text-amber-400 text-xl font-black">DB</div>
                        <div class="text-zinc-400 text-[10px] uppercase tracking-wider">Database</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-1">
                    <a :href="`/anime/${finishedSlug}`" target="_blank"
                       class="w-full sm:w-auto px-6 py-3 bg-[#E63946] hover:bg-red-700 text-white text-xs font-black rounded-xl border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] hover:scale-105 transition-all flex items-center justify-center gap-2">
                        <span>LIHAT ANIME ↗</span>
                    </a>
                    <button @click="showSuccessCard = false"
                            class="w-full sm:w-auto px-5 py-3 bg-[#1A1A1A] hover:bg-zinc-800 text-[#F5F0E6] text-xs font-black rounded-xl border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] transition-all">
                        TUTUP
                    </button>
                </div>

            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function animeImport() {
            return {
                query: '',
                results: [],
                loading: false,
                searched: false,
                showModal: false,
                selectedAnime: null,
                importing: false,
                activeSlug: '',
                activeTitle: '',
                importStatus: 'idle',
                currentEpisodeCount: 0,
                totalEpisodeCount: 0,
                currentEpisodeText: '',
                pollTimer: null,
                showSuccessCard: false,
                finishedTitle: '',
                finishedSlug: '',
                finishedEpisodeCount: 0,

                get progressPercent() {
                    if (!this.totalEpisodeCount || this.totalEpisodeCount === 0) return 0;
                    return Math.round((this.currentEpisodeCount / this.totalEpisodeCount) * 100);
                },

                notify(msg, type = 'info', action = null, duration = 4500) {
                    if (window.showToast) {
                        window.showToast(msg, type, action, duration);
                    }
                },

                async searchAnime() {
                    if (!this.query.trim()) return;
                    this.loading = true;
                    this.searched = true;
                    try {
                        const res = await fetch('{{ route('admin.anime-import.search') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ q: this.query })
                        });
                        const data = await res.json();
                        this.results = data.results || [];
                        if (this.results.length === 0) {
                            this.notify('Tidak ada anime ditemukan untuk pencarian tersebut.', 'warning');
                        }
                    } catch (e) {
                        console.error(e);
                        this.notify('Gagal melakukan pencarian. Cek koneksi internet Anda.', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                confirmImport(item) {
                    this.selectedAnime = item;
                    this.showModal = true;
                },

                async startImport() {
                    this.showModal = false;
                    if (!this.selectedAnime) return;

                    const slug = this.selectedAnime.slug;
                    this.activeSlug = slug;
                    this.activeTitle = this.selectedAnime.title;
                    this.importing = true;
                    this.importStatus = 'queued';
                    this.currentEpisodeCount = 0;
                    this.totalEpisodeCount = 0;
                    this.currentEpisodeText = 'Memasukkan ke antrean...';

                    this.notify(`Memulai import "${this.activeTitle}"...`, 'info', null, 3500);

                    try {
                        const res = await fetch('{{ route('admin.anime-import.import') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ slug: slug })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.notify('Anime masuk ke antrean. Proses import sedang berjalan...', 'info', null, 4000);
                            this.startPolling(slug);
                        } else {
                            this.notify('Gagal memulai import. Coba lagi.', 'error');
                            this.importing = false;
                        }
                    } catch (e) {
                        console.error(e);
                        this.notify('Terjadi kesalahan saat memulai import.', 'error');
                        this.importing = false;
                    }
                },

                startPolling(slug) {
                    if (this.pollTimer) clearInterval(this.pollTimer);

                    this.pollTimer = setInterval(async () => {
                        try {
                            const res = await fetch(`/admin/anime-import/status/${slug}`);
                            const data = await res.json();

                            this.importStatus = data.status;
                            this.currentEpisodeCount = data.current || 0;
                            this.totalEpisodeCount = data.total || 0;
                            this.currentEpisodeText = data.current_episode ? `Sedang mengimport: ${data.current_episode}` : `Status: ${data.status}`;

                            if (data.status === 'completed') {
                                clearInterval(this.pollTimer);
                                this.importing = false;

                                // Set data untuk Success Card
                                this.finishedTitle = this.activeTitle;
                                this.finishedSlug = this.activeSlug;
                                this.finishedEpisodeCount = data.total || this.totalEpisodeCount;

                                // Tampilkan Success Card modal
                                this.showSuccessCard = true;

                                // Toast pendamping
                                this.notify(
                                    `"${this.activeTitle}" berhasil diimport ke database!`,
                                    'success',
                                    {
                                        text: 'LIHAT ANIME ↗',
                                        url: `/anime/${this.activeSlug}`,
                                        target: '_blank'
                                    },
                                    8000
                                );

                                this.searchAnime();

                            } else if (data.status === 'failed') {
                                clearInterval(this.pollTimer);
                                this.importing = false;
                                this.notify(`Import "${this.activeTitle}" gagal. Coba lagi atau periksa log server.`, 'error', null, 6000);
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    }, 2000);
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>