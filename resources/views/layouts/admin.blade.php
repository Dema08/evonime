<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EVONIME - Admin Control Panel">
    <title>{{ $title ?? 'Admin Dashboard - EVONIME' }}</title>
    
    <!-- Google Fonts: Anton, Bangers, Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Bangers&family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0D0D0D] text-[#F5F0E6] font-sans antialiased selection:bg-[#E63946] selection:text-white min-h-screen">

    <div class="flex min-h-screen relative overflow-x-hidden">
        
        <!-- ADMIN SIDEBAR (Full Height Fixed/Sticky) -->
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
                <nav class="space-y-3 text-xs font-black uppercase">
                    
                    <div class="text-[10px] text-zinc-500 font-mono tracking-widest pt-2">CHAPTER 01</div>
                    <a href="/admin/dashboard" class="flex items-center gap-3 p-3 bg-[#E63946] text-white border-2 border-[#F5F0E6] shadow-[3px_3px_0px_#F5F0E6]">
                        <span>◆</span> DASHBOARD
                    </a>

                    <div class="text-[10px] text-zinc-500 font-mono tracking-widest pt-2">CHAPTER 02</div>
                    <a href="/anime" class="flex items-center gap-3 p-3 bg-[#141414] hover:bg-zinc-800 text-[#F5F0E6] border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] transition-all">
                        <span>◇</span> ANIME CATALOG
                    </a>

                    <div class="text-[10px] text-zinc-500 font-mono tracking-widest pt-2">CHAPTER 03</div>
                    <a href="/#schedule" class="flex items-center gap-3 p-3 bg-[#141414] hover:bg-zinc-800 text-[#F5F0E6] border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] transition-all">
                        <span>◇</span> EPISODES & TIMETABLE
                    </a>

                    <div class="text-[10px] text-zinc-500 font-mono tracking-widest pt-2">CHAPTER 04</div>
                    <a href="/#genres" class="flex items-center gap-3 p-3 bg-[#141414] hover:bg-zinc-800 text-[#F5F0E6] border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] transition-all">
                        <span>◇</span> GENRES & THEMES
                    </a>

                    <div class="text-[10px] text-zinc-500 font-mono tracking-widest pt-2">CHAPTER 05</div>
                    <a href="/" class="flex items-center gap-3 p-3 bg-[#141414] hover:bg-zinc-800 text-[#F5F0E6] border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] transition-all">
                        <span>🌐</span> PUBLIC WEBSITE
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

        <!-- MAIN CONTENT CONTAINER -->
        <div id="admin-main-wrapper" class="flex-grow flex flex-col pl-0 lg:pl-72 transition-all duration-300">
            
            <!-- Admin Top Header Bar -->
            <header class="h-20 bg-[#1A1A1A] border-b-2 border-[#F5F0E6] px-6 flex items-center justify-between sticky top-0 z-40">
                <div class="flex items-center gap-4">
                    <!-- Collapse / Toggle Sidebar Button -->
                    <button type="button" onclick="window.toggleAdminSidebar()" class="p-2.5 bg-[#141414] hover:bg-[#E63946] text-[#F5F0E6] border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <span class="text-xs font-mono font-bold text-zinc-400 hidden sm:inline">CONTROL PANEL // v3.0</span>
                </div>

                <div class="flex items-center gap-3">
                    <a href="/" class="px-4 py-2 text-xs font-black bg-[#141414] hover:bg-[#E63946] text-[#F5F0E6] border-2 border-[#F5F0E6] shadow-[2px_2px_0px_#F5F0E6] transition-all">
                        VIEW SITE ↗
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-grow p-6 md:p-8">
                {{ $slot }}
            </main>

        </div>

    </div>

    <!-- Toast Notifications -->
    <x-toast />

    <script>
        window.toggleAdminSidebar = function() {
            const sidebar = document.getElementById('admin-sidebar');
            const wrapper = document.getElementById('admin-main-wrapper');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
            } else if (window.innerWidth >= 1024) {
                sidebar.classList.toggle('lg:-translate-x-full');
                wrapper.classList.toggle('lg:pl-0');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        };

        // Responsive initial state for mobile
        if (window.innerWidth < 1024) {
            document.getElementById('admin-sidebar').classList.add('-translate-x-full');
        }
    </script>

</body>
</html>