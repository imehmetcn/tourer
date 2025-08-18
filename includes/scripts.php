     
     
     
     
 
        <script type="text/javascript" src="/mytransfers/assets/mytransfersweb/prod/js/angular.min.js?id=a8b55518d97946573752"></script>
        <script type="text/javascript" src="/mytransfers/assets/mytransfersweb/prod/js/vendor/angular-animate.min.js"></script>
        <script type="text/javascript" src="/mytransfers/assets/mytransfersweb/prod/js/vendor/angular-sanitize.min.js"></script>
        <script type="text/javascript" src="/mytransfers/assets/mytransfersweb/prod/js/vendor/angular-local-storage.min.js"></script>
        <script src="/mytransfers/assets/mytransfersweb/prod/js/default.js?id=febbf972f31d0a5cd5f6"></script>
        
        <!-- Load our search override BEFORE other scripts -->
        <script src="/mytransfers/assets/mytransfersweb/prod/js/search-override.js"></script>
    
    <script type="text/javascript" src="<?php echo $lang_loader->getLanguageFileWithVersion(); ?>"></script>

    <script>
        // MyTransfers JavaScript Configuration - Orijinal siteden birebir kopyalandı
        window.__mt = window.__mt || {};
        window.__mt.ln = window.__mt.ln || {};
        window.__mt.setting = window.__mt.setting || {};
        window.__mt.setting.user = {};
        
        // Dil ve para birimi ayarları (orijinal sitedeki gibi)
        window.__mt.ln.currency = "EUR";
        window.__mt.ln.lang = "<?php echo $lang_loader->getCurrentLanguage(); ?>";
        window.__mt.ln.cancel = "Cancel";
        window.__mt.ln.ok = "Ok";
        window.__mt.ln.currency_code = "EUR €";
        
        // Asset URL (orijinal sitedeki gibi)
        window.__mt.setting.asset_url = "/mytransfersweb/prod/";
        
        // Sayfa URL'leri (orijinal sitedeki gibi, projenize uyarlandı)
        window.__mt.setting.root_page = "/mytransfers/<?php echo $lang_loader->getCurrentLanguage(); ?>/";
        window.__mt.setting.search_page = "/mytransfers/<?php echo $lang_loader->getCurrentLanguage(); ?>/search/";
        window.__mt.setting.checkout_page = "/mytransfers/<?php echo $lang_loader->getCurrentLanguage(); ?>/checkout/";
        
        // API endpoint'leri (orijinal sitedeki gibi, projenize uyarlandı)
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

        // Google Maps API - Disabled to prevent legacy API errors
        // Using our own API endpoints instead
        window.__mt.setting.google_map = "";
        window.__mt.setting.google_places = "";
        
        // Flag to indicate we're using custom API
        window.__mt.setting.use_custom_api = true;

        window.__mt.setting.user = null;
    </script>
        <script type="text/javascript" src="/mytransfers/assets/mytransfersweb/prod/js/angular.js?id=8013260186aff6a56bb1"></script>
        <script src="/mytransfers/assets/mytransfersweb/prod/js/search/controller.js?id=458fe1323cc70934ba1f"></script>
    <script>
        $(document).ready(function() {
            // Anasayfa ve rezervasyon formu çevirilerini uygula
            function applyTranslations() {
                if (window.__mt) {
                    $('[data-translate]').each(function() {
                        var $element = $(this);
                        var key = $element.attr('data-translate');
                        
                        // homepage. ile başlayan çevirileri işle
                        if (key.startsWith('homepage.') && window.__mt.homepage) {
                            var translationKey = key.replace('homepage.', '');
                            var translation = window.__mt.homepage[translationKey];
                            
                            if (translation) {
                                $element.html(translation);
                            }
                        }
                        
                        // booking_form. ile başlayan çevirileri işle
                        if (key.startsWith('booking_form.') && window.__mt.booking_form) {
                            var translationKey = key.replace('booking_form.', '');
                            var translation = window.__mt.booking_form[translationKey];
                            
                            if (translation) {
                                $element.html(translation);
                            }
                        }
                        
                        // validation_errors. ile başlayan çevirileri işle
                        if (key.startsWith('validation_errors.') && window.__mt.validation_errors) {
                            var translationKey = key.replace('validation_errors.', '');
                            var translation = window.__mt.validation_errors[translationKey];
                            
                            if (translation) {
                                $element.html(translation);
                            }
                        }
                    });
                    
                    // Placeholder çevirilerini uygula
                    $('[data-translate-placeholder]').each(function() {
                        var $element = $(this);
                        var key = $element.attr('data-translate-placeholder');
                        
                        if (key.startsWith('booking_form.') && window.__mt.booking_form) {
                            var translationKey = key.replace('booking_form.', '');
                            var translation = window.__mt.booking_form[translationKey];
                            
                            if (translation) {
                                $element.attr('placeholder', translation);
                            }
                        }
                    });
                }
            }
            
            // Sayfa yüklendiğinde çevirileri uygula
            applyTranslations();
            
            // Dil değiştiğinde çevirileri güncelle
            $(document).on('languageChanged', function() {
                setTimeout(applyTranslations, 100);
            });
        });
    </script>

        <script src="/mytransfers/assets/mytransfersweb/prod/js/home/controller.js?id=0f5e4cf2360450d31bfb"></script>
        <script>
            window.__mt.setting.coupon = null;
        </script>

        <script>
            (function(){
                try {
                    var captureOn = /(?:[?&])captureAll=1(?:&|$)/.test(window.location.search) || (window.localStorage && window.localStorage.getItem('__mt_capture') === '1');
                    if (!captureOn) return;
                    var addFlags = function(u){
                        if (typeof u !== 'string') return u;
                        var sep = u.indexOf('?') === -1 ? '?' : '&';
                        return u + sep + 'origin=1&capture=1&capture_schema=0';
                    };
                    var s = window.__mt && window.__mt.setting ? window.__mt.setting : {};
                    Object.keys(s).forEach(function(k){
                        if (k.indexOf('api_') === 0 && typeof s[k] === 'string' && (s[k].indexOf('/api/') !== -1)) {
                            s[k] = addFlags(s[k]);
                        }
                    });
                    if (window.console && console.log) console.log('CaptureAll ON: origin/capture flags appended to api_* endpoints');
                } catch(e) {}
            })();
        </script>
    </body>

</html>
