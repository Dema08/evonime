<x-admin-layout title="Admin Dashboard - EVONIME">

    <div class="max-w-[1400px] mx-auto space-y-8 text-[#F5F0E6]">
        
        <!-- Dashboard Header Panel -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#1A1A1A] border-2 border-white shadow-[6px_6px_0px_#FFFFFF] p-6 rounded-2xl">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase bg-[#E63946] text-white px-2 py-0.5 border-2 border-white rounded-md">ADMIN CONTROL PANEL</span>
                    <span class="text-xs font-mono text-zinc-400">STATUS: ONLINE // REALTIME SYNC</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight" style="font-family: 'Anton', sans-serif;">
                    ADMIN DASHBOARD OVERVIEW
                </h1>
                <p class="text-xs md:text-sm text-zinc-300 font-bold">Selamat datang di Panel Kontrol EVONIME. Kelola Tampilan Hero Banner dan Rating Anime secara khusus.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="/" target="_blank" class="manga-button px-5 py-2.5 text-xs font-black rounded-xl text-white">
                    LIHAT SITE LIVE ↗
                </a>
            </div>
        </div>

        <!-- Quick Statistics Panels Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Stat 1: Total Catalog -->
            <div class="bg-[#1A1A1A] border-2 border-white shadow-[4px_4px_0px_#FFFFFF] p-5 rounded-xl space-y-1 relative overflow-hidden">
                <span class="text-[10px] font-black uppercase tracking-wider text-zinc-400 bg-[#141414] px-2 py-0.5 border border-white rounded">TOTAL ANIME</span>
                <div class="text-3xl font-black text-white" style="font-family: 'Anton', sans-serif;">{{ $totalAnime }} TITLES</div>
                <p class="text-[11px] text-zinc-400 font-bold">In Database Catalog</p>
            </div>

            <!-- Stat 2: Active Hero Count -->
            <div class="bg-[#1A1A1A] border-2 border-white shadow-[4px_4px_0px_#FFFFFF] p-5 rounded-xl space-y-1 relative overflow-hidden">
                <span class="text-[10px] font-black uppercase tracking-wider text-white bg-[#E63946] px-2 py-0.5 border border-white rounded">HERO CAROUSEL</span>
                <div class="text-3xl font-black text-white" style="font-family: 'Anton', sans-serif;">{{ count($heroItems) }} FEATURED</div>
                <p class="text-[11px] text-emerald-400 font-bold">● Aktif di Banner Utama</p>
            </div>

            <!-- Stat 3: Top Rated Count -->
            <div class="bg-[#1A1A1A] border-2 border-white shadow-[4px_4px_0px_#FFFFFF] p-5 rounded-xl space-y-1 relative overflow-hidden">
                <span class="text-[10px] font-black uppercase tracking-wider text-[#0D0D0D] bg-amber-400 px-2 py-0.5 border border-white rounded">RATING TERTINGGI</span>
                <div class="text-3xl font-black text-white" style="font-family: 'Anton', sans-serif;">{{ count($topRatedItems) }} ANIME (≥9.5)</div>
                <p class="text-[11px] text-amber-400 font-bold">★ Rating High Tier</p>
            </div>

            <!-- Stat 4: Total Episodes -->
            <div class="bg-[#1A1A1A] border-2 border-white shadow-[4px_4px_0px_#FFFFFF] p-5 rounded-xl space-y-1 relative overflow-hidden">
                <span class="text-[10px] font-black uppercase tracking-wider text-zinc-400 bg-[#141414] px-2 py-0.5 border border-white rounded">TOTAL EPISODES</span>
                <div class="text-3xl font-black text-white" style="font-family: 'Anton', sans-serif;">{{ $totalEpisodes }} EPS</div>
                <p class="text-[11px] text-zinc-400 font-bold">1080p Full HD CDN</p>
            </div>
        </div>

        <!-- Management Navigation Shortcut Cards -->
        <div class="space-y-4">
            <h2 class="text-xl font-black text-white flex items-center gap-2" style="font-family: 'Anton', sans-serif;">
                <span class="w-2 h-5 bg-[#E63946] border border-white inline-block"></span>
                PILIH MENU MANAJEMEN HALAMAN ADMIN
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Card 1: Hero Management Page -->
                <div class="bg-[#1A1A1A] border-2 border-white shadow-[6px_6px_0px_#FFFFFF] p-6 rounded-2xl flex flex-col justify-between space-y-4 hover:-translate-y-1 transition-all group">
                    <div class="space-y-2">
                        <div class="w-12 h-12 bg-[#E63946] text-white border-2 border-white shadow-[2px_2px_0px_#FFFFFF] rounded-xl flex items-center justify-center text-xl font-black">
                            ★
                        </div>
                        <h3 class="text-xl font-black text-white group-hover:text-[#E63946] transition-colors" style="font-family: 'Anton', sans-serif;">
                            PENGATURAN HERO BANNER
                        </h3>
                        <p class="text-xs text-zinc-400 font-bold leading-relaxed">
                            Atur anime mana yang tampil di slider banner utama beranda beserta urutan posisinya (Chapter 01, 02, etc.).
                        </p>
                    </div>
                    <a href="/admin/hero" class="manga-button-primary w-full py-3 text-xs font-black rounded-xl text-white text-center">
                        BUKA HALAMAN HERO BANNER ➔
                    </a>
                </div>

                <!-- Card 2: Top Rated Management Page -->
                <div class="bg-[#1A1A1A] border-2 border-white shadow-[6px_6px_0px_#FFFFFF] p-6 rounded-2xl flex flex-col justify-between space-y-4 hover:-translate-y-1 transition-all group">
                    <div class="space-y-2">
                        <div class="w-12 h-12 bg-amber-400 text-[#0D0D0D] border-2 border-white shadow-[2px_2px_0px_#FFFFFF] rounded-xl flex items-center justify-center text-xl font-black">
                            🏆
                        </div>
                        <h3 class="text-xl font-black text-white group-hover:text-amber-400 transition-colors" style="font-family: 'Anton', sans-serif;">
                            PENGATURAN RATING TERTINGGI
                        </h3>
                        <p class="text-xs text-zinc-400 font-bold leading-relaxed">
                            Ubah skor rating anime, atur daftar top-rated, dan ubah ranking nilai bintang anime secara langsung.
                        </p>
                    </div>
                    <a href="/admin/top-rated" class="manga-button w-full py-3 text-xs font-black rounded-xl text-white text-center hover:bg-amber-400 hover:text-[#0D0D0D]">
                        BUKA HALAMAN RATING TERTINGGI ➔
                    </a>
                </div>

                <!-- Card 3: Catalogue Management Page -->
                <div class="bg-[#1A1A1A] border-2 border-white shadow-[6px_6px_0px_#FFFFFF] p-6 rounded-2xl flex flex-col justify-between space-y-4 hover:-translate-y-1 transition-all group">
                    <div class="space-y-2">
                        <div class="w-12 h-12 bg-zinc-800 text-white border-2 border-white shadow-[2px_2px_0px_#FFFFFF] rounded-xl flex items-center justify-center text-xl font-black">
                            📋
                        </div>
                        <h3 class="text-xl font-black text-white group-hover:text-zinc-300 transition-colors" style="font-family: 'Anton', sans-serif;">
                            KATALOG LENGKAP ANIME
                        </h3>
                        <p class="text-xs text-zinc-400 font-bold leading-relaxed">
                            Lihat seluruh daftar anime, jumlah episode, jenis tayangan, dan preview halaman streaming anime.
                        </p>
                    </div>
                    <a href="/admin/anime" class="manga-button w-full py-3 text-xs font-black rounded-xl text-white text-center">
                        BUKA KATALOG LENGKAP ➔
                    </a>
                </div>

            </div>
        </div>

    </div>

</x-admin-layout>