<?php
// Start session for language handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include language loader - it's in the same directory as this file
require_once __DIR__ . '/language_loader.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang_loader->getHtmlLang(); ?>" ng-app="app">

<head ng-cloak>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'>
    <meta name="theme-color" content="#09AFEE">
    <meta name="csrf-token" content="LXmDMFAxYsTneYjTiLpIMciaOttqpm3waqWRNkwH">
    <link rel="preconnect" href="/mytransfers/assets/" crossorigin>
        <link rel="preload" href="/mytransfers/assets/mytransfersweb/prod/css/app.css?id=9eac9b50352bc8ff1ff0" as="style">
    <link rel="preload" href="/mytransfers/assets/mytransfersweb/prod/css/default.css?id=c411c7d2deeb901f63e2" as="style">
    <link rel="preload" href="/mytransfers/assets/mytransfersweb/prod/css/home.css?id=843eea6041a0853423a1" as="style">
            <link rel="preload" href="/mytransfers/assets/mytransfersweb/prod/images/bg-new-home.webp" as="image" fetchpriority="high">
        <link rel="preload" href="/mytransfers/assets/mytransfersweb/prod/images/banner-city.webp" as="image" fetchpriority="high">
        <title>Worldwide Transfers: Taxis and Private Transportation - MyTransfers</title>
                <meta name="robots" content="index, follow">
                        <meta name="description" content="Book your private transfer from the airport to any destination today. Benefit from the best prices on our website with free cancellation up to 24 hours before your trip.">
<meta name="author" content=" MyTransfers">
<meta http-equiv="content-language" content="<?php echo $lang_loader->getMetaLanguage(); ?>">

<link rel="canonical" href="<?php echo $lang_loader->getCanonicalUrl(); ?>">
<?php echo $lang_loader->getHreflangLinks(); ?>

<link rel="apple-touch-icon" href="/mytransfers/assets/mytransfersweb/prod/apple-touch-icon.png" />
<link rel="apple-touch-icon" sizes="57x57" href="/mytransfers/assets/mytransfersweb/prod/apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon" sizes="72x72" href="/mytransfers/assets/mytransfersweb/prod/apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon" sizes="76x76" href="/mytransfers/assets/mytransfersweb/prod/apple-touch-icon-76x76.png" />
<link rel="apple-touch-icon" sizes="114x114" href="/mytransfers/assets/mytransfersweb/prod/apple-touch-icon-114x114.png" />
<link rel="apple-touch-icon" sizes="120x120" href="/mytransfers/assets/mytransfersweb/prod/apple-touch-icon-120x120.png" />
<link rel="apple-touch-icon" sizes="144x144" href="/mytransfers/assets/mytransfersweb/prod/apple-touch-icon-144x144.png" />
<link rel="apple-touch-icon" sizes="152x152" href="/mytransfers/assets/mytransfersweb/prod/apple-touch-icon-152x152.png" />
<link rel="apple-touch-icon" sizes="180x180" href="/mytransfers/assets/mytransfersweb/prod/apple-touch-icon-180x180.png" />


<meta property="og:locale" content="<?php echo $lang_loader->getOgLocale(); ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="Worldwide Transfers: Taxis and Private Transportation - MyTransfers">
<meta property="og:description" content="Book your private transfer from the airport to any destination today. Benefit from the best prices on our website with free cancellation up to 24 hours before your trip.">
<meta property="og:url" content="https://www.mytransfers.com/en/en/">
<meta property="og:image" content="https://www.mytransfers.com/img/background.png">
<meta property="og:site_name" content=" MyTransfers">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:description" content="Book your private transfer from the airport to any destination today. Benefit from the best prices on our website with free cancellation up to 24 hours before your trip.">
<meta name="twitter:title" content="Worldwide Transfers: Taxis and Private Transportation - MyTransfers">
<meta name="twitter:site" content=" MyTransfers">
<meta name="twitter:image" content="https://www.mytransfers.com/img/background.png">

           
    <link rel="icon" href="/mytransfers/assets/mytransfersweb/prod/logo.png">
    <link rel="stylesheet" type="text/css" href="/mytransfers/assets/mytransfersweb/prod/css/app.css?id=9eac9b50352bc8ff1ff0" media="all">

            <link rel="preload" href="/mytransfers/assets/mytransfersweb/prod/css/fonts-text.css?id=984bb359067b1cbf85af" as="style" onload="this.rel='stylesheet'">
        <noscript>
            <link rel="stylesheet" href="/mytransfers/assets/mytransfersweb/prod/css/fonts-text.css?id=984bb359067b1cbf85af">
        </noscript>
    
            <link rel="stylesheet" type="text/css" href="/mytransfers/assets/mytransfersweb/prod/css/default.css?id=c411c7d2deeb901f63e2" media="all" />
    
    
            <link rel="stylesheet" type="text/css" href="/mytransfers/assets/mytransfersweb/prod/css/home.css?id=843eea6041a0853423a1" media="all" />
        <link rel="stylesheet" type="text/css" href="/mytransfers/assets/mytransfersweb/prod/css/base.css?id=1dfface8dff11bc61d4c" media="all" />
    
    <style>
           /* Validation Error Message Styles */
       .validation-error-message {
           background: #dc3545;
           color: white;
           padding: 15px;
           margin: 10px 0;
           border-radius: 5px;
           border-left: 4px solid #c82333;
           box-shadow: 0 2px 4px rgba(0,0,0,0.1);
           animation: slideIn 0.3s ease-out;
       }
       
       .validation-error-message .error-content {
           display: flex;
           align-items: center;
           font-weight: 500;
       }
       
       .validation-error-message .error-content i {
           margin-right: 10px;
           font-size: 18px;
       }
       
       /* Field-specific error messages */
       .field-error-message {
           color: #dc3545;
           font-size: 12px;
           margin-top: 5px;
           display: none;
           animation: fadeIn 0.3s ease-out;
       }
       
       .field-error-message.show {
           display: block;
       }
    
    /* Input Error Styles */
    .input-error {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .input-error:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
           /* Animation for error message */
       @keyframes slideIn {
           from {
               opacity: 0;
               transform: translateY(-10px);
           }
           to {
               opacity: 1;
               transform: translateY(0);
           }
       }
       
       @keyframes fadeIn {
           from {
               opacity: 0;
           }
           to {
               opacity: 1;
           }
       }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .validation-error-message {
            margin: 5px 0;
            padding: 12px;
            font-size: 14px;
        }
    }
    </style>
     
    </head>

<body ng-cloak class="" >
