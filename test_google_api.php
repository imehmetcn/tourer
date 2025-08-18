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
    'google_response' => null,
    'error' => null
];

if ($apiKey) {
    $query = 'Madrid Airport';
    $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?input=' . urlencode($query) . '&key=' . urlencode($apiKey);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        $result['error'] = 'CURL Error: ' . $curlError;
    } else {
        $result['http_code'] = $httpCode;
        $result['google_response'] = json_decode($response, true);
    }
} else {
    $result['error'] = 'No API key found in config';
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>