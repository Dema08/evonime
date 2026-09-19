<!-- Mobile Top Navbar (Visible only on mobile screens < 768px) -->
<header class="md:hidden fixed top-0 left-0 right-0 z-40 h-14 bg-[#070707]/80 backdrop-blur-lg border-b border-white/5 px-4 flex items-center justify-between">
    <a href="/" class="text-xl font-extrabold tracking-wider">
        <span class="text-white">EVO<span class="text-red-500">NIME</span></span>
    </a>
    <button id="mobile-search-trigger" type="button" class="p-2 text-zinc-300 hover:text-white bg-zinc-900/80 border border-zinc-800 rounded-full">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    </button>
</header>

<!-- Mobile Bottom Navigation Bar -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 h-16 bg-[#0B0B0B]/90 backdrop-blur-xl border-t border-zinc-800/80 px-2 flex items-center justify-around">
    <!-- Home -->
    <a href="/" class="flex flex-col items-center gap-1 text-[11px] font-medium transition-colors {{ request()->is('/') ? 'text-red-400 font-semibold' : 'text-zinc-400 hover:text-zinc-200' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        <span>Home</span>
    </a>

    <!-- Explore -->
    <a href="/anime" class="flex flex-col items-center gap-1 text-[11px] font-medium transition-colors {{ request()->is('anime*') ? 'text-red-400 font-semibold' : 'text-zinc-400 hover:text-zinc-200' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
        </svg>
        <span>Explore</span>
    </a>

    <!-- Schedule -->
    <a href="/#schedule" class="flex flex-col items-center gap-1 text-[11px] font-medium transition-colors text-zinc-400 hover:text-zinc-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <span>Schedule</span>
    </a>

    <!-- Watchlist -->
    <a href="/watchlist" class="flex flex-col items-center gap-1 text-[11px] font-medium transition-colors {{ request()->is('watchlist') ? 'text-red-400 font-semibold' : 'text-zinc-400 hover:text-zinc-200' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
        </svg>
        <span>Watchlist</span>
    </a>

    <!-- Profile/Login/Dashboard -->
    @auth
        <a href="/admin/dashboard" class="flex flex-col items-center gap-1 text-[11px] font-medium transition-colors {{ request()->is('admin*') ? 'text-[#E63946] font-semibold' : 'text-zinc-400 hover:text-zinc-200' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-16zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-16z"></path>
            </svg>
            <span>Admin</span>
        </a>
    @else
        <a href="/login" class="flex flex-col items-center gap-1 text-[11px] font-medium transition-colors {{ request()->is('login') || request()->is('register') ? 'text-[#E63946] font-semibold' : 'text-zinc-400 hover:text-zinc-200' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span>Login</span>
        </a>
    @endauth
</nav>
