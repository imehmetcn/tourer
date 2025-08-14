# TOURER Truzim (PHP 7.4 + AngularJS assets)

## Özellikler

- [x] AngularJS tabanlı frontend varlıklarını lokal `assets/` altına klonlama
- [x] PHP 7.4 REST API (`/api/*`) – JSON cevaplar, CORS, OPTIONS
- [x] Proxy/Capture/Replay – orijinal API yanıtlarını yakalama ve tekrar oynatma
- [x] Google Places entegrasyonu (autocomplete + details)
- [x] Dinamik fiyat: mesafe + araç/region çarpanları
- [x] Zone/Zone-Matrix fiyatlandırma (tek bölge ve cross-region)
- [x] Kupon uygulama (yüzde/sabit)
- [x] Mock ödeme akışı + webhook günlüğü
- [x] Voucher ve My Bookings sayfaları
- [x] Admin paneli: Dashboard, Reservations, Pricing, Coupons, Destinations, Users, Payment, Email, Import/Export, Zones, Zone Matrix, Cross Region Matrix, Reports
- [x] RBAC (admin/editor), CSRF koruması, Rate limit, Session hardening, güvenlik başlıkları
- [x] MySQL (PDO) – `reservations` dahil DB kayıtları; JSON fallback
- [x] E-posta şablonları (HTML), loglama
- [x] Karanlık tema (varsayılan), mini-sidebar, modern animasyonlar

## Teknoloji Yığını

- Backend: PHP 7.4 (Apache + `.htaccess`)
- Veritabanı: MySQL/MariaDB (PDO)
- Frontend: AngularJS varlıkları (orijinal siteden klonlanan), jQuery
- Entegrasyon: Google Places API, (hazır) iyzico mock

## Dizin Yapısı

- `index.php` – Frontend giriş noktası (lokal `assets/` referansları, API URL override)
- `api/` – Tüm REST uçları ve yardımcılar
  - `index.php` – Router, uçlar, proxy/capture/replay, pricing, payment, voucher
  - `db.php` – PDO bağlantı yardımcıları
  - `mail.php` – HTML e-posta gönderimi ve şablonlar
  - `config.php` – İsteğe bağlı Google API anahtarı tanımı
- `admin/` – Admin paneli
  - `_bootstrap.php` – Session, CSRF, RBAC, rate limit, güvenlik başlıkları
  - `_layout.php` – Ortak şablon (header/sidebar/content)
  - `assets/admin.css` – Modern tema + animasyonlar + matrix stilleri
  - Sayfalar: `dashboard.php`, `reservations.php`, `pricing.php`, `coupons.php`, `destinations.php`, `users.php`, `payment.php`, `email.php`, `import.php`, `reports.php`, `zones.php`, `zone_matrix.php`, `zone_matrix_cross.php`, `db_setup.php`, `reservations_migrate.php`
- `public/` – `payment.html`, `voucher.html`, `my-bookings.html`, `app.html`
- `storage/` – Kalıcı veri ve loglar
  - `config.json` (ayarlar), `data/*.json` (countries, destinations, coupons, pricing, zones, matrices)
  - `reservations/reservations.json` (fallback), `logs/*`, `captures/*`, `schemas/*`, `templates/reservation.html`
- `scripts/` – Yardımcı scriptler (ör. `clone_frontend.ps1`)

## Kurulum (Windows/XAMPP)

1) Bu projeyi `C:\xampp\htdocs\mytransfers` altına kopyalayın.
2) Apache ve MySQL servislerini başlatın.
3) Veritabanı oluşturun ve yetkileri verin:
   - DB: `mytransfers`
   - Kullanıcı: `mytransfers_user` (ör. `StrongPass!123`)
4) `storage/config.json` örnek yapı:
```json
{
  "maintenance": false,
  "db_host": "localhost",
  "db_port": "3306",
  "db_name": "mytransfers",
  "db_user": "mytransfers_user",
  "db_pass": "StrongPass!123",
  "google_api_key": "",
  "voucher_secret": "local-secret",
  "payment_provider": "mock",
  "currency": "EUR",
  "cors_origin": "http://localhost"
}
```
5) DB tablolarını kurun: `http://localhost/mytransfers/admin/db_setup.php`.
6) Admin giriş: `http://localhost/mytransfers/admin/login.php`
   - Varsayılan: `admin@local / admin123` (ilk girişte değiştirin)

Not: `.htaccess` rewrite ile `/api/*` otomatik `api/index.php`’ye yönlenir.

## Çalıştırma

- Kullanıcı arayüzü: `http://localhost/mytransfers/index.php`
- Admin paneli: `http://localhost/mytransfers/admin/`
- API örnekleri:
  - GET `/mytransfers/api/search?lang=en&query=Madrid`
  - POST `/mytransfers/api/book` – `{ pickup_date, return_date, passengers, pickup, dropoff, vehicle, coupon_code?, email? }`
  - GET `/mytransfers/api/voucher?booking_id=...&token=...`

## Önemli Ayarlar

- CORS: `CORS_ORIGIN` env veya `storage/config.json` → `cors_origin`
- Google Places: `GOOGLE_API_KEY` env veya `api/config.php`
- Ödeme sağlayıcı: `payment_provider` (`mock` / `iyzico` vs.) ve ilgili anahtarlar

## Fiyatlandırma Mantığı

1) Zone Matrix öncelik (tek bölge veya cross-region)
2) Aksi halde mesafe-temelli (Haversine) + `pricing.json` (base_per_km, vehicle_multipliers, region_multipliers)
3) Kupon indirimi (yüzde/sabit)

Rezervasyon kaydında `pricing_method`, `from_zone`, `to_zone` alanları doldurulur ve admin filtrelerinde kullanılabilir.

## Güvenlik

- CSRF token’ları, RBAC (admin/editor)
- Session hardening (HttpOnly, SameSite, strict mode)
- HTTP güvenlik başlıkları (CSP dahil)
- Rate limit (login ve kritik POST’lar)
- Loglarda PII maskeleme (proxy/mail)

## Loglar

- `storage/logs/proxy.log` – Proxy/capture
- `storage/logs/webhook.log` – Ödeme webhook’ları
- `storage/logs/admin.log` – Admin işlemleri
- `storage/logs/db_error.log` – DB hataları

## Scripts

- `scripts/clone_frontend.ps1` – Orijinal CDN varlıklarını `assets/` altına indirir ve yolları günceller.

## To-Do (Yapılanlar ve Planlar)

- [x] Frontend varlıkların lokal klonu ve referansları
- [x] API iskeleti, proxy/capture/replay
- [x] Google Places entegrasyonu (opsiyonel anahtar)
- [x] Dinamik fiyat + kupon
- [x] Zone / Zone-Matrix / Cross-Region Matrix yönetimi (admin UI)
- [x] Mock ödeme akışı + webhook loglama
- [x] Voucher & My Bookings
- [x] Admin panel (Matoxi benzeri görünüm), dark mode, mini-sidebar, animasyonlar
- [x] MySQL geçişi (reservations) + JSON fallback
- [x] Güvenlik (CSRF, rate limit, security headers, input validation)
- [x] CORS yapılandırması (dev/prod)
- [ ] Reservations tablosunda `pricing_method/from_zone/to_zone` kolonlarını görünür hale getirme + CSV export’a ekleme
- [ ] Zones UI: alias düzenleme için daha gelişmiş alanlar (tooltip/etiket editörü)
- [ ] Gerçek ödeme entegrasyonu (Stripe / iyzico / PayTR) + webhook doğrulama
- [ ] Email/SMS şablonlarını çoğaltma (payment received, canceled vb.)
- [ ] Çok dilli yönetim (admin’den dil dosyaları)
- [ ] Import/Export geliştirmeleri, doğrulama raporları
- [ ] Docker compose (apache/php/mysql) + basic CI (GitHub Actions)
- [ ] Health endpoint `/api/health` + admin “system status”
- [ ] Caching (APCu/file) – countries/destinations/predictions
- [ ] GDPR: IP anonymization, cookie consent

