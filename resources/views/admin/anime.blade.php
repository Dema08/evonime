<x-admin-layout title="Katalog Anime - EVONIME Admin">

    <div class="max-w-[1400px] mx-auto space-y-8 text-[#F5F0E6]">
        
        <!-- Header Panel -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#1A1A1A] border-2 border-white shadow-[6px_6px_0px_#FFFFFF] p-6 rounded-2xl">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase bg-[#E63946] text-white px-2 py-0.5 border-2 border-white rounded-md">PAGE // KATALOG ANIME</span>
                    <span class="text-xs font-mono text-zinc-400">DATABASE CATALOGUE // ALL SERIES</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight" style="font-family: 'Anton', sans-serif;">
                    📋 KATALOG LENGKAP ANIME
                </h1>
                <p class="text-xs md:text-sm text-zinc-300 font-bold">Daftar seluruh serial anime dan film yang tersedia di platform EVONIME</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="/anime" target="_blank" class="manga-button-primary px-5 py-2.5 text-xs font-black rounded-xl text-white">
                    EXPLORE SITE ↗
                </a>
            </div>
        </div>

        <!-- Catalog Table Card -->
        <div class="bg-[#1A1A1A] border-2 border-white shadow-[6px_6px_0px_#FFFFFF] p-6 rounded-2xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-white pb-4">
                <h2 class="text-2xl font-black text-white" style="font-family: 'Anton', sans-serif;">DAFTAR ANIME ({{ count($animeList) }} TITLES)</h2>
                <div class="relative w-full sm:w-72">
                    <input type="text" id="catalog-search" placeholder="Cari anime..." onkeyup="window.searchCatalog(this.value)" class="w-full bg-[#141414] border-2 border-white text-white text-xs font-bold rounded-xl px-4 py-2 focus:outline-none">
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#141414] border-2 border-white text-xs font-black uppercase tracking-wider text-white">
                            <th class="p-3.5 border-r-2 border-white">Poster</th>
                            <th class="p-3.5 border-r-2 border-white">Title</th>
                            <th class="p-3.5 border-r-2 border-white">Type</th>
                            <th class="p-3.5 border-r-2 border-white">Rating</th>
                            <th class="p-3.5 border-r-2 border-white">Episodes</th>
                            <th class="p-3.5 border-r-2 border-white">Year</th>
                            <th class="p-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="catalog-table-body" class="divide-y-2 divide-zinc-800 text-xs font-bold text-zinc-300">
                        @foreach($animeList as $anime)
                            <tr class="catalog-row hover:bg-zinc-900/60 transition-colors" data-title="{{ strtolower($anime['title']) }}">
                                <td class="p-3 border-r-2 border-zinc-800">
                                    <img src="{{ $anime['poster'] }}" alt="{{ $anime['title'] }}" class="w-10 h-14 object-cover border-2 border-white rounded-lg">
                                </td>
                                <td class="p-3 border-r-2 border-zinc-800 text-white font-black">
                                    {{ $anime['title'] }}
                                </td>
                                <td class="p-3 border-r-2 border-zinc-800">
                                    <span class="px-2 py-0.5 bg-[#141414] border border-white text-[10px] text-white rounded">{{ $anime['type'] }}</span>
                                </td>
                                <td class="p-3 border-r-2 border-zinc-800 text-amber-400 font-black">
                                    ★ {{ number_format($anime['rating'], 1) }}
                                </td>
                                <td class="p-3 border-r-2 border-zinc-800">
                                    {{ $anime['episodes'] }} Eps
                                </td>
                                <td class="p-3 border-r-2 border-zinc-800">
                                    {{ $anime['year'] }}
                                </td>
                                <td class="p-3 space-x-2">
                                    <a href="/anime/{{ $anime['slug'] }}" target="_blank" class="px-3 py-1 bg-[#141414] hover:bg-[#E63946] text-white border-2 border-white font-black rounded-lg transition-colors inline-block">
                                        PREVIEW ↗
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        window.searchCatalog = function(q) {
            const query = q.trim().toLowerCase();
            const rows = document.querySelectorAll('.catalog-row');
            rows.forEach(r => {
                const title = r.getAttribute('data-title') || '';
                if (!query || title.includes(query)) {
                    r.classList.remove('hidden');
                } else {
                    r.classList.add('hidden');
                }
            });
        };
    </script>

</x-admin-layout>
