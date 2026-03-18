<?php
require_once 'config.php';
require_once 'api/tmdb.php';

$tmdb = new TMDBAPI();

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$season = isset($_GET['season']) ? intval($_GET['season']) : 1;
$episode = isset($_GET['episode']) ? intval($_GET['episode']) : 1;

if (!$id || !in_array($type, ['movie', 'tv'])) {
    header('Location: index.php');
    exit;
}

// Fetch content details
if ($type === 'movie') {
    $content = $tmdb->getMovieDetails($id);
    $title = $content['title'] ?? '';
    $year = date('Y', strtotime($content['release_date'] ?? ''));
    $overview = $content['overview'] ?? '';
    $runtime = $content['runtime'] ?? 0;
    $rating = $content['vote_average'] ?? 0;
    $backdrop = $content['backdrop_path'] ?? '';
    $poster = $content['poster_path'] ?? '';
    $genres = $content['genres'] ?? [];
    
    // Get recommendations
    $recommendations = $content['recommendations']['results'] ?? [];
} else {
    $content = $tmdb->getTVDetails($id);
    $episode_details = $tmdb->getEpisodeDetails($id, $season, $episode);
    $season_details = $tmdb->getSeasonDetails($id, $season);
    
    $title = $content['name'] ?? '';
    $year = date('Y', strtotime($content['first_air_date'] ?? ''));
    $episode_title = $episode_details['name'] ?? "Episode {$episode}";
    $overview = $episode_details['overview'] ?? $content['overview'] ?? '';
    $rating = $episode_details['vote_average'] ?? $content['vote_average'] ?? 0;
    $backdrop = $episode_details['still_path'] ?? $content['backdrop_path'] ?? '';
    $poster = $content['poster_path'] ?? '';
    $genres = $content['genres'] ?? [];
    
    // Get recommendations
    $recommendations = $content['recommendations']['results'] ?? [];
}

// Build player URL with all features
$player_url = "https://player.videasy.net/";
if ($type === 'movie') {
    $player_url .= "movie/{$id}";
    // Add player parameters for movies
    $player_url .= "?overlay=true&color=e50914";
    // Add progress parameter if available (you can implement progress tracking)
    // $player_url .= "&progress=120";
} else {
    $player_url .= "tv/{$id}/{$season}/{$episode}";
    // Add all player parameters for TV shows
    $player_url .= "?nextEpisode=true&autoplayNextEpisode=true&episodeSelector=true&overlay=true&color=e50914";
}

// Default image for missing backdrops
$default_backdrop = 'https://via.placeholder.com/1280x720/1a1a1a/e50914?text=No+Backdrop+Available';

// SEO Meta Tags
$page_title = $type === 'movie' 
    ? "Watch {$title} ({$year}) - " . SITE_NAME
    : "Watch {$title} S{$season}E{$episode} - {$episode_title} - " . SITE_NAME;
$page_description = substr($overview, 0, 160);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Primary Meta Tags -->
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    
    <!-- Open Graph -->
    <meta property="og:type" content="video.other">
    <meta property="og:url" content="<?php echo SITE_URL; ?>/watch.php?type=<?php echo $type; ?>&id=<?php echo $id; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:image" content="<?php echo $tmdb->getImageUrl($backdrop ?: $poster, 'original'); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Watch Page Specific Styles - Matching Season.php Design */
        .watch-page {
            background: var(--background-dark);
            min-height: 100vh;
        }
        
        .watch-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 20px 40px;
        }
        
        /* Breadcrumb Navigation */
        .watch-breadcrumb {
            margin-bottom: 24px;
        }
        
        .watch-breadcrumb h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .back-link {
            color: var(--text-gray);
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease;
        }
        
        .back-link:hover {
            color: var(--primary-color);
        }
        
        .back-link i {
            font-size: 12px;
        }
        
        /* Player Card - Matching Season Info Card Style */
        .player-card {
            background: var(--background-light);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        .player-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .player-title i {
            color: var(--primary-color);
        }
        
        .player-features {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
            padding: 10px;
            background: rgba(0,0,0,0.3);
            border-radius: 30px;
        }
        
        .feature-badge {
            background: rgba(229,9,20,0.2);
            color: var(--text-light);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 4px;
            border: 1px solid rgba(229,9,20,0.3);
        }
        
        .feature-badge i {
            color: var(--primary-color);
            font-size: 10px;
        }
        
        /* Video Player Container - Fixed Size */
        .video-player-wrapper {
            width: 100%;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        .video-player-fixed {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            background: #000;
        }
        
        .video-player-fixed iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        
        /* Episode Navigation - Matching Season Actions Style */
        .episode-navigation {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .nav-btn {
            padding: 10px 20px;
            background: rgba(255,255,255,0.1);
            color: var(--text-light);
            text-decoration: none;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.2s ease;
        }
        
        .nav-btn:hover {
            border-color: var(--primary-color);
            background: rgba(229,9,20,0.1);
        }
        
        .nav-btn i {
            font-size: 12px;
        }
        
        /* Content Info Card - Matching Season Info Card Style */
        .content-info-card {
            background: var(--background-light);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .content-title {
            font-size: 24px;
            font-weight: 600;
        }
        
        .content-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            background: rgba(255,215,0,0.1);
            padding: 5px 15px;
            border-radius: 20px;
            color: #ffd700;
            font-size: 14px;
        }
        
        .content-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 16px;
            color: var(--text-gray);
            font-size: 14px;
            flex-wrap: wrap;
        }
        
        .content-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .content-genres {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }
        
        .genre-tag {
            padding: 4px 12px;
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            color: var(--text-light);
            text-decoration: none;
            font-size: 13px;
            transition: background 0.2s ease;
        }
        
        .genre-tag:hover {
            background: var(--primary-color);
        }
        
        .content-description {
            font-size: 15px;
            line-height: 1.7;
            color: var(--text-gray);
            margin-bottom: 20px;
        }
        
        .content-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
        }
        
        .btn-outline:hover {
            border-color: var(--primary-color);
            background: rgba(229,9,20,0.1);
        }
        
        /* Episode Info - For TV Shows */
        .episode-info-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .episode-info-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .episode-info-header h3 {
            font-size: 18px;
            font-weight: 600;
        }
        
        .episode-selector {
            display: flex;
            gap: 10px;
        }
        
        .episode-selector select {
            background: var(--background-dark);
            color: var(--text-light);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            outline: none;
        }
        
        .episode-selector select:hover {
            border-color: var(--primary-color);
        }
        
        /* Progress Tracking Info */
        .progress-info {
            margin-top: 15px;
            padding: 10px;
            background: rgba(0,0,0,0.3);
            border-radius: 8px;
            font-size: 12px;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        .progress-info i {
            color: var(--primary-color);
        }
        
        /* Recommendations Section - Matching Season.php */
        .recommendations-section {
            margin-top: 40px;
        }
        
        .recommendations-section h2 {
            font-size: 20px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .recommendations-section h2 i {
            color: var(--primary-color);
        }
        
        .recommendations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .recommendation-card {
            background: var(--background-light);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s ease;
            cursor: pointer;
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        .recommendation-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary-color);
        }
        
        .recommendation-card img {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
        }
        
        .recommendation-info {
            padding: 10px;
        }
        
        .recommendation-info h4 {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .rec-year {
            font-size: 12px;
            color: var(--text-gray);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .watch-container {
                padding: 70px 15px 30px;
            }
            
            .watch-breadcrumb h1 {
                font-size: 24px;
            }
            
            .player-card {
                padding: 16px;
            }
            
            .player-features {
                justify-content: center;
            }
            
            .content-info-card {
                padding: 20px;
            }
            
            .content-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .content-title {
                font-size: 20px;
            }
            
            .content-meta {
                gap: 12px;
                font-size: 13px;
            }
            
            .episode-navigation {
                flex-direction: column;
            }
            
            .nav-btn {
                width: 100%;
                justify-content: center;
            }
            
            .episode-info-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .episode-selector {
                width: 100%;
            }
            
            .episode-selector select {
                width: 100%;
            }
            
            .recommendations-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            
            .progress-info {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body class="watch-page">
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php" class="logo"><?php echo SITE_NAME; ?></a>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php?type=movie">Movies</a></li>
                    <li><a href="index.php?type=tv">TV Shows</a></li>
                    <li><a href="index.php?type=anime">Animes</a></li>
                </ul>
            </div>
            
            <div class="nav-center">
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Search..." id="searchInput">
                    <button class="search-btn"><i class="fas fa-search"></i></button>
                    <div class="search-suggestions" id="searchSuggestions"></div>
                </div>
            </div>
            
            <div class="nav-right"></div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="watch-container">
        <!-- Breadcrumb Navigation -->
        <div class="watch-breadcrumb">
            <h1>
                <?php if ($type === 'movie'): ?>
                    <?php echo htmlspecialchars($title); ?>
                <?php else: ?>
                    <?php echo htmlspecialchars($title); ?> - <?php echo htmlspecialchars($season_details['name'] ?? "Season {$season}"); ?>
                <?php endif; ?>
            </h1>
            <a href="<?php echo $type === 'movie' ? "movie.php?id={$id}" : "tv.php?id={$id}"; ?>" class="back-link">
                <i class="fas fa-arrow-left"></i> 
                <?php echo $type === 'movie' ? 'Back to Movie' : 'Back to Show'; ?>
            </a>
        </div>

        <!-- Player Card with All Features -->
        <div class="player-card">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 16px;">
                <h2 class="player-title">
                    <i class="fas fa-play-circle"></i>
                    Now Playing
                </h2>
                
                <!-- Player Features Badges -->
                <div class="player-features">
                    <?php if ($type === 'tv'): ?>
                        <span class="feature-badge"><i class="fas fa-step-forward"></i> Next Episode Button</span>
                        <span class="feature-badge"><i class="fas fa-forward"></i> Autoplay Next</span>
                        <span class="feature-badge"><i class="fas fa-list"></i> Episode Selector</span>
                    <?php endif; ?>
                    <span class="feature-badge"><i class="fas fa-layer-group"></i> Netflix Overlay</span>
                    <span class="feature-badge"><i class="fas fa-palette"></i> Custom Theme</span>
                </div>
            </div>
            
            <!-- Video Player with All Parameters -->
            <div class="video-player-wrapper">
                <div class="video-player-fixed">
                    <iframe src="<?php echo $player_url; ?>" 
                            allowfullscreen 
                            allow="autoplay; fullscreen; picture-in-picture"
                            loading="lazy">
                    </iframe>
                </div>
            </div>

            <!-- Player Features Description -->
            <div class="progress-info">
                <i class="fas fa-info-circle"></i>
                <span>
                    <strong>Player Features Enabled:</strong>
                    <?php if ($type === 'tv'): ?>
                        Next Episode Button • Autoplay Next Episode • Episode Selector • 
                    <?php endif; ?>
                    Netflix-style Overlay • Custom Red Theme • Progress Tracking Ready
                </span>
            </div>

            <!-- Episode Navigation for TV Shows (Fallback) -->
            <?php if ($type === 'tv'): ?>
                <div class="episode-navigation">
                    <?php if ($episode > 1): ?>
                        <a href="watch.php?type=tv&id=<?php echo $id; ?>&season=<?php echo $season; ?>&episode=<?php echo $episode - 1; ?>" class="nav-btn">
                            <i class="fas fa-chevron-left"></i> Previous Episode
                        </a>
                    <?php endif; ?>
                    
                    <a href="season.php?tv_id=<?php echo $id; ?>&season=<?php echo $season; ?>" class="nav-btn">
                        <i class="fas fa-list"></i> All Episodes
                    </a>
                    
                    <?php if ($episode < count($season_details['episodes'])): ?>
                        <a href="watch.php?type=tv&id=<?php echo $id; ?>&season=<?php echo $season; ?>&episode=<?php echo $episode + 1; ?>" class="nav-btn">
                            Next Episode <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Content Info Card -->
        <div class="content-info-card">
            <div class="content-header">
                <h2 class="content-title">
                    <?php if ($type === 'movie'): ?>
                        <?php echo htmlspecialchars($title); ?> (<?php echo $year; ?>)
                    <?php else: ?>
                        <?php echo htmlspecialchars($episode_title); ?>
                    <?php endif; ?>
                </h2>
                <span class="content-rating">
                    <i class="fas fa-star"></i> <?php echo round($rating, 1); ?>/10
                </span>
            </div>
            
            <div class="content-meta">
                <?php if ($type === 'movie'): ?>
                    <span><i class="fas fa-calendar-alt"></i> <?php echo $year; ?></span>
                    <?php if ($runtime): ?>
                        <span><i class="fas fa-clock"></i> <?php echo floor($runtime / 60); ?>h <?php echo $runtime % 60; ?>m</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span><i class="fas fa-tv"></i> <?php echo htmlspecialchars($title); ?></span>
                    <span><i class="fas fa-calendar-alt"></i> S<?php echo str_pad($season, 2, '0', STR_PAD_LEFT); ?>E<?php echo str_pad($episode, 2, '0', STR_PAD_LEFT); ?></span>
                    <?php if ($episode_details['air_date']): ?>
                        <span><i class="fas fa-calendar-day"></i> <?php echo date('M d, Y', strtotime($episode_details['air_date'])); ?></span>
                    <?php endif; ?>
                <?php endif; ?>
                <span><i class="fas fa-eye"></i> <?php echo number_format($content['vote_count'] ?? 0); ?> views</span>
            </div>
            
            <!-- Genres -->
            <?php if (!empty($genres)): ?>
                <div class="content-genres">
                    <?php foreach ($genres as $genre): ?>
                        <a href="genre.php?type=<?php echo $type; ?>&id=<?php echo $genre['id']; ?>" class="genre-tag">
                            <?php echo $genre['name']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <p class="content-description">
                <?php echo nl2br(htmlspecialchars($overview ?: 'No description available.')); ?>
            </p>
            
            <div class="content-actions">
                <button class="btn btn-outline" onclick="addToList(<?php echo $id; ?>, '<?php echo $type; ?>')">
                    <i class="fas fa-plus"></i> Add to My List
                </button>
                <button class="btn btn-outline" onclick="shareContent()">
                    <i class="fas fa-share-alt"></i> Share
                </button>
            </div>

            <!-- Episode Selector for TV Shows (Fallback) -->
            <?php if ($type === 'tv' && !empty($season_details['episodes'])): ?>
                <div class="episode-info-section">
                    <div class="episode-info-header">
                        <h3><i class="fas fa-list"></i> Episode Selector (Built into player)</h3>
                        <div class="episode-selector">
                            <select id="episodeSelect" onchange="changeEpisode()">
                                <?php foreach ($season_details['episodes'] as $ep): ?>
                                    <option value="<?php echo $ep['episode_number']; ?>" 
                                            <?php echo $ep['episode_number'] == $episode ? 'selected' : ''; ?>>
                                        E<?php echo str_pad($ep['episode_number'], 2, '0', STR_PAD_LEFT); ?> - <?php echo htmlspecialchars($ep['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Progress Tracking Script (for future implementation) -->
        <script>
        // Listen for progress updates from player
        window.addEventListener('message', function(event) {
            try {
                const data = JSON.parse(event.data);
                if (data.type === 'progress' || data.progress) {
                    console.log('Progress update:', data);
                    // You can save progress to localStorage here
                    // localStorage.setItem('progress_' + data.id, JSON.stringify(data));
                }
            } catch (e) {
                // Not JSON, ignore
            }
        });
        </script>

        <!-- Recommendations -->
        <?php if (!empty($recommendations)): ?>
            <section class="recommendations-section">
                <h2><i class="fas fa-thumbs-up"></i> You might also like</h2>
                <div class="recommendations-grid">
                    <?php foreach (array_slice($recommendations, 0, 6) as $rec): ?>
                        <div class="recommendation-card" onclick="window.location.href='<?php echo $type === 'movie' ? 'movie.php?id=' . $rec['id'] : 'tv.php?id=' . $rec['id']; ?>'">
                            <img src="<?php echo $tmdb->getImageUrl($rec['poster_path'] ?? '', 'w185'); ?>" 
                                 alt="<?php echo htmlspecialchars($rec['title'] ?? $rec['name']); ?>"
                                 loading="lazy">
                            <div class="recommendation-info">
                                <h4><?php echo htmlspecialchars($rec['title'] ?? $rec['name']); ?></h4>
                                <span class="rec-year"><?php echo date('Y', strtotime($rec['release_date'] ?? $rec['first_air_date'] ?? '')); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
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

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner"></div>
    </div>

    <!-- Back to Top Button -->
    <button class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="script.js"></script>
    <script>
    // Change Episode Function (Fallback)
    function changeEpisode() {
        const select = document.getElementById('episodeSelect');
        const episode = select.value;
        window.location.href = `watch.php?type=tv&id=<?php echo $id; ?>&season=<?php echo $season; ?>&episode=${episode}`;
    }
    
    // Add to List Function
    function addToList(id, type) {
        let myList = JSON.parse(localStorage.getItem('myList')) || [];
        
        const exists = myList.some(item => item.id === id && item.type === type);
        
        if (!exists) {
            myList.push({ id, type, date: new Date().toISOString() });
            localStorage.setItem('myList', JSON.stringify(myList));
            alert('✓ Added to My List');
            
            // Visual feedback
            const btn = event.target.closest('.btn');
            if (btn) {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Added';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            }
        } else {
            alert('This is already in your list');
        }
    }
    
    // Share Function
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
    
    // Save progress to localStorage when player sends updates
    window.addEventListener('message', function(event) {
        try {
            const data = JSON.parse(event.data);
            if (data.type === 'progress' || data.progress) {
                const key = 'progress_' + data.id;
                localStorage.setItem(key, JSON.stringify({
                    progress: data.progress,
                    timestamp: data.timestamp,
                    duration: data.duration,
                    date: new Date().toISOString()
                }));
                console.log('Progress saved:', data.progress + '%');
            }
        } catch (e) {
            // Not JSON or error, ignore
        }
    });
    </script>
</body>
</html>