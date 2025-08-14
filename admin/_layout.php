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
            <div class="admin-logo"><span class="dot"></span><span>MyTransfers Admin</span></div>
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
            <div class="admin-brand"><span class="dot"></span> <span class="brand-text">MyTransfers Admin</span></div>
            <ul class="admin-menu" id="adminMenu">
                <li><a href="/mytransfers/admin/dashboard.php"><i class='bx bx-home-alt'></i> <span class="label">Dashboard</span></a></li>
                <li><a href="/mytransfers/admin"><i class='bx bx-cog'></i> <span class="label">Settings</span></a></li>
                <li><a href="/mytransfers/admin/reservations.php"><i class='bx bx-list-check'></i> <span class="label">Reservations</span></a></li>
                <li><a href="/mytransfers/admin/destinations.php"><i class='bx bx-map-alt'></i> <span class="label">Destinations</span></a></li>
                <li><a href="/mytransfers/admin/coupons.php"><i class='bx bx-purchase-tag'></i> <span class="label">Coupons</span></a></li>
                <li><a href="/mytransfers/admin/pricing.php"><i class='bx bx-dollar'></i> <span class="label">Pricing</span></a></li>
                <li><a href="/mytransfers/admin/users.php"><i class='bx bx-user'></i> <span class="label">Users</span></a></li>
				<li><a href="/mytransfers/admin/payment.php"><i class='bx bx-credit-card'></i> <span class="label">Payment Settings</span></a></li>
				<li><a href="/mytransfers/admin/email.php"><i class='bx bx-envelope'></i> <span class="label">Email Settings</span></a></li>
				<li><a href="/mytransfers/admin/import.php"><i class='bx bx-upload'></i> <span class="label">Import / Export</span></a></li>
				<li><a href="/mytransfers/admin/reports.php"><i class='bx bx-chart'></i> <span class="label">Reports</span></a></li>
				<li><a href="/mytransfers/admin/zones.php"><i class='bx bx-grid'></i> <span class="label">Zones</span></a></li>
				<li><a href="/mytransfers/admin/zone_matrix.php"><i class='bx bx-table'></i> <span class="label">Zone Matrix</span></a></li>
				<li><a href="/mytransfers/admin/zone_matrix_cross.php"><i class='bx bx-table'></i> <span class="label">Cross Region Matrix</span></a></li>
            </ul>
        </aside>
        <main class="admin-content">
            <!-- PAGE_CONTENT -->
        </main>
    </div>
    <script>
    function toggleTheme(){
        // Force dark header/site: keep dark class always on
        document.body.classList.add('theme-dark');
        document.cookie = 'adm_theme=dark; path=/; SameSite=Lax';
        var icon = document.getElementById('themeIcon');
        if(icon){ icon.className = 'bx bx-sun'; }
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
        // Theme icon initial
        if (document.body.classList.contains('theme-dark')) {
            var icon = document.getElementById('themeIcon');
            if(icon){ icon.className = 'bx bx-sun'; }
        }
        // Sidebar state from cookie
        try {
            var cs = document.cookie.split(';').map(function(s){return s.trim();});
            var found = cs.find(function(s){ return s.indexOf('adm_sidebar=')===0; });
            if (found && found.split('=')[1]==='mini') {
                document.body.classList.add('sidebar-mini');
            }
            var theme = cs.find(function(s){ return s.indexOf('adm_theme=')===0; });
            var themeVal = theme ? theme.split('=')[1] : '';
            // Always enforce dark as requested
            document.body.classList.add('theme-dark');
            document.cookie = 'adm_theme=dark; path=/; SameSite=Lax';
            themeVal = 'dark';
            if (themeVal==='dark') {
                document.body.classList.add('theme-dark');
                var icon = document.getElementById('themeIcon');
                if(icon){ icon.className = 'bx bx-sun'; }
            }
        } catch(e){}
    })();
    </script>
</body>
</html>


