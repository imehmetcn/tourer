<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/language_loader.php';
require_once __DIR__ . '/includes/currency_manager.php';

// Dil yükleyici
$lang_loader = new LanguageLoader();
$current_lang = $lang_loader->getCurrentLanguage();

// Para birimi yöneticisi
$currency_manager = new CurrencyManager();

// URL parametrelerini al
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$adults = intval($_GET['adults'] ?? 2);
$children = intval($_GET['children'] ?? 0);
$infants = intval($_GET['infants'] ?? 0);
$transfer_type = $_GET['transfer_type'] ?? 'oneway';
$arrival_date = $_GET['arrival_date'] ?? '';
$departure_date = $_GET['departure_date'] ?? '';

// Koordinat bilgileri (orijinal siteden)
$arrival_lat = $_GET['arrival_lat'] ?? 36.90869610000001;
$arrival_lng = $_GET['arrival_lng'] ?? 30.7981855;
$departure_lat = $_GET['departure_lat'] ?? 36.8635954;
$departure_lng = $_GET['departure_lng'] ?? 31.0607418;
$arrival_type = $_GET['arrival_type'] ?? 'airport';
$departure_type = $_GET['departure_type'] ?? 'address';

// Toplam yolcu sayısı
$total_passengers = $adults + $children + $infants;

// Fiyatlandırma verilerini yükle
$pricingFile = __DIR__ . '/storage/pricing.json';
$pricing = [];
if (is_file($pricingFile)) {
    $pricing = json_decode(file_get_contents($pricingFile), true) ?: [];
}

// Varsayılan fiyatlar
$defaultPricing = [
    'transfers' => [
        'economy' => ['base_price' => 25, 'per_km' => 0.5, 'min_price' => 20, 'max_price' => 200],
        'standard' => ['base_price' => 35, 'per_km' => 0.7, 'min_price' => 30, 'max_price' => 300],
        'premium' => ['base_price' => 50, 'per_km' => 1.0, 'min_price' => 45, 'max_price' => 500],
        'vip' => ['base_price' => 80, 'per_km' => 1.5, 'min_price' => 70, 'max_price' => 800],
        'minibus' => ['base_price' => 65, 'per_km' => 1.2, 'min_price' => 60, 'max_price' => 600],
        'coach' => ['base_price' => 150, 'per_km' => 2.0, 'min_price' => 120, 'max_price' => 1000]
    ]
];

$pricing = array_merge($defaultPricing, $pricing);

// Araç sınıfları ve özellikleri (admin panelinden yüklenir)
$vehicleClassesFile = __DIR__ . '/storage/vehicle_classes.json';
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

// Sadece aktif araç sınıflarını filtrele
$vehicleClasses = array_filter($vehicleClasses, function($vehicle) {
    return isset($vehicle['active']) ? $vehicle['active'] : true;
});

// Haversine formula ile mesafe hesaplama
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // km
    
    // String değerleri float'a çevir
    $lat1 = (float)$lat1;
    $lon1 = (float)$lon1;
    $lat2 = (float)$lat2;
    $lon2 = (float)$lon2;
    
    $latDelta = deg2rad($lat2 - $lat1);
    $lonDelta = deg2rad($lon2 - $lon1);
    
    $a = sin($latDelta / 2) * sin($latDelta / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($lonDelta / 2) * sin($lonDelta / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;
    
    return round($distance, 1);
}

// Gerçek mesafe hesaplama
$estimatedDistance = calculateDistance($arrival_lat, $arrival_lng, $departure_lat, $departure_lng);

// Fiyat hesaplama fonksiyonu (dinamik mesafe ile)
function calculatePrice($basePrice, $perKm, $minPrice, $maxPrice, $distance) {
    $price = $basePrice + ($perKm * $distance);
    return max($minPrice, min($maxPrice, $price));
}

// Coupon verilerini yükle
$couponsFile = __DIR__ . '/storage/coupons.json';
$coupons = [];
if (is_file($couponsFile)) {
    $coupons = json_decode(file_get_contents($couponsFile), true) ?: [];
}

// Aktif kuponları filtrele
$activeCoupons = array_filter($coupons, function($coupon) {
    $now = new DateTime();
    $startDate = new DateTime($coupon['start_date']);
    $endDate = new DateTime($coupon['end_date']);
    
    return $coupon['active'] && $now >= $startDate && $now <= $endDate;
});

// Araç listesini oluştur
$vehicles = [];
foreach ($vehicleClasses as $class => $vehicle) {
    $pricingData = $pricing['transfers'][$class] ?? $pricing['transfers']['standard'];
    $basePrice = calculatePrice(
        $pricingData['base_price'],
        $pricingData['per_km'],
        $pricingData['min_price'],
        $pricingData['max_price'],
        $estimatedDistance
    );
    
    // Eski fiyat (indirim göstermek için)
    $priceOld = $basePrice * 1.2;
    
    // Kupon indirimi hesapla
    $discount = 0;
    $appliedCoupon = null;
    
    foreach ($activeCoupons as $coupon) {
        if ($basePrice >= $coupon['min_amount']) {
            if ($coupon['type'] === 'percentage') {
                $couponDiscount = ($basePrice * $coupon['value']) / 100;
                if ($couponDiscount > $coupon['max_discount']) {
                    $couponDiscount = $coupon['max_discount'];
                }
            } else {
                $couponDiscount = $coupon['value'];
            }
            
            if ($couponDiscount > $discount) {
                $discount = $couponDiscount;
                $appliedCoupon = $coupon;
            }
        }
    }
    
    $finalPrice = $basePrice - $discount;
    
    $vehicles[] = [
        'class' => $class,
        'name' => $vehicle['name'],
        'capacity' => $vehicle['capacity'],
        'luggage' => $vehicle['luggage'],
        'features' => $vehicle['features'],
        'description' => $vehicle['description'],
        'price' => $finalPrice,
        'price_old' => $priceOld,
        'base_price' => $basePrice,
        'discount' => $discount,
        'coupon' => $appliedCoupon,
        'available' => $vehicle['capacity'] >= $total_passengers,
        'meetandgreet' => in_array('Meet & Greet', $vehicle['features']),
        'active' => isset($vehicle['active']) ? $vehicle['active'] : true
    ];
}

// Sadece aktif araçları filtrele
$vehicles = array_filter($vehicles, function($vehicle) {
    return $vehicle['active'];
});

// Fiyata göre sırala
usort($vehicles, function($a, $b) {
    return $a['price'] <=> $b['price'];
});

ob_start();
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes'>
    <meta name="theme-color" content="#09AFEE">
    <title>Transfer Search - MyTransfers</title>
    <meta name="robots" content="noindex, nofollow">
    
    <link rel="stylesheet" href="/mytransfers/assets/mytransfersweb/prod/css/app.css">
    <link rel="stylesheet" href="/mytransfers/assets/mytransfersweb/prod/css/default.css">
    <link rel="stylesheet" href="/mytransfers/assets/mytransfersweb/prod/css/home.css">
    <link rel="stylesheet" href="/mytransfers/assets/mytransfersweb/prod/css/fonts-text.css">
    
    <link rel="icon" href="/mytransfers/assets/mytransfersweb/prod/img/mytransfers-icon.png">
    
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
        }
        
        .search-results-page {
            min-height: 100vh;
        }
        
        .search-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }
        
        /* Route Details Card */
        .route-details-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .route-details-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }
        
        .trip-details {
            margin-bottom: 20px;
        }
        
        .trip-details h5 {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        
        .trip-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
            font-size: 14px;
        }
        
        .trip-info-icon {
            width: 20px;
            height: 20px;
            background: #007bff;
            border-radius: 50%;
            margin-right: 10px;
            flex-shrink: 0;
            margin-top: 2px;
            position: relative;
        }
        
        .trip-info-icon::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
        }
        
        .trip-info-text {
            color: #666;
            line-height: 1.4;
        }
        
        .change-route-btn {
            width: 100%;
            background: #ff6b35;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .change-route-btn:hover {
            background: #e55a2b;
        }
        
                 /* Vehicle Cards */
         .vehicle-cards {
             display: flex;
             flex-direction: column;
             gap: 20px;
         }
         
         .vehicle-card {
             background: white;
             border-radius: 8px;
             padding: 30px;
             box-shadow: 0 2px 10px rgba(0,0,0,0.1);
             display: grid;
             grid-template-columns: 1fr auto;
             gap: 30px;
             align-items: start;
         }
        
        .service-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .vehicle-image {
            width: 200px;
            height: 120px;
            background: #2c3e50;
            border-radius: 8px;
            margin-right: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .vehicle-info h2 {
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }
        
        .vehicle-subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .service-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .overview-item {
            display: flex;
            align-items: center;
        }
        
        .overview-icon {
            width: 24px;
            height: 24px;
            background: #007bff;
            border-radius: 50%;
            margin-right: 10px;
            flex-shrink: 0;
            position: relative;
        }
        
        .overview-icon::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
        }
        
        .overview-text {
            font-size: 14px;
            color: #666;
        }
        
        .overview-text strong {
            color: #333;
        }
        
        .included-features {
            margin-bottom: 30px;
        }
        
        .included-features h4 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 14px;
            color: #666;
        }
        
        .feature-check {
            width: 20px;
            height: 20px;
            background: #28a745;
            border-radius: 50%;
            margin-right: 10px;
            flex-shrink: 0;
            position: relative;
        }
        
        .feature-check::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        
        .key-features {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .key-feature {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #666;
        }
        
        .key-feature-icon {
            width: 16px;
            height: 16px;
            background: #007bff;
            border-radius: 50%;
            margin-right: 8px;
            flex-shrink: 0;
            position: relative;
        }
        
        .key-feature-icon::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 6px;
            height: 6px;
            background: white;
            border-radius: 50%;
        }
        
        .more-info-toggle {
            color: #007bff;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .more-info-toggle::after {
            content: '▼';
            margin-left: 5px;
            transition: transform 0.3s ease;
        }
        
        .more-info-toggle.expanded::after {
            transform: rotate(180deg);
        }
        
        .more-info-content {
            display: none;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }
        
        /* Reviews Section */
        .reviews-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-top: 20px;
        }
        
        .review-rating {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .review-stars {
            margin-bottom: 15px;
        }
        
        .star {
            color: #ffc107;
            font-size: 18px;
            margin: 0 2px;
        }
        
        .review-text {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .trustpilot-logo {
            font-size: 14px;
            color: #007bff;
            font-weight: 600;
        }
        
                 /* Pricing Section */
         .pricing-section {
             background: white;
             border-radius: 8px;
             padding: 30px;
             box-shadow: 0 2px 10px rgba(0,0,0,0.1);
             height: fit-content;
             text-align: center;
             display: flex;
             flex-direction: column;
             justify-content: space-between;
         }
        
        .price-amount {
            font-size: 36px;
            font-weight: 700;
            color: #28a745;
            margin-bottom: 10px;
        }
        
        .price-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 25px;
        }
        
        .book-now-btn {
            width: 100%;
            background: #28a745;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 6px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-bottom: 20px;
        }
        
        .book-now-btn:hover {
            background: #218838;
        }
        
        .contact-options {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .contact-icon {
            width: 40px;
            height: 40px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .contact-icon:hover {
            background: #e9ecef;
        }
        
        /* Filters Sidebar */
        .filters-sidebar {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        
        .filter-section {
            margin-bottom: 25px;
        }
        
        .filter-section h5 {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        
        .price-slider {
            margin-bottom: 20px;
        }
        
        .slider-labels {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .slider-input {
            width: 100%;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            outline: none;
        }
        
        .slider-input::-webkit-slider-thumb {
            appearance: none;
            width: 20px;
            height: 20px;
            background: #007bff;
            border-radius: 50%;
            cursor: pointer;
        }
        
        .filter-checkbox {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .filter-checkbox input[type="checkbox"] {
            margin-right: 10px;
        }
        
        .filter-checkbox label {
            font-size: 14px;
            color: #666;
            cursor: pointer;
        }
        
        /* Map Section */
        .map-section {
            background: #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            cursor: pointer;
        }
        
        .map-icon {
            font-size: 24px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .map-text {
            font-size: 14px;
            color: #666;
        }
        
                 @media (max-width: 1200px) {
             .search-container {
                 grid-template-columns: 300px 1fr;
                 gap: 20px;
             }
         }
        
        @media (max-width: 768px) {
            .search-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .service-header {
                flex-direction: column;
                text-align: center;
            }
            
            .vehicle-image {
                margin-right: 0;
                margin-bottom: 20px;
            }
            
            .service-overview {
                grid-template-columns: 1fr;
            }
            
            .key-features {
                flex-wrap: wrap;
            }
            
                         .vehicle-card {
                 grid-template-columns: 1fr;
                 gap: 20px;
             }
             
             .pricing-section {
                 max-width: none;
             }
        }
    </style>
</head>

<body>
    <div class="search-results-page">
        <!-- Header -->
        <?php include 'includes/header_simple.php'; ?>
        
        <!-- Breadcrumbs -->
        <div style="background: white; padding: 15px 0; border-bottom: 1px solid #eee;">
            <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
                <div style="font-size: 14px; color: #666;">
                    <a href="/mytransfers/<?php echo $current_lang; ?>/" style="color: #007bff; text-decoration: none;">Home</a>
                    <span style="margin: 0 10px;">></span>
                    <span>Destinations</span>
                </div>
            </div>
        </div>
        
        <div class="search-container">
            <!-- Left Column -->
            <div>
                <!-- Route Details Card -->
                <div class="route-details-card">
                    <div class="route-details-title">Route Details</div>
                    
                    <div class="trip-details">
                        <h5>Trip details</h5>
                        
                        <div class="trip-info-item">
                            <div class="trip-info-icon"></div>
                            <div class="trip-info-text">Antalya Airport (AYT)</div>
                        </div>
                        
                        <div class="trip-info-item">
                            <div class="trip-info-icon"></div>
                            <div class="trip-info-text">Belek, Serik/Antalya, Türkiye</div>
                        </div>
                        
                        <div class="trip-info-item">
                            <div class="trip-info-icon"></div>
                            <div class="trip-info-text">Aug 29, 2025 12:00:00</div>
                        </div>
                        
                        <div class="trip-info-item">
                            <div class="trip-info-icon"></div>
                            <div class="trip-info-text"><?php echo $total_passengers; ?> Passengers</div>
                        </div>
                        
                        <div class="trip-info-item">
                            <div class="trip-info-icon"></div>
                            <div class="trip-info-text"><?php echo $transfer_type === 'oneway' ? 'One-way' : 'Round Trip'; ?></div>
                        </div>
                    </div>
                    
                                         <button class="change-route-btn">Change route</button>
                 </div>
                 
                 <!-- Included in the price section -->
                 <div class="route-details-card" style="margin-top: 20px;">
                     <h4 style="font-size: 18px; font-weight: 600; color: #333; margin-bottom: 15px;">Included in the price</h4>
                     <ul class="feature-list">
                         <li class="feature-item">
                             <div class="feature-check"></div>
                             Free amendments
                         </li>
                         <li class="feature-item">
                             <div class="feature-check"></div>
                             Professional driver
                         </li>
                         <li class="feature-item">
                             <div class="feature-check"></div>
                             Instant confirmation
                         </li>
                         <li class="feature-item">
                             <div class="feature-check"></div>
                             Meet & Greet with welcome sign
                         </li>
                         <li class="feature-item">
                             <div class="feature-check"></div>
                             Free cancellations (Up to 24 hours before your arrival)
                         </li>
                     </ul>
                 </div>
                
                <!-- Filters Sidebar -->
                <div class="filters-sidebar">
                    <!-- Map Section -->
                    <div class="map-section">
                        <div class="map-icon">🗺️</div>
                        <div class="map-text">Map</div>
                    </div>
                    
                    <!-- Price Filter -->
                    <div class="filter-section">
                        <h5>Price:</h5>
                        <div class="price-slider">
                            <div class="slider-labels">
                                <span>Min</span>
                                <span>Max</span>
                            </div>
                            <input type="range" class="slider-input" min="0" max="200" value="62">
                                                         <div class="slider-labels">
                                 <span>€<?php echo number_format(min(array_column($vehicles, 'price')), 2); ?></span>
                                 <span>€<?php echo number_format(max(array_column($vehicles, 'price')), 2); ?></span>
                             </div>
                        </div>
                    </div>
                    
                    <!-- Passengers Filter -->
                    <div class="filter-section">
                        <h5>Passengers:</h5>
                        <div class="price-slider">
                            <div class="slider-labels">
                                <span>Min (1)</span>
                                <span>Max (16)</span>
                            </div>
                            <input type="range" class="slider-input" min="1" max="16" value="2">
                        </div>
                    </div>
                    
                    <!-- Suitcases Filter -->
                    <div class="filter-section">
                        <h5>Suitcases capacity:</h5>
                        <div class="price-slider">
                            <div class="slider-labels">
                                <span>Min (2)</span>
                                <span>Max (16)</span>
                            </div>
                            <input type="range" class="slider-input" min="2" max="16" value="12">
                        </div>
                    </div>
                    
                    <!-- Extra Items -->
                    <div class="filter-section">
                        <h5>Extra items</h5>
                        <div class="filter-checkbox">
                            <input type="checkbox" id="meet_greet" checked>
                            <label for="meet_greet">Meet & Greet</label>
                        </div>
                        <div class="filter-checkbox">
                            <input type="checkbox" id="door_to_door" checked>
                            <label for="door_to_door">Door-to-Door</label>
                        </div>
                        <div class="filter-checkbox">
                            <input type="checkbox" id="free_child_seats" checked>
                            <label for="free_child_seats">Free child seats</label>
                        </div>
                        <div class="filter-checkbox">
                            <input type="checkbox" id="booster_seat">
                            <label for="booster_seat">Booster seat</label>
                        </div>
                        <div class="filter-checkbox">
                            <input type="checkbox" id="child_seat">
                            <label for="child_seat">Child seat (from 2 to 12 years)</label>
                        </div>
                    </div>
                </div>
            </div>
            
                         <!-- Right Column - Vehicle Cards -->
             <div class="vehicle-cards">
                 <?php foreach ($vehicles as $index => $vehicle): ?>
                 <div class="vehicle-card">
                     <div class="vehicle-content">
                         <div class="service-header">
                             <div class="vehicle-image" style="background: <?php echo $index % 2 == 0 ? '#000' : '#6c757d'; ?>;">
                                 <?php echo $vehicle['name']; ?>
                             </div>
                             <div class="vehicle-info">
                                 <h2><?php echo $vehicle['name']; ?> (or similar)</h2>
                                 <div class="vehicle-subtitle">Premium service with professional driver</div>
                             </div>
                         </div>
                         
                         <div class="service-overview">
                             <div class="overview-item">
                                 <div class="overview-icon"></div>
                                 <div class="overview-text">
                                     <strong>Passengers</strong><br>
                                     Min: 1 - Max: <?php echo $vehicle['capacity']; ?>
                                 </div>
                             </div>
                             <div class="overview-item">
                                 <div class="overview-icon"></div>
                                 <div class="overview-text">
                                     <strong>Suitcases Capacity</strong><br>
                                     <?php echo $vehicle['luggage']; ?> medium suitcases
                                 </div>
                             </div>
                             <div class="overview-item">
                                 <div class="overview-icon"></div>
                                 <div class="overview-text">
                                     <strong>Route Information</strong><br>
                                     <?php echo $estimatedDistance; ?> km - <?php echo round($estimatedDistance * 1.1); ?> mins
                                 </div>
                             </div>
                         </div>
                         
                         <div class="included-features">
                             <h4>Included in the price</h4>
                             <ul class="feature-list">
                                 <?php foreach ($vehicle['features'] as $feature): ?>
                                 <li class="feature-item">
                                     <div class="feature-check"></div>
                                     <?php echo $feature; ?>
                                 </li>
                                 <?php endforeach; ?>
                             </ul>
                         </div>
                         
                         <div class="more-info-toggle" onclick="toggleMoreInfo('<?php echo $vehicle['class']; ?>')">
                             More information
                         </div>
                         <div class="more-info-content" id="moreInfo<?php echo ucfirst($vehicle['class']); ?>">
                             <?php echo $vehicle['description']; ?>
                         </div>
                     </div>
                     
                     <div class="pricing-section">
                         <?php if ($vehicle['discount'] > 0): ?>
                                                   <div class="price-old" style="text-decoration: line-through; color: #999; font-size: 18px;">
                              <?php echo $currency_manager->format($vehicle['price_old']); ?>
                          </div>
                         <div class="coupon-badge" style="background: #28a745; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; margin-bottom: 10px;">
                             <?php echo $vehicle['coupon']['code']; ?> - <?php echo $vehicle['coupon']['type'] === 'percentage' ? $vehicle['coupon']['value'] . '%' : '€' . $vehicle['coupon']['value']; ?> OFF
                         </div>
                         <?php endif; ?>
                         
                                                   <div class="price-amount"><?php echo $currency_manager->format($vehicle['price']); ?></div>
                         <div class="price-label">Final price</div>
                         <button class="book-now-btn" onclick="bookVehicle('<?php echo $vehicle['class']; ?>', <?php echo $vehicle['price']; ?>)" <?php echo !$vehicle['available'] ? 'disabled' : ''; ?>>
                             <?php echo $vehicle['available'] ? 'Book now' : 'Not available'; ?>
                         </button>
                         <div class="contact-options">
                             <div class="contact-icon">📧</div>
                             <div class="contact-icon">📱</div>
                         </div>
                     </div>
                 </div>
                 <?php endforeach; ?>
             </div>
        </div>
    </div>
    
              <script>
                   // Para birimi verilerini JavaScript'e aktar
          const currencyData = <?php echo json_encode($currency_manager->getCurrencyDataForJS()); ?>;
          
          // Araç verilerini JavaScript'e aktar
          const vehicles = <?php echo json_encode($vehicles); ?>;
          const currentFilters = {
             priceMin: <?php echo min(array_column($vehicles, 'price')); ?>,
             priceMax: <?php echo max(array_column($vehicles, 'price')); ?>,
             passengersMin: 1,
             passengersMax: <?php echo max(array_column($vehicles, 'capacity')); ?>,
             suitcasesMin: 2,
             suitcasesMax: <?php echo max(array_column($vehicles, 'luggage')); ?>,
             meetGreet: true,
             doorToDoor: true,
             freeChildSeats: true,
             boosterSeat: false,
             childSeat: false
         };
         
         function toggleMoreInfo(vehicleType) {
             const toggle = document.querySelector(`[onclick="toggleMoreInfo('${vehicleType}')"]`);
             const content = document.getElementById(`moreInfo${vehicleType.charAt(0).toUpperCase() + vehicleType.slice(1)}`);
             
             if (content.style.display === 'block') {
                 content.style.display = 'none';
                 toggle.classList.remove('expanded');
             } else {
                 content.style.display = 'block';
                 toggle.classList.add('expanded');
             }
         }
         
                   // Para birimi formatlama fonksiyonu
          function formatCurrency(amount, currency = null) {
              if (!currency) {
                  currency = currencyData.current;
              }
              
              if (!currencyData.currencies[currency]) {
                  return amount.toFixed(2);
              }
              
              const currencyInfo = currencyData.currencies[currency];
              const formattedAmount = amount.toFixed(2);
              
              if (currencyInfo.position === 'before') {
                  return currencyInfo.symbol + formattedAmount;
              } else {
                  return formattedAmount + currencyInfo.symbol;
              }
          }
          
          function bookVehicle(vehicleClass, price) {
              const params = new URLSearchParams(window.location.search);
              params.set('vehicle_class', vehicleClass);
              params.set('price', price);
              window.location.href = '/mytransfers/booking.php?' + params.toString();
          }
         
         // Filtre fonksiyonları
         function updatePriceFilter() {
             const priceSlider = document.querySelector('.slider-input');
             const currentPrice = parseFloat(priceSlider.value);
             
             // Fiyat filtresini güncelle
             currentFilters.priceMax = currentPrice;
             applyFilters();
         }
         
         function updatePassengersFilter() {
             const passengersSlider = document.querySelectorAll('.slider-input')[1];
             const currentPassengers = parseInt(passengersSlider.value);
             
             currentFilters.passengersMax = currentPassengers;
             applyFilters();
         }
         
         function updateSuitcasesFilter() {
             const suitcasesSlider = document.querySelectorAll('.slider-input')[2];
             const currentSuitcases = parseInt(suitcasesSlider.value);
             
             currentFilters.suitcasesMax = currentSuitcases;
             applyFilters();
         }
         
         function updateExtraItems() {
             currentFilters.meetGreet = document.getElementById('meet_greet').checked;
             currentFilters.doorToDoor = document.getElementById('door_to_door').checked;
             currentFilters.freeChildSeats = document.getElementById('free_child_seats').checked;
             currentFilters.boosterSeat = document.getElementById('booster_seat').checked;
             currentFilters.childSeat = document.getElementById('child_seat').checked;
             
             applyFilters();
         }
         
         function applyFilters() {
             const vehicleCards = document.querySelectorAll('.vehicle-card');
             
             vehicleCards.forEach((card, index) => {
                 const vehicle = vehicles[index];
                 let show = true;
                 
                 // Fiyat filtresi
                 if (vehicle.price > currentFilters.priceMax) {
                     show = false;
                 }
                 
                 // Yolcu kapasitesi filtresi
                 if (vehicle.capacity < currentFilters.passengersMax) {
                     show = false;
                 }
                 
                 // Bavul kapasitesi filtresi
                 if (vehicle.luggage < currentFilters.suitcasesMax) {
                     show = false;
                 }
                 
                 // Extra items filtreleri
                 if (currentFilters.meetGreet && !vehicle.meetandgreet) {
                     show = false;
                 }
                 
                 if (show) {
                     card.style.display = 'grid';
                 } else {
                     card.style.display = 'none';
                 }
             });
             
             // Sonuç sayısını güncelle
             const visibleCards = document.querySelectorAll('.vehicle-card[style*="grid"]').length;
             console.log(`${visibleCards} araç bulundu`);
         }
         
         // Event listeners
         document.addEventListener('DOMContentLoaded', function() {
             // Slider event listeners
             const sliders = document.querySelectorAll('.slider-input');
             sliders[0].addEventListener('input', updatePriceFilter);
             sliders[1].addEventListener('input', updatePassengersFilter);
             sliders[2].addEventListener('input', updateSuitcasesFilter);
             
             // Checkbox event listeners
             const checkboxes = document.querySelectorAll('.filter-checkbox input[type="checkbox"]');
             checkboxes.forEach(checkbox => {
                 checkbox.addEventListener('change', updateExtraItems);
             });
         });
     </script>
    
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <?php include 'includes/scripts.php'; ?>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
ob_end_flush();
?>
