<?php
// Basit header - AngularJS olmadan
?>
<section class="container-fluid c-banner">
    <header class="c-header text-white">
        <nav class="navbar navbar-expand-lg c-nav">
            <div class="container">
                <a class="navbar-brand" href="/mytransfers/<?php echo $lang_loader->getCurrentLanguage(); ?>/"
                    title="Mytransfers">
                    <img src="/mytransfers/assets/mytransfersweb/prod/logo.png" 
                         alt="MyTransfers Logo" 
                         style="height: 40px; width: auto;">
                </a>
                <div class="menu-mobile">
                    <div class="link-mobile float-right">
                        <button class="navbar-toggler float-right py-2 ml-0" type="button" data-toggle="collapse"
                            data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="true"
                            aria-label="Toggle navigation">
                            <span class="icon icon-menu"></span>
                        </button>
                    </div>
                </div>
                <div class="collapse navbar-collapse p-3 p-lg-0" id="navbarNavAltMarkup">
                    <div class="navbar-nav ml-auto">
                        <a class="nav-item nav-link br-01 px-4 underline-effect" href="/mytransfers/public/my-bookings.html"
                            title="My Bookings"
                            aria-label="My Bookings">My Bookings</a>
                        
                        <a class="nav-item nav-link active px-4 underline-effect"
                            href="/mytransfers/public/help.html"
                            title="Help Centre"
                            aria-label="Help Centre">Help Centre</a>
                        
                        <?php include 'header_language_switcher.php'; ?>
                        
                        <div class="position-relative dropdown c-center hide-mobile">
                            <a class="nav-item nav-link px-4 dropdown-toggle" href="#" id="dropdow-money"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                EUR
                            </a>
                            <div class="dropdown-menu dm-singular px-0" aria-labelledby="dropdow-money">
                                <div class="dropdown-item px-3 text-center">
                                    <div class="row mt-2">
                                        <div class="col-4 px-1">
                                            <a class="d-link px-2 py-1 d-link-currency"
                                                rel="nofollow"
                                                href="https://www.mytransfers.com/currency/USD/?code=USD $">
                                                <span class="pr-0 font-12">$</span>
                                                <span>USD</span>
                                            </a>
                                        </div>
                                        <div class="col-4 px-1">
                                            <a class="d-link px-2 py-1 d-link-currency dl-active"
                                                rel="nofollow"
                                                href="https://www.mytransfers.com/currency/EUR/?code=EUR €">
                                                <span class="pr-0 font-12">€</span>
                                                <span>EUR</span>
                                            </a>
                                        </div>
                                        <div class="col-4 px-1">
                                            <a class="d-link px-2 py-1 d-link-currency"
                                                rel="nofollow"
                                                href="https://www.mytransfers.com/currency/GBP/?code=GBP £">
                                                <span class="pr-0 font-12">£</span>
                                                <span>GBP</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <a class="nav-item nav-link c-login mx-2 px-3 mx-lg-2 my-2 my-lg-0 underline-effect"
                            href="https://www.mytransfers.com/en/login/">
                            <span class="icon icon-person"></span>
                            <span>Login</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
</section>
