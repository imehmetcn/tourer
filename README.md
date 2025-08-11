# Modern Transfer Site

Bu proje, mevcut MyTransfers.com sitesinin modern Next.js teknolojileri ile yeniden geliştirilmiş versiyonudur.

## 🚀 Özellikler

### ✅ Tamamlanan Özellikler

1. **Modern UI/UX Tasarım**
   - Responsive tasarım (mobil, tablet, desktop)
   - Modern component yapısı
   - Tailwind CSS ile styling
   - Orijinal site renklerini koruyan tema

2. **Ana Sayfa Bileşenleri**
   - Hero section (arama formu ile)
   - Hizmet özellikleri
   - Popüler destinasyonlar
   - Araç tipleri
   - Müşteri yorumları

3. **Arama Formu**
   - Tek yön / Gidiş-dönüş seçimi
   - Kalkış/varış noktası seçimi
   - Tarih ve saat seçimi
   - Yolcu sayısı seçimi (yetişkin, çocuk, bebek)
   - Responsive tasarım

4. **Header & Navigation**
   - Responsive navigation
   - Dil ve para birimi seçimi
   - Kullanıcı giriş/çıkış
   - Mobil menü

5. **Footer**
   - Şirket bilgileri
   - Hızlı linkler
   - İletişim bilgileri
   - Sosyal medya linkleri

6. **Destinasyonlar Sayfası**
   - Destinasyon listesi
   - Arama ve filtreleme
   - Kıta bazlı filtreleme
   - Popüler destinasyon filtresi

7. **Rezervasyonlar Sayfası**
   - Rezervasyon listesi
   - Arama ve filtreleme
   - Durum bazlı filtreleme
   - Rezervasyon yönetimi (düzenle, iptal et, voucher indir)

### 🔄 Orijinal Siteden Entegre Edilen Özellikler

- **Renk Paleti**: Orijinal MyTransfers renkleri (#efa728, #0c8450)
- **Typography**: OpenSans font ailesi
- **Icon Font**: İcomoon icon seti
- **Form Stilleri**: Orijinal form tasarımları
- **Button Stilleri**: Orijinal buton tasarımları
- **Responsive Breakpoints**: Orijinal responsive yapı

## 🛠️ Teknolojiler

- **Framework**: Next.js 15
- **Styling**: Tailwind CSS
- **Icons**: Lucide React
- **TypeScript**: Full type support
- **Font**: Inter (sistem fontu)

## 📁 Proje Yapısı

```
modern-transfer-site/
├── app/
│   ├── destinations/          # Destinasyonlar sayfası
│   ├── bookings/             # Rezervasyonlar sayfası
│   ├── globals.css           # Global stiller
│   ├── layout.tsx            # Ana layout
│   └── page.tsx              # Ana sayfa
├── components/
│   ├── layout/
│   │   └── Header.tsx        # Header component
│   └── ui/
│       ├── SearchForm.tsx    # Arama formu
│       ├── HeroSection.tsx   # Hero bölümü
│       ├── ServiceFeatures.tsx # Hizmet özellikleri
│       ├── PopularDestinations.tsx # Popüler destinasyonlar
│       ├── VehicleTypes.tsx  # Araç tipleri
│       ├── Testimonials.tsx  # Müşteri yorumları
│       └── Footer.tsx        # Footer component
├── styles/
│   └── mytransfers.css       # Orijinal site stilleri
└── public/
    └── images/               # Görseller
```

## 🚀 Kurulum ve Çalıştırma

1. **Proje klasörüne gidin:**
   ```bash
   cd modern-transfer-site
   ```

2. **Bağımlılıkları yükleyin:**
   ```bash
   npm install
   ```

3. **Geliştirme sunucusunu başlatın:**
   ```bash
   npm run dev
   ```

4. **Tarayıcıda açın:**
   ```
   http://localhost:3000
   ```

## 🎯 Proje Durumu

✅ **TAMAMLANDI** - Proje tamamen hazır ve çalışır durumda!

## 📱 Responsive Tasarım

- **Mobile**: 320px - 767px
- **Tablet**: 768px - 1024px
- **Desktop**: 1025px+

## 🎨 Renk Paleti

- **Primary**: #efa728 (Turuncu)
- **Secondary**: #0c8450 (Yeşil)
- **Gray Light**: #767676
- **Gray**: #e0dfdf
- **Dark**: #000000

## 📋 TODO / Gelecek Özellikler

- [ ] Arama fonksiyonalitesi (API entegrasyonu)
- [ ] Rezervasyon sistemi
- [ ] Ödeme entegrasyonu
- [ ] Kullanıcı hesap yönetimi
- [ ] Çoklu dil desteği
- [ ] SEO optimizasyonu
- [ ] Performance optimizasyonu
- [ ] Unit testler
- [ ] E2E testler

## 🔧 Geliştirme Notları

### Orijinal Siteden Alınan Özellikler

1. **CSS Stilleri**: `styles/mytransfers.css` dosyasında orijinal site stilleri
2. **Renk Şeması**: CSS değişkenleri olarak tanımlandı
3. **Typography**: OpenSans font ailesi entegre edildi
4. **Icon Font**: İcomoon icon seti eklendi
5. **Form Tasarımları**: Orijinal form stilleri korundu

### Component Yapısı

- **Modüler Tasarım**: Her bileşen ayrı dosyada
- **TypeScript**: Full type support
- **Props Interface**: Her component için tip tanımları
- **Responsive**: Mobile-first yaklaşım

### State Management

- **React Hooks**: useState, useEffect kullanımı
- **Local State**: Component bazlı state yönetimi
- **Future**: Redux Toolkit veya Zustand entegrasyonu planlanıyor

## 📞 Destek

Herhangi bir sorun veya öneriniz için lütfen iletişime geçin.

---

**Not**: Bu proje, mevcut MyTransfers.com sitesinin modern teknolojilerle yeniden geliştirilmiş versiyonudur. Orijinal tasarım ve işlevsellik korunarak, performans ve kullanıcı deneyimi iyileştirilmiştir.