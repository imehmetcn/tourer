// MyTransfers Search Override
// Bu dosya Google Maps API çağrılarını engeller ve kendi API'mizi kullanır

(function() {
    'use strict';
    
    // Wait for page to load
    document.addEventListener('DOMContentLoaded', function() {
        initSearchOverride();
    });
    
    function initSearchOverride() {
        // Immediately block Google API calls
        blockGoogleAPICalls();
        
        // Wait for Angular to initialize
        setTimeout(function() {
            overrideAngularSearchFunctions();
            hideDropdownsOnLoad();
        }, 1000);
        
        // Don't hide dropdowns after delay - let Angular control them
    }
    
    function blockGoogleAPICalls() {
        // Override Google Maps API loading to prevent errors
        if (window.google && window.google.maps) {
            // If already loaded, disable problematic services
            if (window.google.maps.places) {
                // Override AutocompleteService to use our API
                window.google.maps.places.AutocompleteService = function() {
                    return {
                        getPlacePredictions: function(request, callback) {
                            // Use our API instead
                            useOurAPIForPredictions(request.input, callback);
                        }
                    };
                };
                
                // Override PlacesService
                window.google.maps.places.PlacesService = function() {
                    return {
                        getDetails: function(request, callback) {
                            // Use our API for place details
                            useOurAPIForPlaceDetails(request.placeId, callback);
                        }
                    };
                };
            }
        }
        
        // Override fetch to intercept Google API calls
        const originalFetch = window.fetch;
        window.fetch = function(url, options) {
            // Block Google Maps API calls
            if (typeof url === 'string' && (
                url.includes('maps.googleapis.com') ||
                url.includes('google.com/maps')
            )) {
                // Blocked Google API call
                
                // Return empty response to prevent errors
                return Promise.resolve(new Response(JSON.stringify({
                    predictions: [],
                    status: 'ZERO_RESULTS'
                }), {
                    status: 200,
                    statusText: 'OK',
                    headers: { 'Content-Type': 'application/json' }
                }));
            }
            
            // For all other requests, use original fetch
            return originalFetch.apply(this, arguments);
        };
        
        // Override XMLHttpRequest for older API calls
        const originalXHR = window.XMLHttpRequest;
        window.XMLHttpRequest = function() {
            const xhr = new originalXHR();
            const originalOpen = xhr.open;
            
            xhr.open = function(method, url, ...args) {
                if (typeof url === 'string' && (
                    url.includes('maps.googleapis.com') ||
                    url.includes('google.com/maps')
                )) {
                    // Blocked XHR Google API call
                    // Don't actually open the request
                    return;
                }
                return originalOpen.apply(this, [method, url, ...args]);
            };
            
            return xhr;
        };
    }
    
    async function useOurAPIForPredictions(input, callback) {
        try {
            const response = await fetch(`${window.__mt.setting.api_predictions}?q=${encodeURIComponent(input)}`);
            const data = await response.json();
            
            // Transform our API response to Google format
            const predictions = data.map(item => ({
                description: item.description,
                place_id: item.place_id,
                structured_formatting: {
                    main_text: item.description.split(',')[0],
                    secondary_text: item.description.includes(',') ? 
                        item.description.split(',').slice(1).join(',').trim() : ''
                },
                types: item.description.toLowerCase().includes('airport') ? ['airport'] : ['establishment']
            }));
            
            // Call the callback with Google-formatted data
            callback(predictions, 'OK');
            
        } catch (error) {
            // API error occurred
            callback([], 'ERROR');
        }
    }
    
    async function useOurAPIForPlaceDetails(placeId, callback) {
        try {
            const response = await fetch(`${window.__mt.setting.api_prediction_coords}?place_id=${encodeURIComponent(placeId)}`);
            const data = await response.json();
            
            // Transform to Google format
            const result = {
                place_id: data.place_id,
                geometry: {
                    location: {
                        lat: () => data.lat,
                        lng: () => data.lng
                    }
                }
            };
            
            callback(result, 'OK');
            
        } catch (error) {
            // Place details error occurred
            callback(null, 'ERROR');
        }
    }
    
    function overrideAngularSearchFunctions() {
        // Starting Angular override
        
        if (window.angular) {
            // Try multiple times to catch Angular initialization
            let attempts = 0;
            const maxAttempts = 10;
            
            const tryOverride = () => {
                attempts++;
                // Override attempt
                
                const fromInput = document.getElementById('from_view');
                
                if (fromInput && window.angular.element) {
                    try {
                        const scope = window.angular.element(fromInput).scope();
                        if (scope && scope.vm) {
                            // Found Angular scope, overriding functions
                            
                            // Initially hide all dropdowns
                            scope.vm.showfrom = false;
                            scope.vm.showto = false;
                            scope.vm.results = [];
                            scope.vm.results_gm = [];
                            scope.vm.results_to = [];
                            scope.vm.results_gm_to = [];
                            
                            // Override searchInPlaces function
                            scope.vm.searchInPlaces = function(showType, suffix) {
                                // searchInPlaces called
                                
                                const fieldType = suffix === '_to' ? 'to' : 'from';
                                const query = fieldType === 'from' ? scope.vm.data.from_view : scope.vm.data.to_view;
                                
                                if (query && query.length >= 2) {
                                    performAngularSearch(query, scope.vm, showType, fieldType);
                                } else {
                                    // Query too short or empty
                                }
                            };
                            
                            // Override the main search function
                            scope.vm.search = function(showType, query, fieldType) {
                                // Angular search called
                                
                                if (query && query.length >= 2) {
                                    performAngularSearch(query, scope.vm, showType, fieldType);
                                } else {
                                    // Clearing results
                                    // Clear results and hide dropdown
                                    if (fieldType === 'from') {
                                        scope.vm.results = [];
                                        scope.vm.results_gm = [];
                                        scope.vm.showfrom = false;
                                    } else {
                                        scope.vm.results_to = [];
                                        scope.vm.results_gm_to = [];
                                        scope.vm.showto = false;
                                    }
                                    
                                    // Safe apply - check if digest is already in progress
                                    if (!scope.$$phase && !scope.$root.$$phase) {
                                        try {
                                            scope.$apply();
                                        } catch (e) {
                                            // Scope apply error (expected)
                                        }
                                    }
                                }
                            };
                            
                            // Override click handlers to prevent auto-opening
                            scope.vm.deleteItemSelected = function(viewField, searchField, fieldType) {
                                // deleteItemSelected called
                                
                                // Clear the input but don't show dropdown
                                if (fieldType === 'from') {
                                    scope.vm.data.from_view = '';
                                    scope.vm.data.from = null;
                                    scope.vm.showfrom = false;
                                    scope.vm.results = [];
                                    scope.vm.results_gm = [];
                                } else {
                                    scope.vm.data.to_view = '';
                                    scope.vm.data.to = null;
                                    scope.vm.showto = false;
                                    scope.vm.results_to = [];
                                    scope.vm.results_gm_to = [];
                                }
                                
                                // Use $timeout to avoid $apply conflicts
                                if (scope.$timeout) {
                                    scope.$timeout(function() {
                                        // Changes will be applied automatically
                                    }, 0);
                                } else {
                                    // Fallback: use setTimeout
                                    setTimeout(function() {
                                        if (!scope.$$phase && !scope.$root.$$phase) {
                                            try {
                                                scope.$apply();
                                            } catch (e) {
                                                // Scope apply error (expected)
                                            }
                                        }
                                    }, 0);
                                }
                            };
                            
                            // Add input event listeners to trigger search
                            setupInputListeners(scope.vm);
                            
                            // Angular functions overridden successfully
                            return true; // Success
                        }
                    } catch (error) {
                        // Error getting Angular scope
                    }
                }
                
                if (attempts < maxAttempts) {
                    setTimeout(tryOverride, 500);
                } else {
                    // Failed to override Angular after max attempts
                }
            };
            
            tryOverride();
        } else {
            // Angular not found
        }
    }
    
    async function performAngularSearch(query, vm, showType, fieldType) {
        try {
            vm.loading_search = true;
            vm.search_in_places = false;
            vm.search_in_places_to = false;
            
            // Clear previous results
            if (fieldType === 'from') {
                vm.results = [];
                vm.results_gm = [];
            } else {
                vm.results_to = [];
                vm.results_gm_to = [];
            }
            
            // Call our API
            const response = await fetch(`${window.__mt.setting.api_predictions}?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            // Transform data for Angular
            const transformedResults = data.map(item => ({
                description: item.description,
                place_id: item.place_id,
                structured_formatting: {
                    main_text: item.description.split(',')[0],
                    secondary_text: item.description.includes(',') ? 
                        item.description.split(',').slice(1).join(',').trim() : ''
                },
                types: item.description.toLowerCase().includes('airport') ? ['airport'] : ['establishment']
            }));
            
            // Set results in Angular scope
            if (fieldType === 'from') {
                vm.results_gm = transformedResults;
                vm.showfrom = true;
                
                // Also manually show the dropdown
                setTimeout(() => {
                    const dropdown = document.querySelector('#list_search_from').closest('.search.shadow');
                    if (dropdown) {
                        dropdown.style.display = 'block';
                        dropdown.style.visibility = 'visible';
                    }
                }, 50);
            } else {
                vm.results_gm_to = transformedResults;
                vm.showto = true;
                
                // Also manually show the dropdown
                setTimeout(() => {
                    const dropdown = document.querySelector('#list_search_to').closest('.search.shadow');
                    if (dropdown) {
                        dropdown.style.display = 'block';
                        dropdown.style.visibility = 'visible';
                    }
                }, 50);
            }
            
            vm.loading_search = false;
            
            // Search completed
            
            // Apply changes to scope safely
            const applyScope = vm.$scope || (() => {
                const input = document.getElementById(fieldType === 'from' ? 'from_view' : 'to_view');
                return input ? window.angular.element(input).scope() : null;
            })();
            
            if (applyScope && !applyScope.$$phase && !applyScope.$root.$$phase) {
                try {
                    applyScope.$apply();
                } catch (e) {
                    // Scope apply error (expected)
                }
            }
            
        } catch (error) {
            // Search error occurred
            vm.loading_search = false;
            
            // Try to apply scope changes safely
            const applyScope = vm.$scope || (() => {
                const input = document.getElementById(fieldType === 'from' ? 'from_view' : 'to_view');
                return input ? window.angular.element(input).scope() : null;
            })();
            
            if (applyScope && !applyScope.$$phase && !applyScope.$root.$$phase) {
                try {
                    applyScope.$apply();
                } catch (applyError) {
                    // Scope apply error
                }
            }
        }
    }
    
    function setupInputListeners(vm) {
        // Setting up input listeners
        
        const fromInput = document.getElementById('from_view');
        const toInput = document.getElementById('to_view');
        
        if (fromInput) {
            // Remove existing listeners
            fromInput.removeEventListener('input', fromInput._customInputHandler);
            
            // Add new input handler
            fromInput._customInputHandler = function(e) {
                const query = e.target.value.trim();
                // From input changed
                
                if (query.length >= 2) {
                    // Trigger search
                    vm.search('showfrom', query, 'from');
                } else {
                    // Clear results
                    vm.showfrom = false;
                    vm.results = [];
                    vm.results_gm = [];
                    
                    // Safe apply
                    const scope = window.angular.element(fromInput).scope();
                    if (scope && !scope.$$phase && !scope.$root.$$phase) {
                        try {
                            scope.$apply();
                        } catch (e) {
                            // Scope apply error (expected)
                        }
                    }
                }
            };
            
            fromInput.addEventListener('input', fromInput._customInputHandler);
        }
        
        if (toInput) {
            // Remove existing listeners
            toInput.removeEventListener('input', toInput._customInputHandler);
            
            // Add new input handler
            toInput._customInputHandler = function(e) {
                const query = e.target.value.trim();
                // To input changed
                
                if (query.length >= 2) {
                    // Trigger search
                    vm.search('showto', query, 'to');
                } else {
                    // Clear results
                    vm.showto = false;
                    vm.results_to = [];
                    vm.results_gm_to = [];
                    
                    // Safe apply
                    const scope = window.angular.element(toInput).scope();
                    if (scope && !scope.$$phase && !scope.$root.$$phase) {
                        try {
                            scope.$apply();
                        } catch (e) {
                            // Scope apply error (expected)
                        }
                    }
                }
            };
            
            toInput.addEventListener('input', toInput._customInputHandler);
        }
    }
    
    function hideDropdownsOnLoad() {
        // Hiding dropdowns on load
        
        // Hide dropdowns using CSS
        const dropdowns = document.querySelectorAll('.search.shadow, .content-search .search');
        dropdowns.forEach(dropdown => {
            dropdown.style.display = 'none';
        });
        
        // Also try to set Angular scope variables
        const fromInput = document.getElementById('from_view');
        if (fromInput && window.angular && window.angular.element) {
            try {
                const scope = window.angular.element(fromInput).scope();
                if (scope && scope.vm) {
                    scope.vm.showfrom = false;
                    scope.vm.showto = false;
                    scope.vm.results = [];
                    scope.vm.results_gm = [];
                    scope.vm.results_to = [];
                    scope.vm.results_gm_to = [];
                    
                    // Safe apply
                    if (!scope.$$phase && !scope.$root.$$phase) {
                        try {
                            scope.$apply();
                        } catch (e) {
                            // Ignore apply errors
                        }
                    }
                }
            } catch (error) {
                // Error hiding dropdowns
            }
        }
    }
    
    // Prevent Google Maps script loading errors
    window.addEventListener('error', function(e) {
        if (e.message && (
            e.message.includes('google') ||
            e.message.includes('maps') ||
            e.filename && e.filename.includes('maps.googleapis.com')
        )) {
            // Suppressed Google Maps error
            e.preventDefault();
            return false;
        }
    });
    
    // Add CSS to ensure dropdowns work properly
    const style = document.createElement('style');
    style.textContent = `
        /* Let Angular control dropdown visibility naturally */
        .search.shadow {
            visibility: visible !important;
        }
        
        .content-search .search {
            visibility: visible !important;
        }
        
        /* Ensure proper z-index */
        .content-search {
            position: relative;
            z-index: 1000;
        }
    `;
    document.head.appendChild(style);
    
})();