<?php
require_once '../config.php';
require_once 'tmdb.php';

header('Content-Type: application/json');

if (!isset($_GET['q']) || empty($_GET['q'])) {
    echo json_encode(['results' => []]);
    exit;
}

$query = $_GET['q'];
$tmdb = new TMDBAPI();

$results = $tmdb->searchMulti($query, 1);

echo json_encode($results);
?>