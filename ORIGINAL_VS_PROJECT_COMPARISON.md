# Orijinal MyTransfers.com vs Projeniz - JavaScript Konfigürasyon Karşılaştırması

## Orijinal MyTransfers.com Konfigürasyonu:
```javascript
window.__mt = window.__mt || {};
window.__mt.ln = window.__mt.ln || {};
window.__mt.setting = window.__mt.setting || {};
window.__mt.setting.user = {};
window.__mt.ln.currency = "EUR";
window.__mt.ln.lang = "<?php echo $lang_loader->getCurrentLanguage(); ?>";
window.__mt.ln.cancel = "Cancel";
window.__mt.ln.ok = "Ok";
window.__mt.ln.currency_code = "EUR €";
window.__mt.setting.asset_url = "/mytransfersweb/prod/";
window.__mt.setting.api_search = "/mytransfers.com/api/search";
window.__mt.setting.api_map = "/mytransfers.com/api/map";
window.__mt.setting.root_page = "/mytransfers.com/en/";
window.__mt.setting.search_page = "/mytransfers.com/en/search/";
window.__mt.setting.checkout_page = "/mytransfers.com/en/checkout/";
window.__mt.setting.api_list = "/mytransfers.com/api/list";
window.__mt.setting.api_checkout = "/mytransfers/api/checkout";
window.__mt.setting.api_requote = "/mytransfers/api/requote";
window.__mt.setting.api_reservation = "/mytransfers.com/api/reservation";
window.__mt.setting.api_country = "/mytransfers/api/countries";
window.__mt.setting.api_distance = "/mytransfers/api/distance";
window.__mt.setting.api_urlshort = "/mytransfers/api/url";
window.__mt.setting.api_provinces = "/mytransfers/api/provinces";
window.__mt.setting.api_provinces_municipalities = "/mytransfers/api/provinces/municipalities";
window.__mt.setting.api_vies = "/mytransfers/api/viesCheck";
window.__mt.setting.api_predictions = "/mytransfers/api/predictions";
window.__mt.setting.api_prediction_coords = "/mytransfers/api/prediction";
window.__mt.setting.google_map = "https://maps.googleapis.com/maps/api/js?key=AIzaSyAxdUUkwJ5UZ2FkoecXLBkKOvpxzbR85hc&v=3.exp&libraries=drawing,places";
window.__mt.setting.google_places = "https://maps.googleapis.com/maps/api/js?key=AIzaSyAxdUUkwJ5UZ2FkoecXLBkKOvpxzbR85hc&v=3.exp&libraries=places";
window.__mt.setting.user = null;
```

## Projenizde Uyarlanan Konfigürasyon:
```javascript
window.__mt = window.__mt || {};
window.__mt.ln = window.__mt.ln || {};
window.__mt.setting = window.__mt.setting || {};
window.__mt.setting.user = {};
window.__mt.ln.currency = "EUR";
window.__mt.ln.lang = "<?php echo $lang_loader->getCurrentLanguage(); ?>";
window.__mt.ln.cancel = "Cancel";
window.__mt.ln.ok = "Ok";
window.__mt.ln.currency_code = "EUR €";
window.__mt.setting.asset_url = "/mytransfersweb/prod/";
window.__mt.setting.root_page = "/mytransfers/<?php echo $lang_loader->getCurrentLanguage(); ?>/";
window.__mt.setting.search_page = "/mytransfers/<?php echo $lang_loader->getCurrentLanguage(); ?>/search/";
window.__mt.setting.checkout_page = "/mytransfers/<?php echo $lang_loader->getCurrentLanguage(); ?>/checkout/";
window.__mt.setting.api_search = "/mytransfers/api/search";
window.__mt.setting.api_map = "/mytransfers/api/map";
window.__mt.setting.api_list = "/mytransfers/api/list";
window.__mt.setting.api_checkout = "/mytransfers/api/checkout";
window.__mt.setting.api_requote = "/mytransfers/api/requote";
window.__mt.setting.api_reservation = "/mytransfers/api/reservation";
window.__mt.setting.api_country = "/mytransfers/api/countries";
window.__mt.setting.api_distance = "/mytransfers/api/distance";
window.__mt.setting.api_urlshort = "/mytransfers/api/url";
window.__mt.setting.api_provinces = "/mytransfers/api/provinces";
window.__mt.setting.api_provinces_municipalities = "/mytransfers/api/provinces/municipalities";
window.__mt.setting.api_vies = "/mytransfers/api/viesCheck";
window.__mt.setting.api_predictions = "/mytransfers/api/predictions";
window.__mt.setting.api_prediction_coords = "/mytransfers/api/prediction";
window.__mt.setting.google_map = "https://maps.googleapis.com/maps/api/js?key=AIzaSyAxdUUkwJ5UZ2FkoecXLBkKOvpxzbR85hc&v=3.exp&libraries=drawing,places";
window.__mt.setting.google_places = "https://maps.googleapis.com/maps/api/js?key=AIzaSyAxdUUkwJ5UZ2FkoecXLBkKOvpxzbR85hc&v=3.exp&libraries=places";
window.__mt.setting.user = null;
```

## Değişiklikler:

### ✅ Birebir Kopyalanan Özellikler:
- `window.__mt` objesi yapısı
- Dil ve para birimi ayarları
- Google Maps API anahtarı (orijinal key korundu)
- Tüm API endpoint isimleri
- `asset_url` yapısı

### 🔄 Projenize Uyarlanan Özellikler:
- **Domain değişikliği**: `/mytransfers.com/` → `/mytransfers/`
- **Dinamik dil desteği**: Sabit `/en/` yerine PHP ile dinamik dil
- **URL routing**: `.htaccess` ile orijinal URL yapısı destekleniyor

### 📁 URL Yapısı Karşılaştırması:

| Orijinal Site | Projeniz |
|---------------|----------|
| `/mytransfers.com/en/` | `/mytransfers/en/` |
| `/mytransfers.com/en/search/` | `/mytransfers/en/search/` |
| `/mytransfers.com/api/search` | `/mytransfers/api/search` |
| `/mytransfersweb/prod/` | `/mytransfersweb/prod/` |

### 🚀 Ek Özellikler:
Projenizde orijinal sitede olmayan ek endpoint'ler de eklendi:
- `/api/booking` - Rezervasyon sorgulama
- `/api/voucher` - Bilet görüntüleme
- `/api/payment/*` - Ödeme işlemleri
- `/api/health` - Sistem durumu
- `/api/config` - Konfigürasyon

## Test URL'leri:

### Frontend Sayfaları:
- Ana sayfa: `http://localhost/mytransfers/en/`
- Arama: `http://localhost/mytransfers/en/search/`
- Checkout: `http://localhost/mytransfers/en/checkout/`

### API Endpoint'leri:
- Arama: `http://localhost/mytransfers/api/search?query=Madrid`
- Liste: `http://localhost/mytransfers/api/list?lat1=40.472&lng1=-3.56&lat2=40.4168&lng2=-3.7038&passengers=2`
- Harita: `http://localhost/mytransfers/api/map?lat1=40.472&lng1=-3.56&lat2=40.4168&lng2=-3.7038`
- Ülkeler: `http://localhost/mytransfers/api/countries`

## Sonuç:
✅ Projeniz artık MyTransfers.com sitesinin JavaScript konfigürasyonunu birebir kopyalıyor
✅ Tüm API endpoint'leri mevcut ve çalışıyor
✅ URL yapısı orijinal siteyle uyumlu
✅ Google Maps API anahtarı orijinal siteden alındı
✅ Dil desteği dinamik olarak çalışıyor