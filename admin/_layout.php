<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();
$user = current_user();
$cfgFile = $STORAGE . DIRECTORY_SEPARATOR . 'config.json';
$__maintenance = false;
if (is_file($cfgFile)) { $cfg = json_decode(file_get_contents($cfgFile), true); $__maintenance = is_array($cfg) ? !empty($cfg['maintenance']) : false; }
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/mytransfers/admin/assets/admin.css?v=<?php echo @filemtime(__DIR__.'/assets/admin.css') ?: time(); ?>" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body class="<?php echo (empty($_COOKIE['adm_theme']) || $_COOKIE['adm_theme']==='dark') ? 'theme-dark' : '';?>">
    <header class="admin">
        <div class="admin-header-left">
            <div class="admin-logo">
                <img src="/mytransfers/assets/mytransfersweb/prod/logo.png" 
                     alt="MyTransfers Logo" 
                     style="height: 32px; width: auto;">
                <span>Admin</span>
            </div>
            <div class="admin-search"><input class="admin-input" type="search" placeholder="Search… (booking, email, zone)" onkeydown="if(event.key==='Enter'){location.href='/mytransfers/admin/reservations.php?q='+encodeURIComponent(this.value)}" /></div>
            <?php if ($__maintenance): ?><span class="badge-maint">MAINTENANCE</span><?php endif; ?>
        </div>
        <div class="admin-nav">
            <a href="#" onclick="toggleSidebar();return false;"><i class='bx bx-menu'></i> Menu</a>
            <a href="#" onclick="toggleTheme();return false;"><i id="themeIcon" class='bx bx-moon'></i> Theme</a>
            <a href="/mytransfers/admin/logout.php"><i class='bx bx-power-off'></i> Logout</a>
        </div>
    </header>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <ul class="admin-menu" id="adminMenu">
                <!-- Ana Sayfa -->
                <li class="menu-category">
                    <span class="category-title">Ana Sayfa</span>
                </li>
                <li><a href="/mytransfers/admin/dashboard.php"><i class='bx bx-home-alt'></i> <span class="label">Dashboard</span></a></li>
                
                <!-- Rezervasyonlar -->
                <li class="menu-category">
                    <span class="category-title">Rezervasyonlar</span>
                </li>
                <li><a href="/mytransfers/admin/reservations.php"><i class='bx bx-list-check'></i> <span class="label">Rezervasyonlar</span></a></li>
                <li><a href="/mytransfers/admin/reports.php"><i class='bx bx-chart'></i> <span class="label">Raporlar</span></a></li>
                
                <!-- İçerik Yönetimi -->
                <li class="menu-category">
                    <span class="category-title">İçerik Yönetimi</span>
                </li>
                <li><a href="/mytransfers/admin/destinations.php"><i class='bx bx-map-alt'></i> <span class="label">Destinasyonlar</span></a></li>
                <li><a href="/mytransfers/admin/translations.php"><i class='bx bx-globe'></i> <span class="label">Çeviriler</span></a></li>
                <li><a href="/mytransfers/admin/translations_countries.php"><i class='bx bx-world'></i> <span class="label">Ülke & Havalimanı Çevirileri</span></a></li>
                
                <!-- Fiyatlandırma -->
                <li class="menu-category">
                    <span class="category-title">Fiyatlandırma</span>
                </li>
                <li><a href="/mytransfers/admin/pricing.php"><i class='bx bx-dollar'></i> <span class="label">Fiyatlandırma</span></a></li>
                <li><a href="/mytransfers/admin/coupons.php"><i class='bx bx-purchase-tag'></i> <span class="label">Kuponlar</span></a></li>
                <li><a href="/mytransfers/admin/vehicle_classes.php"><i class='bx bx-car'></i> <span class="label">Araç Sınıfları</span></a></li>
                <li><a href="/mytransfers/admin/currency.php"><i class='bx bx-money'></i> <span class="label">Para Birimi</span></a></li>
                
                <!-- Bölge Yönetimi -->
                <li class="menu-category">
                    <span class="category-title">Bölge Yönetimi</span>
                </li>
                <li><a href="/mytransfers/admin/zones.php"><i class='bx bx-grid'></i> <span class="label">Bölgeler</span></a></li>
                <li><a href="/mytransfers/admin/zone_matrix.php"><i class='bx bx-table'></i> <span class="label">Bölge Matrisi</span></a></li>
                <li><a href="/mytransfers/admin/zone_matrix_cross.php"><i class='bx bx-table'></i> <span class="label">Çapraz Bölge Matrisi</span></a></li>
                
                <!-- Kullanıcı Yönetimi -->
                <li class="menu-category">
                    <span class="category-title">Kullanıcı Yönetimi</span>
                </li>
                <li><a href="/mytransfers/admin/users.php"><i class='bx bx-user'></i> <span class="label">Kullanıcılar</span></a></li>
                
                <!-- Sistem Ayarları -->
                <li class="menu-category">
                    <span class="category-title">Sistem Ayarları</span>
                </li>
                <li><a href="/mytransfers/admin"><i class='bx bx-cog'></i> <span class="label">Genel Ayarlar</span></a></li>
                <li><a href="/mytransfers/admin/payment.php"><i class='bx bx-credit-card'></i> <span class="label">Ödeme Ayarları</span></a></li>
                <li><a href="/mytransfers/admin/email.php"><i class='bx bx-envelope'></i> <span class="label">E-posta Ayarları</span></a></li>
                <li><a href="/mytransfers/admin/import.php"><i class='bx bx-upload'></i> <span class="label">İçe/Dışa Aktar</span></a></li>
            </ul>
        </aside>
        <main class="admin-content">
            <!-- PAGE_CONTENT -->
        </main>
    </div>
    <script>
    function toggleTheme(){
        var body = document.body;
        var icon = document.getElementById('themeIcon');
        
        if (body.classList.contains('theme-dark')) {
            // Switch to light theme
            body.classList.remove('theme-dark');
            document.cookie = 'adm_theme=light; path=/; SameSite=Lax';
            if(icon){ icon.className = 'bx bx-moon'; }
        } else {
            // Switch to dark theme
            body.classList.add('theme-dark');
            document.cookie = 'adm_theme=dark; path=/; SameSite=Lax';
            if(icon){ icon.className = 'bx bx-sun'; }
        }
    }
    function toggleSidebar(){
        if (window.innerWidth <= 992) {
            var s = document.querySelector('.admin-sidebar');
            if (s) s.classList.toggle('open');
            return;
        }
        var mini = document.body.classList.toggle('sidebar-mini');
        document.cookie = 'adm_sidebar='+(mini?'mini':'full')+'; path=/';
    }
    // Set active menu item
    (function(){
        var here = location.pathname.toLowerCase();
        var links = document.querySelectorAll('#adminMenu a');
        links.forEach(function(a){
            var href = a.getAttribute('href');
            if (!href) return;
            var path = href.toLowerCase();
            if (here === path || here.endsWith(path.replace('/mytransfers',''))) {
                a.classList.add('active');
            }
        });
        // Theme icon will be set by the cookie-based theme logic below
        // Sidebar state from cookie
        try {
            var cs = document.cookie.split(';').map(function(s){return s.trim();});
            var found = cs.find(function(s){ return s.indexOf('adm_sidebar=')===0; });
            if (found && found.split('=')[1]==='mini') {
                document.body.classList.add('sidebar-mini');
            }
            var theme = cs.find(function(s){ return s.indexOf('adm_theme=')===0; });
            var themeVal = theme ? theme.split('=')[1] : 'dark'; // Default to dark
            
            if (themeVal === 'dark') {
                document.body.classList.add('theme-dark');
                var icon = document.getElementById('themeIcon');
                if(icon){ icon.className = 'bx bx-sun'; }
            } else {
                document.body.classList.remove('theme-dark');
                var icon = document.getElementById('themeIcon');
                if(icon){ icon.className = 'bx bx-moon'; }
            }
        } catch(e){}
    })();
    </script>
</body>
</html>


