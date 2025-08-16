<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

// Kuponlar dosyasının yolu
$couponsFile = $STORAGE . '/coupons.json';

// Mevcut kuponları yükle
$coupons = [];
if (is_file($couponsFile)) {
    $coupons = json_decode(file_get_contents($couponsFile), true) ?: [];
}

// POST işlemi - kupon ekle/düzenle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            // Yeni kupon ekle
            $newCoupon = [
                'id' => uniqid('coupon_'),
                'code' => strtoupper(trim($_POST['code'])),
                'type' => $_POST['type'], // 'percentage' veya 'fixed'
                'value' => floatval($_POST['value']),
                'min_amount' => floatval($_POST['min_amount'] ?? 0),
                'max_discount' => floatval($_POST['max_discount'] ?? 0),
                'usage_limit' => intval($_POST['usage_limit'] ?? 0),
                'used_count' => 0,
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'active' => isset($_POST['active']),
                'description' => $_POST['description'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $coupons[] = $newCoupon;
            
        } elseif ($_POST['action'] === 'edit') {
            // Kupon düzenle
            $couponId = $_POST['coupon_id'];
            foreach ($coupons as &$coupon) {
                if ($coupon['id'] === $couponId) {
                    $coupon['code'] = strtoupper(trim($_POST['code']));
                    $coupon['type'] = $_POST['type'];
                    $coupon['value'] = floatval($_POST['value']);
                    $coupon['min_amount'] = floatval($_POST['min_amount'] ?? 0);
                    $coupon['max_discount'] = floatval($_POST['max_discount'] ?? 0);
                    $coupon['usage_limit'] = intval($_POST['usage_limit'] ?? 0);
                    $coupon['start_date'] = $_POST['start_date'];
                    $coupon['end_date'] = $_POST['end_date'];
                    $coupon['active'] = isset($_POST['active']);
                    $coupon['description'] = $_POST['description'] ?? '';
                    break;
                }
            }
            
        } elseif ($_POST['action'] === 'delete') {
            // Kupon sil
            $couponId = $_POST['coupon_id'];
            $coupons = array_filter($coupons, function($coupon) use ($couponId) {
                return $coupon['id'] !== $couponId;
            });
        }
    }
    
    // Kuponları kaydet
    write_json($couponsFile, array_values($coupons));
    
    header('Location: /mytransfers/admin/coupons.php?success=1');
    exit;
}

ob_start();
?>

<div class="admin-card">
    <h3>Kupon Yönetimi</h3>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Kupon başarıyla kaydedildi!</div>
    <?php endif; ?>
    
    <!-- Yeni Kupon Ekleme Formu -->
    <div class="admin-form-section">
        <h4>Yeni Kupon Ekle</h4>
        <form method="post" class="coupon-form">
            <input class="admin-input" type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
            <input class="admin-input" type="hidden" name="action" value="add" />
            
            <div class="form-grid">
                <div class="form-item">
                    <label>Kupon Kodu *</label>
                    <input class="admin-input" type="text" name="code" required 
                           placeholder="Örn: SUMMER2024" maxlength="20" />
                </div>
                
                <div class="form-item">
                    <label>İndirim Türü *</label>
                    <select class="admin-input" name="type" required>
                        <option value="percentage">Yüzde (%)</option>
                        <option value="fixed">Sabit Tutar (€)</option>
                    </select>
                </div>
                
                <div class="form-item">
                    <label>İndirim Değeri *</label>
                    <input class="admin-input" type="number" name="value" required 
                           step="0.01" min="0" placeholder="Örn: 10" />
                    <small>Yüzde için: 10 = %10, Sabit için: 10 = 10€</small>
                </div>
                
                <div class="form-item">
                    <label>Minimum Sipariş Tutarı (€)</label>
                    <input class="admin-input" type="number" name="min_amount" 
                           step="0.01" min="0" placeholder="0" />
                </div>
                
                <div class="form-item">
                    <label>Maksimum İndirim (€)</label>
                    <input class="admin-input" type="number" name="max_discount" 
                           step="0.01" min="0" placeholder="Sınırsız" />
                </div>
                
                <div class="form-item">
                    <label>Kullanım Limiti</label>
                    <input class="admin-input" type="number" name="usage_limit" 
                           min="0" placeholder="Sınırsız" />
                </div>
                
                <div class="form-item">
                    <label>Başlangıç Tarihi</label>
                    <input class="admin-input" type="date" name="start_date" 
                           value="<?php echo date('Y-m-d'); ?>" />
                </div>
                
                <div class="form-item">
                    <label>Bitiş Tarihi</label>
                    <input class="admin-input" type="date" name="end_date" 
                           value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" />
                </div>
                
                <div class="form-item">
                    <label>Açıklama</label>
                    <textarea class="admin-input" name="description" rows="3" 
                              placeholder="Kupon açıklaması..."></textarea>
                </div>
                
                <div class="form-item">
                    <label class="checkbox-label">
                        <input type="checkbox" name="active" checked />
                        Aktif
                    </label>
                </div>
            </div>
            
            <div style="margin-top: 16px;">
                <button class="admin-btn" type="submit">Kupon Ekle</button>
            </div>
        </form>
    </div>
    
    <!-- Mevcut Kuponlar Listesi -->
    <div class="admin-form-section">
        <h4>Mevcut Kuponlar</h4>
        
        <?php if (empty($coupons)): ?>
            <p class="text-muted">Henüz kupon eklenmemiş.</p>
        <?php else: ?>
            <div class="coupons-table">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Kod</th>
                            <th>İndirim</th>
                            <th>Min. Tutar</th>
                            <th>Kullanım</th>
                            <th>Geçerlilik</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coupons as $coupon): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($coupon['code']); ?></strong>
                                    <?php if ($coupon['description']): ?>
                                        <br><small><?php echo htmlspecialchars($coupon['description']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($coupon['type'] === 'percentage'): ?>
                                        %<?php echo $coupon['value']; ?>
                                    <?php else: ?>
                                        €<?php echo $coupon['value']; ?>
                                    <?php endif; ?>
                                    <?php if ($coupon['max_discount'] > 0): ?>
                                        <br><small>Max: €<?php echo $coupon['max_discount']; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($coupon['min_amount'] > 0): ?>
                                        €<?php echo $coupon['min_amount']; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $coupon['used_count']; ?>
                                    <?php if ($coupon['usage_limit'] > 0): ?>
                                        / <?php echo $coupon['usage_limit']; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $startDate = new DateTime($coupon['start_date']);
                                    $endDate = new DateTime($coupon['end_date']);
                                    $now = new DateTime();
                                    
                                    if ($now < $startDate) {
                                        echo '<span class="badge badge-warning">Başlamadı</span>';
                                    } elseif ($now > $endDate) {
                                        echo '<span class="badge badge-danger">Süresi Doldu</span>';
                                    } else {
                                        echo '<span class="badge badge-success">Aktif</span>';
                                    }
                                    ?>
                                    <br><small><?php echo $startDate->format('d.m.Y'); ?> - <?php echo $endDate->format('d.m.Y'); ?></small>
                                </td>
                                <td>
                                    <?php if ($coupon['active']): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Pasif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-small btn-edit" onclick="editCoupon('<?php echo $coupon['id']; ?>')">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                        <form method="post" style="display: inline;" onsubmit="return confirm('Bu kuponu silmek istediğinizden emin misiniz?')">
                                            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
                                            <input type="hidden" name="action" value="delete" />
                                            <input type="hidden" name="coupon_id" value="<?php echo $coupon['id']; ?>" />
                                            <button class="btn-small btn-delete" type="submit">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.coupon-form {
    margin-bottom: 24px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

.form-item small {
    font-size: 12px;
    color: #999;
    margin-top: 4px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    margin-right: 8px;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 16px;
}

.admin-table th,
.admin-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.admin-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.admin-table td {
    font-size: 14px;
    color: #666;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.badge-success {
    background: #d4edda;
    color: #155724;
}

.badge-warning {
    background: #fff3cd;
    color: #856404;
}

.badge-danger {
    background: #f8d7da;
    color: #721c24;
}

.badge-secondary {
    background: #e2e3e5;
    color: #383d41;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-small {
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-edit {
    background: #007bff;
    color: white;
}

.btn-edit:hover {
    background: #0056b3;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

/* Dark theme support */
body.theme-dark .admin-table th {
    background: #2d3748;
    color: #f7fafc;
}

body.theme-dark .admin-table td {
    color: #a0aec0;
    border-bottom-color: #4a5568;
}

body.theme-dark .form-item label {
    color: #a0aec0;
}

body.theme-dark .form-item small {
    color: #718096;
}
</style>

<script>
function editCoupon(couponId) {
    // Kupon düzenleme modal'ı açılabilir
    alert('Kupon düzenleme özelliği yakında eklenecek!');
}
</script>

<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);
?>


