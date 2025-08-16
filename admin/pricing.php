<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

// Fiyatlandırma dosyasının yolu
$pricingFile = $STORAGE . '/pricing.json';

// Mevcut fiyatlandırma verilerini yükle
$pricing = [];
if (is_file($pricingFile)) {
    $pricing = json_decode(file_get_contents($pricingFile), true) ?: [];
}

// POST işlemi - fiyatlandırma kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $newPricing = [];
    
    // Transfer fiyatları
    $newPricing['transfers'] = [
        'economy' => [
            'base_price' => floatval($_POST['economy_base_price'] ?? 0),
            'per_km' => floatval($_POST['economy_per_km'] ?? 0),
            'min_price' => floatval($_POST['economy_min_price'] ?? 0),
            'max_price' => floatval($_POST['economy_max_price'] ?? 0)
        ],
        'standard' => [
            'base_price' => floatval($_POST['standard_base_price'] ?? 0),
            'per_km' => floatval($_POST['standard_per_km'] ?? 0),
            'min_price' => floatval($_POST['standard_min_price'] ?? 0),
            'max_price' => floatval($_POST['standard_max_price'] ?? 0)
        ],
        'premium' => [
            'base_price' => floatval($_POST['premium_base_price'] ?? 0),
            'per_km' => floatval($_POST['premium_per_km'] ?? 0),
            'min_price' => floatval($_POST['premium_min_price'] ?? 0),
            'max_price' => floatval($_POST['premium_max_price'] ?? 0)
        ],
        'vip' => [
            'base_price' => floatval($_POST['vip_base_price'] ?? 0),
            'per_km' => floatval($_POST['vip_per_km'] ?? 0),
            'min_price' => floatval($_POST['vip_min_price'] ?? 0),
            'max_price' => floatval($_POST['vip_max_price'] ?? 0)
        ],
        'minibus' => [
            'base_price' => floatval($_POST['minibus_base_price'] ?? 0),
            'per_km' => floatval($_POST['minibus_per_km'] ?? 0),
            'min_price' => floatval($_POST['minibus_min_price'] ?? 0),
            'max_price' => floatval($_POST['minibus_max_price'] ?? 0)
        ],
        'coach' => [
            'base_price' => floatval($_POST['coach_base_price'] ?? 0),
            'per_km' => floatval($_POST['coach_per_km'] ?? 0),
            'min_price' => floatval($_POST['coach_min_price'] ?? 0),
            'max_price' => floatval($_POST['coach_max_price'] ?? 0)
        ]
    ];
    
    // Özel rota fiyatları
    $newPricing['special_routes'] = [];
    if (isset($_POST['special_routes']) && is_array($_POST['special_routes'])) {
        foreach ($_POST['special_routes'] as $route) {
            if (!empty($route['from']) && !empty($route['to'])) {
                $newPricing['special_routes'][] = [
                    'from' => $route['from'],
                    'to' => $route['to'],
                    'economy' => floatval($route['economy'] ?? 0),
                    'standard' => floatval($route['standard'] ?? 0),
                    'premium' => floatval($route['premium'] ?? 0),
                    'vip' => floatval($route['vip'] ?? 0)
                ];
            }
        }
    }
    
    // Sezonluk fiyatlar
    $newPricing['seasonal_prices'] = [
        'high_season_multiplier' => floatval($_POST['high_season_multiplier'] ?? 1.2),
        'low_season_multiplier' => floatval($_POST['low_season_multiplier'] ?? 0.8),
        'high_season_months' => $_POST['high_season_months'] ?? [6, 7, 8],
        'low_season_months' => $_POST['low_season_months'] ?? [1, 2, 11, 12]
    ];
    
    // Fiyatlandırmayı kaydet
    write_json($pricingFile, $newPricing);
    
    header('Location: /mytransfers/admin/pricing.php?success=1');
    exit;
}

ob_start();
?>

<div class="admin-card">
    <h3>Fiyatlandırma Yönetimi</h3>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Fiyatlandırma başarıyla kaydedildi!</div>
    <?php endif; ?>
    
    <form method="post">
        <input class="admin-input" type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
        
        <!-- Transfer Fiyatları -->
        <div class="admin-form-section">
            <h4>Transfer Fiyatları</h4>
            <p class="text-muted">Araç sınıflarına göre temel fiyatlandırma ayarları</p>
            
            <!-- Ekonomik -->
            <div class="pricing-category">
                <h5>Ekonomik Araç</h5>
                <div class="pricing-grid">
                    <div class="pricing-item">
                        <label>Temel Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="economy_base_price" 
                               value="<?php echo $pricing['transfers']['economy']['base_price'] ?? 25; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>KM Başına (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="economy_per_km" 
                               value="<?php echo $pricing['transfers']['economy']['per_km'] ?? 0.5; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>Minimum Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="economy_min_price" 
                               value="<?php echo $pricing['transfers']['economy']['min_price'] ?? 20; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>Maksimum Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="economy_max_price" 
                               value="<?php echo $pricing['transfers']['economy']['max_price'] ?? 200; ?>" />
                    </div>
                </div>
            </div>
            
            <!-- Standart -->
            <div class="pricing-category">
                <h5>Standart Araç</h5>
                <div class="pricing-grid">
                    <div class="pricing-item">
                        <label>Temel Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="standard_base_price" 
                               value="<?php echo $pricing['transfers']['standard']['base_price'] ?? 35; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>KM Başına (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="standard_per_km" 
                               value="<?php echo $pricing['transfers']['standard']['per_km'] ?? 0.7; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>Minimum Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="standard_min_price" 
                               value="<?php echo $pricing['transfers']['standard']['min_price'] ?? 30; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>Maksimum Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="standard_max_price" 
                               value="<?php echo $pricing['transfers']['standard']['max_price'] ?? 300; ?>" />
                    </div>
                </div>
            </div>
            
            <!-- Premium -->
            <div class="pricing-category">
                <h5>Premium Araç</h5>
                <div class="pricing-grid">
                    <div class="pricing-item">
                        <label>Temel Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="premium_base_price" 
                               value="<?php echo $pricing['transfers']['premium']['base_price'] ?? 50; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>KM Başına (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="premium_per_km" 
                               value="<?php echo $pricing['transfers']['premium']['per_km'] ?? 1.0; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>Minimum Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="premium_min_price" 
                               value="<?php echo $pricing['transfers']['premium']['min_price'] ?? 45; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>Maksimum Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="premium_max_price" 
                               value="<?php echo $pricing['transfers']['premium']['max_price'] ?? 500; ?>" />
                    </div>
                </div>
            </div>
            
            <!-- VIP -->
            <div class="pricing-category">
                <h5>VIP Araç</h5>
                <div class="pricing-grid">
                    <div class="pricing-item">
                        <label>Temel Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="vip_base_price" 
                               value="<?php echo $pricing['transfers']['vip']['base_price'] ?? 80; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>KM Başına (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="vip_per_km" 
                               value="<?php echo $pricing['transfers']['vip']['per_km'] ?? 1.5; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>Minimum Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="vip_min_price" 
                               value="<?php echo $pricing['transfers']['vip']['min_price'] ?? 70; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>Maksimum Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="vip_max_price" 
                               value="<?php echo $pricing['transfers']['vip']['max_price'] ?? 800; ?>" />
                    </div>
                </div>
            </div>
            
            <!-- Minibus -->
            <div class="pricing-category">
                <h5>Minibus</h5>
                <div class="pricing-grid">
                    <div class="pricing-item">
                        <label>Temel Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="minibus_base_price" 
                               value="<?php echo $pricing['transfers']['minibus']['base_price'] ?? 65; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>KM Başına (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="minibus_per_km" 
                               value="<?php echo $pricing['transfers']['minibus']['per_km'] ?? 1.2; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>Minimum Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="minibus_min_price" 
                               value="<?php echo $pricing['transfers']['minibus']['min_price'] ?? 60; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>Maksimum Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="minibus_max_price" 
                               value="<?php echo $pricing['transfers']['minibus']['max_price'] ?? 600; ?>" />
                    </div>
                </div>
            </div>
            
            <!-- Coach -->
            <div class="pricing-category">
                <h5>Coach</h5>
                <div class="pricing-grid">
                    <div class="pricing-item">
                        <label>Temel Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="coach_base_price" 
                               value="<?php echo $pricing['transfers']['coach']['base_price'] ?? 150; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>KM Başına (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="coach_per_km" 
                               value="<?php echo $pricing['transfers']['coach']['per_km'] ?? 2.0; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>Minimum Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="coach_min_price" 
                               value="<?php echo $pricing['transfers']['coach']['min_price'] ?? 120; ?>" />
                    </div>
                    <div class="pricing-item">
                        <label>Maksimum Fiyat (€)</label>
                        <input class="admin-input" type="number" step="0.01" 
                               name="coach_max_price" 
                               value="<?php echo $pricing['transfers']['coach']['max_price'] ?? 1000; ?>" />
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sezonluk Fiyatlar -->
        <div class="admin-form-section">
            <h4>Sezonluk Fiyatlandırma</h4>
            <p class="text-muted">Yüksek ve düşük sezon için fiyat çarpanları</p>
            
            <div class="pricing-grid">
                <div class="pricing-item">
                    <label>Yüksek Sezon Çarpanı</label>
                    <input class="admin-input" type="number" step="0.1" 
                           name="high_season_multiplier" 
                           value="<?php echo $pricing['seasonal_prices']['high_season_multiplier'] ?? 1.2; ?>" />
                    <small>Örn: 1.2 = %20 artış</small>
                </div>
                <div class="pricing-item">
                    <label>Düşük Sezon Çarpanı</label>
                    <input class="admin-input" type="number" step="0.1" 
                           name="low_season_multiplier" 
                           value="<?php echo $pricing['seasonal_prices']['low_season_multiplier'] ?? 0.8; ?>" />
                    <small>Örn: 0.8 = %20 indirim</small>
                </div>
            </div>
            
            <div class="seasonal-months">
                <h5>Yüksek Sezon Ayları</h5>
                <div class="month-checkboxes">
                    <?php 
                    $months = [
                        1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
                        5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
                        9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
                    ];
                    $highSeasonMonths = $pricing['seasonal_prices']['high_season_months'] ?? [6, 7, 8];
                    foreach ($months as $monthNum => $monthName): ?>
                        <label class="month-checkbox">
                            <input type="checkbox" name="high_season_months[]" 
                                   value="<?php echo $monthNum; ?>" 
                                   <?php echo in_array($monthNum, $highSeasonMonths) ? 'checked' : ''; ?> />
                            <?php echo $monthName; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 16px;">
            <button class="admin-btn" type="submit">Fiyatlandırmayı Kaydet</button>
        </div>
    </form>
</div>

<style>
.pricing-category {
    margin-bottom: 24px;
    padding: 16px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: white;
}

.pricing-category h5 {
    margin: 0 0 16px 0;
    color: #333;
    font-size: 16px;
    font-weight: 600;
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.pricing-item {
    display: flex;
    flex-direction: column;
}

.pricing-item label {
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
    font-weight: 500;
}

.pricing-item small {
    font-size: 12px;
    color: #999;
    margin-top: 4px;
}

.seasonal-months {
    margin-top: 20px;
}

.seasonal-months h5 {
    margin: 0 0 12px 0;
    color: #333;
    font-size: 14px;
    font-weight: 600;
}

.month-checkboxes {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 8px;
}

.month-checkbox {
    display: flex;
    align-items: center;
    font-size: 14px;
    color: #666;
    cursor: pointer;
}

.month-checkbox input[type="checkbox"] {
    margin-right: 8px;
}

.text-muted {
    color: #666;
    font-size: 14px;
    margin-bottom: 16px;
}

/* Dark theme support */
body.theme-dark .pricing-category {
    background: #2d3748;
    border-color: #4a5568;
    color: #e2e8f0;
}

body.theme-dark .pricing-category h5 {
    color: #f7fafc;
}

body.theme-dark .pricing-item label {
    color: #a0aec0;
}

body.theme-dark .month-checkbox {
    color: #a0aec0;
}

body.theme-dark .text-muted {
    color: #a0aec0;
}
</style>

<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);
?>


