<x-admin-layout title="Admin Dashboard - EVONIME">

    <div class="max-w-[1400px] mx-auto space-y-8 text-[#F5F0E6]">

        @if (session('success'))
            <div class="bg-green-600 border-2 border-[#F5F0E6] p-4 text-sm font-black text-white">{{ session('success') }}</div>
        @endif

        <!-- Dashboard Header Panel -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[6px_6px_0px_#F5F0E6] p-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase bg-[#E63946] text-white px-2 py-0.5 border border-[#F5F0E6]">CHAPTER 01 // CONTROL ROOM</span>
                    <span class="text-xs font-mono text-zinc-400">STATUS: ONLINE // REALTIME SYNC</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-[#F5F0E6] tracking-tight" style="font-family: 'Anton', sans-serif;">
                    ADMIN DASHBOARD
                </h1>
                <p class="text-xs md:text-sm text-zinc-400 font-bold">Welcome back, {{ Auth::user()->name ?? 'Admin' }}! Manage your anime catalog & streaming server.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="/" target="_blank" class="px-5 py-2.5 text-xs font-black bg-[#141414] hover:bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] transition-colors">
                    LIHAT SITE LIVE ↗
                </a>
            </div>
        </div>

        <!-- Statistics Panels Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Stat 1: Total Anime -->
            <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-5 space-y-2 relative overflow-hidden">
                <div class="absolute top-2 right-2 text-zinc-700 font-mono text-3xl font-black">01</div>
                <span class="text-xs font-black uppercase tracking-wider text-zinc-400 bg-[#141414] px-2 py-0.5 border border-[#F5F0E6]">TOTAL ANIME</span>
                <div class="text-4xl font-black text-[#F5F0E6]" style="font-family: 'Anton', sans-serif;">{{ $totalAnimes ?? 0 }} SERIES</div>
                <a href="{{ route('admin.animes.index') }}" class="text-[11px] text-[#E63946] font-black hover:underline block">KELOLA ANIME →</a>
            </div>

            <!-- Stat 2: Total Episodes -->
            <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-5 space-y-2 relative overflow-hidden">
                <div class="absolute top-2 right-2 text-zinc-700 font-mono text-3xl font-black">02</div>
                <span class="text-xs font-black uppercase tracking-wider text-zinc-400 bg-[#141414] px-2 py-0.5 border border-[#F5F0E6]">TOTAL EPISODES</span>
                <div class="text-4xl font-black text-[#F5F0E6]" style="font-family: 'Anton', sans-serif;">{{ $totalEpisodes ?? 0 }} EPS</div>
                <p class="text-[11px] text-zinc-400 font-bold">1080p HD CDN Stream</p>
            </div>

            <!-- Stat 3: Total Users -->
            <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-5 space-y-2 relative overflow-hidden">
                <div class="absolute top-2 right-2 text-zinc-700 font-mono text-3xl font-black">03</div>
                <span class="text-xs font-black uppercase tracking-wider text-zinc-400 bg-[#141414] px-2 py-0.5 border border-[#F5F0E6]">TOTAL USERS</span>
                <div class="text-4xl font-black text-[#F5F0E6]" style="font-family: 'Anton', sans-serif;">{{ $totalUsers ?? 0 }} USERS</div>
                <a href="{{ route('admin.users.index') }}" class="text-[11px] text-[#E63946] font-black hover:underline block">KELOLA USER →</a>
            </div>

            <!-- Stat 4: Total Views -->
            <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-5 space-y-2 relative overflow-hidden">
                <div class="absolute top-2 right-2 text-zinc-700 font-mono text-3xl font-black">04</div>
                <span class="text-xs font-black uppercase tracking-wider text-zinc-400 bg-[#141414] px-2 py-0.5 border border-[#F5F0E6]">TOTAL VIEWS</span>
                <div class="text-4xl font-black text-[#F5F0E6]" style="font-family: 'Anton', sans-serif;">{{ number_format($totalViews ?? 0) }} VIEWS</div>
                <p class="text-[11px] text-emerald-400 font-bold">● Streaming Traffic</p>
            </div>

        </div>

        <!-- Anime Management Section -->
        <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[6px_6px_0px_#F5F0E6] p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-[#F5F0E6] pb-4">
                <div>
                    <h2 class="text-2xl font-black text-[#F5F0E6]" style="font-family: 'Anton', sans-serif;">ANIME CATALOGUE MANAGEMENT</h2>
                    <p class="text-xs text-zinc-400 font-bold">Manage series, chapters, and streaming links</p>
                </div>
                <a href="{{ route('admin.animes.create') }}" class="px-5 py-2.5 text-xs font-black bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] hover:bg-red-700 transition-colors text-center">
                    + ADD NEW ANIME
                </a>
            </div>

            <!-- Table of Anime -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#141414] border-2 border-[#F5F0E6] text-xs font-black uppercase tracking-wider text-[#F5F0E6]">
                            <th class="p-3 border-r-2 border-[#F5F0E6]">Poster</th>
                            <th class="p-3 border-r-2 border-[#F5F0E6]">Title</th>
                            <th class="p-3 border-r-2 border-[#F5F0E6]">Type</th>
                            <th class="p-3 border-r-2 border-[#F5F0E6]">Rating</th>
                            <th class="p-3 border-r-2 border-[#F5F0E6]">Status</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-zinc-800 text-xs font-bold text-zinc-300">
                        @forelse($latestAnimes as $anime)
                            <tr class="hover:bg-zinc-900/60 transition-colors">
                                <td class="p-3 border-r-2 border-zinc-800">
                                    <img src="{{ $anime->poster_path ? (str_starts_with($anime->poster_path, 'http') ? $anime->poster_path : Storage::url($anime->poster_path)) : 'https://placehold.co/100x140?text=No+Cover' }}" alt="{{ $anime->title }}" class="w-10 h-14 object-cover border border-[#F5F0E6]">
                                </td>
                                <td class="p-3 border-r-2 border-zinc-800 text-white font-black">
                                    {{ $anime->title }}
                                </td>
                                <td class="p-3 border-r-2 border-zinc-800">
                                    <span class="px-2 py-0.5 bg-[#141414] border border-[#F5F0E6] text-[10px] uppercase">{{ $anime->type }}</span>
                                </td>
                                <td class="p-3 border-r-2 border-zinc-800 text-amber-400 font-black">
                                    ★ {{ number_format($anime->rating ?? 0, 1) }}
                                </td>
                                <td class="p-3 border-r-2 border-zinc-800">
                                    {{ ucfirst($anime->status) }}
                                </td>
                                <td class="p-3 space-x-2">
                                    <a href="{{ route('admin.animes.show', $anime) }}" class="px-3 py-1 bg-[#141414] hover:bg-[#E63946] text-[#F5F0E6] hover:text-white border border-[#F5F0E6] font-black transition-colors inline-block">VIEW</a>
                                    <a href="{{ route('admin.animes.edit', $anime) }}" class="px-3 py-1 bg-[#141414] hover:bg-zinc-800 text-zinc-400 hover:text-white border border-zinc-700 font-bold transition-colors inline-block">EDIT</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-zinc-500 font-bold">Belum ada data anime.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</x-admin-layout>