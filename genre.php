<?php
require_once 'config.php';
require_once 'api/tmdb.php';

$tmdb = new TMDBAPI();
$type = isset($_GET['type']) ? $_GET['type'] : 'movie';
$genre_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'popularity.desc';

// Get genre list
$genres = $type === 'movie' ? $tmdb->getMovieGenres() : $tmdb->getTVGenres();

// Find current genre
$current_genre = null;
foreach ($genres['genres'] as $genre) {
    if ($genre['id'] == $genre_id) {
        $current_genre = $genre;
        break;
    }
}

// Get content for this genre
if ($type === 'movie') {
    $content = $tmdb->discoverMovies($genre_id, $page, $sort);
} else {
    $content = $tmdb->discoverTV($genre_id, $page, $sort);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($current_genre['name'] ?? 'Genre'); ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php" class="logo"><?php echo SITE_NAME; ?></a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="genre-header">
            <h1 class="genre-title"><?php echo htmlspecialchars($current_genre['name'] ?? 'Genre'); ?></h1>
            
            <div class="genre-filters">
                <div class="filter-group">
                    <label for="sortSelect">Sort by:</label>
                    <select id="sortSelect" onchange="changeSort()">
                        <option value="popularity.desc" <?php echo $sort === 'popularity.desc' ? 'selected' : ''; ?>>Popularity</option>
                        <option value="vote_average.desc" <?php echo $sort === 'vote_average.desc' ? 'selected' : ''; ?>>Rating</option>
                        <option value="release_date.desc" <?php echo $sort === 'release_date.desc' ? 'selected' : ''; ?>>Release Date</option>
                        <option value="title.asc" <?php echo $sort === 'title.asc' ? 'selected' : ''; ?>>Title A-Z</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="typeSelect">Type:</label>
                    <select id="typeSelect" onchange="changeType()">
                        <option value="movie" <?php echo $type === 'movie' ? 'selected' : ''; ?>>Movies</option>
                        <option value="tv" <?php echo $type === 'tv' ? 'selected' : ''; ?>>TV Shows</option>
                    </select>
                </div>
            </div>
        </div>

        <?php if (isset($content['results']) && !empty($content['results'])): ?>
            <div class="content-grid">
                <?php foreach ($content['results'] as $item): ?>
                    <div class="movie-card" data-id="<?php echo $item['id']; ?>" data-type="<?php echo $type; ?>">
                        <div class="card-poster">
                            <img src="<?php echo $tmdb->getImageUrl($item['poster_path'] ?? ''); ?>" 
                                 alt="<?php echo htmlspecialchars($item['title'] ?? $item['name']); ?>"
                                 loading="lazy">
                            <div class="card-overlay">
                                <div class="card-actions">
                                    <a href="watch.php?type=<?php echo $type; ?>&id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-play"></i></a>
                                    <a href="<?php echo $type === 'movie' ? 'movie.php' : 'tv.php'; ?>?id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-info"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-info">
                            <h3 class="card-title"><?php echo htmlspecialchars($item['title'] ?? $item['name']); ?></h3>
                            <div class="card-meta">
                                <span class="card-rating"><i class="fas fa-star"></i> <?php echo round($item['vote_average'], 1); ?></span>
                                <span class="card-year"><?php echo date('Y', strtotime($item['release_date'] ?? $item['first_air_date'] ?? '')); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($content['total_pages'] > $page): ?>
                <div class="load-more-container">
                    <button class="btn btn-primary" onclick="loadMoreGenre(<?php echo $page + 1; ?>)">Load More</button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-film fa-4x"></i>
                <h2>No content found</h2>
                <p>Check back later for updates</p>
            </div>
        <?php endif; ?>
    </main>

    <script>
    function changeSort() {
        const sort = document.getElementById('sortSelect').value;
        const type = document.getElementById('typeSelect').value;
        const genreId = <?php echo $genre_id; ?>;
        window.location.href = `genre.php?type=${type}&id=${genreId}&sort=${sort}`;
    }
    
    function changeType() {
        const type = document.getElementById('typeSelect').value;
        const genreId = <?php echo $genre_id; ?>;
        const sort = document.getElementById('sortSelect').value;
        window.location.href = `genre.php?type=${type}&id=${genreId}&sort=${sort}`;
    }
    
    function loadMoreGenre(page) {
        const type = '<?php echo $type; ?>';
        const genreId = <?php echo $genre_id; ?>;
        const sort = '<?php echo $sort; ?>';
        
        fetch(`api/load-more.php?type=genre&media_type=${type}&genre_id=${genreId}&sort=${sort}&page=${page}`)
            .then(response => response.json())
            .then(data => {
                if (data.results && data.results.length > 0) {
                    appendContent(data.results);
                    
                    // Update load more button
                    if (page < data.total_pages) {
                        const loadMoreBtn = document.querySelector('.load-more-container .btn');
                        loadMoreBtn.setAttribute('onclick', `loadMoreGenre(${page + 1})`);
                    } else {
                        document.querySelector('.load-more-container').remove();
                    }
                }
            })
            .catch(error => console.error('Error loading more:', error));
    }
    </script>

    <script src="script.js"></script>
</body>
</html>