<header id="main-navbar" class="hidden md:block fixed top-0 left-0 right-0 z-50 transition-all duration-300 h-20 bg-transparent border-b border-transparent">
    <div class="max-w-[1400px] mx-auto h-full px-6 flex items-center justify-between">

        <!-- Logo & Navigation Links (Dark Manga Spread Style) -->
        <div class="flex items-center gap-8">
            <a href="/" class="group flex items-center gap-2 text-2xl font-black tracking-tighter" style="font-family: 'Anton', sans-serif;">
                <span class="text-[#0D0D0D] bg-[#F5F0E6] px-2.5 py-1 border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] group-hover:bg-[#E63946] group-hover:text-white transition-all transform group-hover:rotate-[-1deg]">EVO</span>
                <span class="text-[#E63946] group-hover:text-[#F5F0E6] transition-colors">NIME</span>
                <span class="text-[10px] font-mono bg-[#F5F0E6] text-[#0D0D0D] px-1.5 py-0.5 rounded uppercase tracking-widest ml-1">VOL. 03</span>
            </a>

            <!-- Navigation Links with Dark Red Capsule Active Pill -->
            <nav class="flex items-center gap-2 text-xs md:text-sm font-bold tracking-wide">
                <a href="/" data-nav-target="home" class="nav-item-link px-4 py-2 rounded-full flex items-center justify-center transition-all duration-200 text-zinc-300 hover:text-white hover:bg-white/10 {{ request()->is('/') && !request()->has('genre') ? 'bg-[#800A20] text-[#FF4D6D] border border-[#E63946]/40 shadow-[0_0_12px_rgba(230,57,70,0.3)] font-extrabold active' : '' }}">
                    <span>HOME</span>
                </a>

                <a href="/anime" data-nav-target="anime" class="nav-item-link px-4 py-2 rounded-full flex items-center justify-center transition-all duration-200 text-zinc-300 hover:text-white hover:bg-white/10 {{ request()->is('anime*') ? 'bg-[#800A20] text-[#FF4D6D] border border-[#E63946]/40 shadow-[0_0_12px_rgba(230,57,70,0.3)] font-extrabold active' : '' }}">
                    <span>ANIME</span>
                </a>

                <a href="/#genres" data-nav-target="genres" class="nav-item-link px-4 py-2 rounded-full flex items-center justify-center transition-all duration-200 text-zinc-300 hover:text-white hover:bg-white/10">
                    <span>GENRES</span>
                </a>

                <a href="/#schedule" data-nav-target="schedule" class="nav-item-link px-4 py-2 rounded-full flex items-center justify-center transition-all duration-200 text-zinc-300 hover:text-white hover:bg-white/10">
                    <span>SCHEDULE</span>
                </a>

                @auth
                    <a href="/admin/dashboard" data-nav-target="dashboard" class="nav-item-link px-4 py-2 rounded-full flex items-center justify-center transition-all duration-200 text-zinc-300 hover:text-white hover:bg-white/10 {{ request()->is('admin*') ? 'bg-[#800A20] text-[#FF4D6D] border border-[#E63946]/40 shadow-[0_0_12px_rgba(230,57,70,0.3)] font-extrabold active' : '' }}">
                        <span>DASHBOARD</span>
                    </a>
                @endauth
            </nav>
        </div>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-3">
            <!-- Search Icon Button -->
            <button id="search-trigger-btn" type="button" aria-label="Search" class="p-2.5 text-zinc-300 hover:text-white hover:bg-white/10 rounded-full transition-all duration-150">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>

            <!-- Watchlist Shortcut -->
            <a href="/watchlist" title="My Watchlist" class="p-2.5 text-zinc-300 hover:text-white hover:bg-white/10 rounded-full transition-all duration-150 relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
                <span id="watchlist-badge" class="hidden absolute -top-1 -right-1 w-4.5 h-4.5 bg-[#E63946] text-[10px] font-black text-white rounded-full flex items-center justify-center">0</span>
            </a>

            <!-- Account / User Button (Matching 'Akun ∨' style in reference image) -->
            @auth
                <a href="/admin/dashboard" class="px-4 py-2 text-xs font-bold text-white bg-zinc-900/90 hover:bg-[#E63946] border border-zinc-700/80 rounded-full transition-all duration-150 flex items-center gap-2 shadow-md">
                    <svg class="w-4 h-4 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>ADMIN</span>
                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
            @else
                <a href="/login" class="px-4 py-2 text-xs font-bold text-white bg-zinc-900/90 hover:bg-[#E63946] border border-zinc-700/80 rounded-full transition-all duration-150 flex items-center gap-2 shadow-md">
                    <svg class="w-4 h-4 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>AKUN</span>
                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
            @endauth
        </div>

    </div>
</header>
