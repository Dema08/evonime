<header id="main-navbar" class="hidden md:block fixed top-0 left-0 right-0 z-40 transition-all duration-300 h-18 bg-[#070707]/40 backdrop-blur-md border-b border-white/5">
    <div class="max-w-[1400px] mx-auto h-full px-6 flex items-center justify-between">
        
        <!-- Logo & Navigation Links -->
        <div class="flex items-center gap-10">
            <a href="/" class="group flex items-center gap-2 text-2xl font-extrabold tracking-wider font-sans">
                <span class="text-white tracking-widest">EVO<span class="text-violet-500 group-hover:text-purple-400 transition-colors drop-shadow-[0_0_12px_rgba(124,58,237,0.6)]">NIME</span></span>
            </a>

            <nav class="flex items-center gap-7 text-sm font-semibold tracking-wide text-zinc-300">
                <a href="/" class="hover:text-violet-400 transition-colors py-1 relative {{ request()->is('/') ? 'text-violet-400 font-bold after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-violet-500 after:rounded-full' : '' }}">Home</a>
                <a href="/anime" class="hover:text-violet-400 transition-colors py-1 relative {{ request()->is('anime*') ? 'text-violet-400 font-bold after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-violet-500 after:rounded-full' : '' }}">Anime</a>
                <a href="/#genres" class="hover:text-violet-400 transition-colors py-1">Genres</a>
                <a href="/#schedule" class="hover:text-violet-400 transition-colors py-1">Schedule</a>
            </nav>
        </div>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-4">
            <!-- Search Icon Button -->
            <button id="search-trigger-btn" type="button" aria-label="Search" class="p-2.5 text-zinc-400 hover:text-white bg-zinc-900/60 hover:bg-zinc-800 border border-zinc-800 rounded-full transition-all duration-200">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>

            <!-- Watchlist Shortcut -->
            <a href="/watchlist" title="My Watchlist" class="p-2.5 text-zinc-400 hover:text-violet-400 bg-zinc-900/60 hover:bg-zinc-800 border border-zinc-800 rounded-full transition-all duration-200 relative">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
                <span id="watchlist-badge" class="hidden absolute -top-1 -right-1 w-4 h-4 bg-violet-600 text-[10px] font-bold text-white rounded-full flex items-center justify-center">0</span>
            </a>

            <!-- Login Button -->
            <a href="/login" class="px-5 py-2 text-xs font-bold tracking-wider text-violet-300 hover:text-white bg-violet-950/40 hover:bg-violet-600 border border-violet-500/50 rounded-full transition-all duration-300 shadow-[0_0_15px_rgba(124,58,237,0.2)] hover:shadow-[0_0_20px_rgba(124,58,237,0.5)]">
                LOGIN
            </a>
        </div>

    </div>
</header>
