<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

$page_title = "API Test";
$current_page = "api_test";

// API endpoint'leri
$api_endpoints = [
    'health' => [
        'name' => 'Health Check',
        'method' => 'GET',
        'description' => 'API sağlık durumu kontrolü',
        'params' => []
    ],
    'search' => [
        'name' => 'Arama',
        'method' => 'GET',
        'description' => 'Transfer arama',
        'params' => [
            'query' => ['type' => 'text', 'required' => true, 'placeholder' => 'Arama terimi'],
            'lang' => ['type' => 'select', 'required' => false, 'options' => ['en', 'tr', 'de', 'es', 'fr'], 'default' => 'en']
        ]
    ],
    'countries' => [
        'name' => 'Ülkeler',
        'method' => 'GET',
        'description' => 'Tüm ülkeleri listele',
        'params' => [
            'lang' => ['type' => 'select', 'required' => false, 'options' => ['en', 'tr', 'de', 'es', 'fr'], 'default' => 'en']
        ]
    ],
    'destinations' => [
        'name' => 'Destinasyonlar',
        'method' => 'GET',
        'description' => 'Destinasyonları listele',
        'params' => [
            'lang' => ['type' => 'select', 'required' => false, 'options' => ['en', 'tr', 'de', 'es', 'fr'], 'default' => 'en']
        ]
    ],
    'pricing' => [
        'name' => 'Fiyatlandırma',
        'method' => 'GET',
        'description' => 'Fiyatlandırma bilgileri',
        'params' => [
            'lang' => ['type' => 'select', 'required' => false, 'options' => ['en', 'tr', 'de', 'es', 'fr'], 'default' => 'en']
        ]
    ],
    'coupons' => [
        'name' => 'Kuponlar',
        'method' => 'GET',
        'description' => 'Aktif kuponları listele',
        'params' => [
            'lang' => ['type' => 'select', 'required' => false, 'options' => ['en', 'tr', 'de', 'es', 'fr'], 'default' => 'en']
        ]
    ],
    'config' => [
        'name' => 'Konfigürasyon',
        'method' => 'GET',
        'description' => 'Sistem konfigürasyonu',
        'params' => []
    ]
];

// API test fonksiyonu
function testApiEndpoint($endpoint, $params = []) {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base_url = 'http://' . $host . '/mytransfers/api/' . $endpoint;
    
    if (!empty($params)) {
        $base_url .= '?' . http_build_query($params);
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'User-Agent: MyTransfers-Admin/1.0'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    
    // Debug bilgisi
    error_log("API Test - URL: $base_url, HTTP Code: $http_code, Error: $error, Response Length: " . strlen($response));
    
    return [
        'success' => $error === '' && $http_code >= 200 && $http_code < 300,
        'http_code' => $http_code,
        'response' => $response,
        'error' => $error,
        'url' => $base_url,
        'info' => $info
    ];
}

// Test sonuçları
$test_results = [];
if (isset($_POST['action']) && $_POST['action'] === 'test_api') {
    $endpoint = $_POST['endpoint'] ?? '';
    $params = [];
    
    // Parametreleri topla
    if (isset($_POST['params']) && is_array($_POST['params'])) {
        foreach ($_POST['params'] as $key => $value) {
            if ($value !== '') {
                $params[$key] = $value;
            }
        }
    }
    
    if (isset($api_endpoints[$endpoint])) {
        $test_results[$endpoint] = testApiEndpoint($endpoint, $params);
    }
}

ob_start();
?>

<div class="admin-page">
    <div class="admin-page-header">
        <h1><i class='bx bx-api'></i> API Test</h1>
        <p>API endpoint'lerini test edin ve çalışma durumlarını kontrol edin.</p>
    </div>

    <div class="admin-content-grid">
        <!-- API Test Formu -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>API Endpoint Test</h3>
            </div>
            <div class="admin-card-body">
                <form method="POST" id="apiTestForm">
                    <input type="hidden" name="action" value="test_api">
                    
                    <div class="form-group">
                        <label for="endpoint">Endpoint Seçin:</label>
                        <select name="endpoint" id="endpoint" class="form-control" required>
                            <option value="">Endpoint seçin...</option>
                            <?php foreach ($api_endpoints as $key => $endpoint): ?>
                                <option value="<?php echo htmlspecialchars($key); ?>">
                                    <?php echo htmlspecialchars($endpoint['name']); ?> (<?php echo $endpoint['method']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="paramsContainer" style="display: none;">
                        <h4>Parametreler</h4>
                        <div id="paramsList"></div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-play'></i> Test Et
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Test Sonuçları -->
        <?php if (!empty($test_results)): ?>
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3>Test Sonuçları</h3>
                </div>
                <div class="admin-card-body">
                    <?php foreach ($test_results as $endpoint => $result): ?>
                        <div class="test-result">
                            <div class="test-result-header">
                                <h4><?php echo htmlspecialchars($api_endpoints[$endpoint]['name']); ?></h4>
                                <span class="status-badge <?php echo $result['success'] ? 'success' : 'error'; ?>">
                                    <?php echo $result['success'] ? 'Başarılı' : 'Hata'; ?>
                                </span>
                            </div>
                            
                                                         <div class="test-result-details">
                                 <p><strong>URL:</strong> <code><?php echo htmlspecialchars($result['url']); ?></code></p>
                                 <p><strong>HTTP Kodu:</strong> <span class="http-code"><?php echo $result['http_code']; ?></span></p>
                                 <p><strong>Yanıt Boyutu:</strong> <span class="response-size"><?php echo strlen($result['response']); ?> bytes</span></p>
                                 
                                 <?php if ($result['error']): ?>
                                     <p><strong>Hata:</strong> <span class="error-text"><?php echo htmlspecialchars($result['error']); ?></span></p>
                                 <?php endif; ?>
                                 
                                 <?php if (isset($result['info'])): ?>
                                     <div class="debug-info">
                                         <h5>Debug Bilgisi:</h5>
                                         <p><strong>Total Time:</strong> <?php echo round($result['info']['total_time'] * 1000, 2); ?>ms</p>
                                         <p><strong>Connect Time:</strong> <?php echo round($result['info']['connect_time'] * 1000, 2); ?>ms</p>
                                         <p><strong>Redirect Count:</strong> <?php echo $result['info']['redirect_count']; ?></p>
                                     </div>
                                 <?php endif; ?>
                                 
                                 <div class="response-section">
                                     <h5>Yanıt:</h5>
                                     <?php if (empty($result['response'])): ?>
                                         <p class="no-response">Yanıt boş</p>
                                     <?php else: ?>
                                         <pre class="response-json"><?php echo htmlspecialchars($result['response']); ?></pre>
                                     <?php endif; ?>
                                 </div>
                             </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- API Durumu -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>API Durumu</h3>
            </div>
            <div class="admin-card-body">
                <div class="api-status-grid">
                    <?php foreach ($api_endpoints as $key => $endpoint): ?>
                        <div class="api-status-item" data-endpoint="<?php echo htmlspecialchars($key); ?>">
                            <div class="api-status-icon">
                                <i class='bx bx-circle'></i>
                            </div>
                            <div class="api-status-info">
                                <h4><?php echo htmlspecialchars($endpoint['name']); ?></h4>
                                <p><?php echo htmlspecialchars($endpoint['description']); ?></p>
                                <small><?php echo $endpoint['method']; ?> /api/<?php echo $key; ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-content-grid {
    display: grid;
    gap: 1.5rem;
    grid-template-columns: 1fr;
}

.test-result {
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    margin-bottom: 1rem;
    overflow: hidden;
}

.test-result-header {
    background: #f8f9fa;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e1e5e9;
}

.test-result-header h4 {
    margin: 0;
    color: #495057;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.status-badge.success {
    background: #d4edda;
    color: #155724;
}

.status-badge.error {
    background: #f8d7da;
    color: #721c24;
}

.test-result-details {
    padding: 1rem;
}

.test-result-details p {
    margin: 0.5rem 0;
}

.http-code {
    font-weight: 600;
    color: #495057;
}

.error-text {
    color: #dc3545;
}

.response-section {
    margin-top: 1rem;
}

.response-json {
    background: #f8f9fa;
    border: 1px solid #e1e5e9;
    border-radius: 4px;
    padding: 1rem;
    font-size: 0.875rem;
    max-height: 300px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-break: break-all;
}

.api-status-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
}

.api-status-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    background: #fff;
    transition: all 0.2s ease;
}

.api-status-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.api-status-icon {
    margin-right: 1rem;
    font-size: 1.5rem;
    color: #6c757d;
}

.api-status-icon i {
    transition: color 0.2s ease;
}

.api-status-item.working .api-status-icon i {
    color: #28a745;
}

.api-status-item.error .api-status-icon i {
    color: #dc3545;
}

.api-status-info h4 {
    margin: 0 0 0.25rem 0;
    color: #495057;
}

.api-status-info p {
    margin: 0 0 0.5rem 0;
    color: #6c757d;
    font-size: 0.875rem;
}

.api-status-info small {
    color: #868e96;
    font-family: monospace;
}

#paramsContainer {
    margin: 1rem 0;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.param-group {
    margin-bottom: 1rem;
}

.param-group label {
    display: block;
    margin-bottom: 0.25rem;
    font-weight: 600;
}

.param-group input,
.param-group select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.required-field::after {
    content: " *";
    color: #dc3545;
}

.debug-info {
    background: #e9ecef;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 0.75rem;
    margin: 1rem 0;
}

.debug-info h5 {
    margin: 0 0 0.5rem 0;
    color: #495057;
    font-size: 0.875rem;
}

.debug-info p {
    margin: 0.25rem 0;
    font-size: 0.875rem;
    color: #6c757d;
}

.response-size {
    font-weight: 600;
    color: #17a2b8;
}

.no-response {
    color: #6c757d;
    font-style: italic;
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border: 1px dashed #dee2e6;
    border-radius: 4px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const endpointSelect = document.getElementById('endpoint');
    const paramsContainer = document.getElementById('paramsContainer');
    const paramsList = document.getElementById('paramsList');
    
    const apiEndpoints = <?php echo json_encode($api_endpoints); ?>;
    
    endpointSelect.addEventListener('change', function() {
        const selectedEndpoint = this.value;
        const endpoint = apiEndpoints[selectedEndpoint];
        
        if (endpoint && endpoint.params && Object.keys(endpoint.params).length > 0) {
            paramsContainer.style.display = 'block';
            paramsList.innerHTML = '';
            
            Object.entries(endpoint.params).forEach(([key, param]) => {
                const paramGroup = document.createElement('div');
                paramGroup.className = 'param-group';
                
                const label = document.createElement('label');
                label.className = param.required ? 'required-field' : '';
                label.textContent = key;
                
                let input;
                if (param.type === 'select') {
                    input = document.createElement('select');
                    input.name = `params[${key}]`;
                    
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Seçin...';
                    input.appendChild(defaultOption);
                    
                    param.options.forEach(option => {
                        const optionElement = document.createElement('option');
                        optionElement.value = option;
                        optionElement.textContent = option;
                        if (option === param.default) {
                            optionElement.selected = true;
                        }
                        input.appendChild(optionElement);
                    });
                } else {
                    input = document.createElement('input');
                    input.type = param.type;
                    input.name = `params[${key}]`;
                    input.placeholder = param.placeholder || '';
                    if (param.required) {
                        input.required = true;
                    }
                }
                
                paramGroup.appendChild(label);
                paramGroup.appendChild(input);
                paramsList.appendChild(paramGroup);
            });
        } else {
            paramsContainer.style.display = 'none';
        }
    });
    
    // API durumu kontrolü
    function checkApiStatus() {
        const statusItems = document.querySelectorAll('.api-status-item');
        
        statusItems.forEach(item => {
            const endpoint = item.dataset.endpoint;
            const icon = item.querySelector('.api-status-icon i');
            
            // Endpoint'e göre test parametreleri
            let testUrl = `/mytransfers/api/${endpoint}`;
            let testParams = {};
            
            // Parametre gerektiren endpoint'ler için test değerleri
            switch(endpoint) {
                case 'search':
                    testParams = { query: 'test', lang: 'en' };
                    break;
                case 'countries':
                case 'destinations':
                case 'pricing':
                case 'coupons':
                    testParams = { lang: 'en' };
                    break;
                default:
                    // health, config gibi parametre gerektirmeyen endpoint'ler
                    break;
            }
            
            // Parametreleri URL'e ekle
            if (Object.keys(testParams).length > 0) {
                const params = new URLSearchParams(testParams);
                testUrl += '?' + params.toString();
            }
            
            // API test
            fetch(testUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    item.classList.remove('error');
                    item.classList.add('working');
                    icon.style.color = '#28a745';
                    icon.className = 'bx bx-check-circle';
                } else {
                    item.classList.remove('working');
                    item.classList.add('error');
                    icon.style.color = '#dc3545';
                    icon.className = 'bx bx-x-circle';
                }
            })
            .catch((error) => {
                console.error(`API Error for ${endpoint}:`, error);
                item.classList.remove('working');
                item.classList.add('error');
                icon.style.color = '#dc3545';
                icon.className = 'bx bx-x-circle';
            });
        });
    }
    
    // Sayfa yüklendiğinde API durumunu kontrol et
    checkApiStatus();
});
</script>

<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);
?>
