<?php
require_once __DIR__ . '/../config.php';

class TMDBAPI {
    private $api_key;
    private $base_url;
    
    public function __construct() {
        $this->api_key = TMDB_API_KEY;
        $this->base_url = TMDB_API_BASE;
    }
    
    private function makeRequest($endpoint, $params = []) {
        $params['api_key'] = $this->api_key;
        $params['language'] = 'en-US';
        
        $url = $this->base_url . $endpoint . '?' . http_build_query($params);
        
        // Check cache if enabled
        if (CACHE_ENABLED) {
            $cache_file = CACHE_DIR . md5($url) . '.cache';
            if (file_exists($cache_file) && (time() - filemtime($cache_file) < 3600)) {
                return json_decode(file_get_contents($cache_file), true);
            }
        }
        
        // Make API request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code == 200) {
            $data = json_decode($response, true);
            
            // Save to cache if enabled
            if (CACHE_ENABLED) {
                if (!is_dir(CACHE_DIR)) {
                    mkdir(CACHE_DIR, 0777, true);
                }
                file_put_contents($cache_file, $response);
            }
            
            return $data;
        }
        
        return null;
    }
    
    // Homepage Sections
    public function getTrending($media_type = 'all', $time_window = 'day') {
        return $this->makeRequest("/trending/{$media_type}/{$time_window}");
    }
    
    public function getPopularMovies($page = 1) {
        return $this->makeRequest("/movie/popular", ['page' => $page]);
    }
    
    public function getTopRatedMovies($page = 1) {
        return $this->makeRequest("/movie/top_rated", ['page' => $page]);
    }
    
    public function getNowPlayingMovies($page = 1) {
        return $this->makeRequest("/movie/now_playing", ['page' => $page]);
    }
    
    public function getUpcomingMovies($page = 1) {
        return $this->makeRequest("/movie/upcoming", ['page' => $page]);
    }
    
    public function getPopularTV($page = 1) {
        return $this->makeRequest("/tv/popular", ['page' => $page]);
    }
    
    public function getTopRatedTV($page = 1) {
        return $this->makeRequest("/tv/top_rated", ['page' => $page]);
    }
    
    public function getAiringTodayTV($page = 1) {
        return $this->makeRequest("/tv/airing_today", ['page' => $page]);
    }
    
    // Movie Details
    public function getMovieDetails($movie_id) {
        return $this->makeRequest("/movie/{$movie_id}", [
            'append_to_response' => 'videos,credits,similar,recommendations'
        ]);
    }
    
    // TV Show Details
    public function getTVDetails($tv_id) {
        return $this->makeRequest("/tv/{$tv_id}", [
            'append_to_response' => 'videos,credits,similar,recommendations,content_ratings'
        ]);
    }
    
    // Season Details
    public function getSeasonDetails($tv_id, $season_number) {
        return $this->makeRequest("/tv/{$tv_id}/season/{$season_number}", [
            'append_to_response' => 'videos,credits'
        ]);
    }
    
    // Episode Details
    public function getEpisodeDetails($tv_id, $season_number, $episode_number) {
        return $this->makeRequest("/tv/{$tv_id}/season/{$season_number}/episode/{$episode_number}", [
            'append_to_response' => 'videos,credits'
        ]);
    }
    
    // Search
    public function searchMulti($query, $page = 1) {
        return $this->makeRequest("/search/multi", [
            'query' => $query,
            'page' => $page,
            'include_adult' => false
        ]);
    }
    
    // Genres
    public function getMovieGenres() {
        return $this->makeRequest("/genre/movie/list");
    }
    
    public function getTVGenres() {
        return $this->makeRequest("/genre/tv/list");
    }
    
    // Discover by Genre
    public function discoverMovies($genre_id, $page = 1, $sort_by = 'popularity.desc') {
        return $this->makeRequest("/discover/movie", [
            'with_genres' => $genre_id,
            'page' => $page,
            'sort_by' => $sort_by
        ]);
    }
    
    public function discoverTV($genre_id, $page = 1, $sort_by = 'popularity.desc') {
        return $this->makeRequest("/discover/tv", [
            'with_genres' => $genre_id,
            'page' => $page,
            'sort_by' => $sort_by
        ]);
    }
    
    // Get Videos (Trailers)
    public function getVideos($media_type, $id) {
        return $this->makeRequest("/{$media_type}/{$id}/videos");
    }
    
    // Get Image URLs
    public function getImageUrl($path, $size = 'w500') {
        if (empty($path)) return '/placeholder.jpg';
        return TMDB_IMAGE_BASE . $size . $path;
    }
    
    public function getBackdropUrl($path, $size = 'original') {
        if (empty($path)) return '/placeholder-backdrop.jpg';
        return TMDB_IMAGE_BASE . $size . $path;
    }
}
?>