// Alpine.js — required for admin pages (anime-import) using x-data / x-model / x-for
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// EVONIME Client-Side Interactive JavaScript Engine

document.addEventListener('DOMContentLoaded', () => {
    initNavbarScroll();
    applyCustomAdminState();
    initHeroCarousel();
    initSearchModal();
    initWatchlistBadge();
    initScheduleTabs();
    initProfileMenu();
});

/* ==========================================
   1. NAVBAR SCROLL & ACTIVE INDICATOR ENGINE
   ========================================== */
function initNavbarScroll() {
    const navbar = document.getElementById('main-navbar');
    if (navbar) {
        const handleScroll = () => {
            if (window.scrollY > 30) {
                navbar.classList.add('bg-[#0A0A0A]/95', 'backdrop-blur-xl', 'border-b', 'border-zinc-800/80', 'shadow-2xl');
                navbar.classList.remove('bg-transparent', 'border-transparent');
            } else {
                navbar.classList.remove('bg-[#0A0A0A]/95', 'backdrop-blur-xl', 'border-b', 'border-zinc-800/80', 'shadow-2xl');
                navbar.classList.add('bg-transparent', 'border-transparent');
            }
        };

        window.addEventListener('scroll', handleScroll);
        handleScroll();
    }

    initNavbarActiveState();
}

function initNavbarActiveState() {
    const desktopLinks = document.querySelectorAll('.nav-item-link');
    const mobileLinks = document.querySelectorAll('.mobile-nav-item-link');
    const allNavLinks = [...desktopLinks, ...mobileLinks];

    const activeClassesDesktop = [
        'bg-[#800A20]',
        'text-[#FF4D6D]',
        'border',
        'border-[#E63946]/40',
        'shadow-[0_0_12px_rgba(230,57,70,0.3)]',
        'font-extrabold',
        'active'
    ];
    const inactiveClassesDesktop = [
        'text-zinc-300',
        'hover:text-white',
        'hover:bg-white/10'
    ];

    let isProgrammaticScroll = false;
    let scrollLockTimeout = null;

    function setActiveNav(targetName) {
        desktopLinks.forEach(link => {
            const target = link.getAttribute('data-nav-target');
            if (target === targetName) {
                link.classList.add(...activeClassesDesktop);
                link.classList.remove(...inactiveClassesDesktop);
            } else {
                link.classList.remove(...activeClassesDesktop);
                link.classList.add(...inactiveClassesDesktop);
            }
        });

        mobileLinks.forEach(link => {
            const target = link.getAttribute('data-nav-target');
            if (target === targetName) {
                link.classList.add('text-[#E63946]', 'font-semibold', 'active');
                link.classList.remove('text-zinc-400');
            } else {
                link.classList.remove('text-[#E63946]', 'font-semibold', 'active');
                link.classList.add('text-zinc-400');
            }
        });
    }

    function scrollToSection(targetId, targetName) {
        const targetElement = document.getElementById(targetId);
        if (!targetElement) return;

        isProgrammaticScroll = true;
        clearTimeout(scrollLockTimeout);

        setActiveNav(targetName);

        const headerOffset = 90;
        const elementPosition = targetElement.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });

        history.pushState(null, null, `#${targetId}`);

        scrollLockTimeout = setTimeout(() => {
            isProgrammaticScroll = false;
        }, 800);
    }

    // Determine initial active state from URL path & hash
    const pathname = window.location.pathname;
    const hash = window.location.hash;

    let initialTarget = 'home';
    if (pathname.startsWith('/anime')) {
        initialTarget = 'anime';
    } else if (pathname.startsWith('/watchlist')) {
        initialTarget = 'watchlist';
    } else if (pathname.startsWith('/admin')) {
        initialTarget = 'dashboard';
    } else if (pathname.startsWith('/login') || pathname.startsWith('/register')) {
        initialTarget = 'login';
    } else if (pathname === '/' || pathname === '') {
        if (hash === '#genres') {
            initialTarget = 'genres';
        } else if (hash === '#schedule') {
            initialTarget = 'schedule';
        } else {
            initialTarget = 'home';
        }
    }
    setActiveNav(initialTarget);

    // If initial page load has a hash on homepage, scroll nicely with offset
    if ((pathname === '/' || pathname === '') && (hash === '#genres' || hash === '#schedule')) {
        setTimeout(() => {
            const targetId = hash.replace('#', '');
            scrollToSection(targetId, targetId);
        }, 150);
    }

    // Event listener for clicking navbar items
    allNavLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const target = link.getAttribute('data-nav-target');
            const href = link.getAttribute('href') || '';

            if (!target) return;

            // Handle hash links on the homepage
            if (href.startsWith('/#') || href.startsWith('#')) {
                const targetId = href.replace(/^\/?#/, '');

                if (window.location.pathname === '/' || window.location.pathname === '') {
                    e.preventDefault();
                    scrollToSection(targetId, target);
                }
            } else if (href === '/' || href === '') {
                if (window.location.pathname === '/' || window.location.pathname === '') {
                    e.preventDefault();
                    isProgrammaticScroll = true;
                    clearTimeout(scrollLockTimeout);
                    setActiveNav('home');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    history.pushState(null, null, '/');
                    scrollLockTimeout = setTimeout(() => {
                        isProgrammaticScroll = false;
                    }, 800);
                }
            } else {
                setActiveNav(target);
            }
        });
    });

    // ScrollSpy for Homepage sections (#genres, #schedule, and home)
    if (window.location.pathname === '/' || window.location.pathname === '') {
        window.addEventListener('scroll', () => {
            if (isProgrammaticScroll) return;

            const genresSec = document.getElementById('genres');
            const scheduleSec = document.getElementById('schedule');
            const headerOffset = 150;

            const scheduleTop = scheduleSec ? scheduleSec.getBoundingClientRect().top : Infinity;
            const genresTop = genresSec ? genresSec.getBoundingClientRect().top : Infinity;

            if (scheduleTop <= headerOffset) {
                setActiveNav('schedule');
            } else if (genresTop <= headerOffset) {
                setActiveNav('genres');
            } else {
                setActiveNav('home');
            }
        });
    }
}

/* ==========================================
   2. HERO CAROUSEL ENGINE (Auto Trailer Play, Controls & Slide Switch)
   ========================================== */
let heroIndex = 0;
let heroSlideTimer = null;
let isHeroVideoPaused = false;
let isHeroVideoMuted = true;

function initHeroCarousel() {
    function getVisibleSlides() {
        return Array.from(document.querySelectorAll('.hero-slide')).filter(slide => !slide.classList.contains('hidden'));
    }

    const prevBtn = document.getElementById('hero-prev');
    const nextBtn = document.getElementById('hero-next');

    let visibleSlides = getVisibleSlides();
    if (!visibleSlides.length) return;

    function parseYouTubeId(url) {
        if (!url) return '';
        const trimmed = url.trim();
        if (/^[a-zA-Z0-9_-]{11}$/.test(trimmed)) {
            return trimmed;
        }
        const regExp = /^.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = trimmed.match(regExp);
        return (match && match[1] && match[1].length === 11) ? match[1] : '';
    }

    function formatYouTubeUrl(url) {
        if (!url) return '';
        const ytId = parseYouTubeId(url);
        if (ytId) {
            const origin = encodeURIComponent(window.location.origin || '');
            return `https://www.youtube.com/embed/${ytId}?enablejsapi=1&autoplay=1&mute=1&controls=0&loop=0&modestbranding=1&rel=0&origin=${origin}&playlist=${ytId}`;
        }
        return url;
    }

    function stopSlideVideo(slide) {
        if (!slide) return;
        const videoContainer = slide.querySelector('.hero-video-container');
        if (!videoContainer) return;

        videoContainer.classList.remove('opacity-100');
        videoContainer.classList.add('opacity-0');

        const iframe = videoContainer.querySelector('iframe');
        if (iframe) {
            iframe.src = '';
        }

        const videoPlayer = videoContainer.querySelector('video');
        if (videoPlayer) {
            videoPlayer.pause();
            videoPlayer.onended = null;
            videoPlayer.removeAttribute('src');
            videoPlayer.load();
        }
    }

    function resetVideoControlUI() {
        isHeroVideoPaused = false;
        isHeroVideoMuted = true;

        document.querySelectorAll('.hero-muted-icon').forEach(el => el.classList.remove('hidden'));
        document.querySelectorAll('.hero-sound-icon').forEach(el => el.classList.add('hidden'));
    }

    function goToSlide(index) {
        if (heroSlideTimer) {
            clearTimeout(heroSlideTimer);
            heroSlideTimer = null;
        }

        visibleSlides = getVisibleSlides();
        if (!visibleSlides.length) return;

        // Stop video on all slides
        const allSlides = document.querySelectorAll('.hero-slide');
        allSlides.forEach(slide => {
            stopSlideVideo(slide);
            slide.classList.remove('opacity-100', 'z-10');
            slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
        });

        heroIndex = index % visibleSlides.length;
        if (heroIndex < 0) heroIndex = visibleSlides.length - 1;

        const activeSlide = visibleSlides[heroIndex];
        if (!activeSlide) return;

        activeSlide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
        activeSlide.classList.add('opacity-100', 'z-10');

        // Reset control UI state for new slide
        resetVideoControlUI();

        // Update dot indicators
        const dots = document.querySelectorAll('.hero-dot');
        dots.forEach((dot, i) => {
            if (i === heroIndex) {
                dot.className = 'hero-dot h-3 border-2 border-[#F5F0E6] transition-all duration-300 w-10 bg-[#E63946] shadow-[2px_2px_0px_#F5F0E6]';
            } else {
                dot.className = 'hero-dot h-3 border-2 border-[#F5F0E6] transition-all duration-300 w-3 bg-[#1A1A1A] hover:bg-zinc-700';
            }
        });

        // Check if current slide has a trailer video
        const mediaType = activeSlide.getAttribute('data-media-type') || 'auto';
        const trailerUrl = activeSlide.getAttribute('data-trailer') || '';
        const videoContainer = activeSlide.querySelector('.hero-video-container');
        const controlsBars = document.querySelectorAll('.hero-video-controls-bar');

        let isPlayingVideo = false;

        if (videoContainer && (mediaType === 'video' || (mediaType !== 'image' && trailerUrl))) {
            const iframe = videoContainer.querySelector('iframe');
            const videoPlayer = videoContainer.querySelector('video');
            const ytId = parseYouTubeId(trailerUrl);

            if (ytId || trailerUrl.includes('youtube.com') || trailerUrl.includes('youtu.be')) {
                if (iframe) {
                    iframe.classList.remove('hidden');
                    if (videoPlayer) videoPlayer.classList.add('hidden');

                    iframe.src = formatYouTubeUrl(trailerUrl);
                    videoContainer.classList.remove('opacity-0');
                    videoContainer.classList.add('opacity-100');
                    isPlayingVideo = true;

                    // Safety net (120s / 2 mins) so trailer is NEVER cut off early!
                    heroSlideTimer = setTimeout(() => {
                        if (!isHeroVideoPaused) nextSlide();
                    }, 120000);
                }
            } else if (trailerUrl) {
                if (videoPlayer) {
                    videoPlayer.classList.remove('hidden');
                    if (iframe) iframe.classList.add('hidden');

                    videoPlayer.src = trailerUrl;
                    videoPlayer.muted = true;
                    videoPlayer.playsInline = true;

                    videoContainer.classList.remove('opacity-0');
                    videoContainer.classList.add('opacity-100');
                    isPlayingVideo = true;

                    videoPlayer.play().catch(e => console.log('Autoplay blocked:', e));

                    // When video naturally ends, transition to next slide
                    videoPlayer.onended = () => {
                        nextSlide();
                    };

                    // Safety net timer (120s)
                    heroSlideTimer = setTimeout(() => {
                        if (!isHeroVideoPaused) nextSlide();
                    }, 120000);
                }
            }
        }

        // Toggle Video Controls Bar visibility
        if (isPlayingVideo) {
            controlsBars.forEach(bar => bar.classList.remove('hidden'));
        } else {
            controlsBars.forEach(bar => bar.classList.add('hidden'));
            // If image banner only, display for 6 seconds then switch
            heroSlideTimer = setTimeout(() => {
                nextSlide();
            }, 6000);
        }
    }

    function nextSlide() {
        visibleSlides = getVisibleSlides();
        if (!visibleSlides.length) return;
        let next = (heroIndex + 1) % visibleSlides.length;
        goToSlide(next);
    }

    function prevSlide() {
        visibleSlides = getVisibleSlides();
        if (!visibleSlides.length) return;
        let prev = (heroIndex - 1 + visibleSlides.length) % visibleSlides.length;
        goToSlide(prev);
    }

    if (nextBtn) nextBtn.onclick = () => { nextSlide(); };
    if (prevBtn) prevBtn.onclick = () => { prevSlide(); };

    const dots = document.querySelectorAll('.hero-dot');
    dots.forEach((dot, i) => {
        dot.onclick = () => { goToSlide(i); };
    });

    // YouTube API postMessage listener for ended state
    window.addEventListener('message', (e) => {
        try {
            const data = typeof e.data === 'string' ? JSON.parse(e.data) : e.data;
            if (data && (data.event === 'onStateChange' || (data.info && data.info.playerState !== undefined))) {
                const state = data.info?.playerState !== undefined ? data.info.playerState : data.info;
                if (state === 0) { // 0 = YT.PlayerState.ENDED
                    nextSlide();
                }
            }
        } catch (err) {}
    });

    goToSlide(0);
}

/* ==========================================
   2B. HERO VIDEO PLAY/PAUSE & MUTE CONTROLLERS
   ========================================== */
window.toggleHeroVideoPlay = function() {
    const activeSlide = document.querySelector('.hero-slide.opacity-100');
    if (!activeSlide) return;

    const videoContainer = activeSlide.querySelector('.hero-video-container');
    if (!videoContainer) return;

    const iframe = videoContainer.querySelector('iframe:not(.hidden)');
    const video = videoContainer.querySelector('video:not(.hidden)');

    isHeroVideoPaused = !isHeroVideoPaused;

    const pauseIcons = document.querySelectorAll('.hero-pause-icon');
    const playIcons = document.querySelectorAll('.hero-play-icon');
    const playTexts = document.querySelectorAll('.hero-play-text');

    if (isHeroVideoPaused) {
        if (heroSlideTimer) clearTimeout(heroSlideTimer);

        if (video) video.pause();
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
        }

        pauseIcons.forEach(el => el.classList.add('hidden'));
        playIcons.forEach(el => el.classList.remove('hidden'));
        playTexts.forEach(el => el.textContent = 'PLAY');
        if (window.showToast) window.showToast('Trailer Video Paused ⏸️');
    } else {
        if (video) video.play().catch(() => {});
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
        }

        pauseIcons.forEach(el => el.classList.remove('hidden'));
        playIcons.forEach(el => el.classList.add('hidden'));
        playTexts.forEach(el => el.textContent = 'PAUSE');
        if (window.showToast) window.showToast('Trailer Video Playing ▶️');
    }
};

window.toggleHeroVideoMute = function() {
    const activeSlide = document.querySelector('.hero-slide.opacity-100');
    if (!activeSlide) return;

    const videoContainer = activeSlide.querySelector('.hero-video-container');
    if (!videoContainer) return;

    const iframe = videoContainer.querySelector('iframe:not(.hidden)');
    const video = videoContainer.querySelector('video:not(.hidden)');

    isHeroVideoMuted = !isHeroVideoMuted;

    const mutedIcons = document.querySelectorAll('.hero-muted-icon');
    const soundIcons = document.querySelectorAll('.hero-sound-icon');

    if (isHeroVideoMuted) {
        if (video) video.muted = true;
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage('{"event":"command","func":"mute","args":""}', '*');
        }

        mutedIcons.forEach(el => el.classList.remove('hidden'));
        soundIcons.forEach(el => el.classList.add('hidden'));
        if (window.showToast) window.showToast('Audio Muted 🔇');
    } else {
        if (video) video.muted = false;
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage('{"event":"command","func":"unMute","args":""}', '*');
        }

        mutedIcons.forEach(el => el.classList.add('hidden'));
        soundIcons.forEach(el => el.classList.remove('hidden'));
        if (window.showToast) window.showToast('Audio Active 🔊');
    }
};

/* ==========================================
   3. REALTIME SEARCH MODAL OVERLAY
   ========================================== */
const sampleSearchData = [
    { title: 'Solo Leveling', slug: 'solo-leveling', poster: 'https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=200&auto=format&fit=crop', ep: 'EP 12', rating: 9.8, year: 2024 },
    { title: 'One Piece', slug: 'one-piece', poster: 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=200&auto=format&fit=crop', ep: 'EP 1142', rating: 9.2, year: 1999 },
    { title: 'Jujutsu Kaisen', slug: 'jujutsu-kaisen', poster: 'https://images.unsplash.com/photo-1563089145-599997674d42?q=80&w=200&auto=format&fit=crop', ep: 'EP 24', rating: 9.5, year: 2023 },
    { title: 'Demon Slayer: Kimetsu no Yaiba', slug: 'demon-slayer', poster: 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?q=80&w=200&auto=format&fit=crop', ep: 'EP 8', rating: 9.6, year: 2024 },
    { title: "Frieren: Beyond Journey's End", slug: 'frieren', poster: 'https://images.unsplash.com/photo-1514539079130-25950c84af65?q=80&w=200&auto=format&fit=crop', ep: 'EP 28', rating: 9.9, year: 2024 },
    { title: 'Attack on Titan', slug: 'attack-on-titan', poster: 'https://images.unsplash.com/photo-1560972550-aba3456b5564?q=80&w=200&auto=format&fit=crop', ep: 'EP 89', rating: 9.7, year: 2023 },
    { title: 'Bleach: Thousand-Year Blood War', slug: 'bleach-thousand-year-blood-war', poster: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=200&auto=format&fit=crop', ep: 'EP 26', rating: 9.4, year: 2024 },
    { title: 'Naruto Shippuden', slug: 'naruto-shippuden', poster: 'https://images.unsplash.com/photo-1578632767115-351597cf2477?q=80&w=200&auto=format&fit=crop', ep: 'EP 500', rating: 9.0, year: 2007 },
    { title: 'My Hero Academia', slug: 'my-hero-academia', poster: 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=200&auto=format&fit=crop', ep: 'EP 19', rating: 8.8, year: 2024 },
    { title: 'One Punch Man', slug: 'one-punch-man', poster: 'https://images.unsplash.com/photo-1563089145-599997674d42?q=80&w=200&auto=format&fit=crop', ep: 'EP 24', rating: 9.1, year: 2015 },
    { title: 'Hunter x Hunter', slug: 'hunter-x-hunter', poster: 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?q=80&w=200&auto=format&fit=crop', ep: 'EP 148', rating: 9.7, year: 2011 },
    { title: 'Black Clover', slug: 'black-clover', poster: 'https://images.unsplash.com/photo-1514539079130-25950c84af65?q=80&w=200&auto=format&fit=crop', ep: 'EP 170', rating: 8.9, year: 2017 }
];

function initSearchModal() {
    const triggerDesktop = document.getElementById('search-trigger-btn');
    const triggerMobile = document.getElementById('mobile-search-trigger');
    const modal = document.getElementById('search-modal');
    const content = document.getElementById('search-modal-content');
    const closeBtn = document.getElementById('search-close-btn');
    const searchInput = document.getElementById('search-input');
    const resultsList = document.getElementById('search-results-list');
    const resultsCount = document.getElementById('results-count');

    if (!modal) return;

    function openModal() {
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
            if (searchInput) searchInput.focus();
        }, 10);
    }

    function closeModal() {
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    if (triggerDesktop) triggerDesktop.addEventListener('click', openModal);
    if (triggerMobile) triggerMobile.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            openModal();
        }
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const q = e.target.value.trim().toLowerCase();
            renderSearchResults(q);
        });
    }

    function renderSearchResults(query) {
        if (!query) {
            resultsList.innerHTML = '<div class="text-center py-6 text-zinc-500 text-xs">Type anime name to search live...</div>';
            if (resultsCount) resultsCount.textContent = '';
            return;
        }

        const filtered = sampleSearchData.filter(item => item.title.toLowerCase().includes(query));
        if (resultsCount) resultsCount.textContent = `${filtered.length} found`;

        if (!filtered.length) {
            resultsList.innerHTML = '<div class="text-center py-8 text-zinc-500 text-xs">No anime matched your search.</div>';
            return;
        }

        resultsList.innerHTML = filtered.map(item => `
            <a href="/anime/${item.slug}" class="flex items-center gap-3 p-2 rounded-xl bg-[#151515] hover:bg-red-600/20 border border-zinc-800/80 hover:border-red-500/50 transition-all group">
                <img src="${item.poster}" alt="${item.title}" class="w-10 h-14 object-cover rounded-lg flex-shrink-0">
                <div class="flex-grow min-w-0">
                    <h4 class="text-sm font-bold text-white group-hover:text-red-400 truncate">${item.title}</h4>
                    <p class="text-xs text-zinc-400 mt-0.5">${item.ep} • ${item.year}</p>
                </div>
                <div class="px-2 py-0.5 bg-black/60 text-amber-400 text-xs font-bold rounded flex-shrink-0">
                    ★ ${item.rating}
                </div>
            </a>
        `).join('');
    }

    window.setSearchQuery = function(term) {
        if (searchInput) {
            searchInput.value = term;
            renderSearchResults(term.toLowerCase());
        }
    };
}

/* ==========================================
   4. ANIME SCHEDULE TABS SWITCHER
   ========================================== */
function initScheduleTabs() {
    window.switchScheduleTab = function(day) {
        const btns = document.querySelectorAll('.schedule-tab-btn');
        const items = document.querySelectorAll('.schedule-item-card');

        btns.forEach(btn => {
            if (btn.getAttribute('data-schedule-tab') === day) {
                btn.className = 'schedule-tab-btn px-5 py-2 text-xs md:text-sm font-bold rounded-xl transition-all duration-200 bg-red-600 text-white shadow-[0_0_15px_rgba(239,68,68,0.5)]';
            } else {
                btn.className = 'schedule-tab-btn px-5 py-2 text-xs md:text-sm font-bold rounded-xl transition-all duration-200 bg-[#151515] text-zinc-400 hover:text-white hover:bg-zinc-800 border border-zinc-800';
            }
        });

        items.forEach(item => {
            if (item.getAttribute('data-schedule-day') === day) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    };
}

/* ==========================================
   5. EXPLORE PAGE FILTER LOGIC
   ========================================== */
window.filterExplore = function(type) {
    const btns = document.querySelectorAll('.explore-tab-btn');
    const cards = document.querySelectorAll('.explore-card-item');

    btns.forEach(btn => {
        if (btn.getAttribute('data-filter') === type) {
            btn.className = 'explore-tab-btn px-4 py-2 text-xs font-bold rounded-xl transition-all bg-red-600 text-white shadow-[0_0_15px_rgba(239,68,68,0.5)]';
        } else {
            btn.className = 'explore-tab-btn px-4 py-2 text-xs font-bold rounded-xl transition-all bg-[#1A1A1A] text-zinc-300 hover:text-white border border-zinc-800';
        }
    });

    cards.forEach(card => {
        const rating = parseFloat(card.getAttribute('data-rating') || '0');
        if (type === 'all') {
            card.classList.remove('hidden');
        } else if (type === 'rating' && rating >= 9.5) {
            card.classList.remove('hidden');
        } else if (type === 'popular' || type === 'latest') {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
};

window.applyDropdownFilters = function() {
    const genre = document.getElementById('filter-genre')?.value.toLowerCase() || '';
    const year = document.getElementById('filter-year')?.value || '';
    const status = document.getElementById('filter-status')?.value || '';
    const type = document.getElementById('filter-type')?.value || '';
    const searchVal = document.getElementById('explore-search-input')?.value.toLowerCase().trim() || '';

    const cards = document.querySelectorAll('.explore-card-item');
    let visibleCount = 0;

    cards.forEach(card => {
        const cTitle = card.getAttribute('data-title') || '';
        const cGenres = card.getAttribute('data-genres') || '';
        const cYear = card.getAttribute('data-year') || '';
        const cStatus = card.getAttribute('data-status') || '';
        const cType = card.getAttribute('data-type') || '';

        const matchSearch = !searchVal || cTitle.includes(searchVal);
        const matchGenre = !genre || cGenres.includes(genre);
        const matchYear = !year || cYear === year;
        const matchStatus = !status || cStatus === status;
        const matchType = !type || cType === type;

        if (matchSearch && matchGenre && matchYear && matchStatus && matchType) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });

    const msg = document.getElementById('no-results-msg');
    if (msg) {
        if (visibleCount === 0) msg.classList.remove('hidden');
        else msg.classList.add('hidden');
    }
};

const exploreSearch = document.getElementById('explore-search-input');
if (exploreSearch) {
    exploreSearch.addEventListener('input', () => window.applyDropdownFilters());
}

/* ==========================================
   6. EPISODE SEARCH FILTER (ANIME DETAIL)
   ========================================== */
window.filterEpisodes = function(query) {
    const q = query.trim().toLowerCase();
    const items = document.querySelectorAll('.episode-item-wrapper');

    items.forEach(item => {
        const title = item.getAttribute('data-title') || '';
        const ep = item.getAttribute('data-ep') || '';
        if (!q || title.includes(q) || ep.includes(q)) {
            item.classList.remove('hidden');
        } else {
            item.classList.add('hidden');
        }
    });
};

/* ==========================================
   7. LOCALSTORAGE WATCHLIST MANAGER
   ========================================== */
function getWatchlist() {
    try {
        return JSON.parse(localStorage.getItem('evonime_watchlist') || '[]');
    } catch {
        return [];
    }
}

function initWatchlistBadge() {
    const list = getWatchlist();
    const badge = document.getElementById('watchlist-badge');
    if (badge) {
        if (list.length > 0) {
            badge.textContent = list.length;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}

window.toggleWatchlist = function(slug, btn) {
    let list = getWatchlist();
    const idx = list.indexOf(slug);

    if (idx > -1) {
        list.splice(idx, 1);
        window.showToast('Removed from your Watchlist');
        if (btn) btn.classList.remove('bg-red-600', 'text-white');
    } else {
        list.push(slug);
        window.showToast('Added to your Watchlist!');
        if (btn) btn.classList.add('bg-red-600', 'text-white');
    }

    localStorage.setItem('evonime_watchlist', JSON.stringify(list));
    initWatchlistBadge();
};

window.clearWatchlist = function() {
    localStorage.removeItem('evonime_watchlist');
    initWatchlistBadge();
    const grid = document.getElementById('watchlist-grid');
    const empty = document.getElementById('watchlist-empty-msg');
    if (grid) grid.innerHTML = '';
    if (empty) empty.classList.remove('hidden');
    window.showToast('Watchlist cleared');
};

window.clearHistory = function() {
    const grid = document.getElementById('history-grid');
    if (grid) grid.innerHTML = '<div class="col-span-full text-center py-16 text-zinc-500 text-sm">Watch history cleared.</div>';
    window.showToast('Watch history cleared');
};

/* ==========================================
   8. CUSTOM VIDEO PLAYER CONTROLS
   ========================================== */
let isPlaying = false;
window.togglePlayState = function() {
    isPlaying = !isPlaying;
    const btn = document.getElementById('big-play-btn');
    const playIcon = document.getElementById('play-icon');

    if (isPlaying) {
        if (btn) btn.classList.add('opacity-0', 'pointer-events-none');
        if (playIcon) playIcon.innerHTML = '<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>';
        window.showToast('Playback Started');
    } else {
        if (btn) btn.classList.remove('opacity-0', 'pointer-events-none');
        if (playIcon) playIcon.innerHTML = '<path d="M8 5v14l11-7z"/>';
        window.showToast('Playback Paused');
    }
};

window.seekPlayer = function(e) {
    const bar = document.getElementById('player-seekbar');
    const progress = document.getElementById('player-progress-bar');
    if (!bar || !progress) return;

    const rect = bar.getBoundingClientRect();
    const pct = Math.max(0, Math.min(100, ((e.clientX - rect.left) / rect.width) * 100));
    progress.style.width = pct + '%';
    const seconds = Math.floor((pct / 100) * 1440);
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    const timeDisplay = document.getElementById('current-time');
    if (timeDisplay) timeDisplay.textContent = `${mins}:${secs < 10 ? '0' : ''}${secs}`;
};

window.selectServer = function(num, btn) {
    document.querySelectorAll('.server-btn').forEach(b => {
        b.className = 'server-btn px-3 py-1.5 bg-[#1A1A1A] hover:bg-zinc-800 text-zinc-300 text-xs font-bold rounded-lg border border-zinc-800 transition-all';
    });
    if (btn) btn.className = 'server-btn px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-lg transition-all border border-red-500 shadow-md';
    window.showToast(`Switched to Server ${num}`);
};

window.toggleTheaterMode = function() {
    const container = document.getElementById('video-player-container');
    if (container) {
        container.classList.toggle('max-w-full');
        window.showToast('Theater mode toggled');
    }
};

window.toggleFullscreen = function() {
    const container = document.getElementById('video-player-container');
    if (!container) return;
    if (!document.fullscreenElement) {
        container.requestFullscreen().catch(() => {});
    } else {
        document.exitFullscreen().catch(() => {});
    }
};

/* ==========================================
   9. TOAST NOTIFICATION HELPER
   ========================================== */
window.showToast = function(msg) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'px-4 py-3 bg-[#151515] border border-red-500/60 text-white text-xs font-bold rounded-xl shadow-2xl backdrop-blur-md flex items-center gap-2 transform translate-y-4 opacity-0 transition-all duration-300 pointer-events-auto';
    toast.innerHTML = `
        <span class="w-2 h-2 rounded-full bg-red-400"></span>
        <span>${msg}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    }, 10);

    setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-4', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

/* ==========================================
   10. ADMIN STATE LIVE SYNC HELPER
   ========================================== */
function applyCustomAdminState() {
    // 1. Sync Custom Ratings
    try {
        const savedRatings = JSON.parse(localStorage.getItem('evonime_custom_ratings') || 'null');
        if (savedRatings) {
            Object.keys(savedRatings).forEach(slug => {
                const val = parseFloat(savedRatings[slug]).toFixed(1);
                const badges = document.querySelectorAll(`[data-slug="${slug}"] .rating-value, [data-rating-slug="${slug}"]`);
                badges.forEach(b => {
                    b.textContent = `★ ${val}`;
                });
            });
        }
    } catch (e) {
        console.warn('Error reading custom ratings', e);
    }

    // 2. Sync Custom Hero Banner & Custom Banner Images / Synopsis
    try {
        const savedHero = JSON.parse(localStorage.getItem('evonime_custom_hero') || 'null');
        const savedHeroDetails = JSON.parse(localStorage.getItem('evonime_custom_hero_details') || 'null');

        const slides = document.querySelectorAll('.hero-slide');
        if (slides.length > 0) {
            let activeCount = 0;
            slides.forEach(slide => {
                let slug = slide.getAttribute('data-slug');
                if (!slug) {
                    const link = slide.querySelector('a[href*="/watch/"]');
                    const href = link ? link.getAttribute('href') : '';
                    const parts = href.split('/');
                    slug = parts[2] || '';
                }

                if (savedHero) {
                    if (slug && !savedHero.hasOwnProperty(slug)) {
                        slide.classList.add('hidden');
                    } else if (slug) {
                        slide.classList.remove('hidden');
                        activeCount++;
                    }
                } else {
                    activeCount++;
                }

                if (slug && savedHeroDetails && savedHeroDetails[slug]) {
                    const detail = savedHeroDetails[slug];
                    const img = slide.querySelector('.hero-banner-img') || slide.querySelector('img');
                    if (img && detail.banner) {
                        img.src = detail.banner;
                    }
                    const synP = slide.querySelector('.hero-synopsis') || slide.querySelector('p');
                    if (synP && detail.synopsis) {
                        synP.textContent = detail.synopsis;
                    }
                    if (detail.media_type) {
                        slide.setAttribute('data-media-type', detail.media_type);
                    }
                    if (detail.trailer) {
                        slide.setAttribute('data-trailer', detail.trailer);
                    }
                }
            });

            // Fallback: If ALL slides were hidden by savedHero state, restore default slides so hero is NEVER empty!
            if (activeCount === 0) {
                slides.forEach(slide => slide.classList.remove('hidden'));
            }
        }
    } catch (e) {
        console.warn('Error reading custom hero state', e);
    }
}

/* ==========================================
   11. PROFILE MENU & MODAL ENGINE
   ========================================== */
function initProfileMenu() {
    // Desktop Dropdown
    const dropdownBtn = document.getElementById('profile-dropdown-btn');
    const dropdownMenu = document.getElementById('profile-dropdown-menu');
    const dropdownChevron = document.getElementById('profile-chevron');
    const dropdownContainer = document.getElementById('profile-dropdown-container');

    // Mobile Modal
    const mobileTrigger = document.getElementById('mobile-profile-trigger');
    const profileModal = document.getElementById('profile-modal');
    const profileModalBackdrop = document.getElementById('profile-modal-backdrop');
    const profileModalContent = document.getElementById('profile-modal-content');
    const profileModalCloseBtn = document.getElementById('profile-modal-close-btn');

    // 1. Desktop Profile Dropdown Toggle
    if (dropdownBtn && dropdownMenu) {
        const toggleDropdown = (forceState) => {
            const isCurrentlyOpen = !dropdownMenu.classList.contains('hidden');
            const shouldOpen = typeof forceState === 'boolean' ? forceState : !isCurrentlyOpen;

            if (shouldOpen) {
                dropdownMenu.classList.remove('hidden');
                dropdownBtn.setAttribute('aria-expanded', 'true');
                if (dropdownChevron) dropdownChevron.classList.add('rotate-180');
            } else {
                dropdownMenu.classList.add('hidden');
                dropdownBtn.setAttribute('aria-expanded', 'false');
                if (dropdownChevron) dropdownChevron.classList.remove('rotate-180');
            }
        };

        dropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleDropdown();
        });

        // Close on click outside
        document.addEventListener('click', (e) => {
            if (dropdownContainer && !dropdownContainer.contains(e.target)) {
                toggleDropdown(false);
            }
        });
    }

    // 2. Mobile Profile Modal Handler
    if (profileModal) {
        const openModal = () => {
            profileModal.classList.remove('hidden');
            profileModal.offsetHeight; // trigger reflow
            profileModal.classList.remove('opacity-0');
            profileModal.classList.add('opacity-100');
            if (profileModalContent) {
                profileModalContent.classList.remove('translate-y-full', 'md:scale-95');
                profileModalContent.classList.add('translate-y-0', 'md:scale-100');
            }
            document.body.classList.add('overflow-hidden');
        };

        const closeModal = () => {
            profileModal.classList.remove('opacity-100');
            profileModal.classList.add('opacity-0');
            if (profileModalContent) {
                profileModalContent.classList.remove('translate-y-0', 'md:scale-100');
                profileModalContent.classList.add('translate-y-full', 'md:scale-95');
            }
            setTimeout(() => {
                profileModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 300);
        };

        if (mobileTrigger) {
            mobileTrigger.addEventListener('click', (e) => {
                e.preventDefault();
                openModal();
            });
        }

        if (profileModalCloseBtn) {
            profileModalCloseBtn.addEventListener('click', closeModal);
        }

        if (profileModalBackdrop) {
            profileModalBackdrop.addEventListener('click', closeModal);
        }

        window.openProfileModal = openModal;
        window.closeProfileModal = closeModal;
    }

    // 3. Close with Escape Key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (dropdownMenu && !dropdownMenu.classList.contains('hidden')) {
                dropdownMenu.classList.add('hidden');
                if (dropdownBtn) dropdownBtn.setAttribute('aria-expanded', 'false');
                if (dropdownChevron) dropdownChevron.classList.remove('rotate-180');
            }
            if (profileModal && !profileModal.classList.contains('hidden')) {
                if (window.closeProfileModal) window.closeProfileModal();
            }
        }
    });
}


