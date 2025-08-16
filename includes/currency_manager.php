<?php
declare(strict_types=1);

class CurrencyManager {
    private $currencyFile;
    private $currencyData;
    private $currentCurrency;
    
    public function __construct() {
        $this->currencyFile = __DIR__ . '/../storage/currency.json';
        $this->loadCurrencyData();
        $this->detectCurrentCurrency();
    }
    
    /**
     * Para birimi verilerini yükle
     */
    private function loadCurrencyData(): void {
        if (is_file($this->currencyFile)) {
            $this->currencyData = json_decode(file_get_contents($this->currencyFile), true) ?: [];
        }
        
        // Varsayılan veriler
        if (empty($this->currencyData)) {
            $this->currencyData = [
                'default_currency' => 'EUR',
                'currencies' => [
                    'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'rate' => 1.08, 'active' => true, 'position' => 'before'],
                    'EUR' => ['name' => 'Euro', 'symbol' => '€', 'rate' => 1.0, 'active' => true, 'position' => 'before'],
                    'GBP' => ['name' => 'British Pound', 'symbol' => '£', 'rate' => 0.85, 'active' => true, 'position' => 'before'],
                    'TRY' => ['name' => 'Turkish Lira', 'symbol' => '₺', 'rate' => 32.5, 'active' => true, 'position' => 'after']
                ]
            ];
        }
    }
    
    /**
     * Mevcut para birimini tespit et
     */
    private function detectCurrentCurrency(): void {
        // Session'dan kontrol et
        if (isset($_SESSION['currency'])) {
            $this->currentCurrency = $_SESSION['currency'];
            return;
        }
        
        // Cookie'den kontrol et
        if (isset($_COOKIE['currency'])) {
            $this->currentCurrency = $_COOKIE['currency'];
            return;
        }
        
        // Varsayılan para birimini kullan
        $this->currentCurrency = $this->currencyData['default_currency'] ?? 'EUR';
    }
    
    /**
     * Para birimi değiştir
     */
    public function setCurrency(string $currency): void {
        if (isset($this->currencyData['currencies'][$currency]) && $this->currencyData['currencies'][$currency]['active']) {
            $this->currentCurrency = $currency;
            $_SESSION['currency'] = $currency;
            setcookie('currency', $currency, time() + (86400 * 30), '/'); // 30 gün
        }
    }
    
    /**
     * Mevcut para birimini al
     */
    public function getCurrentCurrency(): string {
        return $this->currentCurrency;
    }
    
    /**
     * Para birimi dönüştür
     */
    public function convert(float $amount, string $fromCurrency = 'EUR', string $toCurrency = null): float {
        if ($toCurrency === null) {
            $toCurrency = $this->currentCurrency;
        }
        
        // Aynı para birimiyse dönüştürme yapma
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }
        
        // Para birimleri mevcut mu kontrol et
        if (!isset($this->currencyData['currencies'][$fromCurrency]) || 
            !isset($this->currencyData['currencies'][$toCurrency])) {
            return $amount;
        }
        
        // EUR bazında dönüştür
        $eurAmount = $amount / $this->currencyData['currencies'][$fromCurrency]['rate'];
        $convertedAmount = $eurAmount * $this->currencyData['currencies'][$toCurrency]['rate'];
        
        return round($convertedAmount, 2);
    }
    
    /**
     * Para birimi formatla
     */
    public function format(float $amount, string $currency = null): string {
        if ($currency === null) {
            $currency = $this->currentCurrency;
        }
        
        if (!isset($this->currencyData['currencies'][$currency])) {
            return number_format($amount, 2);
        }
        
        $currencyInfo = $this->currencyData['currencies'][$currency];
        $formattedAmount = number_format($amount, 2);
        
        if ($currencyInfo['position'] === 'before') {
            return $currencyInfo['symbol'] . $formattedAmount;
        } else {
            return $formattedAmount . $currencyInfo['symbol'];
        }
    }
    
    /**
     * Aktif para birimlerini al
     */
    public function getActiveCurrencies(): array {
        $active = [];
        foreach ($this->currencyData['currencies'] as $code => $currency) {
            if ($currency['active']) {
                $active[$code] = $currency;
            }
        }
        return $active;
    }
    
    /**
     * Para birimi bilgilerini al
     */
    public function getCurrencyInfo(string $currency = null): ?array {
        if ($currency === null) {
            $currency = $this->currentCurrency;
        }
        
        return $this->currencyData['currencies'][$currency] ?? null;
    }
    
    /**
     * Varsayılan para birimini al
     */
    public function getDefaultCurrency(): string {
        return $this->currencyData['default_currency'] ?? 'EUR';
    }
    
    /**
     * Para birimi seçici HTML'i oluştur
     */
    public function renderCurrencySelector(): string {
        $activeCurrencies = $this->getActiveCurrencies();
        $currentCurrency = $this->getCurrentCurrency();
        
        $html = '<div class="position-relative dropdown c-center hide-mobile">';
        $html .= '<a class="nav-item nav-link px-4 dropdown-toggle" href="#" id="dropdown-money" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
        $html .= htmlspecialchars($currentCurrency);
        $html .= '</a>';
        $html .= '<div class="dropdown-menu dm-singular px-0" aria-labelledby="dropdown-money">';
        $html .= '<div class="dropdown-item px-3 text-center">';
        $html .= '<div class="row mt-2">';
        
        foreach ($activeCurrencies as $code => $currency) {
            $isActive = ($code === $currentCurrency) ? ' dl-active' : '';
            $html .= '<div class="col-4 px-1">';
            $html .= '<a class="d-link px-2 py-1 d-link-currency' . $isActive . '" rel="nofollow" href="/mytransfers/currency/' . $code . '/">';
            $html .= '<span class="pr-0 font-12">' . htmlspecialchars($currency['symbol']) . '</span>';
            $html .= '<span>' . htmlspecialchars($code) . '</span>';
            $html .= '</a>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * JavaScript için para birimi verilerini al
     */
    public function getCurrencyDataForJS(): array {
        $data = [
            'current' => $this->currentCurrency,
            'default' => $this->getDefaultCurrency(),
            'currencies' => []
        ];
        
        foreach ($this->getActiveCurrencies() as $code => $currency) {
            $data['currencies'][$code] = [
                'symbol' => $currency['symbol'],
                'position' => $currency['position'],
                'rate' => $currency['rate']
            ];
        }
        
        return $data;
    }
}
