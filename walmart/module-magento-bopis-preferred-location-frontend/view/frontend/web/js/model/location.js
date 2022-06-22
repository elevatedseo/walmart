/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'jquery',
    'knockout',
    'mage/storage',
    'Magento_Customer/js/customer-data',
    'bopisUrlBuilder',
    'mage/translate',
    'Magento_InventoryInStorePickupFrontend/js/model/pickup-locations-service',
    'Magento_Checkout/js/checkout-data',
    'bopisBrowserLocationStorage'
], function ($, ko, storage, customerData, urlBuilder, $t, pickuplocationsService, checkoutData, browserLocationStorage) {
    'use strict';

    if(window.checkout === undefined) {
        window.checkout = {};
    }

    return {
        stockInfoEndpoint: '/wct-fulfillment/location/get-inventory-availability',
        locationsEndpoint: '/wct-fulfillment/location/get-locations',
        saveUrl: 'wct-fulfillment/location/save',
        locations: ko.observableArray([]),
        locationsStockInfo: ko.observableArray([]),
        selectedLocationStock: ko.observable(false),
        storeCode: ko.observable('all'),
        requestItems: ko.observableArray([]),
        selectedLocation: ko.computed(function (){
            return customerData.get('bopis')()['preferred_location'] || false;
        }),
        section: ko.computed(function () {
            return customerData.get('bopis');
        }),


        /**
         * Get location list by search term or empty
         */
        getLocations: function (
            searchTerm = '',
            scopeCode = 'base',
            scopeType = 'website',
            sku = ''
        ) {
            var self = this;
            let position = browserLocationStorage.getLocation();
            let lat = position.latitude || null;
            let long = position.longitude || null;

            if (searchTerm) {
                lat = null;
                long = null;
            }

            var serviceUrl = urlBuilder.createUrl(this.locationsEndpoint, this.storeCode()),
                searchRequest = {
                    searchTerm: $.trim(searchTerm),
                    scopeCode: scopeCode,
                    scopeType: scopeType,
                    sku: sku,
                    latitude: lat,
                    longitude: long,
                };

            storage.post(
                serviceUrl, JSON.stringify(searchRequest)
            ).done(function (response){
                self.locations(response);
            }).fail(function (xhr){
                alert($t('Sorry, but something went wrong. Please try again or contact us.'))
            });
        },

        updateStockInfo: function (items = this.requestItems()) {
            var self = this;

            self.getLocations();

            storage.post(urlBuilder.createUrl(this.stockInfoEndpoint, this.storeCode()),
                JSON.stringify({
                    'request': {
                        'items': items
                    }
                })
            ).done(function (result) {
                self.locationsStockInfo(result['source_list']);
                self.selectedLocationStock(self.getSelectedLocationStock());
            });
        },

        setLocation: function (locationCode) {
            $('body').trigger('processStart');

            const location = this.getLocationByCode(locationCode);
            if (location) {
                pickuplocationsService.selectForShipping(location);
            }

            storage.post(this.saveUrl, {location_code: locationCode}, true, 'application/x-www-form-urlencoded; charset=UTF-8')
                .done(function () {
                    // reload page to apply default validation
                    if (window.location.href.indexOf('checkout/cart') > -1) {
                        window.location.reload();
                    } else {
                        $('body').trigger('processStop');
                    }
                });
        },

        getLocationByCode: function (code) {
            return this.locations().find(function (location) {
                return location.pickup_location_code === code
            });
        },

        getStockByCode: function (code) {
            return this.locationsStockInfo().find(function (location) {
                return location.source_code === code
            });
        },

        getSelectedLocationStock: function () {
            return this.getStockByCode(this.selectedLocation()['pickup_location_code']);
        },

        getSelectedLocationFromQuote: function () {
            // Polyfil for 2.4.2
            if (!pickuplocationsService.hasOwnProperty('getSelectedPickupAddress')) {
                if (checkoutData.getShippingAddressFromData() && checkoutData.getSelectedShippingRate() === "instore_pickup") {
                    return checkoutData.getShippingAddressFromData();
                }

                return null;
            }

            return pickuplocationsService.getSelectedPickupAddress();
        },

        getSelectedShippingRate: function () {
            return checkoutData.getSelectedShippingRate();
        }
    };
});
