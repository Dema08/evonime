// EVONIME Client-Side Interactive JavaScript Engine

document.addEventListener('DOMContentLoaded', () => {
    initNavbarScroll();
    initHeroCarousel();
    initSearchModal();
    initWatchlistBadge();
    initScheduleTabs();
});

/* ==========================================
   1. NAVBAR SCROLL EFFECT
   ========================================== */
function initNavbarScroll() {
    const navbar = document.getElementById('main-navbar');
    if (!navbar) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 30) {
            navbar.classList.add('bg-[#070707]/95', 'shadow-xl', 'border-zinc-800/80');
            navbar.classList.remove('bg-[#070707]/40', 'border-white/5');
        } else {
            navbar.classList.remove('bg-[#070707]/95', 'shadow-xl', 'border-zinc-800/80');
            navbar.classList.add('bg-[#070707]/40', 'border-white/5');
        }
    });
}

/* ==========================================
   2. HERO CAROUSEL ENGINE (6s Auto Slide)
   ========================================== */
let heroIndex = 0;
let heroInterval = null;

function initHeroCarousel() {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    const prevBtn = document.getElementById('hero-prev');
    const nextBtn = document.getElementById('hero-next');

    if (!slides.length) return;

    function goToSlide(index) {
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                slide.classList.add('opacity-100', 'z-10');
            } else {
                slide.classList.remove('opacity-100', 'z-10');
                slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
            }
        });

        dots.forEach((dot, i) => {
            if (i === index) {
                dot.className = 'hero-dot h-2 rounded-full transition-all duration-300 w-8 bg-violet-500 shadow-[0_0_10px_rgba(124,58,237,0.8)]';
            } else {
                dot.className = 'hero-dot h-2 rounded-full transition-all duration-300 w-2 bg-zinc-600 hover:bg-zinc-400';
            }
        });

        heroIndex = index;
    }

    function nextSlide() {
        let next = (heroIndex + 1) % slides.length;
        goToSlide(next);
    }

    function prevSlide() {
        let prev = (heroIndex - 1 + slides.length) % slides.length;
        goToSlide(prev);
    }

    if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); resetTimer(); });
    if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); resetTimer(); });

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => { goToSlide(i); resetTimer(); });
    });

    function resetTimer() {
        clearInterval(heroInterval);
        heroInterval = setInterval(nextSlide, 6000);
    }

    resetTimer();
}

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
            <a href="/anime/${item.slug}" class="flex items-center gap-3 p-2 rounded-xl bg-[#151515] hover:bg-violet-600/20 border border-zinc-800/80 hover:border-violet-500/50 transition-all group">
                <img src="${item.poster}" alt="${item.title}" class="w-10 h-14 object-cover rounded-lg flex-shrink-0">
                <div class="flex-grow min-w-0">
                    <h4 class="text-sm font-bold text-white group-hover:text-violet-400 truncate">${item.title}</h4>
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
                btn.className = 'schedule-tab-btn px-5 py-2 text-xs md:text-sm font-bold rounded-xl transition-all duration-200 bg-violet-600 text-white shadow-[0_0_15px_rgba(124,58,237,0.5)]';
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
            btn.className = 'explore-tab-btn px-4 py-2 text-xs font-bold rounded-xl transition-all bg-violet-600 text-white shadow-[0_0_15px_rgba(124,58,237,0.5)]';
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
        if (btn) btn.classList.remove('bg-violet-600', 'text-white');
    } else {
        list.push(slug);
        window.showToast('Added to your Watchlist!');
        if (btn) btn.classList.add('bg-violet-600', 'text-white');
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
    if (btn) btn.className = 'server-btn px-3 py-1.5 bg-violet-600 text-white text-xs font-bold rounded-lg transition-all border border-violet-500 shadow-md';
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
    toast.className = 'px-4 py-3 bg-[#151515] border border-violet-500/60 text-white text-xs font-bold rounded-xl shadow-2xl backdrop-blur-md flex items-center gap-2 transform translate-y-4 opacity-0 transition-all duration-300 pointer-events-auto';
    toast.innerHTML = `
        <span class="w-2 h-2 rounded-full bg-violet-400"></span>
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
