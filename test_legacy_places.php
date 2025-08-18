<?php
header('Content-Type: application/json');

$configPath = __DIR__ . '/storage/config.json';
$config = json_decode(file_get_contents($configPath), true);
$apiKey = $config['google_api_key'] ?? null;

$query = $_GET['q'] ?? 'Madrid Airport';

if (!$apiKey) {
    echo json_encode(['error' => 'No API key']);
    exit;
}

// Test legacy Places API with different endpoints
$tests = [];

// Test 1: Places Autocomplete (Legacy)
$url1 = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?input=' . urlencode($query) . '&key=' . urlencode($apiKey);
$response1 = file_get_contents($url1);
$tests['autocomplete_legacy'] = [
    'url' => $url1,
    'response' => json_decode($response1, true)
];

// Test 2: Places Nearby Search
$url2 = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=40.4168,-3.7038&radius=50000&keyword=' . urlencode($query) . '&key=' . urlencode($apiKey);
$response2 = file_get_contents($url2);
$tests['nearby_search'] = [
    'url' => $url2,
    'response' => json_decode($response2, true)
];

// Test 3: Places Text Search
$url3 = 'https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . urlencode($query) . '&key=' . urlencode($apiKey);
$response3 = file_get_contents($url3);
$tests['text_search_legacy'] = [
    'url' => $url3,
    'response' => json_decode($response3, true)
];

// Test 4: Geocoding API (alternative)
$url4 = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($query) . '&key=' . urlencode($apiKey);
$response4 = file_get_contents($url4);
$tests['geocoding'] = [
    'url' => $url4,
    'response' => json_decode($response4, true)
];

echo json_encode([
    'query' => $query,
    'api_key_preview' => substr($apiKey, 0, 20) . '...',
    'tests' => $tests
], JSON_PRETTY_PRINT);
?>