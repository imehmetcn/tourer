<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

// Araç sınıfları dosyasının yolu
$vehicleClassesFile = $STORAGE . '/vehicle_classes.json';

// Mevcut araç sınıflarını yükle
$vehicleClasses = [];
if (is_file($vehicleClassesFile)) {
    $vehicleClasses = json_decode(file_get_contents($vehicleClassesFile), true) ?: [];
}

// Varsayılan araç sınıfları (eğer dosya yoksa)
if (empty($vehicleClasses)) {
    $vehicleClasses = [
        'economy' => [
            'name' => 'Economy Car',
            'capacity' => 3,
            'luggage' => 2,
            'features' => ['Air Conditioning', 'Free Wi-Fi'],
            'description' => 'Comfortable economy vehicle for small groups',
            'active' => true
        ],
        'standard' => [
            'name' => 'Standard Car',
            'capacity' => 4,
            'luggage' => 3,
            'features' => ['Air Conditioning', 'Free Wi-Fi', 'Child Seat'],
            'description' => 'Reliable standard vehicle with child seat option',
            'active' => true
        ],
        'premium' => [
            'name' => 'Premium Van',
            'capacity' => 6,
            'luggage' => 4,
            'features' => ['Air Conditioning', 'Free Wi-Fi', 'Child Seat', 'Meet & Greet'],
            'description' => 'Premium van with meet & greet service',
            'active' => true
        ],
        'vip' => [
            'name' => 'VIP Limousine',
            'capacity' => 8,
            'luggage' => 6,
            'features' => ['Air Conditioning', 'Free Wi-Fi', 'Child Seat', 'Meet & Greet', 'Luggage Assistance'],
            'description' => 'Luxury VIP service with full assistance',
            'active' => true
        ],
        'minibus' => [
            'name' => 'Private Premium Minibus',
            'capacity' => 12,
            'luggage' => 12,
            'features' => ['Air Conditioning', 'Free Wi-Fi', 'Child Seat', 'Meet & Greet', 'Door-to-Door'],
            'description' => 'Private Premium Minibus service for up to 12 passengers and their baggage (max 12 medium suitcases). Please make sure you choose a vehicle with capacity for your group and luggage (normally 1 medium suitcase per passenger).',
            'active' => true
        ],
        'coach' => [
            'name' => 'Coach',
            'capacity' => 25,
            'luggage' => 15,
            'features' => ['Air Conditioning', 'Free Wi-Fi', 'Child Seat', 'Meet & Greet'],
            'description' => 'Large coach for big groups',
            'active' => true
        ]
    ];
}

// POST işlemi - araç sınıfı kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'save') {
            // Tüm araç sınıflarını güncelle
            $newVehicleClasses = [];
            
            foreach ($_POST['vehicle_classes'] as $classKey => $vehicleData) {
                $features = [];
                if (isset($_POST['features'][$classKey]) && is_array($_POST['features'][$classKey])) {
                    $features = array_filter($_POST['features'][$classKey]); // Boş değerleri temizle
                }
                
                $newVehicleClasses[$classKey] = [
                    'name' => trim($vehicleData['name']),
                    'capacity' => intval($vehicleData['capacity']),
                    'luggage' => intval($vehicleData['luggage']),
                    'features' => $features,
                    'description' => trim($vehicleData['description']),
                    'active' => isset($vehicleData['active'])
                ];
            }
            
            $vehicleClasses = $newVehicleClasses;
            
        } elseif ($_POST['action'] === 'add') {
            // Yeni araç sınıfı ekle
            $newClassKey = 'vehicle_' . uniqid();
            $features = [];
            if (isset($_POST['new_features']) && is_array($_POST['new_features'])) {
                $features = array_filter($_POST['new_features']);
            }
            
            $vehicleClasses[$newClassKey] = [
                'name' => trim($_POST['new_name']),
                'capacity' => intval($_POST['new_capacity']),
                'luggage' => intval($_POST['new_luggage']),
                'features' => $features,
                'description' => trim($_POST['new_description']),
                'active' => isset($_POST['new_active'])
            ];
            
        } elseif ($_POST['action'] === 'delete') {
            // Araç sınıfı sil
            $classKey = $_POST['class_key'];
            if (isset($vehicleClasses[$classKey])) {
                unset($vehicleClasses[$classKey]);
            }
        }
    }
    
    // Araç sınıflarını kaydet
    write_json($vehicleClassesFile, $vehicleClasses);
    
    header('Location: /mytransfers/admin/vehicle_classes.php?success=1');
    exit;
}

ob_start();
?>

<div class="admin-card">
    <h3>Araç Sınıfları Yönetimi</h3>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Araç sınıfları başarıyla kaydedildi!</div>
    <?php endif; ?>
    
    <form method="post">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
        <input type="hidden" name="action" value="save" />
        
        <!-- Mevcut Araç Sınıfları -->
        <div class="admin-form-section">
            <h4>Mevcut Araç Sınıfları</h4>
            <p class="text-muted">Araç sınıflarını düzenleyin ve özelliklerini yönetin</p>
            
            <?php foreach ($vehicleClasses as $classKey => $vehicle): ?>
            <div class="vehicle-class-card">
                <div class="vehicle-class-header">
                    <h5><?php echo htmlspecialchars($vehicle['name']); ?></h5>
                    <div class="vehicle-class-actions">
                        <label class="checkbox-label">
                            <input type="checkbox" name="vehicle_classes[<?php echo $classKey; ?>][active]" 
                                   <?php echo $vehicle['active'] ? 'checked' : ''; ?> />
                            Aktif
                        </label>
                        <button type="button" class="btn-small btn-delete" 
                                onclick="deleteVehicleClass('<?php echo $classKey; ?>')">
                            <i class='bx bx-trash'></i> Sil
                        </button>
                    </div>
                </div>
                
                <div class="vehicle-class-content">
                    <div class="form-grid">
                        <div class="form-item">
                            <label>Araç Adı</label>
                            <input class="admin-input" type="text" 
                                   name="vehicle_classes[<?php echo $classKey; ?>][name]" 
                                   value="<?php echo htmlspecialchars($vehicle['name']); ?>" required />
                        </div>
                        
                        <div class="form-item">
                            <label>Yolcu Kapasitesi</label>
                            <input class="admin-input" type="number" min="1" max="50" 
                                   name="vehicle_classes[<?php echo $classKey; ?>][capacity]" 
                                   value="<?php echo $vehicle['capacity']; ?>" required />
                        </div>
                        
                        <div class="form-item">
                            <label>Bavul Kapasitesi</label>
                            <input class="admin-input" type="number" min="1" max="50" 
                                   name="vehicle_classes[<?php echo $classKey; ?>][luggage]" 
                                   value="<?php echo $vehicle['luggage']; ?>" required />
                        </div>
                    </div>
                    
                    <div class="form-item">
                        <label>Açıklama</label>
                        <textarea class="admin-input" name="vehicle_classes[<?php echo $classKey; ?>][description]" 
                                  rows="3"><?php echo htmlspecialchars($vehicle['description']); ?></textarea>
                    </div>
                    
                    <div class="form-item">
                        <label>Özellikler</label>
                        <div class="features-grid">
                            <?php 
                            $allFeatures = [
                                'Air Conditioning', 'Free Wi-Fi', 'Child Seat', 'Meet & Greet', 
                                'Luggage Assistance', 'Door-to-Door', 'Professional Driver',
                                'Instant Confirmation', 'Free Cancellations', 'GPS Navigation',
                                'Refreshments', 'Premium Audio', 'Climate Control', 'Leather Seats'
                            ];
                            
                            foreach ($allFeatures as $feature): ?>
                            <label class="feature-checkbox">
                                <input type="checkbox" name="features[<?php echo $classKey; ?>][]" 
                                       value="<?php echo $feature; ?>" 
                                       <?php echo in_array($feature, $vehicle['features']) ? 'checked' : ''; ?> />
                                <?php echo $feature; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Yeni Araç Sınıfı Ekleme -->
        <div class="admin-form-section">
            <h4>Yeni Araç Sınıfı Ekle</h4>
            <p class="text-muted">Yeni bir araç sınıfı oluşturun</p>
            
            <div class="form-grid">
                <div class="form-item">
                    <label>Araç Adı *</label>
                    <input class="admin-input" type="text" name="new_name" 
                           placeholder="Örn: Luxury SUV" required />
                </div>
                
                <div class="form-item">
                    <label>Yolcu Kapasitesi *</label>
                    <input class="admin-input" type="number" name="new_capacity" 
                           min="1" max="50" placeholder="4" required />
                </div>
                
                <div class="form-item">
                    <label>Bavul Kapasitesi *</label>
                    <input class="admin-input" type="number" name="new_luggage" 
                           min="1" max="50" placeholder="4" required />
                </div>
            </div>
            
            <div class="form-item">
                <label>Açıklama</label>
                <textarea class="admin-input" name="new_description" rows="3" 
                          placeholder="Araç sınıfı açıklaması..."></textarea>
            </div>
            
            <div class="form-item">
                <label>Özellikler</label>
                <div class="features-grid">
                    <?php foreach ($allFeatures as $feature): ?>
                    <label class="feature-checkbox">
                        <input type="checkbox" name="new_features[]" value="<?php echo $feature; ?>" />
                        <?php echo $feature; ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-item">
                <label class="checkbox-label">
                    <input type="checkbox" name="new_active" checked />
                    Aktif
                </label>
            </div>
            
            <div style="margin-top: 16px;">
                <button class="admin-btn" type="submit" name="action" value="add">Yeni Araç Sınıfı Ekle</button>
            </div>
        </div>
        
        <div style="margin-top: 24px;">
            <button class="admin-btn admin-btn-primary" type="submit">Tüm Değişiklikleri Kaydet</button>
        </div>
    </form>
</div>

<style>
.vehicle-class-card {
    margin-bottom: 24px;
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: white;
}

.vehicle-class-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #f0f0f0;
}

.vehicle-class-header h5 {
    margin: 0;
    color: #333;
    font-size: 18px;
    font-weight: 600;
}

.vehicle-class-actions {
    display: flex;
    align-items: center;
    gap: 16px;
}

.vehicle-class-content {
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

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    margin-top: 8px;
}

.feature-checkbox {
    display: flex;
    align-items: center;
    font-size: 14px;
    color: #666;
    cursor: pointer;
    padding: 8px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.feature-checkbox:hover {
    background: #f8f9fa;
    border-color: #007bff;
}

.feature-checkbox input[type="checkbox"] {
    margin-right: 8px;
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
body.theme-dark .vehicle-class-card {
    background: #2d3748;
    border-color: #4a5568;
    color: #e2e8f0;
}

body.theme-dark .vehicle-class-header h5 {
    color: #f7fafc;
}

body.theme-dark .vehicle-class-header {
    border-bottom-color: #4a5568;
}

body.theme-dark .feature-checkbox {
    color: #a0aec0;
    border-color: #4a5568;
}

body.theme-dark .feature-checkbox:hover {
    background: #4a5568;
    border-color: #007bff;
}

body.theme-dark .form-item label {
    color: #a0aec0;
}
</style>

<script>
function deleteVehicleClass(classKey) {
    if (confirm('Bu araç sınıfını silmek istediğinizden emin misiniz?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
            <input type="hidden" name="action" value="delete" />
            <input type="hidden" name="class_key" value="${classKey}" />
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
