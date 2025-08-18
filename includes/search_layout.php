<?php
// Start session for language handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include language loader
require_once __DIR__ . '/language_loader.php';
$lang_loader = new LanguageLoader();
$current_lang = $lang_loader->getCurrentLanguage();
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
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>
    
    <!-- Main Content -->
    <!-- PAGE_CONTENT -->
    
    <!-- Footer -->
    <?php include 'footer.php'; ?>
    
    <!-- Scripts -->
    <?php include 'scripts.php'; ?>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
