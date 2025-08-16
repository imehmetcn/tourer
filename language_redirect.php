<?php
/**
 * Language-based redirect handler
 * Handles root URL access and redirects to appropriate language
 */

// Start session for language detection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include language loader for detection logic
require_once 'includes/language_loader.php';

// Get the detected language
$lang_loader = new LanguageLoader();
$detected_lang = $lang_loader->getCurrentLanguage();

// Get current path info
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH);

// Check if we're at the root or need redirection
if ($path === '/' || $path === '/mytransfers/' || $path === '/mytransfers') {
    // Redirect to language-specific URL
    $redirect_url = '/' . $detected_lang . '/';
    
    // If we're in a subdirectory, include it
    if (strpos($path, '/mytransfers') === 0) {
        $redirect_url = '/mytransfers/' . $detected_lang . '/';
    }
    
    header('Location: ' . $redirect_url, true, 302);
    exit;
}

// Check if current URL has no language prefix
$segments = explode('/', trim($path, '/'));
$available_languages = ['en', 'tr', 'de', 'fr', 'es'];

// If first segment is not a language code, redirect with detected language
if (empty($segments[0]) || !in_array($segments[0], $available_languages)) {
    // Handle mytransfers subdirectory
    if (!empty($segments) && $segments[0] === 'mytransfers') {
        // Check if second segment is language
        if (empty($segments[1]) || !in_array($segments[1], $available_languages)) {
            $remaining_path = array_slice($segments, 1);
            $redirect_url = '/mytransfers/' . $detected_lang . '/' . implode('/', $remaining_path);
            header('Location: ' . $redirect_url, true, 302);
            exit;
        }
    } else {
        // Root level, add language prefix
        $redirect_url = '/' . $detected_lang . '/' . implode('/', $segments);
        header('Location: ' . $redirect_url, true, 302);
        exit;
    }
}

// If we reach here, URL already has proper language prefix, continue normally
?>
