<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/language_loader.php';
require_once __DIR__ . '/includes/currency_manager.php';

// Dil yükleyici
$lang_loader = new LanguageLoader();
$current_lang = $lang_loader->getCurrentLanguage();

// Para birimi yöneticisi
$currency_manager = new CurrencyManager();

// URL'den para birimi kodunu al
$currency_code = $_GET['code'] ?? '';

if (!empty($currency_code)) {
    // Para birimini değiştir
    $currency_manager->setCurrency(strtoupper($currency_code));
}

// Referer URL'yi al veya varsayılan olarak ana sayfaya yönlendir
$referer = $_SERVER['HTTP_REFERER'] ?? '/mytransfers/';

// Eğer referer yoksa veya geçersizse, dil bazlı ana sayfaya yönlendir
if (empty($referer) || !filter_var($referer, FILTER_VALIDATE_URL)) {
    $redirect_url = '/mytransfers/' . $current_lang . '/';
} else {
    // Referer URL'den para birimi kısmını çıkar
    $redirect_url = preg_replace('/\/currency\/[A-Z]{3}\/?/', '/', $referer);
    
    // URL'nin sonunda / yoksa ekle
    if (!preg_match('/\/$/', $redirect_url)) {
        $redirect_url .= '/';
    }
    
    // Eğer URL'de dil kısmı yoksa ekle
    if (!preg_match('/\/' . $current_lang . '\//', $redirect_url)) {
        $redirect_url = '/mytransfers/' . $current_lang . '/';
    }
}

// Yönlendir
header('Location: ' . $redirect_url);
exit;
?>
