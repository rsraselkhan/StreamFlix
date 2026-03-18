<?php
require_once 'config.php';
require_once 'api/tmdb.php';

$tmdb = new TMDBAPI();
$query = isset($_GET['q']) ? $_GET['q'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$results = $tmdb->searchMulti($query, $page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results for "<?php echo htmlspecialchars($query); ?>" - <?php echo SITE_NAME; ?></title>
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
        <div class="search-header">
            <h1 class="search-title">Search Results for "<?php echo htmlspecialchars($query); ?>"</h1>
            <p class="search-count"><?php echo $results['total_results'] ?? 0; ?> results found</p>
        </div>

        <?php if (isset($results['results']) && !empty($results['results'])): ?>
            <div class="content-grid">
                <?php foreach ($results['results'] as $item): ?>
                    <?php if ($item['media_type'] !== 'person'): ?>
                        <div class="movie-card" data-id="<?php echo $item['id']; ?>" data-type="<?php echo $item['media_type']; ?>">
                            <div class="card-poster">
                                <img src="<?php echo $tmdb->getImageUrl($item['poster_path'] ?? ''); ?>" 
                                     alt="<?php echo htmlspecialchars($item['title'] ?? $item['name']); ?>"
                                     loading="lazy">
                                <div class="card-overlay">
                                    <div class="card-actions">
                                        <a href="watch.php?type=<?php echo $item['media_type']; ?>&id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-play"></i></a>
                                        <a href="<?php echo $item['media_type'] === 'movie' ? 'movie.php' : 'tv.php'; ?>?id=<?php echo $item['id']; ?>" class="card-btn"><i class="fas fa-info"></i></a>
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
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <?php if ($results['total_pages'] > $page): ?>
                <div class="load-more-container">
                    <button class="btn btn-primary" onclick="loadMoreSearch(<?php echo $page + 1; ?>)">Load More</button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-search fa-4x"></i>
                <h2>No results found</h2>
                <p>Try different keywords or browse our categories</p>
            </div>
        <?php endif; ?>
    </main>

    <script src="script.js"></script>
</body>
</html>