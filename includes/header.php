    <section class="container-fluid c-banner"  >
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
                            <!-- end nuevo -->

                            <?php include 'header_language_switcher.php'; ?>

                            <?php
                            // Para birimi yöneticisini dahil et
                            require_once __DIR__ . '/currency_manager.php';
                            $currency_manager = new CurrencyManager();
                            echo $currency_manager->renderCurrencySelector();
                            ?>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        <div class="row">
            <div class="col-12">
                                    <div class="container z-index-search" ng-controller="searchController as vm">
    <div class="row my-4">
                    <div class="col-12 text-left">
                                    
                                            <h1 class="cb-txt" data-translate="booking_form.search_title">Are you looking for airport transfers?</h1>
                        <p class="cb-txt-subtitle" data-translate="booking_form.search_subtitle">You have come to the right place</p>
                                                </div>
            <div class="col-12 col-lg-7 c-mobile"></div>
        
        
        
        
        
            </div>

    <div class="row c-box">
        <form name="vm.form" ng-init="selectedTab = 'search'" ng-submit="vm.onSubmit(vm.form)" autocomplete="off"
            novalidate class="col-12" action="/mytransfers/search.php" method="get">

            <div id="myTabContent">
                <div class="tab-pane fade show active" id="oneway" role="tabpanel" aria-labelledby="oneway-tab">
                    <div class="row px-0 d-flex">
                        <div class="col-md-12">
                            <div class="row">
                                <!--search client -->
                                                                <!-- end search client-->

                                <div class="container my-2">
                                    <div class="my-row">
                                        <!-- Columna del switch -->
                                        <div class="switch-col tab-content p-2">
                                            <div class="two-part-switch f-bold">
                                                <input type="radio" id="switch-oneway" name="trip_type"
                                                    class="switch-checkbox" value="oneway" ng-model="vm.data.type"
                                                    ng-value="true">
                                                <label for="switch-oneway" class="switch-option"
                                                    ng-class="{ 'active': vm.data.type }">
                                                    {{ vm.ln.oneway || 'One-way' }}
                                                </label>

                                                <input type="radio" id="switch-roundtrip" name="trip_type"
                                                    class="switch-checkbox" value="roundtrip" ng-model="vm.data.type"
                                                    ng-value="false">
                                                <label for="switch-roundtrip" class="switch-option"
                                                    ng-class="{ 'active': !vm.data.type }">
                                                    {{ vm.ln.roundtrip || 'Round-Trip' }}
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Segunda columna (se ocultará en mobile) -->
                                        <div class="passengers-col tab-content p-2">
                                            <div id="pax_parent">
                                                <div class="c-form position-relative" id="dropdow-persons"
                                                    ng-click="vm.showPassenger($event)">
                                                    <div class="p-2 label-passenger" aria-label="Passengers" data-translate="booking_form.passengers">Passengers</div>
                                                    <div class="value form-control-passengers">
                                                        {{ vm.data.adults }} {{ vm.data.adults == 1 ? vm.ln.adult : vm.ln.adults }}
                                                        {{ (vm.data.children + vm.data.infants) > 0 ? ', ' + (vm.data.children + vm.data.infants) + ' ' + vm.ln.minors : '' }}
                                                    </div>
                                                    <span class="icon-passenger icon-people"></span>
                                                </div>

                                                <!-- Dropdown de pasajeros -->
                                                <div class="dropdown-menu" id="pax-view"
                                                    aria-labelledby="dropdow-persons">
                                                    <div class="dropdown-item py-2">
                                                        <!-- Adultos -->
                                                        <div class="col-12 p-0">
                                                            <div class="row">
                                                                <div class="col-12 p-0">
                                                                    <div class="mb-0" aria-label="Adults" data-translate="booking_form.adults">Adults</div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="btn-float btn-left border-left border-top border-bottom p-2 float-left pointer"
                                                                    ng-click="vm.removePax('adults', 1)"
                                                                    ng-class="{'btn-pax-inactive' : vm.data.adults == 1}">
                                                                    <span class="icon-less color-secundary"></span>
                                                                </div>
                                                                <div
                                                                    class="btn-float border-top border-bottom p-2 float-left font-14 text-dark">
                                                                    <span>{{ vm.data.adults }}</span>
                                                                </div>
                                                                <div class="btn-float btn-right border-top border-bottom border-right p-2 float-left pointer"
                                                                    ng-click="vm.addPax('adults')">
                                                                    <span class="icon-more color-secundary"></span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Niños -->
                                                        <div class="col-12 p-0 mt-2">
                                                            <div class="row">
                                                                <div class="col-12 p-0">
                                                                    <div class="mb-0" aria-label="Children" data-translate="booking_form.children">Children</div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="btn-float btn-left border-left border-top border-bottom p-2 float-left pointer"
                                                                    ng-click="vm.removePax('children', 0)"
                                                                    ng-class="{'btn-pax-inactive' : vm.data.children == 0}">
                                                                    <span class="icon-less color-secundary"></span>
                                                                </div>
                                                                <div
                                                                    class="btn-float border-top border-bottom p-2 float-left font-14 text-dark">
                                                                    <span>{{ vm.data.children }}</span>
                                                                </div>
                                                                <div class="btn-float btn-right border-top border-bottom border-right p-2 float-left pointer"
                                                                    ng-click="vm.addPax('children')">
                                                                    <span class="icon-more color-secundary"></span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Infantes -->
                                                        <div class="col-12 p-0 mt-2">
                                                            <div class="row">
                                                                <div class="col-12 p-0">
                                                                    <div class="mb-0" aria-label="Infants" data-translate="booking_form.infants">Infants</div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="btn-float btn-left border-left border-top border-bottom p-2 float-left pointer"
                                                                    ng-click="vm.removePax('infants', 0)"
                                                                    ng-class="{'btn-pax-inactive' : vm.data.infants == 0}">
                                                                    <span class="icon-less color-secundary"></span>
                                                                </div>
                                                                <div
                                                                    class="btn-float border-top border-bottom p-2 float-left font-14 text-dark">
                                                                    <span>{{ vm.data.infants }}</span>
                                                                </div>
                                                                <div class="btn-float btn-right border-top border-bottom border-right p-2 float-left pointer"
                                                                    ng-click="vm.addPax('infants')">
                                                                    <span class="icon-more color-secundary"></span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="button-close-passengers"
                                                        ng-click="vm.showPassenger($event)" data-translate="booking_form.apply">
                                                        Apply
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="container tab-content">
                                <div class="my-row py-4" ng-class="{ 'roundtrip': !vm.data.type }">
                                                                         <div class="input-pick-up"
                                         ng-class="{'input-error': vm.submit && !vm.form.from.$valid}">
                                         <div class="c-form position-relative">
                                             <label for="from_view"
                                                 class="p-2 label-title" data-translate="booking_form.from">From</label>
                                             <input id="from_view" type="text" class="form-control"
                                                 placeholder="Pickup location" data-translate-placeholder="booking_form.pickup_location"
                                                 ng-change="vm.search('showfrom', vm.data.from_view, 'from')"
                                                 ng-click="vm.deleteItemSelected('from_view', 'search_from', 'from')"
                                                 ng-model="vm.data.from_view" ng-model-options="{ debounce: 700 }"
                                                 oninput="clearInputError(this)"
                                                 aria-label="Pickup location"
                                                 aria-describedby="basic-addon1" />
                                             <span class="icon icon-location"></span>
                                         </div>
                                                                                   <div id="from_error" class="field-error-message" data-translate="validation_errors.from_required">Lütfen kalkış noktasını seçiniz</div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="content-search wide-on-desktop">
                                                    <div class="search shadow" ng-show="vm.showfrom">
                                                        <ul class="list-group list-search" id="list_search_from">
                                                            <li class="list-group-item pl-10 font-14"
                                                                ng-repeat="result in vm.results"
                                                                ng-click="vm.selectResult(result, 'mt', 'from', 'showfrom')">
                                                                <i ng-class="vm.iconType(result)"
                                                                    aria-hidden="true"></i> {{ result.name }}
                                                            </li>

                                                            <li class="list-group-item pl-10 font-14"
                                                                ng-repeat="result in vm.results_gm"
                                                                ng-click="vm.selectResult(result, 'gm', 'from', 'showfrom')">
                                                                <i ng-class="vm.iconType(result)"
                                                                    aria-hidden="true"></i> {{ result.structured_formatting.main_text }}
                                                                <span
                                                                    ng-show="result.structured_formatting.secondary_text"
                                                                    class="form_secondary_text">
                                                                    {{ result.structured_formatting.secondary_text }}</span>
                                                            </li>

                                                            
                                                            <li class="list-group-item pl-10 font-14"
                                                                ng-repeat="result in vm.results_pr"
                                                                ng-click="vm.selectResult(result, 'pr', 'from', 'showfrom')">
                                                                <i ng-class="vm.iconType(result)"
                                                                    aria-hidden="true"></i> {{ result.main_text }}
                                                                <span ng-show="result.description"
                                                                    class="form_secondary_text">
                                                                    {{ result.description }}</span>
                                                            </li>

                                                            

                                                            <li class="list-group-item d-center find_from_gp font-14"
                                                                ng-show="!vm.loading_search && !vm.results_gm.length && !vm.search_in_places">
                                                                <a href="#" class="find_from_gp"
                                                                    ng-click="vm.searchInPlaces('showfrom','')" data-translate="booking_form.see_more_results">See more results</a>
                                                            </li>

                                                            <li class="list-group-item d-center text-center font-14"
                                                                ng-show="vm.loading_search">
                                                                <div class="spinner-border text-info" role="status">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item d-center font-14"
                                                                ng-show="!vm.loading_search && !vm.results.length && !vm.results_gm.length && !vm.results_pr.length">
                                                                <strong data-translate="booking_form.no_results">No results</strong>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                                                         <div class="input-drop-off"
                                         ng-class="{'input-error': vm.submit && !vm.form.to.$valid}">
                                         <div class="c-form position-relative">
                                             <label for="to_view"
                                                 class="p-2 label-title" data-translate="booking_form.to">To</label>
                                             <input id="to_view" type="text" class="form-control"
                                                 placeholder="Dropoff location" data-translate-placeholder="booking_form.dropoff_location"
                                                 ng-change="vm.search('showto', vm.data.to_view, 'to')"
                                                 ng-click="vm.deleteItemSelected('to_view', 'search_to', 'to')"
                                                 ng-model="vm.data.to_view" ng-model-options="{ debounce: 700 }"
                                                 oninput="clearInputError(this)"
                                                 aria-label="Dropoff location"
                                                 aria-describedby="basic-addon2" />
                                             <span class="icon icon-location"></span>
                                         </div>
                                                                                   <div id="to_error" class="field-error-message" data-translate="validation_errors.to_required">Lütfen varış noktasını seçiniz</div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="content-search wide-on-desktop">
                                                    <div class="search shadow" ng-show="vm.showto">
                                                        <ul class="list-group list-search" id="list_search_to">
                                                            <li class="list-group-item pl-10 font-14"
                                                                ng-repeat="result in vm.results_to"
                                                                ng-click="vm.selectResult(result, 'mt', 'to', 'showto')">
                                                                <i ng-class="vm.iconType(result)"
                                                                    aria-hidden="true"></i> {{ result.name }}
                                                            </li>

                                                            
                                                            <li class="list-group-item pl-10 font-14"
                                                                ng-repeat="result in vm.results_gm_to"
                                                                ng-click="vm.selectResult(result, 'gm', 'to', 'showto')">
                                                                <i ng-class="vm.iconType(result)"
                                                                    aria-hidden="true"></i> {{ result.structured_formatting.main_text }}
                                                                <span
                                                                    ng-show="result.structured_formatting.secondary_text"
                                                                    class="form_secondary_text">
                                                                    {{ result.structured_formatting.secondary_text }}</span>
                                                            </li>
                                                            

                                                            
                                                            <li class="list-group-item pl-10 font-14"
                                                                ng-repeat="result in vm.results_pr_to"
                                                                ng-click="vm.selectResult(result, 'pr', 'to', 'showto')">
                                                                <i ng-class="vm.iconType(result)"
                                                                    aria-hidden="true"></i> {{ result.main_text }}
                                                                <span ng-show="result.description"
                                                                    class="form_secondary_text">
                                                                    {{ result.description }}</span>
                                                            </li>

                                                            
                                                            <li class="list-group-item d-center find_to_gp font-14"
                                                                id="find_to_gp"
                                                                ng-show="!vm.loading_search  && !vm.results_gm_to.length && !vm.search_in_places_to">
                                                                <a class="find_to_gp" href="#"
                                                                    ng-click="vm.searchInPlaces('showto','_to')">See more results</a>
                                                            </li>

                                                            <li class="list-group-item d-center text-center font-14"
                                                                ng-show="vm.loading_search">
                                                                <div class="spinner-border text-info" role="status">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div>
                                                            </li>

                                                            <li class="list-group-item d-center font-14"
                                                                ng-show="!vm.loading_search && !vm.results_to.length && !vm.results_gm_to.length && !vm.results_pr_to">
                                                                <strong>No results</strong>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="group-arrival-datetime">
                                                                                 <div class="input-date-arrival"
                                             ng-class="{'input-error': (vm.submit && !vm.form.arrival_date.$valid) || vm.arrival_date}">
                                             <div class="c-form position-relative">
                                                 <label for="from_datepicker"
                                                     class="p-2 label-title" data-translate="booking_form.pickup_date">Pickup date</label>
                                                 <input id="from_datepicker" name="from_datepicker" required readonly
                                                     data-calendar="pickup" class="form-control"
                                                     ng-model="vm.data.arrival_date_only"
                                                     oninput="clearInputError(this)"
                                                     placeholder="Pickup date" />
                                                 <div ng-click="vm.openCalendar('from_datepicker')"
                                                     class="value form-control">
                                                     {{ vm.data.arrival_date_only | asDate | date: "mediumDate" }}
                                                 </div>
                                                 <span class="icon icon-calendar"></span>
                                             </div>
                                                                                           <div id="date_error" class="field-error-message" data-translate="validation_errors.pickup_date_required">Lütfen kalkış tarihini seçiniz</div>
                                         </div>

                                        <div class="input-time-arrival"
                                            ng-class="{'input-error': (vm.submit && !vm.form.arrival_date.$valid) || vm.arrival_date}">
                                            <div class="c-form position-relative">
                                                <label for="arrival_time_picker"
                                                    class="p-2 label-title" data-translate="booking_form.time">Time</label>
                                                <input id="arrival_time_picker" type="text" class="form-control"
                                                    readonly ng-model="vm.data.arrival_time_only" />
                                                <span class="icon icon-clock"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="group-departure-datetime">
                                        <div class="input-date-departure">
                                                                                         <div class="c-form position-relative" ng-show="!vm.data.type">
                                                 <label for="to_datepicker"
                                                     class="p-2 label-title" data-translate="booking_form.return_date">Return date</label>
                                                 <input id="to_datepicker" name="to_datepicker" required readonly
                                                     data-calendar="dropoff" class="form-control"
                                                     ng-model="vm.data.departure_date_only"
                                                     oninput="clearInputError(this)"
                                                     placeholder="Return date" />
                                                 <div ng-click="vm.openCalendar()" class="value form-control">
                                                     {{ vm.data.departure_date_only | asDate | date: "mediumDate" }}
                                                 </div>
                                                 <span ng class="icon icon-calendar"></span>
                                             </div>
                                                                                           <div id="return_date_error" class="field-error-message" data-translate="validation_errors.return_date_required">Lütfen dönüş tarihini seçiniz</div>


                                            <div class="c-form position-relative" ng-show="vm.data.type">
                                                <label for="to_datepicker"
                                                    class="p-2 label-title" data-translate="booking_form.return_date">Return date</label>
                                                <div ng-click="vm.addReturn()" class="add-return pointer" data-translate="booking_form.add_return">
                                                    + Add return
                                                </div>
                                            </div>
                                        </div>

                                        <div class="input-time-departure pointer"
                                            ng-click="vm.data.type && vm.addReturn()">
                                            <div class="c-form position-relative">
                                                <label for="departure_time_picker" class="p-2 label-title" data-translate="booking_form.time">Time</label>
                                                <input ng-show="!vm.data.type" id="departure_time_picker"
                                                    type="text" class="form-control" readonly
                                                    ng-model="vm.data.departure_time_only"
                                                    placeholder="Time" />
                                                <span ng-show="!vm.data.type" class="icon icon-clock"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="input-passengers">
                                        <div id="pax_parent">
                                            <div class="c-form position-relative" id="dropdow-persons"
                                                ng-click="vm.showPassenger($event)">
                                                <div class="p-2 label-title" aria-label="Passengers">Passengers</div>
                                                <div class="value form-control">
                                                    {{ vm.data.adults }} {{ vm.data.adults == 1 ? vm.ln.adult : vm.ln.adults }}
                                                    {{ (vm.data.children + vm.data.infants) > 0 ? ', ' + (vm.data.children + vm.data.infants) + ' ' + vm.ln.minors : '' }}
                                                </div>
                                                <span class="icon icon-people"></span>
                                            </div>
                                            <!-- Dropdown de pasajeros -->
                                            <div class="dropdown-menu" id="pax-view"
                                                aria-labelledby="dropdow-persons">
                                                <div class="dropdown-item py-2">
                                                    <!-- Adultos -->
                                                    <div class="col-12 p-0">
                                                        <div class="row">
                                                            <div class="col-12 p-0">
                                                                <div class="mb-0" aria-label="Adults">Adults</div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="btn-float btn-left border-left border-top border-bottom p-2 float-left pointer"
                                                                ng-click="vm.removePax('adults', 1)"
                                                                ng-class="{'btn-pax-inactive' : vm.data.adults == 1}">
                                                                <span class="icon-less color-secundary"></span>
                                                            </div>
                                                            <div
                                                                class="btn-float border-top border-bottom p-2 float-left font-14 text-dark">
                                                                <span>{{ vm.data.adults }}</span>
                                                            </div>
                                                            <div class="btn-float btn-right border-top border-bottom border-right p-2 float-left pointer"
                                                                ng-click="vm.addPax('adults')">
                                                                <span class="icon-more color-secundary"></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Niños -->
                                                    <div class="col-12 p-0 mt-2">
                                                        <div class="row">
                                                            <div class="col-12 p-0">
                                                                <div class="mb-0" aria-label="Children">Children</div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="btn-float btn-left border-left border-top border-bottom p-2 float-left pointer"
                                                                ng-click="vm.removePax('children', 0)"
                                                                ng-class="{'btn-pax-inactive' : vm.data.children == 0}">
                                                                <span class="icon-less color-secundary"></span>
                                                            </div>
                                                            <div
                                                                class="btn-float border-top border-bottom p-2 float-left font-14 text-dark">
                                                                <span>{{ vm.data.children }}</span>
                                                            </div>
                                                            <div class="btn-float btn-right border-top border-bottom border-right p-2 float-left pointer"
                                                                ng-click="vm.addPax('children')">
                                                                <span class="icon-more color-secundary"></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Infantes -->
                                                    <div class="col-12 p-0 mt-2">
                                                        <div class="row">
                                                            <div class="col-12 p-0">
                                                                <div class="mb-0" aria-label="Infants">Infants</div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="btn-float btn-left border-left border-top border-bottom p-2 float-left pointer"
                                                                ng-click="vm.removePax('infants', 0)"
                                                                ng-class="{'btn-pax-inactive' : vm.data.infants == 0}">
                                                                <span class="icon-less color-secundary"></span>
                                                            </div>
                                                            <div
                                                                class="btn-float border-top border-bottom p-2 float-left font-14 text-dark">
                                                                <span>{{ vm.data.infants }}</span>
                                                            </div>
                                                            <div class="btn-float btn-right border-top border-bottom border-right p-2 float-left pointer"
                                                                ng-click="vm.addPax('infants')">
                                                                <span class="icon-more color-secundary"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="button-close-passengers"
                                                    ng-click="vm.showPassenger($event)">
                                                    Apply
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                                                                                                                                   <div class="button">
                                          <button class="f-bold font-14"
                                              type="submit" data-translate="booking_form.search" onclick="submitSearchForm(event)">Search</button>
                                      </div>

                                    <input type="hidden" name="type" ng-model="vm.data.type">
                                    <input type="hidden" required name="from" ng-model="vm.data.from">
                                    <input type="hidden" required name="to" ng-model="vm.data.to">
                                    <input type="hidden" required name="adults" ng-model="vm.data.adults">
                                    <input type="hidden" name="children" ng-model="vm.data.children">
                                    <input type="hidden" name="infants" ng-model="vm.data.infants">
                                    <input type="hidden" required name="arrival_date"
                                        ng-model="vm.data.arrival_date">
                                    <input type="hidden" ng-required="!vm.data.type" name="departure_date"
                                        ng-model="vm.data.departure_date">
                                                                         <input type="hidden" name="transfer_type" ng-value="vm.data.type ? 'oneway' : 'roundtrip'">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div id="map_search" style="display:none;"></div>
    </div>
    <div class="loading" ng-show="vm.loading">
    <div class="loading-mgs shadow">
        <div class="loader"></div>
        <div class="loading-text"> We are searching for the best transfer options for you...</div>
        <div class="loading-wait"> Please wait</div>
    </div>
</div></div>

 <script>
   function submitSearchForm(event) {
      event.preventDefault();
      
      // Tüm hata mesajlarını temizle
      clearAllErrors();
      
      console.log('submitSearchForm başladı');
      console.log('Angular var mı:', typeof angular !== 'undefined');
      console.log('angular.element var mı:', typeof angular !== 'undefined' && angular.element);
      
      // AngularJS'in yüklenmesini bekle
      function waitForAngular() {
          console.log('waitForAngular çalışıyor...');
          if (typeof angular !== 'undefined' && angular.element) {
                           try {
                  // AngularJS scope'dan verileri al
                  console.log('Tüm ng-controller elementleri:');
                  var allControllers = document.querySelectorAll('[ng-controller]');
                  allControllers.forEach(function(el, index) {
                      console.log('Controller ' + index + ':', el.getAttribute('ng-controller'));
                  });
                  
                  var controllerElement = document.querySelector('[ng-controller="searchController"]');
                  console.log('searchController element:', controllerElement);
                  
                  if (!controllerElement) {
                      console.log('searchController bulunamadı, tüm Angular elementlerini kontrol ediyorum...');
                      
                      // Tüm Angular elementlerini kontrol et
                      var allAngularElements = document.querySelectorAll('[ng-app], [ng-controller], [ng-model]');
                      console.log('Toplam Angular element sayısı:', allAngularElements.length);
                      
                      // Alternatif olarak, form elementini bul ve onun parent'larını kontrol et
                      var form = document.querySelector('form[action="/mytransfers/search.php"]');
                      if (form) {
                          console.log('Form bulundu, parent elementleri kontrol ediyorum...');
                          var parent = form.parentElement;
                          var level = 0;
                          while (parent && level < 10) {
                              console.log('Parent level ' + level + ':', parent);
                              if (parent.getAttribute && parent.getAttribute('ng-controller')) {
                                  console.log('Controller bulundu:', parent.getAttribute('ng-controller'));
                                  controllerElement = parent;
                                  break;
                              }
                              parent = parent.parentElement;
                              level++;
                          }
                      }
                      
                      if (!controllerElement) {
                          console.log('Controller hala bulunamadı, tekrar deneniyor...');
                          setTimeout(waitForAngular, 200);
                          return;
                      }
                  }
                 
                 var scope = angular.element(controllerElement).scope();
                 console.log('Scope:', scope);
                 
                 if (!scope || !scope.vm) {
                     console.log('Scope veya vm bulunamadı, tekrar deneniyor...');
                     setTimeout(waitForAngular, 200);
                     return;
                 }
                 
                 var vm = scope.vm;
                 console.log('VM data:', vm.data);
                 
                 // Form validasyonu
                 var errors = [];
                 var errorFields = [];
                 
                 console.log('Form validasyonu başlıyor...');
                 
                                   // From alanı kontrolü
                  var fromValue = vm.data.from || vm.data.from_view || '';
                  console.log('From value:', fromValue);
                  if (!fromValue.trim()) {
                      console.log('From alanı boş!');
                      var fromError = window.__mt && window.__mt.validation_errors ? window.__mt.validation_errors.from_required : 'Lütfen kalkış noktasını seçiniz';
                      errors.push(fromError);
                      errorFields.push('from_view');
                  }
                  
                  // To alanı kontrolü
                  var toValue = vm.data.to || vm.data.to_view || '';
                  console.log('To value:', toValue);
                  if (!toValue.trim()) {
                      console.log('To alanı boş!');
                      var toError = window.__mt && window.__mt.validation_errors ? window.__mt.validation_errors.to_required : 'Lütfen varış noktasını seçiniz';
                      errors.push(toError);
                      errorFields.push('to_view');
                  }
                  
                  // Tarih kontrolü
                  var arrivalDate = vm.data.arrival_date || vm.data.arrival_date_only || '';
                  console.log('Arrival date:', arrivalDate);
                  if (!arrivalDate) {
                      console.log('Tarih alanı boş!');
                      var dateError = window.__mt && window.__mt.validation_errors ? window.__mt.validation_errors.pickup_date_required : 'Lütfen kalkış tarihini seçiniz';
                      errors.push(dateError);
                      errorFields.push('from_datepicker');
                  }
                  
                  // Round-trip için return date kontrolü
                  if (!vm.data.type && !vm.data.departure_date && !vm.data.departure_date_only) {
                      console.log('Return date alanı boş!');
                      var returnDateError = window.__mt && window.__mt.validation_errors ? window.__mt.validation_errors.return_date_required : 'Lütfen dönüş tarihini seçiniz';
                      errors.push(returnDateError);
                      errorFields.push('to_datepicker');
                  }
                 
                 console.log('Toplam hata sayısı:', errors.length);
                 console.log('Hatalar:', errors);
                 
                                   // Hata varsa kullanıcıya göster ve işlemi durdur
                  if (errors.length > 0) {
                      // Tüm hata mesajlarını temizle
                      clearAllFieldErrors();
                      
                      // Hata alanlarını işaretle ve hata mesajlarını göster
                      errorFields.forEach(function(fieldId) {
                          var field = document.getElementById(fieldId);
                          if (field) {
                              field.classList.add('input-error');
                              // Parent container'ı da işaretle
                              var parentContainer = field.closest('.input-pick-up, .input-drop-off, .input-date-arrival, .input-date-departure');
                              if (parentContainer) {
                                  parentContainer.classList.add('input-error');
                              }
                          }
                          
                          // Hata mesajını göster
                          showFieldError(fieldId);
                      });
                      
                      return;
                  }
                 
                 // Form verilerini hazırla
                 var formData = {
                     from: fromValue,
                     to: toValue,
                     adults: vm.data.adults || 2,
                     children: vm.data.children || 0,
                     infants: vm.data.infants || 0,
                     transfer_type: vm.data.type ? 'oneway' : 'roundtrip',
                     arrival_date: arrivalDate,
                     departure_date: vm.data.departure_date || vm.data.departure_date_only || ''
                 };
                 
                 // URL parametrelerini oluştur
                 var params = new URLSearchParams();
                 for (var key in formData) {
                     if (formData[key]) {
                         params.append(key, formData[key]);
                     }
                 }
                 
                 // Search sayfasına yönlendir
                 window.location.href = '/mytransfers/search.php?' + params.toString();
                 
                           } catch (error) {
                  console.error('submitSearchForm hatası:', error);
                  var generalError = window.__mt && window.__mt.validation_errors ? window.__mt.validation_errors.general_error : 'Bir hata oluştu. Lütfen tekrar deneyiniz.';
                  showValidationError(generalError);
              }
         } else {
             // AngularJS henüz yüklenmemiş, 200ms sonra tekrar dene
             setTimeout(waitForAngular, 200);
         }
     }
     
     // AngularJS'i bekle ve çalıştır
     waitForAngular();
 }
 
   // Input hata mesajlarını temizle
  function clearInputError(input) {
      if (input && input.classList) {
          input.classList.remove('input-error');
          // Parent container'dan da hata class'ını kaldır
          var parentContainer = input.closest('.input-pick-up, .input-drop-off, .input-date-arrival, .input-date-departure');
          if (parentContainer) {
              parentContainer.classList.remove('input-error');
          }
          
          // İlgili hata mesajını da gizle
          var fieldId = input.id;
          hideFieldError(fieldId);
      }
  }
  
  // Alan hata mesajını göster
  function showFieldError(fieldId) {
      var errorMap = {
          'from_view': 'from_error',
          'to_view': 'to_error',
          'from_datepicker': 'date_error',
          'to_datepicker': 'return_date_error'
      };
      
      var errorElementId = errorMap[fieldId];
      if (errorElementId) {
          var errorElement = document.getElementById(errorElementId);
          if (errorElement) {
              errorElement.classList.add('show');
          }
      }
  }
  
  // Alan hata mesajını gizle
  function hideFieldError(fieldId) {
      var errorMap = {
          'from_view': 'from_error',
          'to_view': 'to_error',
          'from_datepicker': 'date_error',
          'to_datepicker': 'return_date_error'
      };
      
      var errorElementId = errorMap[fieldId];
      if (errorElementId) {
          var errorElement = document.getElementById(errorElementId);
          if (errorElement) {
              errorElement.classList.remove('show');
          }
      }
  }
  
  // Tüm alan hata mesajlarını temizle
  function clearAllFieldErrors() {
      var errorElements = document.querySelectorAll('.field-error-message');
      errorElements.forEach(function(element) {
          element.classList.remove('show');
      });
  }
 
   // Tüm hata mesajlarını temizle
  function clearAllErrors() {
      // Tüm input-error class'larını kaldır
      var errorElements = document.querySelectorAll('.input-error');
      errorElements.forEach(function(element) {
          element.classList.remove('input-error');
      });
      
      // Tüm alan hata mesajlarını temizle
      clearAllFieldErrors();
      
      // Validation error mesajını kaldır
      var errorDiv = document.getElementById('validation-error');
      if (errorDiv) {
          errorDiv.remove();
      }
  }
 
 // Validation hata mesajını göster
 function showValidationError(message) {
     // Önceki hata mesajını kaldır
     clearAllErrors();
     
     // Debug için console'a yazdır
     console.log('Hata mesajı gösteriliyor:', message);
     
     // Hata mesajı div'i oluştur
     var errorDiv = document.createElement('div');
     errorDiv.id = 'validation-error';
     errorDiv.className = 'validation-error-message';
     errorDiv.style.cssText = 'background: #dc3545; color: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #c82333; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 9999; position: relative;';
     errorDiv.innerHTML = '<div style="display: flex; align-items: center; font-weight: 500;"><span style="margin-right: 10px; font-size: 18px;">⚠️</span>' + message + '</div>';
     
     // Form'un üstüne ekle
     var form = document.querySelector('form[action="/mytransfers/search.php"]');
     if (form) {
         form.parentNode.insertBefore(errorDiv, form);
         console.log('Hata mesajı eklendi');
         
         // 10 saniye sonra otomatik kaldır (daha uzun süre)
         setTimeout(function() {
             if (errorDiv.parentNode) {
                 errorDiv.remove();
                 console.log('Hata mesajı kaldırıldı');
             }
         }, 10000);
     } else {
         console.error('Form bulunamadı');
         // Fallback: body'nin başına ekle
         document.body.insertBefore(errorDiv, document.body.firstChild);
     }
 }
 
 
 </script>

                            </div>
        </div>
    </section>
