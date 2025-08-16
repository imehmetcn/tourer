<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

$langDir = __DIR__ . '/../assets/mytransfersweb/prod/js/lang/';
$translationsFile = $STORAGE . '/translations.json';

$languages = [
    'en' => 'English',
    'tr' => 'Türkçe', 
    'de' => 'Deutsch',
    'fr' => 'Français',
    'es' => 'Español'
];

function extractTranslationsFromJS($filePath) {
    if (!is_file($filePath)) {
        return [];
    }
    $content = file_get_contents($filePath);
    if (preg_match('/window\.__mt\.ln=(\{.*?\});/s', $content, $matches)) {
        $jsonStr = $matches[1];
        $jsonStr = preg_replace('/(\w+):/i', '"$1":', $jsonStr);
        $jsonStr = preg_replace('/,\s*}/', '}', $jsonStr);
        $translations = json_decode($jsonStr, true);
        return $translations ?: [];
    }
    return [];
}

// AJAX isteği kontrolü
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Önce mevcut translations.json dosyasını kontrol et
$existingTranslations = [];
if (is_file($translationsFile)) {
    $existingTranslations = json_decode(file_get_contents($translationsFile), true) ?: [];
}

if (!empty($existingTranslations)) {
    if ($isAjax) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Mevcut çeviri verileri bulundu!',
            'file' => $translationsFile,
            'languages' => array_keys($existingTranslations)
        ]);
    } else {
        echo "✅ Mevcut çeviri verileri bulundu!<br>";
        echo "📁 Dosya: " . $translationsFile . "<br>";
        echo "🌍 Mevcut diller: " . implode(', ', array_keys($existingTranslations)) . "<br>";
        echo "<br><a href='/mytransfers/admin/translations.php'>← Çeviri Yönetimi Sayfasına Dön</a>";
    }
    exit;
}

// Eğer translations.json yoksa, JS dosyalarından çıkarmaya çalış
$allTranslations = [];
foreach ($languages as $langCode => $langName) {
    $jsFile = $langDir . $langCode . '.js';
    $translations = extractTranslationsFromJS($jsFile);
    if (!empty($translations)) {
        $allTranslations[$langCode] = $translations;
    }
}

if (!empty($allTranslations)) {
    write_json($translationsFile, $allTranslations);
    if ($isAjax) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Çeviriler başarıyla yüklendi!',
            'file' => $translationsFile,
            'languages' => array_keys($allTranslations)
        ]);
    } else {
        echo "✅ Çeviriler başarıyla yüklendi!<br>";
        echo "📁 Kaydedilen dosya: " . $translationsFile . "<br>";
        echo "🌍 Yüklenen diller: " . implode(', ', array_keys($allTranslations)) . "<br>";
        echo "<br><a href='/mytransfers/admin/translations.php'>← Çeviri Yönetimi Sayfasına Dön</a>";
    }
} else {
    if ($isAjax) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Hiç çeviri verisi bulunamadı!'
        ]);
    } else {
        echo "❌ Hiç çeviri verisi bulunamadı!<br>";
        echo "💡 JS dosyalarında genel çeviri verileri (continue, pay, loading, vb.) bulunamadı.<br>";
        echo "📝 Sadece ülke ve havalimanı verileri mevcut.<br>";
        echo "<br><a href='/mytransfers/admin/translations.php'>← Çeviri Yönetimi Sayfasına Dön</a>";
    }
}
?>
