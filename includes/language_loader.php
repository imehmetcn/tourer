<?php
/**
 * Language Loader
 * Handles dynamic language loading for the mytransfers website
 */

class LanguageLoader {
    private $current_language;
    private $available_languages = ['en', 'tr', 'de', 'fr', 'es'];
    private $default_language = 'en';
    
    // Country code to language mapping
    private $country_to_language = [
        'TR' => 'tr', // Turkey
        'DE' => 'de', // Germany
        'AT' => 'de', // Austria (German)
        'CH' => 'de', // Switzerland (German primary)
        'FR' => 'fr', // France
        'BE' => 'fr', // Belgium (French)
        'CA' => 'fr', // Canada (French)
        'ES' => 'es', // Spain
        'MX' => 'es', // Mexico
        'AR' => 'es', // Argentina
        'CO' => 'es', // Colombia
        'CL' => 'es', // Chile
        'PE' => 'es', // Peru
        'VE' => 'es', // Venezuela
        'EC' => 'es', // Ecuador
        'UY' => 'es', // Uruguay
        'PY' => 'es', // Paraguay
        'BO' => 'es', // Bolivia
        'GT' => 'es', // Guatemala
        'CR' => 'es', // Costa Rica
        'PA' => 'es', // Panama
        'DO' => 'es', // Dominican Republic
        'CU' => 'es', // Cuba
        'US' => 'en', // United States
        'GB' => 'en', // United Kingdom
        'IE' => 'en', // Ireland
        'AU' => 'en', // Australia
        'NZ' => 'en', // New Zealand
        'ZA' => 'en', // South Africa
        'CA' => 'en', // Canada (English primary)
        'IN' => 'en', // India
        // Add more mappings as needed
    ];
    
    public function __construct() {
        $this->detectLanguage();
    }
    
    /**
     * Detect the current language from URL, cookie, session, or browser
     */
    private function detectLanguage() {
        // Priority: URL Parameter > Cookie > Session > Browser > Default
        
        // 1. Check URL parameter first (from .htaccess rewrite)
        if (isset($_GET['lang']) && in_array($_GET['lang'], $this->available_languages)) {
            $this->current_language = $_GET['lang'];
            // Update cookie and session to match URL
            $this->setLanguageCookie($_GET['lang']);
            if (session_status() !== PHP_SESSION_NONE) {
                $_SESSION['site_language'] = $_GET['lang'];
            }
            return;
        }
        
        // 2. Check URL path directly (fallback if rewrite fails)
        $url_lang = $this->getLanguageFromUrl();
        if ($url_lang && in_array($url_lang, $this->available_languages)) {
            $this->current_language = $url_lang;
            $this->setLanguageCookie($url_lang);
            if (session_status() !== PHP_SESSION_NONE) {
                $_SESSION['site_language'] = $url_lang;
            }
            return;
        }
        
        // 3. Check cookie
        if (isset($_COOKIE['site_language']) && in_array($_COOKIE['site_language'], $this->available_languages)) {
            $this->current_language = $_COOKIE['site_language'];
            return;
        }
        
        // 4. Check session
        if (isset($_SESSION['site_language']) && in_array($_SESSION['site_language'], $this->available_languages)) {
            $this->current_language = $_SESSION['site_language'];
            return;
        }
        
        // 5. Try to detect from IP geolocation
        $ip_lang = $this->getLanguageFromIP();
        if ($ip_lang && in_array($ip_lang, $this->available_languages)) {
            $this->current_language = $ip_lang;
            return;
        }
        
        // 6. Try to detect from browser language
        $browser_lang = $this->getBrowserLanguage();
        $this->current_language = in_array($browser_lang, $this->available_languages) ? $browser_lang : $this->default_language;
    }
    
    /**
     * Get language from URL path
     */
    private function getLanguageFromUrl() {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $path = parse_url($uri, PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));
        
        // Check if first segment after domain is a language code
        if (!empty($segments)) {
            $potential_lang = $segments[0];
            // Handle cases like /mytransfers/en/ -> check second segment
            if ($potential_lang === 'mytransfers' && isset($segments[1])) {
                $potential_lang = $segments[1];
            }
            return $potential_lang;
        }
        
        return null;
    }
    
    /**
     * Set language cookie
     */
    private function setLanguageCookie($lang) {
        $expires = time() + (365 * 24 * 60 * 60); // 1 year
        setcookie('site_language', $lang, $expires, '/');
    }
    
    /**
     * Get language based on IP geolocation
     */
    private function getLanguageFromIP() {
        $ip = $this->getUserIP();
        
        // Skip local/private IPs
        if ($this->isLocalIP($ip)) {
            return null;
        }
        
        // Check cache first (session-based to avoid multiple API calls)
        $cache_key = 'ip_country_' . md5($ip);
        if (isset($_SESSION[$cache_key])) {
            $country_code = $_SESSION[$cache_key];
            if ($country_code && isset($this->country_to_language[$country_code])) {
                return $this->country_to_language[$country_code];
            }
        }
        
        try {
            // Use free IP geolocation service
            $country_code = $this->getCountryFromIP($ip);
            
            // Cache the result for this session
            $_SESSION[$cache_key] = $country_code;
            
            if ($country_code && isset($this->country_to_language[$country_code])) {
                return $this->country_to_language[$country_code];
            }
        } catch (Exception $e) {
            // Log error but don't break the site
            error_log("IP geolocation failed: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Get user's real IP address
     */
    private function getUserIP() {
        // Check for various headers that might contain the real IP
        $headers = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    /**
     * Check if IP is local/private
     */
    private function isLocalIP($ip) {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
    
    /**
     * Get country code from IP using free geolocation service
     */
    private function getCountryFromIP($ip) {
        // Use multiple free services as fallback
        $services = [
            "http://ip-api.com/json/{$ip}?fields=countryCode",
            "https://ipapi.co/{$ip}/country_code/",
            "http://www.geoplugin.net/json.gp?ip={$ip}"
        ];
        
        foreach ($services as $service) {
            try {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 2, // 2 second timeout
                        'user_agent' => 'MyTransfers Language Detection'
                    ]
                ]);
                
                $response = @file_get_contents($service, false, $context);
                if ($response === false) continue;
                
                // Parse response based on service
                if (strpos($service, 'ip-api.com') !== false) {
                    $data = json_decode($response, true);
                    if (isset($data['countryCode'])) {
                        return strtoupper($data['countryCode']);
                    }
                } elseif (strpos($service, 'ipapi.co') !== false) {
                    $country_code = trim($response);
                    if (strlen($country_code) === 2) {
                        return strtoupper($country_code);
                    }
                } elseif (strpos($service, 'geoplugin.net') !== false) {
                    $data = json_decode($response, true);
                    if (isset($data['geoplugin_countryCode'])) {
                        return strtoupper($data['geoplugin_countryCode']);
                    }
                }
            } catch (Exception $e) {
                continue; // Try next service
            }
        }
        
        return null;
    }
    
    /**
     * Get browser language preference
     */
    private function getBrowserLanguage() {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($browser_languages as $lang) {
                $lang = strtolower(trim(substr($lang, 0, 2)));
                if (in_array($lang, $this->available_languages)) {
                    return $lang;
                }
            }
        }
        return $this->default_language;
    }
    
    /**
     * Get current language
     */
    public function getCurrentLanguage() {
        return $this->current_language;
    }
    
    /**
     * Get language file path for JavaScript
     */
    public function getLanguageFilePath() {
        return "/mytransfers/assets/mytransfersweb/prod/js/lang/{$this->current_language}.js";
    }
    
    /**
     * Get language file with cache busting
     */
    public function getLanguageFileWithVersion() {
        $file_path = $this->getLanguageFilePath();
        // Convert absolute path to relative for file_exists check
        $relative_path = ltrim($file_path, '/mytransfers/');
        $full_path = __DIR__ . '/../' . $relative_path;
        
        if (file_exists($full_path)) {
            $version = filemtime($full_path);
            return $file_path . '?id=' . substr(md5($version), 0, 16);
        }
        
        return $file_path . '?id=' . substr(md5(time()), 0, 16);
    }
    
    /**
     * Get HTML lang attribute
     */
    public function getHtmlLang() {
        $lang_map = [
            'en' => 'en',
            'tr' => 'tr',
            'de' => 'de',
            'fr' => 'fr',
            'es' => 'es'
        ];
        
        return $lang_map[$this->current_language] ?? 'en';
    }
    
    /**
     * Get meta language content
     */
    public function getMetaLanguage() {
        return $this->current_language;
    }
    
    /**
     * Get hreflang links for SEO
     */
    public function getHreflangLinks($base_url = '') {
        $links = [];
        
        foreach ($this->available_languages as $lang) {
            $href = $this->getUrlForLanguage($lang);
            $links[] = '<link rel="alternate" href="' . htmlspecialchars($href) . '" hreflang="' . $lang . '">';
        }
        
        // Add x-default
        $default_href = $this->getUrlForLanguage($this->default_language);
        $links[] = '<link rel="alternate" href="' . htmlspecialchars($default_href) . '" hreflang="x-default">';
        
        return implode("\n", $links);
    }
    
    /**
     * Get canonical URL for current page
     */
    public function getCanonicalUrl() {
        return $this->getUrlForLanguage($this->current_language);
    }
    
    /**
     * Get current URL
     */
    private function getCurrentUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        return $protocol . '://' . $host . $uri;
    }
    
    /**
     * Build language-specific URL
     */
    private function buildLanguageUrl($base_url, $lang) {
        $parsed = parse_url($base_url);
        $host = $parsed['host'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scheme = $parsed['scheme'] ?? (isset($_SERVER['HTTPS']) ? 'https' : 'http');
        $path = $parsed['path'] ?? '/';
        $query = $parsed['query'] ?? '';
        
        // Remove existing language segment if present
        $segments = explode('/', trim($path, '/'));
        $clean_segments = [];
        
        foreach ($segments as $segment) {
            if (!in_array($segment, $this->available_languages)) {
                $clean_segments[] = $segment;
            }
        }
        
        // Build new path with language prefix
        // Check if we have mytransfers in the path
        if (!empty($clean_segments) && $clean_segments[0] === 'mytransfers') {
            // Keep mytransfers first, then add language
            $new_path = '/mytransfers/' . $lang;
            // Add remaining segments if any
            $remaining_segments = array_slice($clean_segments, 1);
            if (!empty($remaining_segments)) {
                $new_path .= '/' . implode('/', $remaining_segments);
            }
        } else {
            // For root level or other structures
            $new_path = '/' . $lang;
            if (!empty($clean_segments)) {
                $new_path .= '/' . implode('/', $clean_segments);
            }
        }
        
        // Rebuild URL
        $new_url = $scheme . '://' . $host . $new_path;
        if ($query) {
            $new_url .= '?' . $query;
        }
        
        return $new_url;
    }
    
    /**
     * Get current URL without language segment
     */
    public function getCurrentUrlWithoutLang() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $query = parse_url($uri, PHP_URL_QUERY);
        
        // Remove language segment from path
        $segments = explode('/', trim($path, '/'));
        $clean_segments = [];
        
        foreach ($segments as $segment) {
            if (!in_array($segment, $this->available_languages)) {
                $clean_segments[] = $segment;
            }
        }
        
        $clean_path = '/' . implode('/', $clean_segments);
        $url = $protocol . '://' . $host . $clean_path;
        
        if ($query) {
            $url .= '?' . $query;
        }
        
        return $url;
    }
    
    /**
     * Get URL for specific language
     */
    public function getUrlForLanguage($lang) {
        if (!in_array($lang, $this->available_languages)) {
            return $this->getCurrentUrlWithoutLang();
        }
        
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $query = parse_url($uri, PHP_URL_QUERY);
        
        // Remove existing language segment from path
        $segments = explode('/', trim($path, '/'));
        $clean_segments = [];
        
        foreach ($segments as $segment) {
            if (!in_array($segment, $this->available_languages)) {
                $clean_segments[] = $segment;
            }
        }
        
        // Build new path with requested language
        // Check if we have mytransfers in the path
        if (!empty($clean_segments) && $clean_segments[0] === 'mytransfers') {
            // Keep mytransfers first, then add language
            $new_path = '/mytransfers/' . $lang;
            // Add remaining segments if any
            $remaining_segments = array_slice($clean_segments, 1);
            if (!empty($remaining_segments)) {
                $new_path .= '/' . implode('/', $remaining_segments);
            }
        } else {
            // For root level or other structures
            $new_path = '/' . $lang;
            if (!empty($clean_segments)) {
                $new_path .= '/' . implode('/', $clean_segments);
            }
        }
        
        $url = $protocol . '://' . $host . $new_path;
        
        if ($query) {
            $url .= '?' . $query;
        }
        
        return $url;
    }
    
    /**
     * Get JavaScript language configuration
     */
    public function getJavaScriptConfig() {
        return [
            'current_lang' => $this->current_language,
            'available_languages' => $this->available_languages,
            'lang_file' => $this->getLanguageFileWithVersion()
        ];
    }
    
    /**
     * Get meta content for og:locale
     */
    public function getOgLocale() {
        $locale_map = [
            'en' => 'en_US',
            'tr' => 'tr_TR',
            'de' => 'de_DE',
            'fr' => 'fr_FR',
            'es' => 'es_ES'
        ];
        
        return $locale_map[$this->current_language] ?? 'en_US';
    }
    
    /**
     * Set language (for programmatic changes)
     */
    public function setLanguage($lang) {
        if (in_array($lang, $this->available_languages)) {
            $this->current_language = $lang;
            
            // Set cookie
            $expires = time() + (365 * 24 * 60 * 60); // 1 year
            setcookie('site_language', $lang, $expires, '/');
            
            // Set session
            $_SESSION['site_language'] = $lang;
            
            return true;
        }
        return false;
    }
    
    /**
     * Get language detection information (for debugging)
     */
    public function getDetectionInfo() {
        $ip = $this->getUserIP();
        $country_code = null;
        $ip_detected_lang = null;
        
        if (!$this->isLocalIP($ip)) {
            $cache_key = 'ip_country_' . md5($ip);
            if (isset($_SESSION[$cache_key])) {
                $country_code = $_SESSION[$cache_key];
            } else {
                try {
                    $country_code = $this->getCountryFromIP($ip);
                    $_SESSION[$cache_key] = $country_code;
                } catch (Exception $e) {
                    // Ignore
                }
            }
            
            if ($country_code && isset($this->country_to_language[$country_code])) {
                $ip_detected_lang = $this->country_to_language[$country_code];
            }
        }
        
        return [
            'current_language' => $this->current_language,
            'user_ip' => $ip,
            'is_local_ip' => $this->isLocalIP($ip),
            'detected_country' => $country_code,
            'ip_detected_language' => $ip_detected_lang,
            'browser_language' => $this->getBrowserLanguage(),
            'has_url_lang' => isset($_GET['lang']),
            'has_cookie' => isset($_COOKIE['site_language']),
            'has_session' => isset($_SESSION['site_language'])
        ];
    }
}

// Initialize the language loader
$lang_loader = new LanguageLoader();
?>
