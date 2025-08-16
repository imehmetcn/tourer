<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

// Para birimi dosyasının yolu
$currencyFile = $STORAGE . '/currency.json';

// Mevcut para birimi verilerini yükle
$currencyData = [];
if (is_file($currencyFile)) {
    $currencyData = json_decode(file_get_contents($currencyFile), true) ?: [];
}

// Varsayılan para birimi verileri (eğer dosya yoksa)
if (empty($currencyData)) {
    $currencyData = [
        'default_currency' => 'EUR',
        'currencies' => [
            'USD' => [
                'name' => 'US Dollar',
                'symbol' => '$',
                'rate' => 1.08,
                'active' => true,
                'position' => 'before'
            ],
            'EUR' => [
                'name' => 'Euro',
                'symbol' => '€',
                'rate' => 1.0,
                'active' => true,
                'position' => 'before'
            ],
            'GBP' => [
                'name' => 'British Pound',
                'symbol' => '£',
                'rate' => 0.85,
                'active' => true,
                'position' => 'before'
            ],
            'TRY' => [
                'name' => 'Turkish Lira',
                'symbol' => '₺',
                'rate' => 32.5,
                'active' => true,
                'position' => 'after'
            ],
            'AUD' => [
                'name' => 'Australian Dollar',
                'symbol' => 'A$',
                'rate' => 1.65,
                'active' => true,
                'position' => 'before'
            ],
            'CAD' => [
                'name' => 'Canadian Dollar',
                'symbol' => 'C$',
                'rate' => 1.48,
                'active' => true,
                'position' => 'before'
            ],
            'CHF' => [
                'name' => 'Swiss Franc',
                'symbol' => 'CHF',
                'rate' => 0.95,
                'active' => true,
                'position' => 'before'
            ],
            'JPY' => [
                'name' => 'Japanese Yen',
                'symbol' => '¥',
                'rate' => 160.0,
                'active' => true,
                'position' => 'before'
            ]
        ],
        'auto_update' => true,
        'update_interval' => 24, // saat
        'last_update' => null
    ];
}

// POST işlemi - para birimi kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'save') {
            // Para birimi verilerini güncelle
            $currencyData['default_currency'] = $_POST['default_currency'];
            $currencyData['auto_update'] = isset($_POST['auto_update']);
            $currencyData['update_interval'] = intval($_POST['update_interval']);
            
            // Para birimlerini güncelle
            $newCurrencies = [];
            foreach ($_POST['currencies'] as $code => $currency) {
                $newCurrencies[$code] = [
                    'name' => trim($currency['name']),
                    'symbol' => trim($currency['symbol']),
                    'rate' => floatval($currency['rate']),
                    'active' => isset($currency['active']),
                    'position' => $currency['position']
                ];
            }
            $currencyData['currencies'] = $newCurrencies;
            
        } elseif ($_POST['action'] === 'add') {
            // Yeni para birimi ekle
            $newCode = strtoupper(trim($_POST['new_code']));
            if (!empty($newCode) && !isset($currencyData['currencies'][$newCode])) {
                $currencyData['currencies'][$newCode] = [
                    'name' => trim($_POST['new_name']),
                    'symbol' => trim($_POST['new_symbol']),
                    'rate' => floatval($_POST['new_rate']),
                    'active' => isset($_POST['new_active']),
                    'position' => $_POST['new_position']
                ];
            }
            
        } elseif ($_POST['action'] === 'delete') {
            // Para birimi sil
            $code = $_POST['code'];
            if (isset($currencyData['currencies'][$code]) && $code !== $currencyData['default_currency']) {
                unset($currencyData['currencies'][$code]);
            }
            
        } elseif ($_POST['action'] === 'update_rates') {
            // Döviz kurlarını güncelle
            $currencyData['last_update'] = date('Y-m-d H:i:s');
            // Burada gerçek API çağrısı yapılabilir
        }
    }
    
    // Para birimi verilerini kaydet
    write_json($currencyFile, $currencyData);
    
    header('Location: /mytransfers/admin/currency.php?success=1');
    exit;
}

ob_start();
?>

<div class="admin-card">
    <h3>Para Birimi Yönetimi</h3>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Para birimi ayarları başarıyla kaydedildi!</div>
    <?php endif; ?>
    
    <form method="post">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
        <input type="hidden" name="action" value="save" />
        
        <!-- Genel Ayarlar -->
        <div class="admin-form-section">
            <h4>Genel Ayarlar</h4>
            <p class="text-muted">Varsayılan para birimi ve otomatik güncelleme ayarları</p>
            
            <div class="form-grid">
                <div class="form-item">
                    <label>Varsayılan Para Birimi</label>
                    <select class="admin-input" name="default_currency">
                        <?php foreach ($currencyData['currencies'] as $code => $currency): ?>
                        <option value="<?php echo $code; ?>" <?php echo $code === $currencyData['default_currency'] ? 'selected' : ''; ?>>
                            <?php echo $code; ?> - <?php echo $currency['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-item">
                    <label>Otomatik Kur Güncelleme</label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="auto_update" <?php echo $currencyData['auto_update'] ? 'checked' : ''; ?> />
                        Aktif
                    </label>
                </div>
                
                <div class="form-item">
                    <label>Güncelleme Aralığı (Saat)</label>
                    <input class="admin-input" type="number" name="update_interval" 
                           value="<?php echo $currencyData['update_interval']; ?>" min="1" max="168" />
                </div>
            </div>
            
            <?php if ($currencyData['last_update']): ?>
            <div class="form-item">
                <small class="text-muted">Son güncelleme: <?php echo $currencyData['last_update']; ?></small>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Mevcut Para Birimleri -->
        <div class="admin-form-section">
            <h4>Para Birimleri</h4>
            <p class="text-muted">Para birimlerini düzenleyin ve döviz kurlarını yönetin</p>
            
            <?php foreach ($currencyData['currencies'] as $code => $currency): ?>
            <div class="currency-card">
                <div class="currency-header">
                    <h5><?php echo $code; ?> - <?php echo htmlspecialchars($currency['name']); ?></h5>
                    <div class="currency-actions">
                        <label class="checkbox-label">
                            <input type="checkbox" name="currencies[<?php echo $code; ?>][active]" 
                                   <?php echo $currency['active'] ? 'checked' : ''; ?> />
                            Aktif
                        </label>
                        <?php if ($code !== $currencyData['default_currency']): ?>
                        <button type="button" class="btn-small btn-delete" 
                                onclick="deleteCurrency('<?php echo $code; ?>')">
                            <i class='bx bx-trash'></i> Sil
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="currency-content">
                    <div class="form-grid">
                        <div class="form-item">
                            <label>Para Birimi Adı</label>
                            <input class="admin-input" type="text" 
                                   name="currencies[<?php echo $code; ?>][name]" 
                                   value="<?php echo htmlspecialchars($currency['name']); ?>" required />
                        </div>
                        
                        <div class="form-item">
                            <label>Sembol</label>
                            <input class="admin-input" type="text" 
                                   name="currencies[<?php echo $code; ?>][symbol]" 
                                   value="<?php echo htmlspecialchars($currency['symbol']); ?>" required />
                        </div>
                        
                        <div class="form-item">
                            <label>Döviz Kuru (EUR bazında)</label>
                            <input class="admin-input" type="number" step="0.0001" 
                                   name="currencies[<?php echo $code; ?>][rate]" 
                                   value="<?php echo $currency['rate']; ?>" required />
                        </div>
                        
                        <div class="form-item">
                            <label>Sembol Pozisyonu</label>
                            <select class="admin-input" name="currencies[<?php echo $code; ?>][position]">
                                <option value="before" <?php echo $currency['position'] === 'before' ? 'selected' : ''; ?>>Önce (€100)</option>
                                <option value="after" <?php echo $currency['position'] === 'after' ? 'selected' : ''; ?>>Sonra (100₺)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Yeni Para Birimi Ekleme -->
        <div class="admin-form-section">
            <h4>Yeni Para Birimi Ekle</h4>
            <p class="text-muted">Yeni bir para birimi oluşturun</p>
            
            <div class="form-grid">
                <div class="form-item">
                    <label>Para Birimi Kodu *</label>
                    <input class="admin-input" type="text" name="new_code" 
                           placeholder="Örn: CNY" maxlength="3" required />
                </div>
                
                <div class="form-item">
                    <label>Para Birimi Adı *</label>
                    <input class="admin-input" type="text" name="new_name" 
                           placeholder="Örn: Chinese Yuan" required />
                </div>
                
                <div class="form-item">
                    <label>Sembol *</label>
                    <input class="admin-input" type="text" name="new_symbol" 
                           placeholder="Örn: ¥" required />
                </div>
                
                <div class="form-item">
                    <label>Döviz Kuru (EUR bazında) *</label>
                    <input class="admin-input" type="number" name="new_rate" 
                           step="0.0001" placeholder="7.85" required />
                </div>
                
                <div class="form-item">
                    <label>Sembol Pozisyonu</label>
                    <select class="admin-input" name="new_position">
                        <option value="before">Önce (€100)</option>
                        <option value="after">Sonra (100₺)</option>
                    </select>
                </div>
                
                <div class="form-item">
                    <label class="checkbox-label">
                        <input type="checkbox" name="new_active" checked />
                        Aktif
                    </label>
                </div>
            </div>
            
            <div style="margin-top: 16px;">
                <button class="admin-btn" type="submit" name="action" value="add">Yeni Para Birimi Ekle</button>
            </div>
        </div>
        
        <div style="margin-top: 24px;">
            <button class="admin-btn admin-btn-primary" type="submit">Tüm Değişiklikleri Kaydet</button>
            <button class="admin-btn" type="submit" name="action" value="update_rates" style="margin-left: 10px;">
                <i class='bx bx-refresh'></i> Kurları Güncelle
            </button>
        </div>
    </form>
</div>

<style>
.currency-card {
    margin-bottom: 24px;
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: white;
}

.currency-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #f0f0f0;
}

.currency-header h5 {
    margin: 0;
    color: #333;
    font-size: 18px;
    font-weight: 600;
}

.currency-actions {
    display: flex;
    align-items: center;
    gap: 16px;
}

.currency-content {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.form-item {
    display: flex;
    flex-direction: column;
}

.form-item label {
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
    font-weight: 500;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    margin-right: 8px;
}

.btn-small {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

.admin-btn-primary {
    background: #007bff;
    color: white;
}

.admin-btn-primary:hover {
    background: #0056b3;
}

/* Dark theme support */
body.theme-dark .currency-card {
    background: #2d3748;
    border-color: #4a5568;
    color: #e2e8f0;
}

body.theme-dark .currency-header h5 {
    color: #f7fafc;
}

body.theme-dark .currency-header {
    border-bottom-color: #4a5568;
}

body.theme-dark .form-item label {
    color: #a0aec0;
}
</style>

<script>
function deleteCurrency(code) {
    if (confirm('Bu para birimini silmek istediğinizden emin misiniz?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
            <input type="hidden" name="action" value="delete" />
            <input type="hidden" name="code" value="${code}" />
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);
?>
