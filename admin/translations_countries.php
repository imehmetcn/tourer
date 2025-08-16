<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

// Çeviri dosyalarının yolu
$langDir = __DIR__ . '/../assets/mytransfersweb/prod/js/lang/';
$translationsFile = $STORAGE . '/translations.json';

// Mevcut diller
$languages = [
    'en' => 'English',
    'tr' => 'Türkçe', 
    'de' => 'Deutsch',
    'fr' => 'Français',
    'es' => 'Español'
];

// Mevcut çevirileri yükle
$translations = [];
if (is_file($translationsFile)) {
    $translations = json_decode(file_get_contents($translationsFile), true) ?: [];
}

// Temel ülke verileri (İngilizce referans)
$baseCountries = [
    ['name' => 'Turkey', 'dial_code' => '+90', 'code' => 'TR'],
    ['name' => 'Israel', 'dial_code' => '+972', 'code' => 'IL'],
    ['name' => 'Germany', 'dial_code' => '+49', 'code' => 'DE'],
    ['name' => 'France', 'dial_code' => '+33', 'code' => 'FR'],
    ['name' => 'Spain', 'dial_code' => '+34', 'code' => 'ES'],
    ['name' => 'Italy', 'dial_code' => '+39', 'code' => 'IT'],
    ['name' => 'United Kingdom', 'dial_code' => '+44', 'code' => 'GB'],
    ['name' => 'United States', 'dial_code' => '+1', 'code' => 'US'],
    ['name' => 'Netherlands', 'dial_code' => '+31', 'code' => 'NL'],
    ['name' => 'Belgium', 'dial_code' => '+32', 'code' => 'BE'],
    ['name' => 'Austria', 'dial_code' => '+43', 'code' => 'AT'],
    ['name' => 'Switzerland', 'dial_code' => '+41', 'code' => 'CH'],
    ['name' => 'Poland', 'dial_code' => '+48', 'code' => 'PL'],
    ['name' => 'Czech Republic', 'dial_code' => '+420', 'code' => 'CZ'],
    ['name' => 'Hungary', 'dial_code' => '+36', 'code' => 'HU'],
    ['name' => 'Romania', 'dial_code' => '+40', 'code' => 'RO'],
    ['name' => 'Bulgaria', 'dial_code' => '+359', 'code' => 'BG'],
    ['name' => 'Greece', 'dial_code' => '+30', 'code' => 'GR'],
    ['name' => 'Croatia', 'dial_code' => '+385', 'code' => 'HR'],
    ['name' => 'Slovenia', 'dial_code' => '+386', 'code' => 'SI'],
    ['name' => 'Slovakia', 'dial_code' => '+421', 'code' => 'SK'],
    ['name' => 'Lithuania', 'dial_code' => '+370', 'code' => 'LT'],
    ['name' => 'Latvia', 'dial_code' => '+371', 'code' => 'LV'],
    ['name' => 'Estonia', 'dial_code' => '+372', 'code' => 'EE'],
    ['name' => 'Finland', 'dial_code' => '+358', 'code' => 'FI'],
    ['name' => 'Sweden', 'dial_code' => '+46', 'code' => 'SE'],
    ['name' => 'Norway', 'dial_code' => '+47', 'code' => 'NO'],
    ['name' => 'Denmark', 'dial_code' => '+45', 'code' => 'DK'],
    ['name' => 'Iceland', 'dial_code' => '+354', 'code' => 'IS'],
    ['name' => 'Ireland', 'dial_code' => '+353', 'code' => 'IE'],
    ['name' => 'Portugal', 'dial_code' => '+351', 'code' => 'PT'],
    ['name' => 'Luxembourg', 'dial_code' => '+352', 'code' => 'LU'],
    ['name' => 'Malta', 'dial_code' => '+356', 'code' => 'MT'],
    ['name' => 'Cyprus', 'dial_code' => '+537', 'code' => 'CY']
];

// Her dil için ülke verilerini oluştur
function getCountriesForLanguage($langCode, $translations, $baseCountries) {
    $countries = [];
    foreach ($baseCountries as $baseCountry) {
        $countryName = $baseCountry['name']; // Varsayılan İngilizce isim
        
        // Eğer bu dil için çeviri varsa kullan
        if (isset($translations[$langCode]['codes_phone'])) {
            foreach ($translations[$langCode]['codes_phone'] as $phoneCountry) {
                if ($phoneCountry['code'] === $baseCountry['code']) {
                    $countryName = $phoneCountry['name'];
                    break;
                }
            }
        }
        
        $countries[] = [
            'name' => $countryName,
            'dial_code' => $baseCountry['dial_code'],
            'code' => $baseCountry['code']
        ];
    }
    return $countries;
}

// Temel havalimanı verileri (İngilizce referans)
$baseAirports = [
    [
        'country' => 'Italy',
        'country_code' => 'IT',
        'airports' => [
            ['name' => 'Ancona Airport', 'code' => 'AOI'],
            ['name' => 'Verona Airport', 'code' => 'VRN'],
            ['name' => 'Venice Treviso Airport', 'code' => 'TSF'],
            ['name' => 'Venice Marco Polo Airport', 'code' => 'VCE'],
            ['name' => 'Rome Fiumicino Airport', 'code' => 'FCO'],
            ['name' => 'Milan Malpensa Airport', 'code' => 'MXP'],
            ['name' => 'Milan Linate Airport', 'code' => 'LIN'],
            ['name' => 'Naples Airport', 'code' => 'NAP'],
            ['name' => 'Florence Airport', 'code' => 'FLR'],
            ['name' => 'Bologna Airport', 'code' => 'BLQ']
        ]
    ],
    [
        'country' => 'Germany',
        'country_code' => 'DE',
        'airports' => [
            ['name' => 'Berlin Central Train Station', 'code' => 'BER'],
            ['name' => 'Stuttgart Airport', 'code' => 'STR'],
            ['name' => 'Nuremberg Airport', 'code' => 'NUE'],
            ['name' => 'Munich Airport', 'code' => 'MUC'],
            ['name' => 'Frankfurt Airport', 'code' => 'FRA'],
            ['name' => 'Düsseldorf Airport', 'code' => 'DUS'],
            ['name' => 'Hamburg Airport', 'code' => 'HAM'],
            ['name' => 'Cologne Airport', 'code' => 'CGN'],
            ['name' => 'Berlin Tegel Airport', 'code' => 'TXL'],
            ['name' => 'Leipzig Airport', 'code' => 'LEJ']
        ]
    ],
    [
        'country' => 'Turkey',
        'country_code' => 'TR',
        'airports' => [
            ['name' => 'Istanbul Airport', 'code' => 'IST'],
            ['name' => 'Istanbul Sabiha Gökçen Airport', 'code' => 'SAW'],
            ['name' => 'Ankara Esenboğa Airport', 'code' => 'ESB'],
            ['name' => 'Antalya Airport', 'code' => 'AYT'],
            ['name' => 'İzmir Adnan Menderes Airport', 'code' => 'ADB'],
            ['name' => 'Bodrum Milas Airport', 'code' => 'BJV'],
            ['name' => 'Dalaman Airport', 'code' => 'DLM'],
            ['name' => 'Trabzon Airport', 'code' => 'TZX'],
            ['name' => 'Adana Şakirpaşa Airport', 'code' => 'ADA'],
            ['name' => 'Gaziantep Airport', 'code' => 'GZT']
        ]
    ]
];

// Her dil için havalimanı verilerini oluştur
function getAirportsForLanguage($langCode, $translations, $baseAirports) {
    $airports = [];
    foreach ($baseAirports as $baseCountryData) {
        $countryName = $baseCountryData['country']; // Varsayılan İngilizce ülke ismi
        
        // Ülke ismini çevir
        if (isset($translations[$langCode]['codes_phone'])) {
            foreach ($translations[$langCode]['codes_phone'] as $phoneCountry) {
                if ($phoneCountry['code'] === $baseCountryData['country_code']) {
                    $countryName = $phoneCountry['name'];
                    break;
                }
            }
        }
        
        $countryAirports = [];
        foreach ($baseCountryData['airports'] as $baseAirport) {
            $airportName = $baseAirport['name']; // Varsayılan İngilizce havalimanı ismi
            
            // Havalimanı ismini çevir
            if (isset($translations[$langCode]['places'])) {
                foreach ($translations[$langCode]['places'] as $place) {
                    if (strpos(strtolower($place['name']), strtolower($baseCountryData['country'])) !== false) {
                        foreach ($place['places'] as $placeAirport) {
                            if (strpos($placeAirport['url'], strtolower($baseAirport['code'])) !== false) {
                                $airportName = $placeAirport['name'];
                                break 2;
                            }
                        }
                        break;
                    }
                }
            }
            
            $countryAirports[] = [
                'name' => $airportName,
                'code' => $baseAirport['code']
            ];
        }
        
        $airports[] = [
            'country' => $countryName,
            'country_code' => $baseCountryData['country_code'],
            'airports' => $countryAirports
        ];
    }
    return $airports;
}

// POST işlemi - ülke çevirilerini kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $newTranslations = $translations;
    
    foreach ($languages as $langCode => $langName) {
        if (!isset($newTranslations[$langCode])) {
            $newTranslations[$langCode] = [];
        }
        
        // Ülke çevirileri
        $newTranslations[$langCode]['codes_phone'] = [];
        foreach ($countries as $country) {
            $postKey = "country_{$langCode}_{$country['code']}";
            $countryName = $_POST[$postKey] ?? $country['name'];
            
            $newTranslations[$langCode]['codes_phone'][] = [
                'name' => $countryName,
                'dial_code' => $country['dial_code'],
                'code' => $country['code']
            ];
        }
        
        // Ülke kodları mapping
        $newTranslations[$langCode]['format_codes'] = [];
        foreach ($countries as $country) {
            $postKey = "country_{$langCode}_{$country['code']}";
            $countryName = $_POST[$postKey] ?? $country['name'];
            
            $newTranslations[$langCode]['format_codes'][strtolower($country['code'])] = $countryName;
        }
        
        // Havalimanı çevirileri
        $newTranslations[$langCode]['places'] = [];
        foreach ($airports as $countryData) {
            $countryPlaces = [
                'name' => $_POST["country_{$langCode}_{$countryData['country_code']}"] ?? $countryData['country'],
                'url' => "//www.mytransfers.com/destination/" . strtolower($countryData['country']) . "/",
                'img' => "https://www.mytransfers.com/wp-content/uploads/2018/06/" . strtolower($countryData['country']) . "-1-400x267.jpg",
                'places' => []
            ];
            
            foreach ($countryData['airports'] as $airport) {
                $postKey = "airport_{$langCode}_{$airport['code']}";
                $airportName = $_POST[$postKey] ?? $airport['name'];
                
                $countryPlaces['places'][] = [
                    'name' => $airportName,
                    'url' => "//www.mytransfers.com/destination/" . strtolower($countryData['country']) . "/" . strtolower(str_replace(' ', '-', $airport['name'])) . "-" . strtolower($airport['code']) . "/"
                ];
            }
            
            $newTranslations[$langCode]['places'][] = $countryPlaces;
        }
    }
    
    // Çevirileri kaydet
    write_json($translationsFile, $newTranslations);
    
    // JavaScript dosyalarını güncelle
    foreach ($languages as $langCode => $langName) {
        generateLanguageFile($langCode, $newTranslations[$langCode], $langDir);
    }
    
    header('Location: /mytransfers/admin/translations_countries.php?success=1');
    exit;
}

// JavaScript dosyası oluşturma fonksiyonu
function generateLanguageFile($langCode, $translations, $langDir) {
    $content = "!function(e){var a={};function d(o){if(a[o])return a[o].exports;var n=a[o]={i:o,l:!1,exports:{}};return e[o].call(n.exports,n,n.exports,d),n.l=!0,n.exports}d.m=e,d.c=a,d.d=function(e,a,o){d.o(e,a)||Object.defineProperty(e,a,{enumerable:!0,get:o})},d.r=function(e){\"undefined\"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:\"Module\"}),Object.defineProperty(e,\"__esModule\",{value:!0})},d.t=function(e,a){if(1&a&&(e=d(e)),8&a)return e;if(4&a&&\"object\"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(d.r(o),Object.defineProperty(o,\"default\",{enumerable:!0,value:e}),2&a&&\"string\"!=typeof e)for(var n in e)d.d(o,n,function(a){return e[a]}.bind(null,n));return o},d.n=function(e){var a=e&&e.__esModule?function(){return e.default}:function(){return e};return d.d(a,\"a\",a),a},d.o=function(e,a){return Object.prototype.hasOwnProperty.call(e,a)},d.p=\"/\",d(d.s=20)}({\"1YU0\":function(e,a){window.__mt.ln=" . json_encode($translations) . ",window.__mt.setting.format={daysOfWeek:" . json_encode($translations['daysOfWeek'] ?? ['Su','Mo','Tu','We','Th','Fr','Sa']) . ",monthNames:" . json_encode($translations['monthNames'] ?? ['January','February','March','April','May','June','July','August','September','October','November','December']) . "},window.__mt.setting.payments=" . json_encode($translations['payments'] ?? [['name'=>'Credit or debit card','img'=>'/img/visa.png','description'=>'Pay with a credit card','code'=>'creditcard']]) . ",window.__mt.ln.places=" . json_encode($translations['places'] ?? []) . ",window.__mt.setting.codes_phone=" . json_encode($translations['codes_phone'] ?? []) . ",window.__mt.setting.format_codes=" . json_encode($translations['format_codes'] ?? []) . ",angular.module(\"ngLocale\",[],[\"\$provide\",function(e){var a=\"one\",d=\"other\";e.value(\"\$locale\",{DATETIME_FORMATS:{AMPMS:[\"am\",\"pm\"],DAY:[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\",\"Saturday\"],ERANAMES:[\"Before Christ\",\"Anno Domini\"],ERAS:[\"BC\",\"AD\"],FIRSTDAYOFWEEK:0,MONTH:" . json_encode($translations['monthNames'] ?? ['January','February','March','April','May','June','July','August','September','October','November','December']) . ",SHORTDAY:" . json_encode($translations['daysOfWeek'] ?? ['Su','Mo','Tu','We','Th','Fr','Sa']) . ",SHORTMONTH:" . json_encode($translations['monthNames'] ?? ['January','February','March','April','May','June','July','August','September','October','November','December']) . ",STANDALONEMONTH:" . json_encode($translations['monthNames'] ?? ['January','February','March','April','May','June','July','August','September','October','November','December']) . ",WEEKENDRANGE:[5,6],fullDate:\"EEEE, d MMMM y\",longDate:\"d MMMM y\",medium:\"d MMM y HH:mm:ss\",mediumDate:\"d MMM y\",mediumTime:\"HH:mm:ss\",short:\"dd/MM/y HH:mm\",shortDate:\"dd/MM/y\",shortTime:\"HH:mm\"},NUMBER_FORMATS:{CURRENCY_SYM:\"£\",DECIMAL_SEP:\".\",GROUP_SEP:\",\",PATTERNS:[{gSize:3,lgSize:3,maxFrac:3,minFrac:0,minInt:1,negPre:\"-\",negSuf:\"\",posPre:\"\",posSuf:\"\"},{gSize:3,lgSize:3,maxFrac:2,minFrac:2,minInt:1,negPre:\"-¤\",negSuf:\"\",posPre:\"¤\",posSuf:\"\"}]},id:\"{$langCode}-{$langCode}\",localeID:\"{$langCode}_{$langCode}\",pluralCat:function(e,o){var n=0|e,i=function(e,a){var d=a;void 0===d&&(d=Math.min(function(e){var a=(e+=\"\").indexOf(\".\");return-1==a?0:e.length-a-1}(e),3));var o=Math.pow(10,d);return{v:d,f:(e*o|0)%o}}(e,o);return 1==n&&0==i.v?a:d}})}])},20:function(e,a,d){e.exports=d(\"1YU0\")}});";
    
    file_put_contents($langDir . $langCode . '.js', $content);
}

ob_start();
?>

<div class="admin-card">
    <h3>Ülke ve Havalimanı Çevirileri</h3>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Ülke ve havalimanı çevirileri başarıyla kaydedildi!</div>
    <?php endif; ?>
    
    <div style="margin-bottom: 16px;">
        <a href="/mytransfers/admin/translations.php" class="admin-btn" style="background: #007bff; color: white; text-decoration: none; padding: 8px 16px; border-radius: 4px;">
            ← Genel Çeviriler
        </a>
        <span style="margin-left: 12px; color: #666; font-size: 14px;">
            Ülke isimleri ve havalimanı çevirilerini yönetin
        </span>
    </div>
    
    <form method="post">
        <input class="admin-input" type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
        
        <?php foreach ($languages as $langCode => $langName): ?>
            <?php 
            $countries = getCountriesForLanguage($langCode, $translations, $baseCountries);
            $airports = getAirportsForLanguage($langCode, $translations, $baseAirports);
            ?>
            <div class="admin-card" style="margin-top: 16px;">
                <h4><?php echo htmlspecialchars($langName); ?> (<?php echo strtoupper($langCode); ?>)</h4>
                
                <!-- Ülkeler -->
                <div class="admin-form-section">
                    <h5>Ülke İsimleri</h5>
                    <div class="countries-grid">
                        <?php foreach ($countries as $country): ?>
                            <div class="country-item">
                                <label><?php echo htmlspecialchars($country['name']); ?> (<?php echo $country['code']; ?>)</label>
                                <input class="admin-input" type="text" 
                                       name="country_<?php echo $langCode; ?>_<?php echo $country['code']; ?>" 
                                       value="<?php echo htmlspecialchars($country['name'], ENT_QUOTES); ?>" 
                                       placeholder="<?php echo $country['name']; ?>" />
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Havalimanları -->
                <div class="admin-form-section">
                    <h5>Havalimanları</h5>
                    <?php foreach ($airports as $countryData): ?>
                        <div class="airport-country">
                            <h6><?php echo htmlspecialchars($countryData['country']); ?> Havalimanları</h6>
                            <div class="airports-grid">
                                <?php foreach ($countryData['airports'] as $airport): ?>
                                    <div class="airport-item">
                                        <label><?php echo htmlspecialchars($airport['name']); ?> (<?php echo $airport['code']; ?>)</label>
                                        <input class="admin-input" type="text" 
                                               name="airport_<?php echo $langCode; ?>_<?php echo $airport['code']; ?>" 
                                               value="<?php echo htmlspecialchars($airport['name'], ENT_QUOTES); ?>" 
                                               placeholder="<?php echo $airport['name']; ?>" />
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div style="margin-top: 16px;">
            <button class="admin-btn" type="submit">Ülke ve Havalimanı Çevirilerini Kaydet</button>
        </div>
    </form>
</div>

<style>

.admin-form-section {
    margin-bottom: 24px;
    padding: 16px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: #f9f9f9;
}

.admin-form-section h5 {
    margin: 0 0 16px 0;
    color: #333;
    font-size: 14px;
    font-weight: 600;
}

.admin-form-section h6 {
    margin: 16px 0 12px 0;
    color: #555;
    font-size: 13px;
    font-weight: 600;
}

.countries-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 12px;
}

.country-item {
    display: flex;
    flex-direction: column;
}

.country-item label {
    font-size: 12px;
    color: #666;
    margin-bottom: 4px;
}

.airport-country {
    margin-bottom: 20px;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: white;
}

.airports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 8px;
}

.airport-item {
    display: flex;
    flex-direction: column;
}

.airport-item label {
    font-size: 12px;
    color: #666;
    margin-bottom: 4px;
}

.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 16px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

/* Dark theme support */
body.theme-dark .admin-form-section {
    background: #2d3748;
    border-color: #4a5568;
    color: #e2e8f0;
}

body.theme-dark .admin-form-section h5 {
    color: #f7fafc;
}

body.theme-dark .admin-form-section h6 {
    color: #f7fafc;
}



body.theme-dark .admin-card {
    background: #2d3748;
    border-color: #4a5568;
    color: #e2e8f0;
}

body.theme-dark .admin-input {
    background: #4a5568;
    border-color: #718096;
    color: #f7fafc;
}

body.theme-dark .admin-input:focus {
    border-color: #63b3ed;
    box-shadow: 0 0 0 2px rgba(99, 179, 237, 0.2);
}

body.theme-dark .admin-btn {
    background: #4a5568;
    color: #f7fafc;
    border-color: #718096;
}

body.theme-dark .admin-btn:hover {
    background: #718096;
}

body.theme-dark .alert-success {
    background: #22543d;
    color: #9ae6b4;
    border-color: #38a169;
}

body.theme-dark label {
    color: #e2e8f0;
}

body.theme-dark .countries-grid,
body.theme-dark .airports-grid {
    background: transparent;
}

body.theme-dark .country-item label,
body.theme-dark .airport-item label {
    color: #e2e8f0;
}

body.theme-dark .airport-country {
    background: #2d3748;
    border-color: #4a5568;
}
</style>



<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);
?>
