// Global Variables
let currentSlide = 0;
let slideInterval;
let searchTimeout;
let currentPage = 1;
let loading = false;
let hasMore = true;
let currentType = '';

// Initialize on DOM Load
document.addEventListener('DOMContentLoaded', function() {
    initNavbar();
    initSlider();
    initSearch();
    initLazyLoading();
    initMovieCards();
    initBackToTop();
    initInfiniteScroll();
    
    // Get current type from URL
    const urlParams = new URLSearchParams(window.location.search);
    currentType = urlParams.get('type') || 'all';
});

// Navbar Scroll Effect
function initNavbar() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}

// Hero Slider
function initSlider() {
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    const dots = document.querySelectorAll('.dot');
    
    if (!slides.length) return;
    
    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        slides[index].classList.add('active');
        dots[index].classList.add('active');
        currentSlide = index;
    }
    
    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }
    
    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(currentSlide);
    }
    
    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            resetSliderInterval();
        });
        
        nextBtn.addEventListener('click', () => {
            nextSlide();
            resetSliderInterval();
        });
    }
    
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
            resetSliderInterval();
        });
    });
    
    startSliderInterval();
    
    const slider = document.querySelector('.hero-slider');
    if (slider) {
        slider.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
        });
        
        slider.addEventListener('mouseleave', () => {
            startSliderInterval();
        });
    }
}

function startSliderInterval() {
    slideInterval = setInterval(() => {
        const slides = document.querySelectorAll('.slide');
        if (slides.length) {
            currentSlide = (currentSlide + 1) % slides.length;
            document.querySelectorAll('.slide').forEach((slide, index) => {
                slide.classList.toggle('active', index === currentSlide);
            });
            document.querySelectorAll('.dot').forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }
    }, 5000);
}

function resetSliderInterval() {
    clearInterval(slideInterval);
    startSliderInterval();
}

// Search Functionality
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    
    if (!searchInput || !searchSuggestions) return;
    
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchSuggestions.classList.remove('active');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetchSearchSuggestions(query);
        }, 300);
    });
    
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
            searchSuggestions.classList.remove('active');
        }
    });
    
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query) {
                window.location.href = `search.php?q=${encodeURIComponent(query)}`;
            }
        }
    });
}

function fetchSearchSuggestions(query) {
    const searchSuggestions = document.getElementById('searchSuggestions');
    
    fetch(`api/search-suggestions.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.results && data.results.length > 0) {
                renderSuggestions(data.results);
                searchSuggestions.classList.add('active');
            } else {
                searchSuggestions.classList.remove('active');
            }
        })
        .catch(error => console.error('Search error:', error));
}

function renderSuggestions(results) {
    const suggestions = document.getElementById('searchSuggestions');
    const tmdbImageBase = 'https://image.tmdb.org/t/p/w92';
    
    let html = '';
    results.slice(0, 5).forEach(item => {
        const title = item.title || item.name;
        const year = item.release_date || item.first_air_date;
        const yearFormatted = year ? new Date(year).getFullYear() : '';
        const type = item.media_type === 'movie' ? 'Movie' : 'TV Show';
        const imagePath = item.poster_path || item.profile_path;
        const link = item.media_type === 'movie' ? `movie.php?id=${item.id}` : `tv.php?id=${item.id}`;
        
        html += `
            <div class="suggestion-item" onclick="window.location.href='${link}'">
                ${imagePath ? `<img src="${tmdbImageBase}${imagePath}" alt="${title}">` : '<div class="no-photo-small"></div>'}
                <div class="suggestion-info">
                    <h4>${title}</h4>
                    <p>${type} ${yearFormatted ? '• ' + yearFormatted : ''}</p>
                </div>
            </div>
        `;
    });
    
    html += `
        <div class="suggestion-item view-all" onclick="window.location.href='search.php?q=${encodeURIComponent(document.getElementById('searchInput').value)}'">
            <div class="suggestion-info">
                <h4>View all results <i class="fas fa-arrow-right"></i></h4>
            </div>
        </div>
    `;
    
    suggestions.innerHTML = html;
}

// Lazy Loading Images
function initLazyLoading() {
    const images = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                    }
                    imageObserver.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px'
        });
        
        images.forEach(img => {
            if (!img.dataset.src && img.src && !img.src.includes('placeholder')) {
                img.dataset.src = img.src;
                img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1%3E%3C/svg%3E';
            }
            imageObserver.observe(img);
        });
    } else {
        images.forEach(img => {
            if (img.dataset.src) {
                img.src = img.dataset.src;
            }
        });
    }
}

// Movie Cards Interactions
function initMovieCards() {
    const cards = document.querySelectorAll('.movie-card');
    
    cards.forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.closest('.card-btn')) return;
            
            const id = this.dataset.id;
            const type = this.dataset.type;
            
            if (id && type) {
                window.location.href = `${type === 'movie' ? 'movie.php' : 'tv.php'}?id=${id}`;
            }
        });
    });
}

// Infinite Scroll
function initInfiniteScroll() {
    window.addEventListener('scroll', function() {
        if (!hasMore || loading) return;
        
        const scrollY = window.scrollY;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        
        if (scrollY + windowHeight >= documentHeight - 1000) {
            loadMoreContent();
        }
    });
}

function loadMoreContent() {
    loading = true;
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) spinner.classList.add('active');
    
    currentPage++;
    
    fetch(`api/load-more.php?page=${currentPage}&type=${currentType}`)
        .then(response => response.json())
        .then(data => {
            if (data.results && data.results.length > 0) {
                appendContent(data.results);
                if (currentPage >= data.total_pages) {
                    hasMore = false;
                }
            } else {
                hasMore = false;
            }
        })
        .catch(error => {
            console.error('Error loading more content:', error);
            hasMore = false;
        })
        .finally(() => {
            loading = false;
            if (spinner) spinner.classList.remove('active');
        });
}

function appendContent(items) {
    const grids = document.querySelectorAll('.content-grid');
    if (!grids.length) return;
    
    const tmdbImageBase = 'https://image.tmdb.org/t/p/w500';
    
    grids.forEach(grid => {
        items.forEach(item => {
            const title = item.title || item.name;
            const year = item.release_date || item.first_air_date;
            const yearFormatted = year ? new Date(year).getFullYear() : 'TBA';
            const type = item.title ? 'movie' : 'tv';
            
            const card = document.createElement('div');
            card.className = 'movie-card';
            card.dataset.id = item.id;
            card.dataset.type = type;
            
            card.innerHTML = `
                <div class="card-poster">
                    <img src="${item.poster_path ? tmdbImageBase + item.poster_path : 'placeholder.jpg'}" 
                         alt="${title}"
                         loading="lazy">
                    <div class="card-overlay">
                        <div class="card-actions">
                            <a href="watch.php?type=${type}&id=${item.id}" class="card-btn"><i class="fas fa-play"></i></a>
                            <a href="${type === 'movie' ? 'movie.php' : 'tv.php'}?id=${item.id}" class="card-btn"><i class="fas fa-info"></i></a>
                        </div>
                    </div>
                </div>
                <div class="card-info">
                    <h3 class="card-title">${title}</h3>
                    <div class="card-meta">
                        <span class="card-rating"><i class="fas fa-star"></i> ${item.vote_average?.toFixed(1) || '0.0'}</span>
                        <span class="card-year">${yearFormatted}</span>
                    </div>
                </div>
            `;
            
            grid.appendChild(card);
        });
    });
    
    // Reinitialize lazy loading and card effects
    initLazyLoading();
    initMovieCards();
}

// Trailer Modal
function openTrailer(videoKey) {
    const modal = document.getElementById('trailerModal');
    const iframe = document.getElementById('trailerIframe');
    
    if (modal && iframe) {
        iframe.src = `https://www.youtube.com/embed/${videoKey}?autoplay=1&rel=0&modestbranding=1`;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeTrailer() {
    const modal = document.getElementById('trailerModal');
    const iframe = document.getElementById('trailerIframe');
    
    if (modal && iframe) {
        iframe.src = '';
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTrailer();
    }
});

// Add to My List
function addToList(id, type) {
    let myList = JSON.parse(localStorage.getItem('myList')) || [];
    
    const exists = myList.some(item => item.id === id && item.type === type);
    
    if (!exists) {
        myList.push({ id, type, date: new Date().toISOString() });
        localStorage.setItem('myList', JSON.stringify(myList));
        alert('Added to My List');
    } else {
        alert('Already in My List');
    }
}

// Share Content
function shareContent() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: window.location.href
        }).catch(console.error);
    } else {
        navigator.clipboard.writeText(window.location.href);
        alert('Link copied to clipboard!');
    }
}

// Episode Sorting
function sortEpisodes() {
    const sortBy = document.getElementById('sortEpisodes')?.value;
    const grid = document.getElementById('episodesGrid');
    if (!grid) return;
    
    const episodes = Array.from(grid.children);
    
    episodes.sort((a, b) => {
        if (sortBy === 'number') {
            return parseInt(a.dataset.episode) - parseInt(b.dataset.episode);
        } else if (sortBy === 'rating') {
            return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
        } else if (sortBy === 'date') {
            return parseInt(b.dataset.date) - parseInt(a.dataset.date);
        }
    });
    
    grid.innerHTML = '';
    episodes.forEach(episode => grid.appendChild(episode));
}

// Back to Top Button
function initBackToTop() {
    const backToTop = document.querySelector('.back-to-top');
    if (!backToTop) return;
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 500) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    });
}

// Image Fallbacks
document.addEventListener('error', function(e) {
    if (e.target.tagName === 'IMG' && !e.target.src.includes('placeholder')) {
        e.target.src = 'placeholder.jpg';
    }
}, true);

// Initialize on dynamic content
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.addedNodes.length) {
            initLazyLoading();
            initMovieCards();
        }
    });
});

observer.observe(document.body, {
    childList: true,
    subtree: true
});