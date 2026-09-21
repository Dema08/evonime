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

                get progressPercent() {
                    if (!this.totalEpisodeCount || this.totalEpisodeCount === 0) return 0;
                    return Math.round((this.currentEpisodeCount / this.totalEpisodeCount) * 100);
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
                    } catch (e) {
                        console.error(e);
                        alert('Gagal melakukan pencarian.');
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
                            this.startPolling(slug);
                        } else {
                            alert('Gagal memulai import.');
                            this.importing = false;
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Terjadi kesalahan saat memulai import.');
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
                                alert(`Import anime "${this.activeTitle}" berhasil diselesaikan!`);
                                this.searchAnime();
                            } else if (data.status === 'failed') {
                                clearInterval(this.pollTimer);
                                this.importing = false;
                                alert(`Import anime "${this.activeTitle}" gagal.`);
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