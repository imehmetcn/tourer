<?php
// Configuration for API integration

// Prefer environment variable if set (Windows: setx GOOGLE_API_KEY "your-key")
$envKey = getenv('GOOGLE_API_KEY');
define('GOOGLE_API_KEY', $envKey !== false ? $envKey : 'REPLACE_WITH_YOUR_GOOGLE_API_KEY');


