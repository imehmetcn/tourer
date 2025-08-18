<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Load config
$configPath = __DIR__ . '/storage/config.json';
$config = json_decode(file_get_contents($configPath), true);
$apiKey = $config['google_api_key'] ?? null;

$result = [
    'config_loaded' => !empty($config),
    'api_key_exists' => !empty($apiKey),
    'api_key_preview' => $apiKey ? substr($apiKey, 0, 20) . '...' : null,
    'test_query' => 'Madrid Airport',
    'tests' => []
];

if ($apiKey) {
    $query = $_GET['q'] ?? 'Madrid Airport';
    
    // Test 1: New Places Autocomplete API
    $autocompleteUrl = 'https://places.googleapis.com/v1/places:autocomplete';
    $autocompleteData = [
        'input' => $query,
        'languageCode' => 'en'
    ];
    
    $ch = curl_init($autocompleteUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($autocompleteData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Goog-Api-Key: ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $result['tests']['autocomplete'] = [
        'url' => $autocompleteUrl,
        'http_code' => $httpCode,
        'curl_error' => $error,
        'response' => json_decode($response, true),
        'raw_response' => $response
    ];
    
    // Test 2: New Places Text Search API
    $textSearchUrl = 'https://places.googleapis.com/v1/places:searchText';
    $textSearchData = [
        'textQuery' => $query,
        'languageCode' => 'en',
        'maxResultCount' => 5
    ];
    
    $ch = curl_init($textSearchUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($textSearchData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Goog-Api-Key: ' . $apiKey,
        'X-Goog-FieldMask: places.id,places.displayName,places.formattedAddress,places.types'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $result['tests']['text_search'] = [
        'url' => $textSearchUrl,
        'http_code' => $httpCode,
        'curl_error' => $error,
        'response' => json_decode($response, true),
        'raw_response' => $response
    ];
    
    // Test 3: Legacy API (for comparison)
    $legacyUrl = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?input=' . urlencode($query) . '&key=' . urlencode($apiKey);
    
    $ch = curl_init($legacyUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $result['tests']['legacy'] = [
        'url' => $legacyUrl,
        'http_code' => $httpCode,
        'curl_error' => $error,
        'response' => json_decode($response, true),
        'raw_response' => $response
    ];
    
} else {
    $result['error'] = 'No API key found in config';
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>