<aside id="admin-sidebar" class="w-72 bg-[#1A1A1A] border-r-4 border-[#F5F0E6] flex flex-col justify-between fixed inset-y-0 left-0 z-50 transition-transform duration-300 ease-in-out">
            
    <div class="p-6 space-y-6 overflow-y-auto scrollbar-hide">
        <!-- Brand / Logo -->
        <div class="flex items-center justify-between border-b-2 border-[#F5F0E6] pb-4">
            <a href="/" class="group flex items-center gap-2 text-2xl font-black tracking-tighter" style="font-family: 'Anton', sans-serif;">
                <span class="text-[#0D0D0D] bg-[#F5F0E6] px-2 py-0.5 border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#E63946]">EVO</span>
                <span class="text-[#E63946]">NIME</span>
            </a>
            <button type="button" onclick="window.toggleAdminSidebar()" class="lg:hidden p-1 bg-[#141414] border border-[#F5F0E6] text-[#F5F0E6]">
                ✕
            </button>
        </div>

        <!-- Admin Profile Badge -->
        <div class="p-3 bg-[#141414] border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6]">
            <span class="text-[10px] font-black uppercase text-[#E63946] block">SECURE OPERATOR</span>
            <span class="text-xs font-black text-white truncate block">{{ Auth::user()->name ?? 'Admin' }}</span>
        </div>

        <!-- Sidebar Navigation Menu -->
        <nav class="space-y-2 text-xs font-black uppercase">
            <div class="text-[10px] text-zinc-500 font-mono tracking-widest pt-2">CHAPTER 01 // OVERVIEW & HERO</div>
            <a href="/admin" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#E63946] text-white' : 'bg-[#141414] text-[#F5F0E6] hover:bg-[#E63946] hover:text-white' }} border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] transition-colors">
                <span>◆</span> DASHBOARD
            </a>
            <a href="/admin/hero" class="flex items-center gap-3 p-3 {{ request()->is('admin/hero*') ? 'bg-[#E63946] text-white' : 'bg-[#141414] text-[#F5F0E6] hover:bg-[#E63946] hover:text-white' }} border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] transition-colors">
                <span>★</span> HERO BANNER
            </a>
            <a href="/admin/top-rated" class="flex items-center gap-3 p-3 {{ request()->is('admin/top-rated*') ? 'bg-[#E63946] text-white' : 'bg-[#141414] text-[#F5F0E6] hover:bg-[#E63946] hover:text-white' }} border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] transition-colors">
                <span>🏆</span> RATING TERTINGGI
            </a>
            <a href="/admin/health" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.health') ? 'bg-[#E63946] text-white' : 'bg-[#141414] text-[#F5F0E6] hover:bg-[#E63946] hover:text-white' }} border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] transition-colors">
                <span>🩺</span> HEALTH CHECK
            </a>

            <div class="text-[10px] text-zinc-500 font-mono tracking-widest pt-2">CHAPTER 02 // KATALOG</div>
            <a href="/admin/anime-import" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.anime-import') ? 'bg-[#E63946] text-white' : 'bg-[#141414] text-[#F5F0E6] hover:bg-[#E63946] hover:text-white' }} border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] transition-colors">
                <span>📥</span> IMPORT ANIME
            </a>
            <a href="/admin/animes" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.animes.*') ? 'bg-[#E63946] text-white' : 'bg-[#141414] text-[#F5F0E6] hover:bg-[#E63946] hover:text-white' }} border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] transition-colors">
                <span>▣</span> ANIME
            </a>
            <a href="/admin/genres" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.genres.*') ? 'bg-[#E63946] text-white' : 'bg-[#141414] text-[#F5F0E6] hover:bg-[#E63946] hover:text-white' }} border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] transition-colors">
                <span>◈</span> GENRES
            </a>
            <a href="/admin/users" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.users.*') ? 'bg-[#E63946] text-white' : 'bg-[#141414] text-[#F5F0E6] hover:bg-[#E63946] hover:text-white' }} border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] transition-colors">
                <span>●</span> USERS
            </a>
        </nav>
    </div>

    <!-- Sidebar Footer (Logout) -->
    <div class="p-6 border-t-2 border-[#F5F0E6] bg-[#141414]">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="manga-button w-full py-3 text-xs font-black uppercase rounded-lg text-[#F5F0E6] bg-[#1A1A1A] hover:bg-[#E63946] hover:text-white transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                LOGOUT
            </button>
        </form>
    </div>

</aside>
