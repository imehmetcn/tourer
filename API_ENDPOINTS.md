# MyTransfers API Endpoints

Bu dosya projenizde mevcut olan tüm API endpoint'lerini listeler.

## Arama ve Lokasyon

### GET /api/search
- **Açıklama**: Lokasyon arama ve otomatik tamamlama
- **Parametreler**: 
  - `query` (string): Arama terimi
  - `lang` (string, opsiyonel): Dil kodu (varsayılan: en)
- **Örnek**: `/api/search?query=Madrid&lang=en`

### GET /api/predictions
- **Açıklama**: Google Places otomatik tamamlama
- **Parametreler**: 
  - `q` veya `query` (string): Arama terimi
  - `lang` (string, opsiyonel): Dil kodu
- **Örnek**: `/api/predictions?q=Madrid Airport`

### GET /api/prediction
- **Açıklama**: Belirli bir place_id için koordinat bilgisi
- **Parametreler**: 
  - `place_id` (string): Google Places place_id
- **Örnek**: `/api/prediction?place_id=ChIJgTwKgJcpQg0RaSKMYcHeNsQ`

## Transfer Listeleme ve Fiyatlandırma

### GET /api/list
- **Açıklama**: Mevcut transfer seçeneklerini listeler
- **Parametreler**: 
  - `lat1`, `lng1`: Başlangıç koordinatları
  - `lat2`, `lng2`: Bitiş koordinatları
  - `passengers` veya `adults`: Yolcu sayısı
- **Örnek**: `/api/list?lat1=40.472&lng1=-3.56&lat2=40.4168&lng2=-3.7038&passengers=2`

### POST /api/requote
- **Açıklama**: Fiyat yeniden hesaplama
- **Body**: JSON formatında transfer detayları
- **Örnek**: `{"pickup": {...}, "dropoff": {...}, "passengers": 2}`

## Rezervasyon ve Ödeme

### POST /api/reservation
- **Açıklama**: Yeni rezervasyon oluşturma
- **Body**: JSON formatında rezervasyon detayları
- **Gerekli alanlar**: `pickup_date`, `return_date`, `passengers`, `pickup`, `dropoff`

### POST /api/book
- **Açıklama**: Rezervasyon oluşturma (reservation ile aynı)
- **Body**: JSON formatında rezervasyon detayları

### GET /api/booking
- **Açıklama**: Rezervasyon sorgulama
- **Parametreler**: 
  - `booking_id` (string): Rezervasyon ID
  - `email` (string, opsiyonel): E-posta adresi
- **Örnek**: `/api/booking?booking_id=bk_123456&email=user@example.com`

### POST /api/checkout
- **Açıklama**: Ödeme sayfasına yönlendirme
- **Body**: `{"booking_id": "bk_123456"}`

## Ödeme İşlemleri

### POST /api/payment/create
- **Açıklama**: Ödeme oturumu oluşturma
- **Body**: `{"booking_id": "bk_123456", "amount": 49.90, "currency": "EUR"}`

### POST /api/payment/confirm
- **Açıklama**: Ödeme onaylama
- **Body**: `{"booking_id": "bk_123456"}`

### POST /api/payment/cancel
- **Açıklama**: Ödeme iptal etme
- **Body**: `{"booking_id": "bk_123456"}`

### GET|POST /api/payment/webhook
- **Açıklama**: Ödeme sağlayıcısından webhook
- **Parametreler**: `bookingId`, `status`

### GET /api/payment/test
- **Açıklama**: Ödeme sağlayıcısı bağlantı testi

## Voucher ve Bilet

### GET /api/voucher
- **Açıklama**: Voucher/bilet görüntüleme
- **Parametreler**: 
  - `booking_id` (string): Rezervasyon ID
  - `token` (string): Güvenlik token'ı
- **Örnek**: `/api/voucher?booking_id=bk_123456&token=abc123`

### GET /api/voucher/token
- **Açıklama**: Voucher token'ı oluşturma
- **Parametreler**: 
  - `booking_id` (string): Rezervasyon ID

## Coğrafi Veriler

### GET /api/countries
- **Açıklama**: Ülke listesi
- **Örnek**: `[{"code": "ES", "name": "Spain"}, ...]`

### GET /api/provinces
- **Açıklama**: İl/eyalet listesi
- **Örnek**: `[{"code": "MD", "name": "Madrid"}, ...]`

### GET /api/provinces/municipalities
- **Açıklama**: İlçe/belediye listesi
- **Örnek**: `[{"code": "MAD", "name": "Madrid"}, ...]`

### GET /api/destinations
- **Açıklama**: Popüler destinasyonlar
- **Örnek**: Havalimanları ve şehir merkezleri

## Harita ve Mesafe

### GET /api/map
- **Açıklama**: Harita verileri ve rota bilgisi
- **Parametreler**: 
  - `lat1`, `lng1`: Başlangıç koordinatları
  - `lat2`, `lng2`: Bitiş koordinatları
- **Örnek**: `/api/map?lat1=40.472&lng1=-3.56&lat2=40.4168&lng2=-3.7038`

### GET /api/distance
- **Açıklama**: İki nokta arası mesafe hesaplama
- **Parametreler**: 
  - `lat1`, `lng1`: Başlangıç koordinatları
  - `lat2`, `lng2`: Bitiş koordinatları
- **Örnek**: `/api/distance?lat1=40.472&lng1=-3.56&lat2=40.4168&lng2=-3.7038`

## Yardımcı Endpoint'ler

### POST /api/url
- **Açıklama**: URL kısaltma servisi
- **Body**: `{"url": "https://example.com/long-url"}`

### GET /api/viesCheck
- **Açıklama**: AB VAT numarası doğrulama
- **Parametreler**: VAT numarası bilgileri

### GET /api/health
- **Açıklama**: API sağlık kontrolü
- **Örnek**: `{"status": "ok", "timestamp": "2024-01-01T12:00:00Z"}`

### GET /api/config
- **Açıklama**: Frontend konfigürasyon bilgileri
- **Örnek**: `{"currency": "EUR", "payment_provider": "mock"}`

## Özel Parametreler

Tüm endpoint'ler aşağıdaki özel parametreleri destekler:

- `origin=1`: Orijinal MyTransfers API'sine proxy
- `capture=1`: API yanıtlarını kaydet
- `replay=1`: Kaydedilmiş yanıtları kullan
- `capture_schema=1`: API şemasını kaydet

## Kimlik Doğrulama

Bazı endpoint'ler için `Authorization: Bearer TOKEN` header'ı gerekebilir.
Test için `demo-secret-token` kullanabilirsiniz.

## Hata Kodları

- `400`: Bad Request - Eksik veya geçersiz parametreler
- `401`: Unauthorized - Geçersiz token
- `404`: Not Found - Endpoint veya kaynak bulunamadı
- `405`: Method Not Allowed - Desteklenmeyen HTTP metodu
- `503`: Service Unavailable - Bakım modu