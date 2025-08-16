<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

// Çeviri dosyalarının yolu
$langDir = __DIR__ . '/../assets/mytransfersweb/prod/js/lang/';
$translationsFile = $STORAGE . '/translations.json';

// Mevcut diller
$languages = [
    'en' => 'English',
    'tr' => 'Türkçe', 
    'de' => 'Deutsch',
    'fr' => 'Français',
    'es' => 'Español'
];

// Çeviri kategorileri
$categories = [
    'basic' => 'Temel Metinler',
    'forms' => 'Form Alanları',
    'dates' => 'Tarih/Saat',
    'payment' => 'Ödeme',
    'services' => 'Hizmetler',
    'status' => 'Durumlar',
    'homepage' => 'Anasayfa İçerikleri',
    'booking_form' => 'Rezervasyon Formu',
    'validation_errors' => 'Form Validasyon Hataları',
    'countries' => 'Ülkeler',
    'airports' => 'Havalimanları'
];

// Çeviri anahtarları (İngilizce referans)
$translationKeys = [
    'basic' => [
        'continue' => 'Continue',
        'pay' => 'Place order',
        'loading' => 'loading..',
        'send_by' => 'Mytransfers Offer',
        'free' => ' Free',
        'mins' => 'mins',
        'locale_lang' => 'en',
        'locale' => 'en-us'
    ],
    
    'forms' => [
        'adult' => 'Adult',
        'adults' => 'Adults',
        'minors' => 'Children',
        'oneway' => 'One-way',
        'roundtrip' => 'Round-Trip',
        'msn_error' => 'An error occurred at the end of the process, please contact customer service or try again.',
        'pickup_date_flight' => 'Flight arrival date and time',
        'pickup_date_train' => 'Train arrival date and time',
        'pickup_date_port' => 'Port arrival date and time',
        'pickup_date_address' => 'Pickup date and time',
        'dropoff_date_flight' => 'Flight departure date and time',
        'dropoff_date_train' => 'Train departure date and time',
        'dropoff_date_port' => 'Port departure date and time',
        'dropoff_date_address' => 'Dropoff date and time',
        'msn_searchbox_date_pickup' => 'Please select your flight arrival time. We track all flights and our driver will be waiting for you',
        'msn_searchbox_date_dropoff' => 'Please select your flight departure time. In the final step you can select pickup time from your hotel/address',
        'msn_searchbox_date_airport_to_airport' => 'Please select your flight arrival time. We track all flights and our driver will be waiting for you',
        'msn_searchbox_pickuptime' => 'Please select your pickup time',
        'msn_searchbox_pickupdate' => 'Select your departure date',
        'msn_searchbox_returndate' => 'Select your return date'
    ],
    
    'dates' => [
        'daysOfWeek' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        'monthNames' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
    ],
    
    'payment' => [
        'payment_name' => 'Select payment method',
        'payment_description' => 'Select from the list of payment methods',
        'payment_img' => 'https://www.mytransfers.com/assets/mytransfersweb/prod/images/payment/credit-card.png',
        'credit_card_name' => 'Credit or debit card',
        'credit_card_description' => 'Pay with credit card',
        'credit_card_code' => 'credit_card'
    ],
    
    'services' => [
        'includes' => [
            'free_amendments' => 'Free amendments',
            'professional_driver' => 'Professional driver',
            'instant_confirmation' => 'Instant confirmation',
            'meet_greet' => 'Meet & greet service and welcome sign',
            'free_cancellations' => 'Free cancellations (up to 24 hours before your arrival)'
        ],
        'extras' => [
            'door_to_door' => 'Door to Door',
            'free_child_seats' => 'Free child seats',
            'meet_greet_service' => 'Meet & Greet Service'
        ]
    ],
    
    'status' => [
        'completed' => 'Completed',
        'on_hold' => 'Cancelled',
        'cancelled' => 'Cancelled',
        'noshowprovider' => 'Cancelled',
        'processing' => 'Cancelled',
        'confirmed' => 'Completed'
    ],
    
    'homepage' => [
        'banner_title' => 'Book your transfer to the Airport or your private ride with ease. Enjoy our service at best rates available.',
        'why_different_title' => 'Discover why we are different',
        'door_to_door_title' => 'Door to Door',
        'door_to_door_desc' => 'From the Airport directly to your destination',
        'private_transfers_title' => 'Private Transfers',
        'private_transfers_desc' => 'We only offer private transfers, no shared services',
        'meet_greet_title' => 'Meet & Greet',
        'meet_greet_desc' => 'Our driver will meet you in the arrival hall',
        'customer_support_title' => '24/7 Customer Support',
        'customer_support_desc' => 'We are here to help you! Before, during and after your journey',
        'reviews_title' => 'Our Reviews',
        'reviews_rated' => 'Excellent rating based on 7,420 reviews on Trustpilot',
        'excellent_service' => 'Excellent Service',
        'did_you_know_title' => 'Did you know?',
        'did_you_know_1' => 'We know that finding taxi service in a foreign country can be difficult and stressful. Book in advance and enjoy the best all-inclusive service with no surprises.',
        'did_you_know_2' => 'MyTransfers offers reliable transfers from major airports worldwide, safer, more comfortable and cheaper than taxi service. We have a vehicle for every situation.',
        'comfort_safety_title' => 'Comfort and Safety',
        'comfort_safety_desc' => 'All our vehicles are equipped with air conditioning and our drivers are professional and punctual',
        'how_it_works_title' => 'How it works',
        'step_1_title' => 'Book your transfer',
        'step_1_desc' => 'Choose your destination and book your transfer online',
        'step_2_title' => 'Receive confirmation',
        'step_2_desc' => 'Get instant confirmation and all details by email',
        'step_3_title' => 'Meet your driver',
        'step_3_desc' => 'Your driver will be waiting for you with a welcome sign',
        'popular_destinations_title' => 'Popular Destinations'
    ],
    
    'booking_form' => [
        'search_title' => 'Are you looking for airport transfers?',
        'search_subtitle' => 'You have come to the right place',
        'oneway' => 'One-way',
        'roundtrip' => 'Round-Trip',
        'passengers' => 'Passengers',
        'adults' => 'Adults',
        'children' => 'Children',
        'infants' => 'Infants',
        'from' => 'From',
        'to' => 'To',
        'pickup_location' => 'Pickup location',
        'dropoff_location' => 'Dropoff location',
        'pickup_date' => 'Pickup date',
        'return_date' => 'Return date',
        'time' => 'Time',
        'add_return' => '+ Add return',
        'search' => 'Search',
        'apply' => 'Apply',
        'see_more_results' => 'See more results',
        'loading' => 'Loading...',
        'no_results' => 'No results',
        'loading_message' => 'We are searching for the best transfer options for you...',
        'loading_wait' => 'Please wait'
    ],
    
    'validation_errors' => [
        'from_required' => 'Please select a pickup location',
        'to_required' => 'Please select a dropoff location',
        'pickup_date_required' => 'Please select a pickup date',
        'return_date_required' => 'Please select a return date',
        'general_error' => 'An error occurred. Please try again.'
    ],
    
    // Dil özel çeviriler
    'translations' => [
        'tr' => [
            'basic' => [
                'continue' => 'Devam Et',
                'pay' => 'Sipariş Ver',
                'loading' => 'yükleniyor..',
                'send_by' => 'Mytransfers Teklifi',
                'free' => ' Ücretsiz',
                'mins' => 'dakika',
                'locale_lang' => 'tr',
                'locale' => 'tr-tr'
            ],
            'forms' => [
                'adult' => 'Yetişkin',
                'adults' => 'Yetişkin',
                'minors' => 'Çocuk',
                'oneway' => 'Tek Yön',
                'roundtrip' => 'Gidiş-Dönüş',
                'msn_error' => 'İşlem sonunda bir hata oluştu, lütfen müşteri hizmetleri ile iletişime geçin veya tekrar deneyin.',
                'pickup_date_flight' => 'Uçuş varış tarihi ve saati',
                'pickup_date_train' => 'Tren varış tarihi ve saati',
                'pickup_date_port' => 'İniş tarihi ve saati',
                'pickup_date_address' => 'Alış tarihi ve saati',
                'dropoff_date_flight' => 'Uçuş kalkış tarihi ve saati',
                'dropoff_date_train' => 'Tren kalkış tarihi ve saati',
                'dropoff_date_port' => 'Liman kalkış tarihi ve saati',
                'dropoff_date_address' => 'Bırakış tarihi ve saati',
                'msn_searchbox_date_pickup' => 'Lütfen uçuş varış saatinizi seçin. Tüm uçuşları takip ediyoruz ve şoförümüz sizi bekliyor olacak',
                'msn_searchbox_date_dropoff' => 'Lütfen uçuş kalkış saatinizi seçin. Son adımda otel/adresinizden alış saatini seçebilirsiniz',
                'msn_searchbox_date_airport_to_airport' => 'Lütfen uçuş varış saatinizi seçin. Tüm uçuşları takip ediyoruz ve şoförümüz sizi bekliyor olacak',
                'msn_searchbox_pickuptime' => 'Lütfen alış saatinizi seçin',
                'msn_searchbox_pickupdate' => 'Gidiş tarihinizi seçin',
                'msn_searchbox_returndate' => 'Dönüş tarihinizi seçin'
            ],
            'dates' => [
                'daysOfWeek' => ['Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt'],
                'monthNames' => ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık']
            ],
            'payment' => [
                'payment_name' => 'Ödeme yöntemini seçin',
                'payment_description' => 'Ödeme yöntemleri listesinden seçin',
                'credit_card_name' => 'Kredi veya banka kartı',
                'credit_card_description' => 'Kredi kartı ile öde'
            ],
            'services' => [
                'includes' => [
                    'free_amendments' => 'Ücretsiz değişiklik',
                    'professional_driver' => 'Profesyonel şoför',
                    'instant_confirmation' => 'Anında onay',
                    'meet_greet' => 'Karşılama hizmeti ve hoş geldin tabelası',
                    'free_cancellations' => 'Ücretsiz iptal (Varışınızdan 24 saat öncesine kadar)'
                ],
                'extras' => [
                    'door_to_door' => 'Kapıdan Kapıya',
                    'free_child_seats' => 'Ücretsiz çocuk koltuğu',
                    'meet_greet_service' => 'Karşılama Hizmeti'
                ]
            ],
            'status' => [
                'completed' => 'Tamamlandı',
                'on_hold' => 'İptal Edildi',
                'cancelled' => 'İptal Edildi',
                'noshowprovider' => 'İptal Edildi',
                'processing' => 'İptal Edildi',
                'confirmed' => 'Tamamlandı'
            ],
            'homepage' => [
                'banner_title' => 'Havalimanı transferinizi veya özel yolculuğunuzu kolayca rezerve edin. Hizmetimizden en iyi fiyatlarla yararlanın.',
                'why_different_title' => 'Neden farklı olduğumuzu keşfedin',
                'door_to_door_title' => 'Kapıdan Kapıya',
                'door_to_door_desc' => 'Havalimanından doğrudan destinasyonunuza',
                'private_transfers_title' => 'Özel Transferler',
                'private_transfers_desc' => 'Sadece özel transferler sunuyoruz, paylaşımlı hizmet yok',
                'meet_greet_title' => 'Karşılama',
                'meet_greet_desc' => 'Şoförümüz varış salonunda sizi karşılayacak',
                'customer_support_title' => '7/24 Müşteri Desteği',
                'customer_support_desc' => 'Size yardım etmek için buradayız! Yolculuğunuzdan önce, sırasında ve sonrasında',
                'reviews_title' => 'Değerlendirmelerimiz',
                'reviews_rated' => 'Trustpilot\'ta 7.420 değerlendirmeye dayalı olarak Mükemmel puanı',
                'excellent_service' => 'Mükemmel Hizmet',
                'did_you_know_title' => 'Biliyor muydunuz?',
                'did_you_know_1' => 'Yabancı bir ülkede taksi hizmeti bulmanın zor ve stresli olabileceğini biliyoruz. Önceden rezerve edin ve sürpriz olmayan, her şey dahil en iyi hizmetin keyfini çıkarın.',
                'did_you_know_2' => 'MyTransfers, dünya çapındaki büyük havalimanlarından güvenilir transferler sunar, taksi hizmetinden daha güvenli, konforlu ve ucuzdur. Her durum için uygun bir aracımız var.',
                'did_you_know_3' => 'Birkaç mevcut araç arasından seçim yapabilirsiniz, böylece her zaman maksimum konforun keyfini çıkarabilirsiniz. Tek başınıza, çift olarak veya grup halinde seyahat ediyor olsanız da, her durum için mükemmel aracımız var.',
                'did_you_know_4' => 'Şoför sizi doğrudan havalimanı terminalinden alacak, bagajınızla yardım edecek ve sizi doğrudan otelinize veya başka bir destinasyona götürecek, ve tam tersi.',
                'comfort_safety_title' => 'Yolculuğunuz sırasında maksimum konfor ve güvenlik',
                'comfort_safety_subtitle' => 'Yetkili araçlar, deneyimli şoförler',
                'economy_class_title' => 'EKONOMİ SINIFI',
                'economy_class_desc' => 'Çift veya çocuklu aile için',
                'business_class_title' => 'İŞ SINIFI',
                'business_class_desc' => 'İş seyahatleri için konforlu',
                'groups_title' => 'GRUPLAR İÇİN',
                'groups_desc' => '19 kişiye kadar gruplar veya büyük bagajlı kişiler için',
                'how_it_works_title' => 'Nasıl Çalışır?',
                'step1_title' => 'Dünya çapında havalimanı transferinizi rezerve edin',
                'step1_desc1' => 'Mytransfers, dünya çapında anında onay ile özel havalimanı transfer rezervasyonları sunar. Doğrudan yerel şoförlerle çalışır ve müşterilerimiz için en iyi hizmetleri seçeriz. Bu şekilde tatilinizin mümkün olan en iyi şekilde başlamasını sağlarız.',
                'step1_desc2' => 'Hizmetimizi sürekli gözden geçiriyoruz, böylece seçtiğiniz herhangi bir destinasyonda Mytransfers ile yolculuğunuzun keyfini çıkaracaksınız.',
                'step2_title' => 'Kolay transfer rezervasyon sürecimiz',
                'step2_desc' => 'Menşe ve destinasyonunuzu seçin ve sadece 3 tıklamada özel transferiniz rezerve edilmiş olacak. Havalimanında uzun taksi kuyruklarını beklemeyi unutun, şoförümüz varışınızda bir tabela ile sizi bekliyor olacak.',
                'step3_title' => 'Havalimanı, Liman veya Tren İstasyonları',
                'step3_desc' => 'Bizimle herhangi bir havalimanı, liman veya tren istasyonundan özel transferinizi rezerve edebilirsiniz. Transferinizin zamanında ve kalite ve güvenlik garantileriyle gerçekleştirilmesini sağlamak için ulaşım araçlarının varışını izliyoruz.',
                'popular_destinations_title' => 'En Popüler Destinasyonlar'
            ],
            'booking_form' => [
                'search_title' => 'Havalimanı transferleri mi arıyorsunuz?',
                'search_subtitle' => 'Doğru yere geldiniz',
                'oneway' => 'Tek Yön',
                'roundtrip' => 'Gidiş-Dönüş',
                'passengers' => 'Yolcular',
                'adults' => 'Yetişkin',
                'children' => 'Çocuk',
                'infants' => 'Bebek',
                'from' => 'Nereden',
                'to' => 'Nereye',
                'pickup_location' => 'Alış noktası',
                'dropoff_location' => 'Bırakış noktası',
                'pickup_date' => 'Alış tarihi',
                'return_date' => 'Dönüş tarihi',
                'time' => 'Saat',
                'add_return' => '+ Dönüş ekle',
                'search' => 'Ara',
                'apply' => 'Uygula',
                'see_more_results' => 'Daha fazla sonuç gör',
                'loading' => 'Yükleniyor...',
                'no_results' => 'Sonuç bulunamadı',
                'loading_message' => 'Sizin için en iyi transfer seçeneklerini arıyoruz...',
                'loading_wait' => 'Lütfen bekleyin'
            ],
            'validation_errors' => [
                'from_required' => 'Lütfen kalkış noktasını seçiniz',
                'to_required' => 'Lütfen varış noktasını seçiniz',
                'pickup_date_required' => 'Lütfen kalkış tarihini seçiniz',
                'return_date_required' => 'Lütfen dönüş tarihini seçiniz',
                'general_error' => 'Bir hata oluştu. Lütfen tekrar deneyiniz.'
            ]
        ],
        'de' => [
            'basic' => [
                'continue' => 'Weiter',
                'pay' => 'Bestellung aufgeben',
                'loading' => 'lädt..',
                'send_by' => 'Mytransfers Angebot',
                'free' => ' Kostenlos',
                'mins' => 'Min',
                'locale_lang' => 'de',
                'locale' => 'de-de'
            ],
            'forms' => [
                'adult' => 'Erwachsener',
                'adults' => 'Erwachsene',
                'minors' => 'Kinder',
                'oneway' => 'Einfach',
                'roundtrip' => 'Hin- und Rückfahrt',
                'msn_error' => 'Am Ende des Prozesses ist ein Fehler aufgetreten. Bitte wenden Sie sich an den Kundendienst oder versuchen Sie es erneut.',
                'pickup_date_flight' => 'Flugankunftsdatum und -zeit',
                'pickup_date_train' => 'Zugankunftsdatum und -zeit',
                'pickup_date_port' => 'Datum und Zeit der Ausschiffung',
                'pickup_date_address' => 'Abholungsdatum und -zeit',
                'dropoff_date_flight' => 'Flugabflugdatum und -zeit',
                'dropoff_date_train' => 'Zugabfahrtsdatum und -zeit',
                'dropoff_date_port' => 'Hafenabfahrtsdatum und -zeit',
                'dropoff_date_address' => 'Abgabedatum und -zeit',
                'msn_searchbox_date_pickup' => 'Bitte wählen Sie Ihre Flugankunftszeit. Wir überwachen alle Flüge und unser Fahrer wird auf Sie warten',
                'msn_searchbox_date_dropoff' => 'Bitte wählen Sie Ihre Flugabflugzeit. Sie können die Abholzeit von Ihrem Hotel/Adresse im letzten Schritt wählen',
                'msn_searchbox_date_airport_to_airport' => 'Bitte wählen Sie Ihre Flugankunftszeit. Wir überwachen alle Flüge und unser Fahrer wird auf Sie warten',
                'msn_searchbox_pickuptime' => 'Bitte wählen Sie Ihre Abholzeit',
                'msn_searchbox_pickupdate' => 'Wählen Sie Ihr Abreisedatum',
                'msn_searchbox_returndate' => 'Wählen Sie Ihr Rückkehrdatum'
            ],
            'dates' => [
                'daysOfWeek' => ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
                'monthNames' => ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
            ],
            'payment' => [
                'payment_name' => 'Zahlungsmethode auswählen',
                'payment_description' => 'Aus der Liste der Zahlungsmethoden auswählen',
                'credit_card_name' => 'Kredit- oder Debitkarte',
                'credit_card_description' => 'Mit Kreditkarte bezahlen'
            ],
            'services' => [
                'includes' => [
                    'free_amendments' => 'Kostenlose Änderungen',
                    'professional_driver' => 'Professioneller Fahrer',
                    'instant_confirmation' => 'Sofortige Bestätigung',
                    'meet_greet' => 'Abholung mit Willkommensschild',
                    'free_cancellations' => 'Kostenlose Stornierung (Bis zu 24 Stunden vor Ihrer Ankunft)'
                ],
                'extras' => [
                    'door_to_door' => 'Tür-zu-Tür',
                    'free_child_seats' => 'Kostenlose Kindersitze',
                    'meet_greet_service' => 'Abholservice'
                ]
            ],
            'status' => [
                'completed' => 'Abgeschlossen',
                'on_hold' => 'Storniert',
                'cancelled' => 'Storniert',
                'noshowprovider' => 'Storniert',
                'processing' => 'Storniert',
                'confirmed' => 'Abgeschlossen'
            ],
            'homepage' => [
                'banner_title' => 'Buchen Sie Ihren Transfer zum Flughafen oder Ihre private Fahrt ganz einfach. Genießen Sie unseren Service zu den besten verfügbaren Preisen.',
                'why_different_title' => 'Entdecken Sie, warum wir anders sind',
                'door_to_door_title' => 'Tür zu Tür',
                'door_to_door_desc' => 'Vom Flughafen direkt zu Ihrem Ziel',
                'private_transfers_title' => 'Private Transfers',
                'private_transfers_desc' => 'Wir bieten nur private Transfers, keinen gemeinsamen Service',
                'meet_greet_title' => 'Meet & Greet',
                'meet_greet_desc' => 'Unser Fahrer wird Sie in der Ankunftshalle begrüßen',
                'customer_support_title' => '24/7 Kundenservice',
                'customer_support_desc' => 'Wir sind hier, um zu helfen! Vor, während und nach Ihrer Reise',
                'reviews_title' => 'Unsere Bewertungen',
                'reviews_rated' => 'Bewertet als Ausgezeichnet basierend auf 7.420 Bewertungen auf Trustpilot',
                'excellent_service' => 'Ausgezeichneter Service',
                'did_you_know_title' => 'Wussten Sie schon?',
                'did_you_know_1' => 'Wir wissen, dass es schwierig und stressig sein kann, einen Taxiservice in einem fremden Land zu finden. Buchen Sie im Voraus und genießen Sie den besten Service mit allem inklusive, keine Überraschungen.',
                'did_you_know_2' => 'MyTransfers bietet zuverlässige Transfers zu und von großen Flughäfen weltweit, sicherer, komfortabler und günstiger als der Taxiservice. Wir haben ein passendes Fahrzeug für jede Situation.',
                'did_you_know_3' => 'Sie können aus mehreren verfügbaren Fahrzeugen wählen, sodass Sie immer maximalen Komfort genießen können. Ob Sie alleine, zu zweit oder in Gruppen reisen, wir haben das perfekte Fahrzeug für jeden Anlass.',
                'did_you_know_4' => 'Der Fahrer holt Sie direkt vom Flughafenterminal ab, hilft Ihnen beim Gepäck und bringt Sie direkt zu Ihrem Hotel oder einem anderen Ziel und umgekehrt.',
                'comfort_safety_title' => 'Maximaler Komfort und Sicherheit während Ihrer Reise',
                'comfort_safety_subtitle' => 'Zugelassene Fahrzeuge, erfahrene Fahrer',
                'economy_class_title' => 'ECONOMY KLASSE',
                'economy_class_desc' => 'Für ein Paar oder eine Familie mit Kindern',
                'business_class_title' => 'BUSINESS KLASSE',
                'business_class_desc' => 'Komfortabel für Geschäftsreisen',
                'groups_title' => 'FÜR GRUPPEN',
                'groups_desc' => 'Für Gruppen bis zu 19 Personen oder mit großem Gepäck',
                'how_it_works_title' => 'Wie es funktioniert',
                'step1_title' => 'Buchen Sie Ihren Flughafentransfer weltweit',
                'step1_desc1' => 'Mytransfers bietet private Flughafentransfer-Buchungen mit sofortiger Bestätigung weltweit. Wir arbeiten direkt mit lokalen Fahrern zusammen und wählen die besten Services für unsere Kunden aus. Auf diese Weise stellen wir sicher, dass Ihr Urlaub bestmöglich beginnt.',
                'step1_desc2' => 'Wir überprüfen unseren Service ständig, um sicherzustellen, dass Sie Ihre Fahrt mit Mytransfers in jedem gewählten Ziel genießen werden.',
                'step2_title' => 'Unser einfacher Transfer-Buchungsprozess',
                'step2_desc' => 'Wählen Sie Ihren Ursprung und Ihr Ziel und in nur 3 Klicks haben Sie Ihren privaten Transfer gebucht. Vergessen Sie lange Warteschlangen für Taxis am Flughafen, unser Fahrer wird Sie bei Ihrer Ankunft mit einem Schild erwarten.',
                'step3_title' => 'Flughafen, Hafen oder Bahnhöfe',
                'step3_desc' => 'Mit uns können Sie Ihren privaten Transfer von jedem Flughafen, Hafen oder Bahnhof buchen. Wir überwachen die Ankunft der Transportmittel, um sicherzustellen, dass Ihr Transfer pünktlich und mit maximalen Garantien für Qualität und Sicherheit durchgeführt wird.',
                'popular_destinations_title' => 'Die beliebtesten Ziele'
            ],
            'booking_form' => [
                'search_title' => 'Suchen Sie nach Flughafentransfers?',
                'search_subtitle' => 'Sie sind am richtigen Ort',
                'oneway' => 'Einweg',
                'roundtrip' => 'Hin- und Rückfahrt',
                'passengers' => 'Passagiere',
                'adults' => 'Erwachsene',
                'children' => 'Kinder',
                'infants' => 'Säuglinge',
                'from' => 'Von',
                'to' => 'Nach',
                'pickup_location' => 'Abholort',
                'dropoff_location' => 'Zielort',
                'pickup_date' => 'Abholdatum',
                'return_date' => 'Rückkehrdatum',
                'time' => 'Zeit',
                'add_return' => '+ Rückfahrt hinzufügen',
                'search' => 'Suchen',
                'apply' => 'Anwenden',
                'see_more_results' => 'Weitere Ergebnisse anzeigen',
                'loading' => 'Wird geladen...',
                'no_results' => 'Keine Ergebnisse',
                'loading_message' => 'Wir suchen nach den besten Transferoptionen für Sie...',
                'loading_wait' => 'Bitte warten'
            ],
            'validation_errors' => [
                'from_required' => 'Bitte wählen Sie einen Abfahrtsort',
                'to_required' => 'Bitte wählen Sie einen Zielort',
                'pickup_date_required' => 'Bitte wählen Sie ein Abholdatum',
                'return_date_required' => 'Bitte wählen Sie ein Rückkehrdatum',
                'general_error' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.'
            ]
        ],
        'fr' => [
            'basic' => [
                'continue' => 'Continuer',
                'pay' => 'Passer la commande',
                'loading' => 'chargement..',
                'send_by' => 'Offre Mytransfers',
                'free' => ' Gratuit',
                'mins' => 'min',
                'locale_lang' => 'fr',
                'locale' => 'fr-fr'
            ],
            'forms' => [
                'adult' => 'Adulte',
                'adults' => 'Adultes',
                'minors' => 'Enfants',
                'oneway' => 'Aller simple',
                'roundtrip' => 'Aller-retour',
                'msn_error' => 'Une erreur s\'est produite à la fin du processus, veuillez contacter le service client ou réessayer.',
                'pickup_date_flight' => 'Date et heure d\'arrivée du vol',
                'pickup_date_train' => 'Date et heure d\'arrivée du train',
                'pickup_date_port' => 'Date et heure de débarquement',
                'pickup_date_address' => 'Date et heure de prise en charge',
                'dropoff_date_flight' => 'Date et heure de départ du vol',
                'dropoff_date_train' => 'Date et heure de départ du train',
                'dropoff_date_port' => 'Date et heure de départ du port',
                'dropoff_date_address' => 'Date et heure de dépose',
                'msn_searchbox_date_pickup' => 'Veuillez sélectionner votre heure d\'arrivée de vol. Nous surveillons tous les vols et notre chauffeur vous attendra',
                'msn_searchbox_date_dropoff' => 'Veuillez sélectionner votre heure de départ de vol. Vous pouvez choisir l\'heure de prise en charge de votre hôtel/adresse à la dernière étape',
                'msn_searchbox_date_airport_to_airport' => 'Veuillez sélectionner votre heure d\'arrivée de vol. Nous surveillons tous les vols et notre chauffeur vous attendra',
                'msn_searchbox_pickuptime' => 'Veuillez choisir votre heure de prise en charge',
                'msn_searchbox_pickupdate' => 'Sélectionnez votre date de départ',
                'msn_searchbox_returndate' => 'Sélectionnez votre date de retour'
            ],
            'dates' => [
                'daysOfWeek' => ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
                'monthNames' => ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
            ],
            'payment' => [
                'payment_name' => 'Sélectionner le mode de paiement',
                'payment_description' => 'Sélectionner dans la liste des modes de paiement',
                'credit_card_name' => 'Carte de crédit ou de débit',
                'credit_card_description' => 'Payer avec une carte de crédit'
            ],
            'services' => [
                'includes' => [
                    'free_amendments' => 'Modifications gratuites',
                    'professional_driver' => 'Chauffeur professionnel',
                    'instant_confirmation' => 'Confirmation instantanée',
                    'meet_greet' => 'Accueil avec pancarte de bienvenue',
                    'free_cancellations' => 'Annulations gratuites (Jusqu\'à 24 heures avant votre arrivée)'
                ],
                'extras' => [
                    'door_to_door' => 'Porte à porte',
                    'free_child_seats' => 'Sièges enfants gratuits',
                    'meet_greet_service' => 'Service d\'accueil'
                ]
            ],
            'status' => [
                'completed' => 'Terminé',
                'on_hold' => 'Annulé',
                'cancelled' => 'Annulé',
                'noshowprovider' => 'Annulé',
                'processing' => 'Annulé',
                'confirmed' => 'Terminé'
            ],
            'homepage' => [
                'banner_title' => 'Réservez votre transfert vers l\'aéroport ou votre trajet privé en toute simplicité. Profitez de notre service aux meilleurs tarifs disponibles.',
                'why_different_title' => 'Découvrez pourquoi nous sommes différents',
                'door_to_door_title' => 'Porte à porte',
                'door_to_door_desc' => 'De l\'aéroport directement à votre destination',
                'private_transfers_title' => 'Transfers privés',
                'private_transfers_desc' => 'Nous n\'offrons que des transfers privés, pas de service partagé',
                'meet_greet_title' => 'Accueil',
                'meet_greet_desc' => 'Notre chauffeur vous accueillera dans le hall d\'arrivée',
                'customer_support_title' => 'Support client 24/7',
                'customer_support_desc' => 'Nous sommes là pour vous aider ! Avant, pendant et après votre voyage',
                'reviews_title' => 'Nos avis',
                'reviews_rated' => 'Noté Excellent basé sur 7 420 avis sur Trustpilot',
                'excellent_service' => 'Service excellent',
                'did_you_know_title' => 'Le saviez-vous ?',
                'did_you_know_1' => 'Nous savons que trouver un service de taxi dans un pays étranger peut être difficile et stressant. Réservez à l\'avance et profitez du meilleur service avec tout inclus, pas de surprises.',
                'did_you_know_2' => 'MyTransfers propose des transfers fiables vers et depuis les principaux aéroports du monde, plus sûrs, plus confortables et moins chers que le service de taxi. Nous avons un véhicule approprié pour chaque situation.',
                'did_you_know_3' => 'Vous pouvez choisir parmi plusieurs véhicules disponibles, vous pouvez donc toujours profiter d\'un confort maximum. Que vous voyagiez seul, en couple ou en groupe, nous avons le véhicule parfait pour chaque occasion.',
                'did_you_know_4' => 'Le chauffeur vous récupérera directement du terminal de l\'aéroport, vous aidera avec vos bagages et vous emmènera directement à votre hôtel ou toute autre destination, et vice versa.',
                'comfort_safety_title' => 'Confort et sécurité maximum pendant votre voyage',
                'comfort_safety_subtitle' => 'Véhicules autorisés, chauffeurs expérimentés',
                'economy_class_title' => 'CLASSE ÉCONOMIQUE',
                'economy_class_desc' => 'Pour un couple ou une famille avec enfants',
                'business_class_title' => 'CLASSE AFFAIRES',
                'business_class_desc' => 'Confortable pour les voyages d\'affaires',
                'groups_title' => 'POUR LES GROUPES',
                'groups_desc' => 'Pour les groupes jusqu\'à 19 personnes ou avec de gros bagages',
                'how_it_works_title' => 'Comment ça marche',
                'step1_title' => 'Réservez votre transfert aéroport dans le monde entier',
                'step1_desc1' => 'Mytransfers propose des réservations de transfers aéroport privés avec confirmation instantanée dans le monde entier. Nous travaillons directement avec des chauffeurs locaux et sélectionnons les meilleurs services pour nos clients. De cette façon, nous nous assurons que vos vacances commenceront de la meilleure façon possible.',
                'step1_desc2' => 'Nous révisons constamment notre service pour nous assurer que vous apprécierez votre trajet avec Mytransfers dans n\'importe quelle destination que vous choisissez.',
                'step2_title' => 'Notre processus de réservation de transfert facile',
                'step2_desc' => 'Choisissez votre origine et votre destination et en seulement 3 clics, vous aurez votre transfert privé réservé. Oubliez les longues files d\'attente de taxis à l\'aéroport, notre chauffeur vous attendra avec une pancarte à votre arrivée.',
                'step3_title' => 'Aéroport, Port ou Gares',
                'step3_desc' => 'Avec nous, vous pouvez réserver votre transfert privé depuis n\'importe quel aéroport, port ou gare. Nous surveillons l\'arrivée des moyens de transport pour nous assurer que votre transfert sera effectué à temps et avec les garanties maximales de qualité et de sécurité.',
                'popular_destinations_title' => 'Les destinations les plus populaires'
            ],
            'booking_form' => [
                'search_title' => 'Cherchez-vous des transfers aéroport?',
                'search_subtitle' => 'Vous êtes au bon endroit',
                'oneway' => 'Aller simple',
                'roundtrip' => 'Aller-retour',
                'passengers' => 'Passagers',
                'adults' => 'Adultes',
                'children' => 'Enfants',
                'infants' => 'Nourrissons',
                'from' => 'De',
                'to' => 'À',
                'pickup_location' => 'Lieu de prise en charge',
                'dropoff_location' => 'Lieu de dépose',
                'pickup_date' => 'Date de prise en charge',
                'return_date' => 'Date de retour',
                'time' => 'Heure',
                'add_return' => '+ Ajouter le retour',
                'search' => 'Rechercher',
                'apply' => 'Appliquer',
                'see_more_results' => 'Voir plus de résultats',
                'loading' => 'Chargement...',
                'no_results' => 'Aucun résultat',
                'loading_message' => 'Nous recherchons les meilleures options de transfert pour vous...',
                'loading_wait' => 'Veuillez patienter'
            ],
            'validation_errors' => [
                'from_required' => 'Veuillez sélectionner un lieu de départ',
                'to_required' => 'Veuillez sélectionner un lieu de destination',
                'pickup_date_required' => 'Veuillez sélectionner une date de prise en charge',
                'return_date_required' => 'Veuillez sélectionner une date de retour',
                'general_error' => 'Une erreur s\'est produite. Veuillez réessayer.'
            ]
        ],
        'es' => [
            'basic' => [
                'continue' => 'Continuar',
                'pay' => 'Realizar pedido',
                'loading' => 'cargando..',
                'send_by' => 'Oferta Mytransfers',
                'free' => ' Gratis',
                'mins' => 'min',
                'locale_lang' => 'es',
                'locale' => 'es-es'
            ],
            'forms' => [
                'adult' => 'Adulto',
                'adults' => 'Adultos',
                'minors' => 'Niños',
                'oneway' => 'Solo ida',
                'roundtrip' => 'Ida y vuelta',
                'msn_error' => 'Ha ocurrido un error al final del proceso, por favor contacte con atención al cliente o inténtelo de nuevo.',
                'pickup_date_flight' => 'Fecha y hora de llegada del vuelo',
                'pickup_date_train' => 'Fecha y hora de llegada del tren',
                'pickup_date_port' => 'Fecha y hora de desembarque',
                'pickup_date_address' => 'Fecha y hora de recogida',
                'dropoff_date_flight' => 'Fecha y hora de salida del vuelo',
                'dropoff_date_train' => 'Fecha y hora de salida del tren',
                'dropoff_date_port' => 'Fecha y hora de salida del puerto',
                'dropoff_date_address' => 'Fecha y hora de entrega',
                'msn_searchbox_date_pickup' => 'Por favor seleccione su hora de llegada de vuelo. Monitoreamos todos los vuelos y nuestro conductor lo estará esperando',
                'msn_searchbox_date_dropoff' => 'Por favor seleccione su hora de salida de vuelo. Puede elegir la hora de recogida de su hotel/dirección en el último paso',
                'msn_searchbox_date_airport_to_airport' => 'Por favor seleccione su hora de llegada de vuelo. Monitoreamos todos los vuelos y nuestro conductor lo estará esperando',
                'msn_searchbox_pickuptime' => 'Por favor elija su hora de recogida',
                'msn_searchbox_pickupdate' => 'Seleccione su fecha de salida',
                'msn_searchbox_returndate' => 'Seleccione su fecha de regreso'
            ],
            'dates' => [
                'daysOfWeek' => ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                'monthNames' => ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
            ],
            'payment' => [
                'payment_name' => 'Seleccionar método de pago',
                'payment_description' => 'Seleccionar de la lista de métodos de pago',
                'credit_card_name' => 'Tarjeta de crédito o débito',
                'credit_card_description' => 'Pagar con tarjeta de crédito'
            ],
            'services' => [
                'includes' => [
                    'free_amendments' => 'Modificaciones gratuitas',
                    'professional_driver' => 'Conductor profesional',
                    'instant_confirmation' => 'Confirmación instantánea',
                    'meet_greet' => 'Recepción con cartel de bienvenida',
                    'free_cancellations' => 'Cancelaciones gratuitas (Hasta 24 horas antes de su llegada)'
                ],
                'extras' => [
                    'door_to_door' => 'Puerta a puerta',
                    'free_child_seats' => 'Asientos infantiles gratuitos',
                    'meet_greet_service' => 'Servicio de recepción'
                ]
            ],
            'status' => [
                'completed' => 'Completado',
                'on_hold' => 'Cancelado',
                'cancelled' => 'Cancelado',
                'noshowprovider' => 'Cancelado',
                'processing' => 'Cancelado',
                'confirmed' => 'Completado'
            ],
            'homepage' => [
                'banner_title' => 'Reserve su transfer al aeropuerto o su viaje privado con facilidad. Disfrute de nuestro servicio a las mejores tarifas disponibles.',
                'why_different_title' => 'Descubra por qué somos diferentes',
                'door_to_door_title' => 'Puerta a puerta',
                'door_to_door_desc' => 'Desde el aeropuerto directamente a su destino',
                'private_transfers_title' => 'Transfers privados',
                'private_transfers_desc' => 'Solo ofrecemos transfers privados, sin servicio compartido',
                'meet_greet_title' => 'Recepción',
                'meet_greet_desc' => 'Nuestro conductor lo recibirá en el hall de llegadas',
                'customer_support_title' => 'Soporte al cliente 24/7',
                'customer_support_desc' => '¡Estamos aquí para ayudar! Antes, durante y después de su viaje',
                'reviews_title' => 'Nuestras reseñas',
                'reviews_rated' => 'Calificado como Excelente basado en 7.420 reseñas en Trustpilot',
                'excellent_service' => 'Servicio excelente',
                'did_you_know_title' => '¿Sabía que?',
                'did_you_know_1' => 'Sabemos que encontrar un servicio de taxi en un país extranjero puede ser difícil y estresante. Reserve con anticipación y disfrute del mejor servicio con todo incluido, sin sorpresas.',
                'did_you_know_2' => 'MyTransfers ofrece transfers confiables hacia y desde los principales aeropuertos del mundo, más seguros, cómodos y económicos que el servicio de taxi. Tenemos un vehículo apropiado para cada situación.',
                'did_you_know_3' => 'Puede elegir entre varios vehículos disponibles, por lo que siempre puede disfrutar del máximo confort. Ya sea que viaje solo, en pareja o en grupo, tenemos el vehículo perfecto para cada ocasión.',
                'did_you_know_4' => 'El conductor lo recogerá directamente del terminal del aeropuerto, lo ayudará con su equipaje y lo llevará directamente a su hotel u otro destino, y viceversa.',
                'comfort_safety_title' => 'Máximo confort y seguridad durante su viaje',
                'comfort_safety_subtitle' => 'Vehículos autorizados, conductores experimentados',
                'economy_class_title' => 'CLASE ECONÓMICA',
                'economy_class_desc' => 'Para una pareja o una familia con niños',
                'business_class_title' => 'CLASE EJECUTIVA',
                'business_class_desc' => 'Cómodo para viajes de negocios',
                'groups_title' => 'PARA GRUPOS',
                'groups_desc' => 'Para grupos de hasta 19 personas o con equipaje grande',
                'how_it_works_title' => 'Cómo funciona',
                'step1_title' => 'Reserve su transfer de aeropuerto en todo el mundo',
                'step1_desc1' => 'Mytransfers ofrece reservas de transfers de aeropuerto privados con confirmación instantánea en todo el mundo. Trabajamos directamente con conductores locales y seleccionamos los mejores servicios para nuestros clientes. De esta manera nos aseguramos de que sus vacaciones comiencen de la mejor manera posible.',
                'step1_desc2' => 'Revisamos constantemente nuestro servicio para asegurarnos de que disfrutará de su viaje con Mytransfers en cualquier destino que elija.',
                'step2_title' => 'Nuestro proceso fácil de reserva de transfer',
                'step2_desc' => 'Elija su origen y destino y en solo 3 clics tendrá su transfer privado reservado. Olvídese de las largas filas de taxis en el aeropuerto, nuestro conductor lo estará esperando con un cartel a su llegada.',
                'step3_title' => 'Aeropuerto, Puerto o Estaciones de Tren',
                'step3_desc' => 'Con nosotros puede reservar su transfer privado desde cualquier aeropuerto, puerto o estación de tren. Monitoreamos la llegada de los medios de transporte para asegurar que su transfer se realice a tiempo y con las máximas garantías de calidad y seguridad.',
                'popular_destinations_title' => 'Los destinos más populares'
            ],
            'booking_form' => [
                'search_title' => '¿Busca transfers de aeropuerto?',
                'search_subtitle' => 'Ha llegado al lugar correcto',
                'oneway' => 'Solo ida',
                'roundtrip' => 'Ida y vuelta',
                'passengers' => 'Pasajeros',
                'adults' => 'Adultos',
                'children' => 'Niños',
                'infants' => 'Bebés',
                'from' => 'Desde',
                'to' => 'Hasta',
                'pickup_location' => 'Lugar de recogida',
                'dropoff_location' => 'Lugar de entrega',
                'pickup_date' => 'Fecha de recogida',
                'return_date' => 'Fecha de regreso',
                'time' => 'Hora',
                'add_return' => '+ Agregar regreso',
                'search' => 'Buscar',
                'apply' => 'Aplicar',
                'see_more_results' => 'Ver más resultados',
                'loading' => 'Cargando...',
                'no_results' => 'Sin resultados',
                'loading_message' => 'Estamos buscando las mejores opciones de transfer para usted...',
                'loading_wait' => 'Por favor espere'
            ],
            'validation_errors' => [
                'from_required' => 'Por favor seleccione un lugar de salida',
                'to_required' => 'Por favor seleccione un lugar de destino',
                'pickup_date_required' => 'Por favor seleccione una fecha de recogida',
                'return_date_required' => 'Por favor seleccione una fecha de regreso',
                'general_error' => 'Ha ocurrido un error. Por favor inténtelo de nuevo.'
            ]
        ]
    ],
    'forms' => [
        'adult' => 'Adult',
        'adults' => 'Adults', 
        'minors' => 'Minors',
        'oneway' => 'One-way',
        'roundtrip' => 'Round-Trip',
        'msn_error' => 'An error has occurred at the end of the process, please contact customer service or retry again.',
        'pickup_date_flight' => 'Flight arrival date & time',
        'pickup_date_train' => 'Train arrival date & time',
        'pickup_date_port' => 'Disembarkation date & time',
        'pickup_date_address' => 'Pickup date & time',
        'dropoff_date_flight' => 'Flight departure date & time',
        'dropoff_date_train' => 'Train departure date & time',
        'dropoff_date_port' => 'Port departure date & time',
        'dropoff_date_address' => 'Dropoff date & time',
        'msn_searchbox_date_pickup' => 'Please select your flight arrival hour. We monitor all flights and our driver will be waiting for you',
        'msn_searchbox_date_dropoff' => 'Please select your flight departure time. You can choose the pickup hour from your hotel/address on the last step',
        'msn_searchbox_date_airport_to_airport' => 'Please select your flight arrival hour. We monitor all flights and our driver will be waiting for you',
        'msn_searchbox_pickuptime' => 'Please choose your pick-up time',
        'msn_searchbox_pickupdate' => 'Select your departure date',
        'msn_searchbox_returndate' => 'Select your return date'
    ],
    'dates' => [
        'daysOfWeek' => ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
        'monthNames' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
    ],
    'payment' => [
        'payment_name' => 'Select payment method',
        'payment_description' => 'Select from the list of payment methods',
        'payment_img' => '/img/visa.png',
        'credit_card_name' => 'Credit or debit card',
        'credit_card_description' => 'Pay with a credit card',
        'credit_card_code' => 'creditcard'
    ],
    'services' => [
        'includes' => [
            'free_amendments' => 'Free amendments',
            'professional_driver' => 'Professional driver',
            'instant_confirmation' => 'Instant confirmation',
            'meet_greet' => 'Meet & Greet with welcome sign',
            'free_cancellations' => 'Free cancellations (Up to 24 hours before your arrival)'
        ],
        'extras' => [
            'door_to_door' => 'Door-to-Door',
            'free_child_seats' => 'Free child seats',
            'meet_greet_service' => 'Meet & Greet'
        ]
    ],
    'status' => [
        'completed' => 'Completed',
        'on_hold' => 'Cancelled',
        'cancelled' => 'Cancelled',
        'noshowprovider' => 'Cancelled',
        'processing' => 'Cancelled',
        'confirmed' => 'Completed'
    ],
    'homepage' => [
        'banner_title' => 'Book your transfer to the Airport or your private ride with ease. Enjoy our service at best rates available.',
        'why_different_title' => 'Discover why we are different',
        'door_to_door_title' => 'Door To Door',
        'door_to_door_desc' => 'From the Airport directly to your destination',
        'private_transfers_title' => 'Private Transfers',
        'private_transfers_desc' => 'We offer only private transfers, no shared service',
        'meet_greet_title' => 'Meet & Greet',
        'meet_greet_desc' => 'Our driver will meet & greet you in the arrivals hall',
        'customer_support_title' => '24/7 Customer Support',
        'customer_support_desc' => 'We are here to help! Before, during and after your trip',
        'reviews_title' => 'Our Reviews',
        'reviews_rated' => 'Rated Excellent Based on 7,420 reviews on Trustpilot',
        'excellent_service' => 'Excellent Service',
        'did_you_know_title' => 'Did you know',
        'did_you_know_1' => 'We know that finding a taxi service in a foreign country can be difficult and stressful. Book ahead and enjoy the best service with all inclusive, no surprises.',
        'did_you_know_2' => 'MyTransfers offers reliable transfers to and from major airports around the world, safer, more comfortable, and cheaper than the taxi service. We have an appropriate vehicle for every situation.',
        'did_you_know_3' => 'You can choose from several available vehicles, so you can always enjoy maximum comfort. Whether you are traveling alone, in pairs or in groups, we have the perfect vehicle for every occasion.',
        'did_you_know_4' => 'The driver will pick you up directly from the airport terminal, it will help with your luggage and take you directly to your hotel or any other destination, and vice versa.',
        'comfort_safety_title' => 'Maximum comfort and safety during your trip',
        'comfort_safety_subtitle' => 'Authorized vehicles, experienced drivers',
        'economy_class_title' => 'ECONOMY CLASS',
        'economy_class_desc' => 'For a couple or a family with children',
        'business_class_title' => 'BUSINESS CLASS',
        'business_class_desc' => 'Comfortable for business trips',
        'groups_title' => 'FOR GROUPS',
        'groups_desc' => 'For groups up to 19 people or with large luggage',
        'how_it_works_title' => 'How it works',
        'step1_title' => 'Book your airport transfer worldwide',
        'step1_desc1' => 'Mytransfers offers private airport transfers bookings with instant confirmation worldwide. We work directly with local drivers and select the best services for our clients. In this way we ensure that your vacation will start in the best possible way.',
        'step1_desc2' => 'We constantly review our service to ensure that you will enjoy your ride with Mytransfers in any destination you choose.',
        'step2_title' => 'Our easy transfer booking process',
        'step2_desc' => 'Choose your origin and destination and in just 3 click you will have your private transfer booked. Forget about long waiting taxi lines at the airport, our driver will be waiting for you with a sign at your arrival.',
        'step3_title' => 'Airport, Port or Train Stations',
        'step3_desc' => 'With us you can book your private transfer from any airport, port or train station. We monitor the arrival of the means of transport to ensure that your transfer will be carried out on time and with the maximum guarantees of quality and safety.',
        'popular_destinations_title' => 'The most popular destinations'
    ],
    'booking_form' => [
        'search_title' => 'Are you looking for airport transfers?',
        'search_subtitle' => 'You have come to the right place',
        'oneway' => 'One-way',
        'roundtrip' => 'Round-Trip',
        'passengers' => 'Passengers',
        'adults' => 'Adults',
        'children' => 'Children',
        'infants' => 'Infants',
        'from' => 'From',
        'to' => 'To',
        'pickup_location' => 'Pickup location',
        'dropoff_location' => 'Dropoff location',
        'pickup_date' => 'Pickup date',
        'return_date' => 'Return date',
        'time' => 'Time',
        'add_return' => '+ Add return',
        'search' => 'Search',
        'apply' => 'Apply',
        'see_more_results' => 'See more results',
        'loading' => 'Loading...',
        'no_results' => 'No results',
        'loading_message' => 'We are searching for the best transfer options for you...',
        'loading_wait' => 'Please wait'
    ],
];


// Mevcut çevirileri yükle
$translations = [];
if (is_file($translationsFile)) {
    $translations = json_decode(file_get_contents($translationsFile), true) ?: [];
}

// Eğer çeviri dosyası yoksa veya boşsa, mevcut JS dosyalarından yükle
if (empty($translations)) {
    foreach ($languages as $langCode => $langName) {
        $jsFile = $langDir . $langCode . '.js';
        if (is_file($jsFile)) {
            $content = file_get_contents($jsFile);
            
            // window.__mt.ln= kısmını bul
            if (preg_match('/window\.__mt\.ln=(\{.*?\});/s', $content, $matches)) {
                $jsonStr = $matches[1];
                
                // JavaScript objesini JSON'a çevir
                $jsonStr = preg_replace('/(\w+):/i', '"$1":', $jsonStr);
                $jsonStr = preg_replace('/,\s*}/', '}', $jsonStr);
                
                $translations[$langCode] = json_decode($jsonStr, true) ?: [];
            }
        }
    }
    
    // Eğer hala boşsa, varsayılan değerlerle doldur
    if (empty($translations)) {
        foreach ($languages as $langCode => $langName) {
            $translations[$langCode] = [];
            
            // Dil özel çeviriler varsa kullan, yoksa İngilizce varsayılanları kullan
            $langTranslations = $translationKeys['translations'][$langCode] ?? null;
            
            // Temel çeviriler
            if ($langTranslations && isset($langTranslations['basic'])) {
                foreach ($langTranslations['basic'] as $key => $value) {
                    $translations[$langCode][$key] = $value;
                }
            } else {
                foreach ($translationKeys['basic'] as $key => $defaultValue) {
                    $translations[$langCode][$key] = $defaultValue;
                }
            }
            
            // Form çevirileri
            if ($langTranslations && isset($langTranslations['forms'])) {
                foreach ($langTranslations['forms'] as $key => $value) {
                    $translations[$langCode][$key] = $value;
                }
            } else {
                foreach ($translationKeys['forms'] as $key => $defaultValue) {
                    $translations[$langCode][$key] = $defaultValue;
                }
            }
            
            // Tarih çevirileri
            if ($langTranslations && isset($langTranslations['dates'])) {
                $translations[$langCode]['daysOfWeek'] = $langTranslations['dates']['daysOfWeek'];
                $translations[$langCode]['monthNames'] = $langTranslations['dates']['monthNames'];
            } else {
                $translations[$langCode]['daysOfWeek'] = $translationKeys['dates']['daysOfWeek'];
                $translations[$langCode]['monthNames'] = $translationKeys['dates']['monthNames'];
            }
            
            // Ödeme çevirileri
            if ($langTranslations && isset($langTranslations['payment'])) {
                $translations[$langCode]['payment'] = [
                    'name' => $langTranslations['payment']['payment_name'],
                    'description' => $langTranslations['payment']['payment_description'],
                    'img' => $translationKeys['payment']['payment_img']
                ];
                
                $translations[$langCode]['payments'] = [[
                    'name' => $langTranslations['payment']['credit_card_name'],
                    'img' => $translationKeys['payment']['payment_img'],
                    'description' => $langTranslations['payment']['credit_card_description'],
                    'code' => $translationKeys['payment']['credit_card_code']
                ]];
            } else {
                $translations[$langCode]['payment'] = [
                    'name' => $translationKeys['payment']['payment_name'],
                    'description' => $translationKeys['payment']['payment_description'],
                    'img' => $translationKeys['payment']['payment_img']
                ];
                
                $translations[$langCode]['payments'] = [[
                    'name' => $translationKeys['payment']['credit_card_name'],
                    'img' => $translationKeys['payment']['payment_img'],
                    'description' => $translationKeys['payment']['credit_card_description'],
                    'code' => $translationKeys['payment']['credit_card_code']
                ]];
            }
            
            // Hizmet çevirileri
            if ($langTranslations && isset($langTranslations['services'])) {
                $translations[$langCode]['list_includes'] = [
                    ['name' => $langTranslations['services']['includes']['free_amendments']],
                    ['name' => $langTranslations['services']['includes']['professional_driver']],
                    ['name' => $langTranslations['services']['includes']['instant_confirmation']],
                    ['name' => $langTranslations['services']['includes']['meet_greet'], 'is_meet' => true],
                    ['name' => $langTranslations['services']['includes']['free_cancellations']]
                ];
                
                $translations[$langCode]['list_extras'] = [
                    ['name' => $langTranslations['services']['extras']['door_to_door']],
                    ['name' => $langTranslations['services']['extras']['free_child_seats']],
                    ['name' => $langTranslations['services']['extras']['meet_greet_service']]
                ];
            } else {
                $translations[$langCode]['list_includes'] = [
                    ['name' => $translationKeys['services']['includes']['free_amendments']],
                    ['name' => $translationKeys['services']['includes']['professional_driver']],
                    ['name' => $translationKeys['services']['includes']['instant_confirmation']],
                    ['name' => $translationKeys['services']['includes']['meet_greet'], 'is_meet' => true],
                    ['name' => $translationKeys['services']['includes']['free_cancellations']]
                ];
                
                $translations[$langCode]['list_extras'] = [
                    ['name' => $translationKeys['services']['extras']['door_to_door']],
                    ['name' => $translationKeys['services']['extras']['free_child_seats']],
                    ['name' => $translationKeys['services']['extras']['meet_greet_service']]
                ];
            }
            
            // Durum çevirileri
            if ($langTranslations && isset($langTranslations['status'])) {
                foreach ($langTranslations['status'] as $key => $value) {
                    $translations[$langCode][$key] = $value;
                }
            } else {
                foreach ($translationKeys['status'] as $key => $defaultValue) {
                    $translations[$langCode][$key] = $defaultValue;
                }
            }
        }
    }
}

// POST işlemi - çevirileri kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $newTranslations = [];
    foreach ($languages as $langCode => $langName) {
        $newTranslations[$langCode] = [];
        
        // Temel çeviriler
        foreach ($translationKeys['basic'] as $key => $defaultValue) {
            $postKey = "basic_{$langCode}_{$key}";
            $newTranslations[$langCode][$key] = $_POST[$postKey] ?? $defaultValue;
        }
        
        // Form çevirileri
        foreach ($translationKeys['forms'] as $key => $defaultValue) {
            $postKey = "forms_{$langCode}_{$key}";
            $newTranslations[$langCode][$key] = $_POST[$postKey] ?? $defaultValue;
        }
        
        // Tarih çevirileri
        if ($langCode === 'en') {
            $newTranslations[$langCode]['daysOfWeek'] = $translationKeys['dates']['daysOfWeek'];
            $newTranslations[$langCode]['monthNames'] = $translationKeys['dates']['monthNames'];
        } else {
            $newTranslations[$langCode]['daysOfWeek'] = [];
            $newTranslations[$langCode]['monthNames'] = [];
            
            for ($i = 0; $i < 7; $i++) {
                $postKey = "dates_{$langCode}_days_{$i}";
                $newTranslations[$langCode]['daysOfWeek'][] = $_POST[$postKey] ?? $translationKeys['dates']['daysOfWeek'][$i];
            }
            
            for ($i = 0; $i < 12; $i++) {
                $postKey = "dates_{$langCode}_months_{$i}";
                $newTranslations[$langCode]['monthNames'][] = $_POST[$postKey] ?? $translationKeys['dates']['monthNames'][$i];
            }
        }
        
        // Ödeme çevirileri
        $newTranslations[$langCode]['payment'] = [
            'name' => $_POST["payment_{$langCode}_name"] ?? $translationKeys['payment']['payment_name'],
            'description' => $_POST["payment_{$langCode}_description"] ?? $translationKeys['payment']['payment_description'],
            'img' => $translationKeys['payment']['payment_img']
        ];
        
        $newTranslations[$langCode]['payments'] = [[
            'name' => $_POST["payment_{$langCode}_credit_name"] ?? $translationKeys['payment']['credit_card_name'],
            'img' => $translationKeys['payment']['payment_img'],
            'description' => $_POST["payment_{$langCode}_credit_description"] ?? $translationKeys['payment']['credit_card_description'],
            'code' => $translationKeys['payment']['credit_card_code']
        ]];
        
        // Hizmet çevirileri
        $newTranslations[$langCode]['list_includes'] = [
            ['name' => $_POST["services_{$langCode}_includes_free_amendments"] ?? $translationKeys['services']['includes']['free_amendments']],
            ['name' => $_POST["services_{$langCode}_includes_professional_driver"] ?? $translationKeys['services']['includes']['professional_driver']],
            ['name' => $_POST["services_{$langCode}_includes_instant_confirmation"] ?? $translationKeys['services']['includes']['instant_confirmation']],
            ['name' => $_POST["services_{$langCode}_includes_meet_greet"] ?? $translationKeys['services']['includes']['meet_greet'], 'is_meet' => true],
            ['name' => $_POST["services_{$langCode}_includes_free_cancellations"] ?? $translationKeys['services']['includes']['free_cancellations']]
        ];
        
        $newTranslations[$langCode]['list_extras'] = [
            ['name' => $_POST["services_{$langCode}_extras_door_to_door"] ?? $translationKeys['services']['extras']['door_to_door']],
            ['name' => $_POST["services_{$langCode}_extras_free_child_seats"] ?? $translationKeys['services']['extras']['free_child_seats']],
            ['name' => $_POST["services_{$langCode}_extras_meet_greet_service"] ?? $translationKeys['services']['extras']['meet_greet_service']]
        ];
        
        // Durum çevirileri
        foreach ($translationKeys['status'] as $key => $defaultValue) {
            $postKey = "status_{$langCode}_{$key}";
            $newTranslations[$langCode][$key] = $_POST[$postKey] ?? $defaultValue;
        }
        
        // Anasayfa içerikleri
        $newTranslations[$langCode]['homepage'] = [];
        foreach ($translationKeys['homepage'] as $key => $defaultValue) {
            $postKey = "homepage_{$langCode}_{$key}";
            $newTranslations[$langCode]['homepage'][$key] = $_POST[$postKey] ?? $defaultValue;
        }
        
        // Rezervasyon formu çevirileri
        $newTranslations[$langCode]['booking_form'] = [];
        foreach ($translationKeys['booking_form'] as $key => $defaultValue) {
            $postKey = "booking_form_{$langCode}_{$key}";
            $newTranslations[$langCode]['booking_form'][$key] = $_POST[$postKey] ?? $defaultValue;
        }
        
        // Validasyon hataları çevirileri
        $newTranslations[$langCode]['validation_errors'] = [];
        foreach ($translationKeys['validation_errors'] as $key => $defaultValue) {
            $postKey = "validation_errors_{$langCode}_{$key}";
            $newTranslations[$langCode]['validation_errors'][$key] = $_POST[$postKey] ?? $defaultValue;
        }
    }
    
    // Çevirileri kaydet
    write_json($translationsFile, $newTranslations);
    
    // JavaScript dosyalarını güncelle
    foreach ($languages as $langCode => $langName) {
        generateLanguageFile($langCode, $newTranslations[$langCode], $langDir);
    }
    
    header('Location: /mytransfers/admin/translations.php?success=1');
    exit;
}

// JavaScript dosyası oluşturma fonksiyonu
function generateLanguageFile($langCode, $translations, $langDir) {
    // Anasayfa içeriklerini window.__mt.homepage olarak ekle
    $homepageContent = isset($translations['homepage']) ? $translations['homepage'] : [];
    
    // Rezervasyon formu çevirilerini window.__mt.booking_form olarak ekle
    $bookingFormContent = isset($translations['booking_form']) ? $translations['booking_form'] : [];
    
    // Validasyon hataları çevirilerini window.__mt.validation_errors olarak ekle
    $validationErrorsContent = isset($translations['validation_errors']) ? $translations['validation_errors'] : [];
    
    $content = "!function(e){var a={};function d(o){if(a[o])return a[o].exports;var n=a[o]={i:o,l:!1,exports:{}};return e[o].call(n.exports,n,n.exports,d),n.l=!0,n.exports}d.m=e,d.c=a,d.d=function(e,a,o){d.o(e,a)||Object.defineProperty(e,a,{enumerable:!0,get:o})},d.r=function(e){\"undefined\"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:\"Module\"}),Object.defineProperty(e,\"__esModule\",{value:!0})},d.t=function(e,a){if(1&a&&(e=d(e)),8&a)return e;if(4&a&&\"object\"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(d.r(o),Object.defineProperty(o,\"default\",{enumerable:!0,value:e}),2&a&&\"string\"!=typeof e)for(var n in e)d.d(o,n,function(a){return e[a]}.bind(null,n));return o},d.n=function(e){var a=e&&e.__esModule?function(){return e.default}:function(){return e};return d.d(a,\"a\",a),a},d.o=function(e,a){return Object.prototype.hasOwnProperty.call(e,a)},d.p=\"/\",d(d.s=20)}({\"1YU0\":function(e,a){window.__mt.ln=" . json_encode($translations) . ",window.__mt.homepage=" . json_encode($homepageContent) . ",window.__mt.booking_form=" . json_encode($bookingFormContent) . ",window.__mt.validation_errors=" . json_encode($validationErrorsContent) . ",window.__mt.setting.format={daysOfWeek:" . json_encode($translations['daysOfWeek']) . ",monthNames:" . json_encode($translations['monthNames']) . "},window.__mt.setting.payments=" . json_encode($translations['payments']) . ",window.__mt.ln.places=[],window.__mt.setting.codes_phone=[],window.__mt.setting.format_codes={},angular.module(\"ngLocale\",[],[\"\$provide\",function(e){var a=\"one\",d=\"other\";e.value(\"\$locale\",{DATETIME_FORMATS:{AMPMS:[\"am\",\"pm\"],DAY:[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\",\"Saturday\"],ERANAMES:[\"Before Christ\",\"Anno Domini\"],ERAS:[\"BC\",\"AD\"],FIRSTDAYOFWEEK:0,MONTH:" . json_encode($translations['monthNames']) . ",SHORTDAY:" . json_encode($translations['daysOfWeek']) . ",SHORTMONTH:" . json_encode($translations['monthNames']) . ",STANDALONEMONTH:" . json_encode($translations['monthNames']) . ",WEEKENDRANGE:[5,6],fullDate:\"EEEE, d MMMM y\",longDate:\"d MMMM y\",medium:\"d MMM y HH:mm:ss\",mediumDate:\"d MMM y\",mediumTime:\"HH:mm:ss\",short:\"dd/MM/y HH:mm\",shortDate:\"dd/MM/y\",shortTime:\"HH:mm\"},NUMBER_FORMATS:{CURRENCY_SYM:\"£\",DECIMAL_SEP:\".\",GROUP_SEP:\",\",PATTERNS:[{gSize:3,lgSize:3,maxFrac:3,minFrac:0,minInt:1,negPre:\"-\",negSuf:\"\",posPre:\"\",posSuf:\"\"},{gSize:3,lgSize:3,maxFrac:2,minFrac:2,minInt:1,negPre:\"-¤\",negSuf:\"\",posPre:\"¤\",posSuf:\"\"}]},id:\"{$langCode}-{$langCode}\",localeID:\"{$langCode}_{$langCode}\",pluralCat:function(e,o){var n=0|e,i=function(e,a){var d=a;void 0===d&&(d=Math.min(function(e){var a=(e+=\"\").indexOf(\".\");return-1==a?0:e.length-a-1}(e),3));var o=Math.pow(10,d);return{v:d,f:(e*o|0)%o}}(e,o);return 1==n&&0==i.v?a:d}})}])},20:function(e,a,d){e.exports=d(\"1YU0\")}});";
    
    file_put_contents($langDir . $langCode . '.js', $content);
}

ob_start();
?>

<div class="admin-card">
    <h3>Çeviri Yönetimi</h3>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Çeviriler başarıyla kaydedildi!</div>
    <?php endif; ?>
    
    <div style="margin-bottom: 16px;">
        <button type="button" onclick="loadExistingTranslations()" class="admin-btn" style="background: #28a745; color: white; text-decoration: none; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;">
            📥 Mevcut Çevirileri Yükle
        </button>
        <a href="/mytransfers/admin/translations_countries.php" class="admin-btn" style="background: #17a2b8; color: white; text-decoration: none; padding: 8px 16px; border-radius: 4px; margin-left: 8px;">
            🌍 Ülke & Havalimanı Çevirileri
        </a>
    </div>
    
    <!-- Bildirim alanı -->
    <div id="notification" style="display: none; padding: 12px; margin: 10px 0; border-radius: 4px; font-weight: 500;"></div>
    
    <form method="post">
        <input class="admin-input" type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
        
        <!-- Dil Seçimi Tabları -->
        <div class="language-tabs">
            <?php $first = true; foreach ($languages as $langCode => $langName): ?>
                <button type="button" class="tab-button <?php echo $first ? 'active' : ''; ?>" 
                        onclick="showLanguageTab('<?php echo $langCode; ?>', this)">
                    <?php echo htmlspecialchars($langName); ?> (<?php echo strtoupper($langCode); ?>)
                </button>
            <?php $first = false; endforeach; ?>
        </div>
        
        <!-- Kategori Seçimi Tabları -->
        <div class="category-tabs">
            <?php 
            $categories = [
                'basic' => 'Temel Metinler',
                'forms' => 'Form Alanları', 
                'dates' => 'Tarih/Saat',
                'payment' => 'Ödeme',
                'services' => 'Hizmetler',
                'status' => 'Durumlar',
                'homepage' => 'Anasayfa İçerikleri',
                'booking_form' => 'Rezervasyon Formu',
                'validation_errors' => 'Form Validasyon Hataları'
            ];
            $firstCat = true;
            foreach ($categories as $catCode => $catName): ?>
                <button type="button" class="category-tab-button <?php echo $firstCat ? 'active' : ''; ?>" 
                        onclick="showCategoryTab('<?php echo $catCode; ?>', this)">
                    <?php echo htmlspecialchars($catName); ?>
                </button>
            <?php $firstCat = false; endforeach; ?>
        </div>
        
        <!-- İçerik Alanı -->
        <div id="content-area" class="content-area">
            <?php $first = true; foreach ($languages as $langCode => $langName): ?>
            <div id="lang-<?php echo $langCode; ?>" class="language-content <?php echo $first ? 'active' : ''; ?>">
                <div class="admin-card" style="margin-top: 16px;">
                    <h4><?php echo htmlspecialchars($langName); ?> (<?php echo strtoupper($langCode); ?>) - Çeviri Yönetimi</h4>
                    
                    <!-- Temel Metinler -->
                    <div id="content-<?php echo $langCode; ?>-basic" class="category-content <?php echo $first ? 'active' : ''; ?>">
                        <h5>Temel Metinler</h5>
                        <?php foreach ($translationKeys['basic'] as $key => $defaultValue): ?>
                            <label><?php echo htmlspecialchars($key); ?></label>
                            <input class="admin-input" type="text" 
                                   name="basic_<?php echo $langCode; ?>_<?php echo $key; ?>" 
                                   value="<?php echo htmlspecialchars($translations[$langCode][$key] ?? $defaultValue, ENT_QUOTES); ?>" />
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Form Alanları -->
                    <div id="content-<?php echo $langCode; ?>-forms" class="category-content">
                        <h5>Form Alanları</h5>
                        <?php foreach ($translationKeys['forms'] as $key => $defaultValue): ?>
                            <label><?php echo htmlspecialchars($key); ?></label>
                            <input class="admin-input" type="text" 
                                   name="forms_<?php echo $langCode; ?>_<?php echo $key; ?>" 
                                   value="<?php echo htmlspecialchars($translations[$langCode][$key] ?? $defaultValue, ENT_QUOTES); ?>" />
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Tarih/Saat -->
                    <div id="content-<?php echo $langCode; ?>-dates" class="category-content">
                        <h5>Tarih/Saat</h5>
                        <label>Günler (7 gün)</label>
                        <div class="admin-form-row">
                            <?php for ($i = 0; $i < 7; $i++): ?>
                                <input class="admin-input" type="text" 
                                       name="dates_<?php echo $langCode; ?>_days_<?php echo $i; ?>" 
                                       value="<?php echo htmlspecialchars($translations[$langCode]['daysOfWeek'][$i] ?? $translationKeys['dates']['daysOfWeek'][$i], ENT_QUOTES); ?>" 
                                       placeholder="<?php echo $translationKeys['dates']['daysOfWeek'][$i]; ?>" />
                            <?php endfor; ?>
                        </div>
                        
                        <label>Aylar (12 ay)</label>
                        <div class="admin-form-row">
                            <?php for ($i = 0; $i < 12; $i++): ?>
                                <input class="admin-input" type="text" 
                                       name="dates_<?php echo $langCode; ?>_months_<?php echo $i; ?>" 
                                       value="<?php echo htmlspecialchars($translations[$langCode]['monthNames'][$i] ?? $translationKeys['dates']['monthNames'][$i], ENT_QUOTES); ?>" 
                                       placeholder="<?php echo $translationKeys['dates']['monthNames'][$i]; ?>" />
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <!-- Ödeme -->
                    <div id="content-<?php echo $langCode; ?>-payment" class="category-content">
                        <h5>Ödeme</h5>
                        <label>Ödeme Yöntemi Adı</label>
                        <input class="admin-input" type="text" 
                               name="payment_<?php echo $langCode; ?>_name" 
                               value="<?php echo htmlspecialchars($translations[$langCode]['payment']['name'] ?? $translationKeys['payment']['payment_name'], ENT_QUOTES); ?>" />
                        
                        <label>Ödeme Açıklaması</label>
                        <input class="admin-input" type="text" 
                               name="payment_<?php echo $langCode; ?>_description" 
                               value="<?php echo htmlspecialchars($translations[$langCode]['payment']['description'] ?? $translationKeys['payment']['payment_description'], ENT_QUOTES); ?>" />
                        
                        <label>Kredi Kartı Adı</label>
                        <input class="admin-input" type="text" 
                               name="payment_<?php echo $langCode; ?>_credit_name" 
                               value="<?php echo htmlspecialchars($translations[$langCode]['payments'][0]['name'] ?? $translationKeys['payment']['credit_card_name'], ENT_QUOTES); ?>" />
                        
                        <label>Kredi Kartı Açıklaması</label>
                        <input class="admin-input" type="text" 
                               name="payment_<?php echo $langCode; ?>_credit_description" 
                               value="<?php echo htmlspecialchars($translations[$langCode]['payments'][0]['description'] ?? $translationKeys['payment']['credit_card_description'], ENT_QUOTES); ?>" />
                    </div>
                    
                    <!-- Hizmetler -->
                    <div id="content-<?php echo $langCode; ?>-services" class="category-content">
                        <h5>Hizmetler - Dahil Olanlar</h5>
                        <?php foreach ($translationKeys['services']['includes'] as $key => $defaultValue): ?>
                            <label><?php echo htmlspecialchars($key); ?></label>
                            <input class="admin-input" type="text" 
                                   name="services_<?php echo $langCode; ?>_includes_<?php echo $key; ?>" 
                                   value="<?php echo htmlspecialchars($translations[$langCode]['list_includes'][array_search($key, array_keys($translationKeys['services']['includes']))]['name'] ?? $defaultValue, ENT_QUOTES); ?>" />
                        <?php endforeach; ?>
                        
                        <h5>Hizmetler - Ekstralar</h5>
                        <?php foreach ($translationKeys['services']['extras'] as $key => $defaultValue): ?>
                            <label><?php echo htmlspecialchars($key); ?></label>
                            <input class="admin-input" type="text" 
                                   name="services_<?php echo $langCode; ?>_extras_<?php echo $key; ?>" 
                                   value="<?php echo htmlspecialchars($translations[$langCode]['list_extras'][array_search($key, array_keys($translationKeys['services']['extras']))]['name'] ?? $defaultValue, ENT_QUOTES); ?>" />
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Durumlar -->
                    <div id="content-<?php echo $langCode; ?>-status" class="category-content">
                        <h5>Durumlar</h5>
                        <?php foreach ($translationKeys['status'] as $key => $defaultValue): ?>
                            <label><?php echo htmlspecialchars($key); ?></label>
                            <input class="admin-input" type="text" 
                                   name="status_<?php echo $langCode; ?>_<?php echo $key; ?>" 
                                   value="<?php echo htmlspecialchars($translations[$langCode][$key] ?? $defaultValue, ENT_QUOTES); ?>" />
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Anasayfa İçerikleri -->
                    <div id="content-<?php echo $langCode; ?>-homepage" class="category-content">
                        <h5>Anasayfa İçerikleri</h5>
                        <?php foreach ($translationKeys['homepage'] as $key => $defaultValue): ?>
                            <label><?php echo htmlspecialchars($key); ?></label>
                            <textarea class="admin-input" rows="3" 
                                      name="homepage_<?php echo $langCode; ?>_<?php echo $key; ?>" 
                                      placeholder="<?php echo htmlspecialchars($defaultValue, ENT_QUOTES); ?>"><?php echo htmlspecialchars($translations[$langCode]['homepage'][$key] ?? $defaultValue, ENT_QUOTES); ?></textarea>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Rezervasyon Formu -->
                    <div id="content-<?php echo $langCode; ?>-booking_form" class="category-content">
                        <h5>Rezervasyon Formu</h5>
                        <?php foreach ($translationKeys['booking_form'] as $key => $defaultValue): ?>
                            <label><?php echo htmlspecialchars($key); ?></label>
                            <input class="admin-input" type="text" 
                                   name="booking_form_<?php echo $langCode; ?>_<?php echo $key; ?>" 
                                   value="<?php echo htmlspecialchars($translations[$langCode]['booking_form'][$key] ?? $defaultValue, ENT_QUOTES); ?>" 
                                   placeholder="<?php echo htmlspecialchars($defaultValue, ENT_QUOTES); ?>" />
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Form Validasyon Hataları -->
                    <div id="content-<?php echo $langCode; ?>-validation_errors" class="category-content">
                        <h5>Form Validasyon Hataları</h5>
                        <?php foreach ($translationKeys['validation_errors'] as $key => $defaultValue): ?>
                            <label><?php echo htmlspecialchars($key); ?></label>
                            <input class="admin-input" type="text" 
                                   name="validation_errors_<?php echo $langCode; ?>_<?php echo $key; ?>" 
                                   value="<?php echo htmlspecialchars($translations[$langCode]['validation_errors'][$key] ?? $defaultValue, ENT_QUOTES); ?>" 
                                   placeholder="<?php echo htmlspecialchars($defaultValue, ENT_QUOTES); ?>" />
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php $first = false; endforeach; ?>
                

        
        <div style="margin-top: 16px;">
            <button class="admin-btn" type="submit">Tüm Çevirileri Kaydet</button>
        </div>
    </form>
</div>

<style>
.admin-form-section {
    margin-bottom: 24px;
    padding: 16px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: #f9f9f9;
}

.admin-form-section h5 {
    margin: 0 0 16px 0;
    color: #333;
    font-size: 14px;
    font-weight: 600;
}

.admin-form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 8px;
    margin-bottom: 16px;
}

.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 16px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.language-tabs {
    display: flex;
    border-bottom: 2px solid #e0e0e0;
    margin-bottom: 20px;
    overflow-x: auto;
}

.tab-button {
    background: none;
    border: none;
    padding: 12px 20px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #666;
    border-bottom: 2px solid transparent;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.tab-button:hover {
    background: #f5f5f5;
    color: #333;
}

.tab-button.active {
    color: #007bff;
    border-bottom-color: #007bff;
    background: #f8f9fa;
}

.category-tabs {
    display: flex;
    border-bottom: 2px solid #e0e0e0;
    margin-bottom: 20px;
    overflow-x: auto;
    margin-top: 16px;
}

.category-tab-button {
    background: none;
    border: none;
    padding: 8px 16px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: #666;
    border-bottom: 2px solid transparent;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.category-tab-button:hover {
    background: #f5f5f5;
    color: #333;
}

.category-tab-button.active {
    color: #007bff;
    border-bottom-color: #007bff;
    background: #f8f9fa;
}

.content-area {
    margin-top: 16px;
}

.language-content {
    display: none !important;
}

.language-content.active {
    display: block !important;
}

.category-content {
    display: none !important;
}

.category-content.active {
    display: block !important;
}

/* Sadece aktif dil içindeki aktif kategoriyi göster */
.language-content:not(.active) .category-content {
    display: none !important;
}

.language-content.active .category-content:not(.active) {
    display: none !important;
}

/* Dark theme support */
body.theme-dark .admin-form-section {
    background: #2d3748;
    border-color: #4a5568;
    color: #e2e8f0;
}

body.theme-dark .admin-form-section h5 {
    color: #f7fafc;
}

body.theme-dark .tab-button {
    color: #a0aec0;
}

body.theme-dark .tab-button:hover {
    background: #4a5568;
    color: #f7fafc;
}

body.theme-dark .tab-button.active {
    color: #63b3ed;
    border-bottom-color: #63b3ed;
    background: #2d3748;
}

body.theme-dark .language-tabs {
    border-bottom-color: #4a5568;
}

body.theme-dark .admin-card {
    background: #2d3748;
    border-color: #4a5568;
    color: #e2e8f0;
}

body.theme-dark .admin-input {
    background: #4a5568;
    border-color: #718096;
    color: #f7fafc;
}

body.theme-dark .admin-input:focus {
    border-color: #63b3ed;
    box-shadow: 0 0 0 2px rgba(99, 179, 237, 0.2);
}

body.theme-dark .admin-btn {
    background: #4a5568;
    color: #f7fafc;
    border-color: #718096;
}

body.theme-dark .admin-btn:hover {
    background: #718096;
}

body.theme-dark .alert-success {
    background: #22543d;
    color: #9ae6b4;
    border-color: #38a169;
}

body.theme-dark label {
    color: #e2e8f0;
}

body.theme-dark .category-tabs {
    border-bottom-color: #4a5568;
}

body.theme-dark .category-tab-button {
    color: #a0aec0;
}

body.theme-dark .category-tab-button:hover {
    background: #4a5568;
    color: #f7fafc;
}

body.theme-dark .category-tab-button.active {
    color: #63b3ed;
    border-bottom-color: #63b3ed;
    background: #2d3748;
}
</style>

<script>
function showLanguageTab(langCode, element) {
    // Tüm dil içeriklerini gizle
    document.querySelectorAll('.language-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Tüm dil tab butonlarını pasif yap
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Seçilen dil içeriğini göster
    const targetLang = document.getElementById('lang-' + langCode);
    if (targetLang) {
        targetLang.classList.add('active');
        
        // İlk kategoriyi göster
        const firstCategory = targetLang.querySelector('.category-content');
        if (firstCategory) {
            showCategoryTab(firstCategory.id.split('-')[2], document.querySelector('.category-tab-button.active'));
        }
    }
    
    // Seçilen dil tab butonunu aktif yap
    element.classList.add('active');
}

function showCategoryTab(catCode, element) {
    // Aktif dil kodunu al
    const activeLang = document.querySelector('.language-content.active');
    if (!activeLang) return;
    
    const langCode = activeLang.id.replace('lang-', '');
    
    // Sadece aktif dil içindeki kategori içeriklerini gizle
    const activeLangContent = document.querySelector('.language-content.active');
    if (activeLangContent) {
        activeLangContent.querySelectorAll('.category-content').forEach(content => {
            content.classList.remove('active');
        });
    }
    
    // Tüm kategori tab butonlarını pasif yap
    document.querySelectorAll('.category-tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Seçilen kategori içeriğini göster
    const targetContent = document.getElementById('content-' + langCode + '-' + catCode);
    if (targetContent) {
        targetContent.classList.add('active');
        console.log('Showing content for:', langCode + '-' + catCode);
    } else {
        console.log('Content not found for:', 'content-' + langCode + '-' + catCode);
    }
    
    // Seçilen kategori tab butonunu aktif yap
    element.classList.add('active');
}

function loadLanguageFile(langCode) {
    // window.__mt objesini oluştur (eğer yoksa)
    if (!window.__mt) {
        window.__mt = {
            ln: 'en',
            setting: {
                format: {
                    ln: 'en'
                }
            }
        };
    }
    
    // Angular.js'i yükle (eğer yoksa)
    if (!window.angular) {
        const angularScript = document.createElement('script');
        angularScript.src = 'https://cdn.jsdelivr.net/npm/angular@1.8.3/angular.min.js';
        angularScript.onload = function() {
            console.log('Angular.js loaded');
            // Angular yüklendikten sonra dil dosyasını yükle
            loadLanguageScript(langCode);
        };
        angularScript.onerror = function() {
            console.error('Failed to load Angular.js from CDN, trying alternative...');
            // Alternatif olarak unpkg'dan dene
            const altScript = document.createElement('script');
            altScript.src = 'https://unpkg.com/angular@1.8.3/angular.min.js';
            altScript.onload = function() {
                console.log('Angular.js loaded from alternative source');
                loadLanguageScript(langCode);
            };
            altScript.onerror = function() {
                console.error('Failed to load Angular.js from all sources');
            };
            document.head.appendChild(altScript);
        };
        document.head.appendChild(angularScript);
    } else {
        // Angular zaten yüklü, dil dosyasını yükle
        loadLanguageScript(langCode);
    }
}

function loadLanguageScript(langCode) {
    // Mevcut dil script tag'ini kaldır
    const existingScript = document.getElementById('language-script');
    if (existingScript) {
        existingScript.remove();
    }
    
    // Yeni dil dosyasını yükle
    const script = document.createElement('script');
    script.id = 'language-script';
    script.src = '/mytransfers/assets/mytransfersweb/prod/js/lang/' + langCode + '.js';
    script.onload = function() {
        console.log('Language file loaded:', langCode + '.js');
        // Dil değişikliğini global olarak bildir
        if (window.__mt) {
            window.__mt.ln = langCode;
            window.__mt.setting = window.__mt.setting || {};
            window.__mt.setting.format = window.__mt.setting.format || {};
            window.__mt.setting.format.ln = langCode;
        }
        
        // Form alanlarını güncelle
        updateFormFields(langCode);
    };
    script.onerror = function() {
        console.error('Failed to load language file:', langCode + '.js');
    };
    
    document.head.appendChild(script);
}

function updateFormFields(langCode) {
    // Dil dosyasından gelen verileri form alanlarına yansıt
    if (window.__mt && window.__mt[langCode]) {
        const langData = window.__mt[langCode];
        
        // Temel metinler
        if (langData.basic) {
            Object.keys(langData.basic).forEach(key => {
                const input = document.querySelector(`input[name="basic_${langCode}_${key}"]`);
                if (input) {
                    input.value = langData.basic[key];
                }
            });
        }
        
        // Form alanları
        if (langData.forms) {
            Object.keys(langData.forms).forEach(key => {
                const input = document.querySelector(`input[name="forms_${langCode}_${key}"]`);
                if (input) {
                    input.value = langData.forms[key];
                }
            });
        }
        
        // Tarih/Saat
        if (langData.dates) {
            if (langData.dates.daysOfWeek) {
                langData.dates.daysOfWeek.forEach((day, index) => {
                    const input = document.querySelector(`input[name="dates_${langCode}_days_${index}"]`);
                    if (input) {
                        input.value = day;
                    }
                });
            }
            
            if (langData.dates.monthNames) {
                langData.dates.monthNames.forEach((month, index) => {
                    const input = document.querySelector(`input[name="dates_${langCode}_months_${index}"]`);
                    if (input) {
                        input.value = month;
                    }
                });
            }
        }
        
        // Ödeme
        if (langData.payment) {
            const paymentNameInput = document.querySelector(`input[name="payment_${langCode}_name"]`);
            if (paymentNameInput && langData.payment.name) {
                paymentNameInput.value = langData.payment.name;
            }
            
            const paymentDescInput = document.querySelector(`input[name="payment_${langCode}_description"]`);
            if (paymentDescInput && langData.payment.description) {
                paymentDescInput.value = langData.payment.description;
            }
        }
        
        if (langData.payments && langData.payments[0]) {
            const creditNameInput = document.querySelector(`input[name="payment_${langCode}_credit_name"]`);
            if (creditNameInput && langData.payments[0].name) {
                creditNameInput.value = langData.payments[0].name;
            }
            
            const creditDescInput = document.querySelector(`input[name="payment_${langCode}_credit_description"]`);
            if (creditDescInput && langData.payments[0].description) {
                creditDescInput.value = langData.payments[0].description;
            }
        }
        
        // Hizmetler
        if (langData.list_includes) {
            langData.list_includes.forEach((service, index) => {
                const key = Object.keys(window.__mt.en.list_includes)[index];
                if (key) {
                    const input = document.querySelector(`input[name="services_${langCode}_includes_${key}"]`);
                    if (input) {
                        input.value = service.name;
                    }
                }
            });
        }
        
        if (langData.list_extras) {
            langData.list_extras.forEach((service, index) => {
                const key = Object.keys(window.__mt.en.list_extras)[index];
                if (key) {
                    const input = document.querySelector(`input[name="services_${langCode}_extras_${key}"]`);
                    if (input) {
                        input.value = service.name;
                    }
                }
            });
        }
        
        console.log('Form fields updated for language:', langCode);
    }
}

// Sayfa yüklendiğinde ilk dil sekmesine otomatik kaydır
document.addEventListener('DOMContentLoaded', function() {
    // window.__mt objesini oluştur
    if (!window.__mt) {
        window.__mt = {
            ln: 'en',
            setting: {
                format: {
                    ln: 'en'
                }
            }
        };
    }
    
    // İlk aktif tabı bul
    const firstActiveTab = document.querySelector('.language-tab-content.active');
    if (firstActiveTab) {
        setTimeout(() => {
            firstActiveTab.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start',
                inline: 'nearest'
            });
        }, 200);
    }
    
    // İlk dil dosyasını yükle (varsayılan olarak en.js)
    const firstActiveButton = document.querySelector('.tab-button.active');
    if (firstActiveButton) {
        const langCode = firstActiveButton.getAttribute('onclick').match(/'([^']+)'/)[1];
        loadLanguageFile(langCode);
    }
});

// Mevcut çevirileri yükle fonksiyonu
function loadExistingTranslations() {
    const notification = document.getElementById('notification');
    const button = event.target;
    const originalText = button.innerHTML;
    
    // Butonu devre dışı bırak ve yükleniyor mesajı göster
    button.disabled = true;
    button.innerHTML = '⏳ Yükleniyor...';
    
    // Bildirim alanını temizle
    notification.style.display = 'none';
    
    // AJAX isteği gönder
    fetch('/mytransfers/admin/load_translations.php', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Başarılı bildirim göster
            notification.style.display = 'block';
            notification.style.backgroundColor = '#d4edda';
            notification.style.color = '#155724';
            notification.style.border = '1px solid #c3e6cb';
            notification.innerHTML = `✅ ${data.message} Sayfa yenileniyor...`;
            
            // 2 saniye sonra sayfayı yenile
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            // Hata bildirimi göster
            notification.style.display = 'block';
            notification.style.backgroundColor = '#f8d7da';
            notification.style.color = '#721c24';
            notification.style.border = '1px solid #f5c6cb';
            notification.innerHTML = `❌ ${data.message}`;
            
            // Butonu tekrar aktif et
            button.disabled = false;
            button.innerHTML = originalText;
        }
    })
    .catch(error => {
        // Hata bildirimi göster
        notification.style.display = 'block';
        notification.style.backgroundColor = '#f8d7da';
        notification.style.color = '#721c24';
        notification.style.border = '1px solid #f5c6cb';
        notification.innerHTML = '❌ Hata oluştu: ' + error.message;
        
        // Butonu tekrar aktif et
        button.disabled = false;
        button.innerHTML = originalText;
    });
}
</script>

<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);
?>
