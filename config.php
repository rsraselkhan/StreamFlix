<?php
// TMDB API Configuration
define('TMDB_API_KEY', '603012c6211557c86f04a30876807052'); // Replace with your actual API key
define('TMDB_API_BASE', 'https://api.themoviedb.org/3');
define('TMDB_IMAGE_BASE', 'https://image.tmdb.org/t/p/');
define('TMDB_SECURE_IMAGE_BASE', 'https://image.tmdb.org/t/p/');

// Site Configuration
define('SITE_NAME', 'StreamFlix');
define('SITE_URL', 'https://animedub.gt.tc/');

// Cache settings (optional - reduces API calls)
define('CACHE_ENABLED', false);
define('CACHE_DIR', __DIR__ . '/cache/');
?>