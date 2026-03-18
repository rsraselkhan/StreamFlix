<?php
require_once 'config.php';
require_once 'api/tmdb.php';

$tmdb = new TMDBAPI();
$tv_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$tv_id) {
    header('Location: index.php');
    exit;
}

$show = $tmdb->getTVDetails($tv_id);

if (!$show) {
    header('HTTP/1.0 404 Not Found');
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>TV Show Not Found - <?php echo SITE_NAME; ?></title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="error-page">
            <h1>404</h1>
            <p>TV Show not found</p>
            <a href="index.php" class="btn btn-primary">Go Home</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Get trailer
$trailer = null;
if (isset($show['videos']['results'])) {
    foreach ($show['videos']['results'] as $video) {
        if ($video['type'] === 'Trailer' && $video['site'] === 'YouTube') {
            $trailer = $video;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($show['name']); ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php" class="logo"><?php echo SITE_NAME; ?></a>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#">Movies</a></li>
                    <li><a href="#">TV Shows</a></li>
                    <li><a href="#">My List</a></li>
                </ul>
            </div>
            <div class="nav-right">
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Search..." id="searchInput">
                    <button class="search-btn"><i class="fas fa-search"></i></button>
                </div>
                <div class="profile-menu">
                    <i class="fas fa-user-circle profile-icon"></i>
                </div>
            </div>
        </div>
    </nav>

    <!-- TV Show Hero -->
    <section class="movie-hero" style="background-image: url('<?php echo $tmdb->getBackdropUrl($show['backdrop_path'] ?? '', 'original'); ?>')">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="movie-poster">
                <img src="<?php echo $tmdb->getImageUrl($show['poster_path'] ?? '', 'w500'); ?>" 
                     alt="<?php echo htmlspecialchars($show['name']); ?>">
            </div>
            <div class="movie-info">
                <h1 class="movie-title"><?php echo htmlspecialchars($show['name']); ?></h1>
                
                <div class="movie-meta">
                    <span class="movie-rating"><i class="fas fa-star"></i> <?php echo round($show['vote_average'], 1); ?></span>
                    <span class="movie-year"><?php echo date('Y', strtotime($show['first_air_date'])); ?></span>
                    <span class="movie-seasons"><?php echo $show['number_of_seasons']; ?> Seasons</span>
                    <span class="movie-episodes"><?php echo $show['number_of_episodes']; ?> Episodes</span>
                    <span class="movie-status"><?php echo $show['status']; ?></span>
                </div>
                
                <div class="movie-genres">
                    <?php foreach ($show['genres'] as $genre): ?>
                        <a href="genre.php?type=tv&id=<?php echo $genre['id']; ?>" class="genre-tag"><?php echo $genre['name']; ?></a>
                    <?php endforeach; ?>
                </div>
                
                <p class="movie-overview"><?php echo nl2br(htmlspecialchars($show['overview'])); ?></p>
                
                <div class="movie-actions">
                    <a href="watch.php?type=tv&id=<?php echo $show['id']; ?>&season=1&episode=1" class="btn btn-primary btn-large">
                        <i class="fas fa-play"></i> Start Watching
                    </a>
                    
                    <?php if ($trailer): ?>
                        <button class="btn btn-secondary btn-large" onclick="openTrailer('<?php echo $trailer['key']; ?>')">
                            <i class="fas fa-film"></i> Trailer
                        </button>
                    <?php endif; ?>
                    
                    <button class="btn btn-outline btn-large" onclick="addToList(<?php echo $show['id']; ?>, 'tv')">
                        <i class="fas fa-plus"></i> My List
                    </button>
                </div>
                
                <?php if (isset($show['created_by']) && !empty($show['created_by'])): ?>
                    <div class="movie-crew">
                        <div class="crew-item">
                            <span class="crew-label">Created by:</span>
                            <span class="crew-value">
                                <?php 
                                $creators = array_map(function($c) { return $c['name']; }, $show['created_by']);
                                echo implode(', ', $creators);
                                ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Seasons Section -->
    <section class="seasons-section">
        <div class="section-header">
            <h2 class="section-title">Seasons</h2>
        </div>
        <div class="seasons-grid">
            <?php foreach ($show['seasons'] as $season): ?>
                <?php if ($season['season_number'] > 0): ?>
                    <div class="season-card">
                        <div class="season-poster">
                            <img src="<?php echo $tmdb->getImageUrl($season['poster_path'] ?? '', 'w342'); ?>" 
                                 alt="<?php echo htmlspecialchars($season['name']); ?>">
                        </div>
                        <div class="season-info">
                            <h3 class="season-name"><?php echo htmlspecialchars($season['name']); ?></h3>
                            <p class="season-episodes"><?php echo $season['episode_count']; ?> Episodes</p>
                            <p class="season-date"><?php echo date('Y', strtotime($season['air_date'])); ?></p>
                            <a href="season.php?tv_id=<?php echo $show['id']; ?>&season=<?php echo $season['season_number']; ?>" class="btn btn-primary btn-small">
                                View Episodes
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Cast Section -->
    <section class="cast-section">
        <div class="section-header">
            <h2 class="section-title">Cast</h2>
        </div>
        <div class="cast-grid">
            <?php if (isset($show['credits']['cast'])): ?>
                <?php foreach (array_slice($show['credits']['cast'], 0, 10) as $cast): ?>
                    <div class="cast-card">
                        <div class="cast-photo">
                            <?php if ($cast['profile_path']): ?>
                                <img src="<?php echo $tmdb->getImageUrl($cast['profile_path'], 'w185'); ?>" 
                                     alt="<?php echo htmlspecialchars($cast['name']); ?>">
                            <?php else: ?>
                                <div class="no-photo"><i class="fas fa-user"></i></div>
                            <?php endif; ?>
                        </div>
                        <h3 class="cast-name"><?php echo htmlspecialchars($cast['name']); ?></h3>
                        <p class="cast-character"><?php echo htmlspecialchars($cast['character']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Similar Shows -->
    <section class="content-section">
        <div class="section-header">
            <h2 class="section-title">Similar Shows</h2>
        </div>
        <div class="content-grid">
            <?php if (isset($show['similar']['results'])): ?>
                <?php foreach (array_slice($show['similar']['results'], 0, 10) as $similar): ?>
                    <div class="movie-card" data-id="<?php echo $similar['id']; ?>" data-type="tv">
                        <div class="card-poster">
                            <img src="<?php echo $tmdb->getImageUrl($similar['poster_path'] ?? '', 'w500'); ?>" 
                                 alt="<?php echo htmlspecialchars($similar['name']); ?>"
                                 loading="lazy">
                            <div class="card-overlay">
                                <div class="card-actions">
                                    <a href="watch.php?type=tv&id=<?php echo $similar['id']; ?>&season=1&episode=1" class="card-btn"><i class="fas fa-play"></i></a>
                                    <a href="tv.php?id=<?php echo $similar['id']; ?>" class="card-btn"><i class="fas fa-info"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-info">
                            <h3 class="card-title"><?php echo htmlspecialchars($similar['name']); ?></h3>
                            <div class="card-meta">
                                <span class="card-rating"><i class="fas fa-star"></i> <?php echo round($similar['vote_average'], 1); ?></span>
                                <span class="card-year"><?php echo date('Y', strtotime($similar['first_air_date'])); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Trailer Modal -->
    <div class="modal" id="trailerModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeTrailer()">&times;</span>
            <div class="video-container">
                <iframe id="trailerIframe" src="" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>