<x-admin-layout title="System Health Check - EVONIME Admin">
    <div class="max-w-[1400px] mx-auto space-y-6 text-[#F5F0E6]">
        
        @if (session('success'))
            <div class="bg-green-600 border-2 border-[#F5F0E6] p-4 text-sm font-black text-white">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-600 border-2 border-[#F5F0E6] p-4 text-sm font-black text-white">{{ session('error') }}</div>
        @endif

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[6px_6px_0px_#F5F0E6] p-6">
            <div>
                <h1 class="text-3xl font-black" style="font-family: 'Anton', sans-serif;">SYSTEM HEALTH CHECK</h1>
                <p class="text-xs text-zinc-400 font-bold">Monitor kesehatan server, database, cache, dan jalankan perintah sistem</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.anime-import') }}" class="px-5 py-2.5 text-xs font-black bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] hover:bg-red-700 transition-all">
                    📥 IMPORT ANIME
                </a>
            </div>
        </div>

        <!-- Status Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Database -->
            <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-6 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-mono font-bold text-zinc-400">DATABASE CONNECTION</span>
                    <span>{{ $checks['database'] ? '✅' : '❌' }}</span>
                </div>
                <h3 class="text-xl font-black text-white">MySQL / DB</h3>
                <p class="text-xs font-bold {{ $checks['database'] ? 'text-green-400' : 'text-red-500' }}">
                    {{ $checks['database'] ? 'Connected & Operational' : 'Connection Failed' }}
                </p>
            </div>

            <!-- Storage Writable -->
            <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-6 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-mono font-bold text-zinc-400">STORAGE WRITABLE</span>
                    <span>{{ $checks['storage'] ? '✅' : '❌' }}</span>
                </div>
                <h3 class="text-xl font-black text-white">storage/</h3>
                <p class="text-xs font-bold {{ $checks['storage'] ? 'text-green-400' : 'text-red-500' }}">
                    {{ $checks['storage'] ? 'Writable' : 'Not Writable' }}
                </p>
            </div>

            <!-- Bootstrap Cache Writable -->
            <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-6 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-mono font-bold text-zinc-400">BOOTSTRAP CACHE</span>
                    <span>{{ $checks['bootstrap_cache'] ? '✅' : '❌' }}</span>
                </div>
                <h3 class="text-xl font-black text-white">bootstrap/cache/</h3>
                <p class="text-xs font-bold {{ $checks['bootstrap_cache'] ? 'text-green-400' : 'text-red-500' }}">
                    {{ $checks['bootstrap_cache'] ? 'Writable' : 'Not Writable' }}
                </p>
            </div>

            <!-- Redis -->
            <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-6 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-mono font-bold text-zinc-400">REDIS / CACHE STORE</span>
                    <span>{{ $checks['redis'] === true ? '✅' : '⚠️' }}</span>
                </div>
                <h3 class="text-xl font-black text-white">Redis / File</h3>
                <p class="text-xs font-bold {{ $checks['redis'] === true ? 'text-green-400' : 'text-amber-400' }}">
                    {{ $checks['redis'] === true ? 'Redis Aktif & Terhubung' : 'Tidak aktif (Fallback ke File)' }}
                </p>
            </div>

            <!-- APP_KEY -->
            <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[4px_4px_0px_#F5F0E6] p-6 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-mono font-bold text-zinc-400">APPLICATION KEY</span>
                    <span>{{ $checks['app_key'] ? '✅' : '❌' }}</span>
                </div>
                <h3 class="text-xl font-black text-white">APP_KEY</h3>
                <p class="text-xs font-bold {{ $checks['app_key'] ? 'text-green-400' : 'text-red-500' }}">
                    {{ $checks['app_key'] ? 'Generated & Set' : 'Missing (Run key:generate)' }}
                </p>
            </div>
        </div>

        <!-- Action Tools -->
        <div class="bg-[#1A1A1A] border-2 border-[#F5F0E6] shadow-[6px_6px_0px_#F5F0E6] p-6 space-y-4">
            <h2 class="text-xl font-black uppercase tracking-wider" style="font-family: 'Anton', sans-serif;">SYSTEM ACTIONS</h2>
            <div class="flex flex-wrap gap-4">
                <!-- Clear Cache -->
                <form method="POST" action="{{ route('admin.health.clear-cache') }}">
                    @csrf
                    <button type="submit" class="px-5 py-3 text-xs font-black bg-[#141414] hover:bg-amber-600 text-white border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] transition-all">
                        🧹 CLEAR ALL CACHE
                    </button>
                </form>

                <!-- Run Migration -->
                <form method="POST" action="{{ route('admin.health.migrate') }}" onsubmit="return confirm('Jalankan database migration sekarang?');">
                    @csrf
                    <button type="submit" class="px-5 py-3 text-xs font-black bg-[#141414] hover:bg-blue-600 text-white border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6] transition-all">
                        ⚡ JALANKAN MIGRATION
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-admin-layout>