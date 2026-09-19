<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EVONIME - Your Anime, Your Universe. Dark, cinematic, modern anime streaming platform.">
    <title>{{ $title ?? 'EVONIME - Your Anime, Your Universe.' }}</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#070707] text-zinc-100 font-sans antialiased selection:bg-violet-600 selection:text-white flex flex-col min-h-screen">

    <!-- Desktop Navbar -->
    <x-navbar />

    <!-- Mobile Top Navigation & Bottom Fixed Navigation -->
    <x-mobile-nav />

    <!-- Main Content Container -->
    <main class="flex-grow pb-24 md:pb-12 pt-16 md:pt-20">
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
