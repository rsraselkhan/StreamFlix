<?php
require_once 'config.php';
require_once 'api/tmdb.php';

$tmdb = new TMDBAPI();
$tv_id = isset($_GET['tv_id']) ? intval($_GET['tv_id']) : 0;
$season_number = isset($_GET['season']) ? intval($_GET['season']) : 1;

if (!$tv_id) {
    header('Location: index.php');
    exit;
}

$season = $tmdb->getSeasonDetails($tv_id, $season_number);
$show = $tmdb->getTVDetails($tv_id);

if (!$season || !$show) {
    header('HTTP/1.0 404 Not Found');
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Season Not Found - <?php echo SITE_NAME; ?></title>
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body>
        <div class="error-page">
            <h1>404</h1>
            <p>Season not found</p>
            <a href="index.php" class="btn btn-primary">Go Home</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// SEO Meta Tags
$page_title = $show['name'] . ' - ' . $season['name'] . ' | ' . SITE_NAME;
$page_description = substr($season['overview'] ?? $show['overview'] ?? '', 0, 160);
$page_image = $tmdb->getImageUrl($season['poster_path'] ?? $show['poster_path'], 'original');

// Default image for episodes without still
$default_episode_image = 'https://via.placeholder.com/300x169/1a1a1a/e50914?text=No+Preview';
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
    <meta name="keywords" content="<?php echo htmlspecialchars($show['name']); ?>, season <?php echo $season_number; ?>, episodes, tv show, streaming">
    
    <!-- Open Graph -->
    <meta property="og:type" content="video.tv_show">
    <meta property="og:url" content="<?php echo SITE_URL; ?>/season.php?tv_id=<?php echo $tv_id; ?>&season=<?php echo $season_number; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($page_image); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Season Page Specific Styles */
        .seasons-page {
            padding: 80px 20px 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .seasons-header {
            margin-bottom: 24px;
        }
        
        .seasons-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .show-link {
            color: var(--text-gray);
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease;
        }
        
        .show-link:hover {
            color: var(--primary-color);
        }
        
        .season-info-card {
            background: var(--background-light);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 32px;
            display: flex;
            gap: 24px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        .season-info-poster {
            flex: 0 0 150px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .season-info-poster img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        
        .season-info-details {
            flex: 1;
        }
        
        .season-info-details h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        
        .season-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 16px;
            color: var(--text-gray);
            font-size: 14px;
            flex-wrap: wrap;
        }
        
        .season-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .season-overview {
            font-size: 14px;
            line-height: 1.7;
            color: var(--text-gray);
            margin-bottom: 20px;
        }
        
        .season-actions {
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
        
        .episodes-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .episodes-header h3 {
            font-size: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .episodes-filter {
            display: flex;
            gap: 10px;
        }
        
        .episodes-filter select {
            background: var(--background-light);
            color: var(--text-light);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            outline: none;
        }
        
        .episodes-filter select:hover {
            border-color: var(--primary-color);
        }
        
        .episodes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .episode-item {
            background: var(--background-light);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s ease;
            cursor: pointer;
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        .episode-item:hover {
            transform: translateY(-4px);
            border-color: var(--primary-color);
        }
        
        .episode-thumbnail {
            position: relative;
            aspect-ratio: 16/9;
            overflow: hidden;
            background: #2a2a2a;
        }
        
        .episode-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .episode-item:hover .episode-thumbnail img {
            transform: scale(1.05);
        }
        
        .episode-number-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--primary-color);
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            z-index: 2;
        }
        
        .episode-runtime {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            z-index: 2;
        }
        
        .episode-info {
            padding: 15px;
        }
        
        .episode-info h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .episode-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 13px;
            color: var(--text-gray);
        }
        
        .episode-rating {
            color: #ffd700;
            display: flex;
            align-items: center;
            gap: 3px;
        }
        
        .episode-date {
            display: flex;
            align-items: center;
            gap: 3px;
        }
        
        .episode-overview {
            font-size: 13px;
            color: var(--text-gray);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 12px;
            line-height: 1.5;
        }
        
        .episode-watch-btn {
            width: 100%;
            padding: 10px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .episode-watch-btn:hover {
            background: var(--primary-hover);
        }
        
        .episode-watch-btn i {
            font-size: 12px;
        }
        
        .recommendations-section {
            margin-top: 50px;
        }
        
        .recommendations-section h2 {
            font-size: 20px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
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
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: var(--background-light);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        .no-results i {
            color: var(--text-gray);
            margin-bottom: 20px;
        }
        
        .no-results h3 {
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .no-results p {
            color: var(--text-gray);
        }
        
        @media (max-width: 768px) {
            .seasons-page {
                padding: 70px 15px 30px;
            }
            
            .season-info-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 20px;
            }
            
            .season-info-poster {
                flex: 0 0 120px;
                width: 120px;
            }
            
            .season-meta {
                justify-content: center;
            }
            
            .season-actions {
                justify-content: center;
            }
            
            .episodes-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .episodes-filter {
                width: 100%;
            }
            
            .episodes-filter select {
                width: 100%;
            }
            
            .episodes-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .episode-info h4 {
                font-size: 15px;
            }
            
            .episode-meta {
                font-size: 12px;
            }
            
            .episode-overview {
                font-size: 12px;
            }
            
            .recommendations-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
        }
    </style>
</head>
<body>
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
                    <input type="text" class="search-input" placeholder="Search movies, TV shows..." id="searchInput">
                    <button class="search-btn"><i class="fas fa-search"></i></button>
                    <div class="search-suggestions" id="searchSuggestions"></div>
                </div>
            </div>
            
            <div class="nav-right"></div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="seasons-page">
        <!-- Breadcrumb Navigation -->
        <div class="seasons-header">
            <h1><?php echo htmlspecialchars($show['name']); ?></h1>
            <a href="tv.php?id=<?php echo $tv_id; ?>" class="show-link">
                <i class="fas fa-arrow-left"></i> Back to Show Details
            </a>
        </div>

        <!-- Season Info Card -->
        <div class="season-info-card">
            <div class="season-info-poster">
                <img src="<?php echo $tmdb->getImageUrl($season['poster_path'] ?? $show['poster_path'], 'w342'); ?>" 
                     alt="<?php echo htmlspecialchars($season['name']); ?>">
            </div>
            
            <div class="season-info-details">
                <h2><?php echo htmlspecialchars($season['name']); ?></h2>
                
                <div class="season-meta">
                    <span><i class="fas fa-calendar-alt"></i> <?php echo $season['air_date'] ? date('F j, Y', strtotime($season['air_date'])) : 'TBA'; ?></span>
                    <span><i class="fas fa-film"></i> <?php echo count($season['episodes']); ?> Episodes</span>
                    <?php if ($season['vote_average'] > 0): ?>
                        <span><i class="fas fa-star" style="color: #ffd700;"></i> <?php echo round($season['vote_average'], 1); ?>/10</span>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($season['overview'])): ?>
                    <p class="season-overview"><?php echo nl2br(htmlspecialchars($season['overview'])); ?></p>
                <?php endif; ?>
                
                <div class="season-actions">
                    <?php if (!empty($season['episodes'])): ?>
                        <a href="watch.php?type=tv&id=<?php echo $tv_id; ?>&season=<?php echo $season_number; ?>&episode=1" class="btn btn-primary">
                            <i class="fas fa-play"></i> Start Watching
                        </a>
                    <?php endif; ?>
                    <button class="btn btn-outline" onclick="addToList(<?php echo $tv_id; ?>, 'tv')">
                        <i class="fas fa-plus"></i> Add to My List
                    </button>
                </div>
            </div>
        </div>

        <!-- Episodes Section -->
        <section class="episodes-section">
            <div class="episodes-header">
                <h3><i class="fas fa-list"></i> All Episodes</h3>
                <div class="episodes-filter">
                    <select id="sortEpisodes" onchange="sortEpisodes()">
                        <option value="number">Sort by Episode Number</option>
                        <option value="rating">Sort by Rating (Highest)</option>
                        <option value="date">Sort by Air Date (Newest)</option>
                    </select>
                </div>
            </div>
            
            <?php if (!empty($season['episodes'])): ?>
                <div class="episodes-grid" id="episodesGrid">
                    <?php foreach ($season['episodes'] as $episode): 
                        // Check if episode has a still image, if not use default
                        $episode_image = $episode['still_path'] 
                            ? $tmdb->getImageUrl($episode['still_path'], 'w300') 
                            : $default_episode_image;
                    ?>
                        <div class="episode-item" data-episode="<?php echo $episode['episode_number']; ?>" 
                             data-rating="<?php echo $episode['vote_average']; ?>" 
                             data-date="<?php echo strtotime($episode['air_date']); ?>">
                            <div class="episode-thumbnail">
                                <img src="<?php echo $episode_image; ?>" 
                                     alt="<?php echo htmlspecialchars($episode['name'] ?: "Episode {$episode['episode_number']}"); ?>"
                                     loading="lazy"
                                     onerror="this.src='<?php echo $default_episode_image; ?>'">
                                <span class="episode-number-badge">E<?php echo $episode['episode_number']; ?></span>
                                <?php if ($episode['runtime']): ?>
                                    <span class="episode-runtime"><?php echo floor($episode['runtime'] / 60); ?>h <?php echo $episode['runtime'] % 60; ?>m</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="episode-info">
                                <h4><?php echo htmlspecialchars($episode['name'] ?: "Episode {$episode['episode_number']}"); ?></h4>
                                <div class="episode-meta">
                                    <span class="episode-rating">
                                        <i class="fas fa-star"></i> <?php echo round($episode['vote_average'], 1); ?>
                                    </span>
                                    <span class="episode-date">
                                        <i class="fas fa-calendar-day"></i> 
                                        <?php echo $episode['air_date'] ? date('M d, Y', strtotime($episode['air_date'])) : 'TBA'; ?>
                                    </span>
                                </div>
                                <p class="episode-overview">
                                    <?php echo $episode['overview'] ?: 'No overview available for this episode.'; ?>
                                </p>
                                <a href="watch.php?type=tv&id=<?php echo $tv_id; ?>&season=<?php echo $season_number; ?>&episode=<?php echo $episode['episode_number']; ?>" 
                                   class="episode-watch-btn">
                                    <i class="fas fa-play"></i> Watch Episode
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-film fa-4x"></i>
                    <h3>No episodes available</h3>
                    <p>Check back later for updates</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Recommendations -->
        <?php if (!empty($show['recommendations']['results'])): ?>
            <section class="recommendations-section">
                <h2><i class="fas fa-thumbs-up"></i> You might also like</h2>
                <div class="recommendations-grid">
                    <?php foreach (array_slice($show['recommendations']['results'], 0, 6) as $rec): ?>
                        <div class="recommendation-card" onclick="window.location.href='tv.php?id=<?php echo $rec['id']; ?>'">
                            <img src="<?php echo $tmdb->getImageUrl($rec['poster_path'] ?? '', 'w185'); ?>" 
                                 alt="<?php echo htmlspecialchars($rec['name']); ?>"
                                 loading="lazy">
                            <div class="recommendation-info">
                                <h4><?php echo htmlspecialchars($rec['name']); ?></h4>
                                <span class="rec-year"><?php echo date('Y', strtotime($rec['first_air_date'] ?? '')); ?></span>
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
                <li><a href="#">Terms of Service</a></li>
                <li><a href="#">Privacy Policy</a></li>
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
    // Sort Episodes Function
    function sortEpisodes() {
        const sortBy = document.getElementById('sortEpisodes').value;
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
        
        // Clear and re-append sorted episodes
        grid.innerHTML = '';
        episodes.forEach(episode => grid.appendChild(episode));
    }
    
    // Add to List Function
    function addToList(id, type) {
        let myList = JSON.parse(localStorage.getItem('myList')) || [];
        
        const exists = myList.some(item => item.id === id && item.type === type);
        
        if (!exists) {
            myList.push({ id, type, date: new Date().toISOString() });
            localStorage.setItem('myList', JSON.stringify(myList));
            
            // Simple feedback
            alert('✓ Added to My List');
            
            // Optional: Change button text temporarily
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
    
    // Initialize any episode thumbnails that might have failed to load
    document.addEventListener('DOMContentLoaded', function() {
        const episodeImages = document.querySelectorAll('.episode-thumbnail img');
        episodeImages.forEach(img => {
            img.addEventListener('error', function() {
                this.src = '<?php echo $default_episode_image; ?>';
            });
        });
    });
    </script>
</body>
</html>