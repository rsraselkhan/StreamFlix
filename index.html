<?php
require_once 'config.php';
require_once 'api/tmdb.php';

$tmdb = new TMDBAPI();

// Get content type from URL
$type = isset($_GET['type']) ? $_GET['type'] : 'all';

// Fetch homepage sections based on type
if ($type === 'movie') {
    $trending = $tmdb->getTrending('movie', 'day');
    $popular = $tmdb->getPopularMovies();
    $topRated = $tmdb->getTopRatedMovies();
    $nowPlaying = $tmdb->getNowPlayingMovies();
    $upcoming = $tmdb->getUpcomingMovies();
    $pageTitle = 'Movies';
} elseif ($type === 'tv') {
    $trending = $tmdb->getTrending('tv', 'day');
    $popular = $tmdb->getPopularTV();
    $topRated = $tmdb->getTopRatedTV();
    $airingToday = $tmdb->getAiringTodayTV();
    $pageTitle = 'TV Shows';
} elseif ($type === 'anime') {
    // For anime, we'll use animation genre (id: 16)
    $trending = $tmdb->getTrending('tv', 'day');
    $popular = $tmdb->discoverTV(16, 1);
    $topRated = $tmdb->discoverTV(16, 1, 'vote_average.desc');
    $pageTitle = 'Animes';
} else {
    $trending = $tmdb->getTrending('all', 'day');
    $popularMovies = $tmdb->getPopularMovies();
    $popularTV = $tmdb->getPopularTV();
    $topRatedMovies = $tmdb->getTopRatedMovies();
    $topRatedTV = $tmdb->getTopRatedTV();
    $pageTitle = 'Home';
}

// SEO Meta Tags
$site_description = 'Watch free movies, TV shows, and animes online. Stream the latest releases, popular series, and classic favorites in HD quality.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    
    <!-- Primary Meta Tags -->
    <title><?php echo SITE_NAME; ?> - Watch Free Movies, TV Shows & Animes Online</title>
    <meta name="title" content="<?php echo SITE_NAME; ?> - Watch Free Movies, TV Shows & Animes Online">
    <meta name="description" content="<?php echo $site_description; ?>">
    <meta name="keywords" content="movies, tv shows, animes, streaming, watch online, free movies, hd streaming">
    <meta name="author" content="<?php echo SITE_NAME; ?>">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    <meta property="og:title" content="<?php echo SITE_NAME; ?> - Watch Free Movies, TV Shows & Animes Online">
    <meta property="og:description" content="<?php echo $site_description; ?>">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/og-image.jpg">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo SITE_URL; ?>">
    <meta property="twitter:title" content="<?php echo SITE_NAME; ?> - Watch Free Movies, TV Shows & Animes Online">
    <meta property="twitter:description" content="<?php echo $site_description; ?>">
    <meta property="twitter:image" content="<?php echo SITE_URL; ?>/twitter-image.jpg">
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="msapplication-TileColor" content="#e50914">
    <meta name="theme-color" content="#e50914">
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Desktop Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php" class="logo">
                    <?php echo SITE_NAME; ?>
                </a>
                <ul class="nav-links">
                    <li><a href="index.php" class="<?php echo !isset($_GET['type']) ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="index.php?type=movie" class="<?php echo isset($_GET['type']) && $_GET['type'] == 'movie' ? 'active' : ''; ?>">Movies</a></li>
                    <li><a href="index.php?type=tv" class="<?php echo isset($_GET['type']) && $_GET['type'] == 'tv' ? 'active' : ''; ?>">TV Shows</a></li>
                    <li><a href="index.php?type=anime" class="<?php echo isset($_GET['type']) && $_GET['type'] == 'anime' ? 'active' : ''; ?>">Animes</a></li>
                </ul>
            </div>
            
            <div class="nav-center">
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Search movies, TV shows..." id="searchInput">
                    <button class="search-btn"><i class="fas fa-search"></i></button>
                    <div class="search-suggestions" id="searchSuggestions"></div>
                </div>
            </div>
            
            <div class="nav-right"></div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Hero Slider - Only on home page -->
        <?php if (!isset($_GET['type']) && isset($trending['results']) && !empty($trending['results'])): ?>
        <section class="hero-slider">
            <div class="slider-container" id="heroSlider">
                <?php foreach (array_slice($trending['results'], 0, 5) as $index => $item): ?>
                    <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                         style="background-image: url('<?php echo $tmdb->getBackdropUrl($item['backdrop_path'] ?? '', 'original'); ?>')">
                        <div class="slide-content">
                            <h1 class="slide-title"><?php echo $item['title'] ?? $item['name'] ?? ''; ?></h1>
                            <div class="slide-meta">
                                <span class="rating"><i class="fas fa-star"></i> <?php echo round($item['vote_average'] ?? 0, 1); ?></span>
                                <span class="year"><?php echo date('Y', strtotime($item['release_date'] ?? $item['first_air_date'] ?? '')); ?></span>
                                <span class="type"><?php echo isset($item['title']) ? 'Movie' : 'TV'; ?></span>
                            </div>
                            <p class="slide-overview"><?php echo substr($item['overview'] ?? '', 0, 150) . '...'; ?></p>
                            <div class="slide-buttons">
                                <a href="<?php echo isset($item['title']) ? 'movie.php?id=' . $item['id'] : 'tv.php?id=' . $item['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-info-circle"></i> Details
                                </a>
                                <a href="watch.php?type=<?php echo isset($item['title']) ? 'movie' : 'tv'; ?>&id=<?php echo $item['id']; ?>" class="btn btn-secondary">
                                    <i class="fas fa-play"></i> Watch
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="slider-controls">
                <button class="slider-prev"><i class="fas fa-chevron-left"></i></button>
                <button class="slider-next"><i class="fas fa-chevron-right"></i></button>
                <div class="slider-dots">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></span>
                    <?php endfor; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Movies Section -->
        <?php if ($type === 'movie' || $type === 'all'): ?>
            <!-- Popular Movies -->
            <?php if (isset($popularMovies['results']) || isset($popular['results'])): ?>
            <section class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Popular <?php echo $type === 'movie' ? 'Movies' : ''; ?></h2>
                    <a href="#" class="view-all">View All <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="content-grid" id="popularMoviesGrid">
                    <?php 
                    $items = $type === 'movie' ? $popular['results'] : $popularMovies['results'];
                    foreach (array_slice($items, 0, 10) as $item): 
                    ?>
                        <div class="movie-card" data-id="<?php echo $item['id']; ?>" data-type="movie">
                            <div class="card-poster">
                                <img src="<?php echo $tmdb->getImageUrl($item['poster_path'] ?? ''); ?>" 
                                     alt="<?php echo htmlspecialchars($item['title'] ?? $item['name']); ?>"
                                     loading="lazy">
                                <div class="card-overlay">
                                    <div class="card-actions">
                                        <a href="watch.php?type=movie&id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-play"></i></a>
                                        <a href="movie.php?id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-info"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-info">
                                <h3 class="card-title"><?php echo htmlspecialchars($item['title'] ?? $item['name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-rating"><i class="fas fa-star"></i> <?php echo round($item['vote_average'] ?? 0, 1); ?></span>
                                    <span class="card-year"><?php echo date('Y', strtotime($item['release_date'] ?? $item['first_air_date'] ?? '')); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Top Rated Movies -->
            <?php if (isset($topRatedMovies['results']) || isset($topRated['results'])): ?>
            <section class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Top Rated <?php echo $type === 'movie' ? 'Movies' : ''; ?></h2>
                    <a href="#" class="view-all">View All <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="content-grid" id="topRatedMoviesGrid">
                    <?php 
                    $items = $type === 'movie' ? $topRated['results'] : $topRatedMovies['results'];
                    foreach (array_slice($items, 0, 10) as $item): 
                    ?>
                        <div class="movie-card" data-id="<?php echo $item['id']; ?>" data-type="movie">
                            <div class="card-poster">
                                <img src="<?php echo $tmdb->getImageUrl($item['poster_path'] ?? ''); ?>" 
                                     alt="<?php echo htmlspecialchars($item['title'] ?? $item['name']); ?>"
                                     loading="lazy">
                                <div class="card-overlay">
                                    <div class="card-actions">
                                        <a href="watch.php?type=movie&id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-play"></i></a>
                                        <a href="movie.php?id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-info"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-info">
                                <h3 class="card-title"><?php echo htmlspecialchars($item['title'] ?? $item['name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-rating"><i class="fas fa-star"></i> <?php echo round($item['vote_average'] ?? 0, 1); ?></span>
                                    <span class="card-year"><?php echo date('Y', strtotime($item['release_date'] ?? $item['first_air_date'] ?? '')); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Now Playing / Upcoming for Movies -->
            <?php if ($type === 'movie'): ?>
                <?php if (isset($nowPlaying['results'])): ?>
                <section class="content-section">
                    <div class="section-header">
                        <h2 class="section-title">Now Playing</h2>
                        <a href="#" class="view-all">View All <i class="fas fa-chevron-right"></i></a>
                    </div>
                    <div class="content-grid" id="nowPlayingGrid">
                        <?php foreach (array_slice($nowPlaying['results'], 0, 10) as $item): ?>
                            <div class="movie-card" data-id="<?php echo $item['id']; ?>" data-type="movie">
                                <div class="card-poster">
                                    <img src="<?php echo $tmdb->getImageUrl($item['poster_path'] ?? ''); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                         loading="lazy">
                                    <div class="card-overlay">
                                        <div class="card-actions">
                                            <a href="watch.php?type=movie&id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-play"></i></a>
                                            <a href="movie.php?id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-info"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h3 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <div class="card-meta">
                                        <span class="card-rating"><i class="fas fa-star"></i> <?php echo round($item['vote_average'], 1); ?></span>
                                        <span class="card-year"><?php echo date('Y', strtotime($item['release_date'])); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php if (isset($upcoming['results'])): ?>
                <section class="content-section">
                    <div class="section-header">
                        <h2 class="section-title">Coming Soon</h2>
                        <a href="#" class="view-all">View All <i class="fas fa-chevron-right"></i></a>
                    </div>
                    <div class="content-grid" id="upcomingGrid">
                        <?php foreach (array_slice($upcoming['results'], 0, 10) as $item): ?>
                            <div class="movie-card" data-id="<?php echo $item['id']; ?>" data-type="movie">
                                <div class="card-poster">
                                    <img src="<?php echo $tmdb->getImageUrl($item['poster_path'] ?? ''); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                         loading="lazy">
                                    <div class="card-overlay">
                                        <div class="card-actions">
                                            <a href="movie.php?id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-info"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h3 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <div class="card-meta">
                                        <span class="card-year"><?php echo date('Y', strtotime($item['release_date'])); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

        <!-- TV Shows Section -->
        <?php if ($type === 'tv' || $type === 'all'): ?>
            <!-- Popular TV Shows -->
            <?php if (isset($popularTV['results']) || isset($popular['results'])): ?>
            <section class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Popular <?php echo $type === 'tv' ? 'TV Shows' : ''; ?></h2>
                    <a href="#" class="view-all">View All <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="content-grid" id="popularTVGrid">
                    <?php 
                    $items = $type === 'tv' ? $popular['results'] : $popularTV['results'];
                    foreach (array_slice($items, 0, 10) as $item): 
                    ?>
                        <div class="movie-card" data-id="<?php echo $item['id']; ?>" data-type="tv">
                            <div class="card-poster">
                                <img src="<?php echo $tmdb->getImageUrl($item['poster_path'] ?? ''); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     loading="lazy">
                                <div class="card-overlay">
                                    <div class="card-actions">
                                        <a href="watch.php?type=tv&id=<?php echo $item['id']; ?>&season=1&episode=1" class="card-btn"><i class="fas fa-play"></i></a>
                                        <a href="tv.php?id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-info"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-info">
                                <h3 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-rating"><i class="fas fa-star"></i> <?php echo round($item['vote_average'] ?? 0, 1); ?></span>
                                    <span class="card-year"><?php echo date('Y', strtotime($item['first_air_date'] ?? '')); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Top Rated TV Shows -->
            <?php if (isset($topRatedTV['results']) || isset($topRated['results'])): ?>
            <section class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Top Rated <?php echo $type === 'tv' ? 'TV Shows' : ''; ?></h2>
                    <a href="#" class="view-all">View All <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="content-grid" id="topRatedTVGrid">
                    <?php 
                    $items = $type === 'tv' ? $topRated['results'] : $topRatedTV['results'];
                    foreach (array_slice($items, 0, 10) as $item): 
                    ?>
                        <div class="movie-card" data-id="<?php echo $item['id']; ?>" data-type="tv">
                            <div class="card-poster">
                                <img src="<?php echo $tmdb->getImageUrl($item['poster_path'] ?? ''); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     loading="lazy">
                                <div class="card-overlay">
                                    <div class="card-actions">
                                        <a href="watch.php?type=tv&id=<?php echo $item['id']; ?>&season=1&episode=1" class="card-btn"><i class="fas fa-play"></i></a>
                                        <a href="tv.php?id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-info"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-info">
                                <h3 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-rating"><i class="fas fa-star"></i> <?php echo round($item['vote_average'] ?? 0, 1); ?></span>
                                    <span class="card-year"><?php echo date('Y', strtotime($item['first_air_date'] ?? '')); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Airing Today for TV Shows -->
            <?php if ($type === 'tv' && isset($airingToday['results'])): ?>
            <section class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Airing Today</h2>
                    <a href="#" class="view-all">View All <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="content-grid" id="airingTodayGrid">
                    <?php foreach (array_slice($airingToday['results'], 0, 10) as $item): ?>
                        <div class="movie-card" data-id="<?php echo $item['id']; ?>" data-type="tv">
                            <div class="card-poster">
                                <img src="<?php echo $tmdb->getImageUrl($item['poster_path'] ?? ''); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     loading="lazy">
                                <div class="card-overlay">
                                    <div class="card-actions">
                                        <a href="watch.php?type=tv&id=<?php echo $item['id']; ?>&season=1&episode=1" class="card-btn"><i class="fas fa-play"></i></a>
                                        <a href="tv.php?id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-info"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-info">
                                <h3 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-rating"><i class="fas fa-star"></i> <?php echo round($item['vote_average'], 1); ?></span>
                                    <span class="card-year"><?php echo date('Y', strtotime($item['first_air_date'])); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Anime Section -->
        <?php if ($type === 'anime'): ?>
            <!-- Popular Anime -->
            <?php if (isset($popular['results'])): ?>
            <section class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Popular Anime</h2>
                    <a href="#" class="view-all">View All <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="content-grid" id="popularAnimeGrid">
                    <?php foreach (array_slice($popular['results'], 0, 10) as $item): ?>
                        <div class="movie-card" data-id="<?php echo $item['id']; ?>" data-type="tv">
                            <div class="card-poster">
                                <img src="<?php echo $tmdb->getImageUrl($item['poster_path'] ?? ''); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     loading="lazy">
                                <div class="card-overlay">
                                    <div class="card-actions">
                                        <a href="watch.php?type=tv&id=<?php echo $item['id']; ?>&season=1&episode=1" class="card-btn"><i class="fas fa-play"></i></a>
                                        <a href="tv.php?id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-info"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-info">
                                <h3 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-rating"><i class="fas fa-star"></i> <?php echo round($item['vote_average'] ?? 0, 1); ?></span>
                                    <span class="card-year"><?php echo date('Y', strtotime($item['first_air_date'] ?? '')); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Top Rated Anime -->
            <?php if (isset($topRated['results'])): ?>
            <section class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Top Rated Anime</h2>
                    <a href="#" class="view-all">View All <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="content-grid" id="topRatedAnimeGrid">
                    <?php foreach (array_slice($topRated['results'], 0, 10) as $item): ?>
                        <div class="movie-card" data-id="<?php echo $item['id']; ?>" data-type="tv">
                            <div class="card-poster">
                                <img src="<?php echo $tmdb->getImageUrl($item['poster_path'] ?? ''); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     loading="lazy">
                                <div class="card-overlay">
                                    <div class="card-actions">
                                        <a href="watch.php?type=tv&id=<?php echo $item['id']; ?>&season=1&episode=1" class="card-btn"><i class="fas fa-play"></i></a>
                                        <a href="tv.php?id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-info"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-info">
                                <h3 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-rating"><i class="fas fa-star"></i> <?php echo round($item['vote_average'] ?? 0, 1); ?></span>
                                    <span class="card-year"><?php echo date('Y', strtotime($item['first_air_date'] ?? '')); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo"><?php echo SITE_NAME; ?></div>
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php?type=movie">Movies</a></li>
                <li><a href="index.php?type=tv">TV Shows</a></li>
                <li><a href="index.php?type=anime">Animes</a></li>
                <li><a href="#">Terms</a></li>
                <li><a href="#">Privacy</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
            <p class="footer-copyright">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation -->
    <div class="mobile-bottom-nav">
        <a href="index.php" class="mobile-nav-item <?php echo !isset($_GET['type']) ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="index.php?type=movie" class="mobile-nav-item <?php echo isset($_GET['type']) && $_GET['type'] == 'movie' ? 'active' : ''; ?>">
            <i class="fas fa-film"></i>
            <span>Movies</span>
        </a>
        <a href="index.php?type=tv" class="mobile-nav-item <?php echo isset($_GET['type']) && $_GET['type'] == 'tv' ? 'active' : ''; ?>">
            <i class="fas fa-tv"></i>
            <span>TV Shows</span>
        </a>
        <a href="index.php?type=anime" class="mobile-nav-item <?php echo isset($_GET['type']) && $_GET['type'] == 'anime' ? 'active' : ''; ?>">
            <i class="fas fa-dragon"></i>
            <span>Anime</span>
        </a>
    </div>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner"></div>
    </div>

    <!-- Back to Top Button -->
    <button class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="script.js"></script>
</body>
</html>
