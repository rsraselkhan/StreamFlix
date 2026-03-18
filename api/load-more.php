<?php
require_once '../config.php';
require_once 'tmdb.php';

header('Content-Type: application/json');

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$type = isset($_GET['type']) ? $_GET['type'] : 'all';

$tmdb = new TMDBAPI();
$results = [];

switch ($type) {
    case 'movie':
        $results = $tmdb->getPopularMovies($page);
        break;
    case 'tv':
        $results = $tmdb->getPopularTV($page);
        break;
    case 'anime':
        $results = $tmdb->discoverTV(16, $page);
        break;
    default:
        $results = $tmdb->getTrending('all', 'week', $page);
}

echo json_encode($results);
?>
