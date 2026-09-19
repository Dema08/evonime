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
        
        <!-- ADMIN SIDEBAR COMPONENT -->
        <x-admin-sidebar />

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
