<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EVONIME - Modern Manga Paper & Dynamic Anime Streaming Platform.">
    <title>{{ $title ?? 'EVONIME - Manga Paper Anime Universe' }}</title>
    
    <!-- Google Fonts: Anton, Bangers, Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Bangers&family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0D0D0D] text-[#F5F0E6] font-sans antialiased selection:bg-[#E63946] selection:text-white flex flex-col min-h-screen">

    <!-- Desktop Navbar (Manga Magazine Header Style) -->
    <x-navbar />

    <!-- Mobile Top Navigation & Bottom Fixed Navigation -->
    <x-mobile-nav />

    <!-- Main Content Container -->
    <main class="flex-grow pb-24 md:pb-12">
        {{ $slot }}
    </main>

    <!-- Search Modal Overlay -->
    <x-search-overlay />

    <!-- Toast Notifications -->
    <x-toast />

    <!-- Footer -->
    <x-footer />

</body>
</html>
