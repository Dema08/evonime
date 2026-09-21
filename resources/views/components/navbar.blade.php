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

            <!-- Profile / Account Dropdown Menu -->
            <div class="relative" id="profile-dropdown-container">
                <!-- Dropdown Trigger Button -->
                <button id="profile-dropdown-btn" 
                        type="button" 
                        aria-expanded="false" 
                        aria-haspopup="true" 
                        class="px-3 py-1.5 text-xs font-bold text-white bg-zinc-900/90 hover:bg-zinc-800 border border-zinc-700/80 rounded-full transition-all duration-150 flex items-center gap-2 shadow-md hover:border-[#E63946]/60 cursor-pointer select-none">
                    @auth
                        <img src="{{ auth()->user()->avatarUrl() }}" 
                             alt="{{ auth()->user()->name }}" 
                             class="w-6 h-6 rounded-full object-cover border border-[#E63946] flex-shrink-0"
                             onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=E63946&color=fff';">
                        <span class="max-w-[110px] truncate font-black">{{ auth()->user()->name }}</span>
                    @else
                        <div class="w-6 h-6 rounded-full bg-zinc-800 flex items-center justify-center text-zinc-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <span class="font-black tracking-wider">AKUN</span>
                    @endauth
                    <svg id="profile-chevron" class="w-3.5 h-3.5 text-zinc-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Popover Content -->
                <div id="profile-dropdown-menu" 
                     class="hidden absolute right-0 mt-3 w-80 bg-[#121212] border-2 border-[#F5F0E6]/80 rounded-2xl shadow-[6px_6px_0px_rgba(230,57,70,0.5)] z-50 overflow-hidden transform transition-all duration-200 origin-top-right">
                    
                    @auth
                        <!-- Authenticated User Profile Card -->
                        <div class="px-4 py-3 bg-[#181818] border-b-2 border-zinc-800 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-[#F5F0E6]">
                                <span class="text-[#E63946] text-base">👤</span>
                                <span>PROFIL</span>
                            </div>
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded border {{ auth()->user()->isAdmin() ? 'bg-[#800A20] text-[#FF4D6D] border-[#E63946]/40' : 'bg-blue-950/60 text-blue-400 border-blue-500/40' }}">
                                {{ auth()->user()->isAdmin() ? 'ADMIN' : 'MEMBER' }}
                            </span>
                        </div>

                        <!-- User Info Body -->
                        <div class="p-4 flex items-center gap-3.5 border-b border-zinc-800 bg-[#141414]">
                            <img src="{{ auth()->user()->avatarUrl() }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="w-14 h-14 rounded-full object-cover border-2 border-[#E63946] shadow-md flex-shrink-0 bg-zinc-900"
                                 onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=E63946&color=fff';">
                            <div class="min-w-0 flex-1">
                                <h4 class="text-sm font-black text-white truncate">{{ auth()->user()->name }}</h4>
                                <p class="text-xs text-zinc-400 font-mono truncate">{{ auth()->user()->email }}</p>
                                <div class="mt-1.5 flex items-center gap-1.5 text-[11px] font-bold text-emerald-400">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_6px_#10B981]"></span>
                                    <span>{{ (auth()->user()->is_active ?? true) ? 'Akun Aktif' : 'Nonaktif' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Links & Action Buttons -->
                        <div class="p-2.5 space-y-1 bg-[#101010]">
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-bold text-zinc-200 hover:text-white hover:bg-[#800A20]/40 rounded-lg transition-colors border border-transparent hover:border-[#E63946]/30">
                                    <svg class="w-4 h-4 text-[#E63946]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-16zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-16z"></path>
                                    </svg>
                                    <span>Dashboard Admin</span>
                                </a>
                            @endif

                            @if(Route::has('profile'))
                                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-bold text-zinc-200 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors border border-transparent hover:border-zinc-700">
                                    <svg class="w-4 h-4 text-[#E63946]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Profil Saya</span>
                                </a>
                            @endif

                            <a href="/watchlist" class="flex items-center gap-3 px-3 py-2 text-xs font-bold text-zinc-200 hover:text-white hover:bg-zinc-800 rounded-lg transition-colors border border-transparent hover:border-zinc-700">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                </svg>
                                <span>Watchlist Saya</span>
                            </a>

                            <div class="border-t border-zinc-800/80 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-xs font-bold text-red-400 hover:text-white hover:bg-red-600/20 rounded-lg transition-colors text-left border border-transparent hover:border-red-500/30">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Unauthenticated State (Guest) -->
                        <div class="px-4 py-3 bg-[#181818] border-b-2 border-zinc-800 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-[#F5F0E6]">
                                <span class="text-[#E63946] text-base">👤</span>
                                <span>PROFIL</span>
                            </div>
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-zinc-800 text-zinc-400 border border-zinc-700">
                                GUEST
                            </span>
                        </div>

                        <div class="p-6 text-center flex flex-col items-center bg-[#141414]">
                            <!-- Default Avatar Icon -->
                            <div class="w-16 h-16 rounded-full bg-zinc-800 border-2 border-zinc-700 flex items-center justify-center text-zinc-400 mb-3 shadow-inner">
                                <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>

                            <h4 class="text-sm font-black text-white mb-1">Anda belum login</h4>
                            <p class="text-xs text-zinc-400 leading-relaxed mb-4 max-w-[220px]">
                                Silakan login untuk mengakses akun Anda.
                            </p>

                            <a href="{{ route('login') }}" class="w-full py-2.5 px-4 bg-[#E63946] hover:bg-[#c92a37] text-white text-xs font-black tracking-wider rounded-xl shadow-lg shadow-red-900/30 transition-all text-center uppercase border border-[#F5F0E6]/20">
                                Login
                            </a>

                            <div class="mt-3 text-[11px] text-zinc-400">
                                Belum punya akun? <a href="{{ route('register') }}" class="text-[#E63946] hover:underline font-bold">Daftar</a>
                            </div>
                        </div>
                    @endauth

                </div>
            </div>
        </div>

    </div>
</header>
