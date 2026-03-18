<?php
require_once 'config.php';
require_once 'api/tmdb.php';

$tmdb = new TMDBAPI();

// Get movie ID from URL
$movie_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$movie_id) {
    header('Location: index.php');
    exit;
}

// Fetch movie details
$movie = $tmdb->getMovieDetails($movie_id);

if (!$movie) {
    // Handle 404
    header('HTTP/1.0 404 Not Found');
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Movie Not Found - <?php echo SITE_NAME; ?></title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="error-page">
            <h1>404</h1>
            <p>Movie not found</p>
            <a href="index.php" class="btn btn-primary">Go Home</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Get trailer
$trailer = null;
if (isset($movie['videos']['results'])) {
    foreach ($movie['videos']['results'] as $video) {
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
    <title><?php echo htmlspecialchars($movie['title']); ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
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

    <!-- Movie Hero Section -->
    <section class="movie-hero" style="background-image: url('<?php echo $tmdb->getBackdropUrl($movie['backdrop_path'] ?? '', 'original'); ?>')">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="movie-poster">
                <img src="<?php echo $tmdb->getImageUrl($movie['poster_path'] ?? '', 'w500'); ?>" 
                     alt="<?php echo htmlspecialchars($movie['title']); ?>">
            </div>
            <div class="movie-info">
                <h1 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h1>
                
                <?php if (!empty($movie['tagline'])): ?>
                    <p class="movie-tagline"><?php echo htmlspecialchars($movie['tagline']); ?></p>
                <?php endif; ?>
                
                <div class="movie-meta">
                    <span class="movie-rating"><i class="fas fa-star"></i> <?php echo round($movie['vote_average'], 1); ?></span>
                    <span class="movie-year"><?php echo date('Y', strtotime($movie['release_date'])); ?></span>
                    <span class="movie-runtime"><?php echo $movie['runtime']; ?> min</span>
                    <span class="movie-status">Released</span>
                </div>
                
                <div class="movie-genres">
                    <?php foreach ($movie['genres'] as $genre): ?>
                        <a href="genre.php?type=movie&id=<?php echo $genre['id']; ?>" class="genre-tag"><?php echo $genre['name']; ?></a>
                    <?php endforeach; ?>
                </div>
                
                <p class="movie-overview"><?php echo nl2br(htmlspecialchars($movie['overview'])); ?></p>
                
                <div class="movie-actions">
                    <a href="watch.php?type=movie&id=<?php echo $movie['id']; ?>" class="btn btn-primary btn-large">
                        <i class="fas fa-play"></i> Watch Now
                    </a>
                    
                    <?php if ($trailer): ?>
                        <button class="btn btn-secondary btn-large" onclick="openTrailer('<?php echo $trailer['key']; ?>')">
                            <i class="fas fa-film"></i> Trailer
                        </button>
                    <?php endif; ?>
                    
                    <button class="btn btn-outline btn-large" onclick="addToList(<?php echo $movie['id']; ?>, 'movie')">
                        <i class="fas fa-plus"></i> My List
                    </button>
                </div>
                
                <?php if (isset($movie['credits']['crew'])): ?>
                    <div class="movie-crew">
                        <?php 
                        $directors = array_filter($movie['credits']['crew'], function($crew) {
                            return $crew['job'] === 'Director';
                        });
                        $writers = array_filter($movie['credits']['crew'], function($crew) {
                            return $crew['job'] === 'Writer' || $crew['job'] === 'Screenplay';
                        });
                        ?>
                        
                        <?php if (!empty($directors)): ?>
                            <div class="crew-item">
                                <span class="crew-label">Director:</span>
                                <span class="crew-value">
                                    <?php 
                                    $names = array_map(function($d) { return $d['name']; }, array_slice($directors, 0, 2));
                                    echo implode(', ', $names);
                                    ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($writers)): ?>
                            <div class="crew-item">
                                <span class="crew-label">Writer:</span>
                                <span class="crew-value">
                                    <?php 
                                    $names = array_map(function($w) { return $w['name']; }, array_slice($writers, 0, 2));
                                    echo implode(', ', $names);
                                    ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="crew-item">
                            <span class="crew-label">Stars:</span>
                            <span class="crew-value">
                                <?php 
                                $stars = array_slice($movie['credits']['cast'], 0, 3);
                                $starNames = array_map(function($s) { return $s['name']; }, $stars);
                                echo implode(', ', $starNames);
                                ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Cast Section -->
    <section class="cast-section">
        <div class="section-header">
            <h2 class="section-title">Cast</h2>
        </div>
        <div class="cast-grid">
            <?php if (isset($movie['credits']['cast'])): ?>
                <?php foreach (array_slice($movie['credits']['cast'], 0, 10) as $cast): ?>
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

    <!-- Similar Movies -->
    <section class="content-section">
        <div class="section-header">
            <h2 class="section-title">Similar Movies</h2>
        </div>
        <div class="content-grid">
            <?php if (isset($movie['similar']['results'])): ?>
                <?php foreach (array_slice($movie['similar']['results'], 0, 10) as $similar): ?>
                    <div class="movie-card" data-id="<?php echo $similar['id']; ?>" data-type="movie">
                        <div class="card-poster">
                            <img src="<?php echo $tmdb->getImageUrl($similar['poster_path'] ?? '', 'w500'); ?>" 
                                 alt="<?php echo htmlspecialchars($similar['title']); ?>"
                                 loading="lazy">
                            <div class="card-overlay">
                                <div class="card-actions">
                                    <a href="watch.php?type=movie&id=<?php echo $similar['id']; ?>" class="card-btn"><i class="fas fa-play"></i></a>
                                    <a href="movie.php?id=<?php echo $similar['id']; ?>" class="card-btn"><i class="fas fa-info"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-info">
                            <h3 class="card-title"><?php echo htmlspecialchars($similar['title']); ?></h3>
                            <div class="card-meta">
                                <span class="card-rating"><i class="fas fa-star"></i> <?php echo round($similar['vote_average'], 1); ?></span>
                                <span class="card-year"><?php echo date('Y', strtotime($similar['release_date'])); ?></span>
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