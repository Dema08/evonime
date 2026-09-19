<header id="main-navbar" class="hidden md:block fixed top-0 left-0 right-0 z-40 transition-all duration-300 h-20 bg-[#0D0D0D]/95 backdrop-blur-md border-b-2 border-[#F5F0E6]">
    <div class="max-w-[1400px] mx-auto h-full px-6 flex items-center justify-between">

        <!-- Logo & Navigation Links (Dark Manga Magazine Style) -->
        <div class="flex items-center gap-10">
            <a href="/" class="group flex items-center gap-2 text-2xl font-black tracking-tighter" style="font-family: 'Anton', sans-serif;">
                <span class="text-[#0D0D0D] bg-[#F5F0E6] px-2.5 py-1 border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] group-hover:bg-[#E63946] group-hover:text-white transition-all transform group-hover:rotate-[-1deg]">EVO</span>
                <span class="text-[#E63946] group-hover:text-[#F5F0E6] transition-colors">NIME</span>
                <span class="text-[10px] font-mono bg-[#F5F0E6] text-[#0D0D0D] px-1.5 py-0.5 rounded uppercase tracking-widest ml-1">VOL. 03</span>
            </a>

            <nav class="flex items-center gap-7 text-sm font-extrabold tracking-wide text-[#F5F0E6]">
                <a href="/" class="hover:text-[#E63946] transition-colors py-1 relative {{ request()->is('/') ? 'text-[#E63946] after:absolute after:bottom-0 after:left-0 after:w-full after:h-1 after:bg-[#E63946] after:rounded-none' : '' }}">HOME</a>
                <a href="/anime" class="hover:text-[#E63946] transition-colors py-1 relative {{ request()->is('anime*') ? 'text-[#E63946] after:absolute after:bottom-0 after:left-0 after:w-full after:h-1 after:bg-[#E63946] after:rounded-none' : '' }}">ANIME</a>
                <a href="/#genres" class="hover:text-[#E63946] transition-colors py-1">GENRES</a>
                <a href="/#schedule" class="hover:text-[#E63946] transition-colors py-1">SCHEDULE</a>
                @auth
                    <a href="/admin/dashboard" class="hover:text-[#E63946] transition-colors py-1 relative {{ request()->is('admin*') ? 'text-[#E63946] after:absolute after:bottom-0 after:left-0 after:w-full after:h-1 after:bg-[#E63946] after:rounded-none' : '' }}">DASHBOARD</a>
                @endauth
            </nav>
        </div>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-4">
            <!-- Search Icon Button -->
            <button id="search-trigger-btn" type="button" aria-label="Search" class="p-2.5 text-white bg-[#161616] hover:bg-[#E63946] hover:text-white border-2 border-white shadow-[3px_3px_0px_#FFFFFF] rounded-xl transition-all duration-150 transform hover:-translate-y-0.5">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>

            <!-- Watchlist Shortcut -->
            <a href="/watchlist" title="My Watchlist" class="p-2.5 text-white bg-[#161616] hover:bg-[#E63946] hover:text-white border-2 border-white shadow-[3px_3px_0px_#FFFFFF] rounded-xl transition-all duration-150 transform hover:-translate-y-0.5 relative">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
                <span id="watchlist-badge" class="hidden absolute -top-2 -right-2 w-5 h-5 bg-[#E63946] text-[10px] font-extrabold text-white rounded-full border-2 border-white flex items-center justify-center">0</span>
            </a>

            <!-- Auth Button: Admin / Login -->
            @auth
                <a href="/admin/dashboard" class="px-5 py-2 text-xs font-black tracking-wider text-white bg-[#E63946] hover:bg-red-700 border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] rounded-lg transition-all duration-150 transform hover:-translate-y-0.5 flex items-center gap-1.5">
                    <span>ADMIN</span>
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                </a>
            @else
                <a href="/login" class="px-5 py-2 text-xs font-black tracking-wider text-[#F5F0E6] bg-[#1A1A1A] hover:bg-[#E63946] hover:text-white border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] rounded-lg transition-all duration-150 transform hover:-translate-y-0.5">
                    LOGIN →
                </a>
            @endauth
        </div>

    </div>
</header>
