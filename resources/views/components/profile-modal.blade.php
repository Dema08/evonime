<!-- Profile Modal / Bottom Sheet (Responsive for Mobile & Tablet) -->
<div id="profile-modal" 
     class="fixed inset-0 z-50 hidden flex items-end md:items-center justify-center p-0 md:p-4 bg-black/80 backdrop-blur-sm transition-opacity duration-300 opacity-0"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="profile-modal-title">

    <!-- Modal Backdrop Click Area -->
    <div id="profile-modal-backdrop" class="absolute inset-0"></div>

    <!-- Modal Dialog Card -->
    <div id="profile-modal-content" 
         class="relative w-full md:max-w-md bg-[#121212] border-t-2 md:border-2 border-[#F5F0E6] rounded-t-3xl md:rounded-2xl shadow-[0_-10px_30px_rgba(0,0,0,0.8)] md:shadow-[6px_6px_0px_#E63946] overflow-hidden transform translate-y-full md:translate-y-0 md:scale-95 transition-all duration-300 z-10">

        <!-- Mobile Pull Indicator -->
        <div class="md:hidden w-12 h-1 bg-zinc-700 rounded-full mx-auto mt-3 mb-1"></div>

        <!-- Header -->
        <div class="px-5 py-4 border-b-2 border-zinc-800 flex items-center justify-between bg-[#181818]">
            <div class="flex items-center gap-2">
                <span class="text-[#E63946] text-lg">👤</span>
                <h3 id="profile-modal-title" class="text-sm md:text-base font-black uppercase tracking-wider text-[#F5F0E6]">
                    PROFIL
                </h3>
            </div>
            
            <div class="flex items-center gap-2">
                @auth
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded border {{ auth()->user()->isAdmin() ? 'bg-[#800A20] text-[#FF4D6D] border-[#E63946]/40' : 'bg-blue-950/60 text-blue-400 border-blue-500/40' }}">
                        {{ auth()->user()->isAdmin() ? 'ADMIN' : 'MEMBER' }}
                    </span>
                @else
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-zinc-800 text-zinc-400 border border-zinc-700">
                        GUEST
                    </span>
                @endauth

                <!-- Close Button -->
                <button type="button" 
                        id="profile-modal-close-btn" 
                        class="p-1.5 text-zinc-400 hover:text-white bg-zinc-800/80 hover:bg-zinc-700 rounded-lg transition-colors"
                        aria-label="Tutup Profil">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        @auth
            <!-- Authenticated Profile Body -->
            <div class="p-6">
                <!-- User Profile Info -->
                <div class="flex items-center gap-4 pb-5 border-b border-zinc-800">
                    <img src="{{ auth()->user()->avatarUrl() }}" 
                         alt="{{ auth()->user()->name }}" 
                         class="w-16 h-16 rounded-full object-cover border-2 border-[#E63946] shadow-md flex-shrink-0 bg-zinc-900"
                         onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=E63946&color=fff';">
                    <div class="min-w-0 flex-1">
                        <h4 class="text-base font-black text-white truncate">{{ auth()->user()->name }}</h4>
                        <p class="text-xs text-zinc-400 font-mono truncate mt-0.5">{{ auth()->user()->email }}</p>
                        <div class="mt-2 flex items-center gap-1.5 text-xs font-bold text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_6px_#10B981]"></span>
                            <span>{{ (auth()->user()->is_active ?? true) ? 'Akun Aktif' : 'Nonaktif' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Links & Logout Button -->
                <div class="space-y-2 pt-4">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center justify-between px-4 py-3 bg-[#800A20]/40 hover:bg-[#800A20] text-[#FF4D6D] hover:text-white rounded-xl font-black text-xs tracking-wider border border-[#E63946]/40 transition-all">
                            <span class="flex items-center gap-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-16zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-16z"></path>
                                </svg>
                                <span>DASHBOARD ADMIN</span>
                            </span>
                            <span>→</span>
                        </a>
                    @endif

                    @if(Route::has('profile'))
                        <a href="{{ route('profile') }}" class="w-full flex items-center justify-between px-4 py-3 bg-zinc-800/80 hover:bg-zinc-700 text-white rounded-xl font-black text-xs tracking-wider border border-zinc-700 transition-all">
                            <span class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-[#E63946]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>PROFIL SAYA</span>
                            </span>
                            <span>→</span>
                        </a>
                    @endif

                    <a href="/watchlist" class="w-full flex items-center justify-between px-4 py-3 bg-zinc-800/80 hover:bg-zinc-700 text-white rounded-xl font-black text-xs tracking-wider border border-zinc-700 transition-all">
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                            <span>WATCHLIST SAYA</span>
                        </span>
                        <span>→</span>
                    </a>

                    <div class="pt-2">
                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-red-950/40 hover:bg-red-900/60 text-red-400 hover:text-white rounded-xl font-black text-xs tracking-wider border border-red-500/40 transition-all">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>LOGOUT</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <!-- Unauthenticated Profile Body (Guest) -->
            <div class="p-6 text-center flex flex-col items-center">
                <!-- Avatar Placeholder -->
                <div class="w-20 h-20 rounded-full bg-zinc-800 border-2 border-zinc-700 flex items-center justify-center text-zinc-400 mb-4 shadow-inner">
                    <svg class="w-10 h-10 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>

                <h4 class="text-base font-black text-white mb-1">Anda belum login</h4>
                <p class="text-xs text-zinc-400 leading-relaxed mb-6 max-w-[260px]">
                    Silakan login untuk mengakses akun Anda.
                </p>

                <a href="{{ route('login') }}" class="w-full py-3 px-4 bg-[#E63946] hover:bg-[#c92a37] text-white text-xs font-black tracking-wider rounded-xl shadow-lg shadow-red-900/30 transition-all text-center uppercase border border-[#F5F0E6]/20">
                    Login
                </a>

                <div class="mt-4 text-xs text-zinc-400">
                    Belum punya akun? <a href="{{ route('register') }}" class="text-[#E63946] hover:underline font-bold">Daftar Akun</a>
                </div>
            </div>
        @endauth

    </div>
</div>
