<x-app-layout title="Profil Saya - EVONIME">

    <div class="max-w-[1000px] mx-auto px-4 md:px-6 space-y-8 mt-4 pt-24 md:pt-28 text-[#F5F0E6]">

        <!-- Page Header (Manga Style) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-[#F5F0E6] pb-6">
            <div>
                <h1 class="text-2xl md:text-4xl font-black text-[#F5F0E6] tracking-tight flex items-center gap-3" style="font-family: 'Anton', sans-serif;">
                    <span class="w-2.5 h-7 bg-[#E63946] border border-[#F5F0E6] inline-block"></span>
                    PROFIL SAYA
                </h1>
                <p class="text-xs md:text-sm text-zinc-400 font-bold mt-1">Informasi Akun & Pengaturan Pengguna</p>
            </div>

            <!-- Role Badge -->
            <div class="flex items-center gap-2">
                <span class="px-3.5 py-1.5 text-xs font-black uppercase tracking-wider rounded-lg border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] {{ $user->isAdmin() ? 'bg-[#E63946] text-white' : 'bg-blue-600 text-white' }}">
                    {{ $user->isAdmin() ? 'OPERATOR / ADMIN' : 'ANGGOTA / MEMBER' }}
                </span>
            </div>
        </div>

        <!-- Main Profile Card -->
        <div class="bg-[#141414] border-2 border-[#F5F0E6] shadow-[6px_6px_0px_#F5F0E6] rounded-2xl p-6 md:p-8 relative overflow-hidden">
            <!-- Decorative Manga Corner Accent -->
            <div class="absolute top-0 right-0 bg-[#E63946] text-white text-[10px] font-black font-mono px-4 py-1 border-b-2 border-l-2 border-[#F5F0E6] uppercase tracking-widest">
                CHAPTER 00 // USER PROFILE
            </div>

            <div class="flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-8 mt-2">
                <!-- Avatar with Manga Red Ring -->
                <div class="relative group">
                    <img src="{{ $user->avatarUrl() }}" 
                         alt="{{ $user->name }}" 
                         class="w-28 h-28 md:w-32 md:h-32 rounded-full object-cover border-4 border-[#E63946] shadow-[0_0_20px_rgba(230,57,70,0.4)] bg-zinc-900 flex-shrink-0"
                         onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=E63946&color=fff';">
                    
                    @if($user->google_id)
                        <div class="absolute -bottom-1 -right-1 bg-white p-1.5 rounded-full shadow-md border border-zinc-300" title="Terhubung dengan Google OAuth">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Profile Details -->
                <div class="flex-grow text-center md:text-left space-y-4">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-black text-white tracking-tight flex flex-wrap items-center justify-center md:justify-start gap-2">
                            <span>{{ $user->name }}</span>
                            @if($user->isAdmin())
                                <span class="text-xs bg-[#E63946] text-white px-2 py-0.5 rounded font-black tracking-widest uppercase">ADMIN</span>
                            @endif
                        </h2>
                        <p class="text-sm md:text-base text-zinc-400 font-mono mt-0.5">{{ $user->email }}</p>
                    </div>

                    <!-- Meta Information Badges Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <!-- Account Status -->
                        <div class="bg-[#1C1C1C] p-3 rounded-xl border border-zinc-800 flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full {{ ($user->is_active ?? true) ? 'bg-emerald-500 shadow-[0_0_8px_#10B981]' : 'bg-amber-500' }}"></span>
                            <div>
                                <span class="text-[10px] text-zinc-400 uppercase font-black tracking-wider block">Status Akun</span>
                                <span class="text-xs font-black text-white">{{ ($user->is_active ?? true) ? 'Aktif' : 'Nonaktif' }}</span>
                            </div>
                        </div>

                        <!-- Login Method -->
                        <div class="bg-[#1C1C1C] p-3 rounded-xl border border-zinc-800 flex items-center gap-3">
                            <span class="text-base">🔐</span>
                            <div>
                                <span class="text-[10px] text-zinc-400 uppercase font-black tracking-wider block">Metode Login</span>
                                <span class="text-xs font-black text-white">{{ $user->google_id ? 'Google OAuth' : 'Email & Password' }}</span>
                            </div>
                        </div>

                        <!-- Member Since -->
                        <div class="bg-[#1C1C1C] p-3 rounded-xl border border-zinc-800 flex items-center gap-3">
                            <span class="text-base">📅</span>
                            <div>
                                <span class="text-[10px] text-zinc-400 uppercase font-black tracking-wider block">Bergabung Sejak</span>
                                <span class="text-xs font-black text-white">{{ $user->created_at ? $user->created_at->format('M Y') : 'Recently' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 pt-4 border-t border-zinc-800/80">
                        @if($user->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 bg-[#800A20] hover:bg-[#a50d2a] text-[#FF4D6D] hover:text-white border-2 border-[#E63946]/50 rounded-xl text-xs font-black tracking-wider shadow-lg flex items-center gap-2 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-16zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-16z"></path>
                                </svg>
                                <span>DASHBOARD ADMIN</span>
                            </a>
                        @endif

                        <a href="/watchlist" class="px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-white rounded-xl text-xs font-black tracking-wider border border-zinc-700 flex items-center gap-2 transition-all">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                            <span>WATCHLIST SAYA</span>
                        </a>

                        <a href="/history" class="px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-white rounded-xl text-xs font-black tracking-wider border border-zinc-700 flex items-center gap-2 transition-all">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>RIWAYAT NONTON</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-[#E63946] hover:bg-[#c92a37] text-white rounded-xl text-xs font-black tracking-wider shadow-lg flex items-center gap-2 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>LOGOUT</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-app-layout>
