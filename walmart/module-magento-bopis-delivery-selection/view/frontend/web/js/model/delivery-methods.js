/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'jquery',
    'knockout',
    'mage/storage',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/checkout-data',
    'bopisLocation'
], function ($, ko, storage, customerData, checkoutData, bopisLocation) {
    'use strict';

    return {
        selectedMethod: ko.computed(function (){
            let shippingRate = checkoutData.getSelectedShippingRate();

            if (shippingRate) {
                if(shippingRate !== 'instore_pickup'){
                    return "home";
                }
                return shippingRate;
            }

            return customerData.get('bopis')()['preferred_method'] || false;
        }),
        saveUrl: 'wct-fulfillment/deliverymethod/save',
        methodsConfig: {
            home: false,
            instore: false
        },

        setPreferredMethod: function (method, shippingMethod, location = null) {
            $('body').trigger('processStart');
            storage.post(
                this.saveUrl, {
                    method: method,
                    shipping_method: shippingMethod ? shippingMethod['carrier_code'] + '_' + shippingMethod['method_code'] : null,
                    location_code: location,
                    is_cart_page: window.location.href.indexOf('checkout/cart') > -1 ? 1 : 0
                },
                true,
                'application/x-www-form-urlencoded; charset=UTF-8'
            )
            .done(function (response) {
                checkoutData.setSelectedShippingRate(method);
                let pickupAddressQuote = bopisLocation.getSelectedLocationFromQuote();

                if (pickupAddressQuote && !bopisLocation.selectedLocation()) {
                    let extensionAttribute = pickupAddressQuote['extension_attributes'] || pickupAddressQuote['extensionAttributes'];
                    bopisLocation.setLocation(extensionAttribute['pickup_location_code']);
                }
                $('body').trigger('processStop');

                // reload page to apply stock validation per delivery method
                if (response.reload) {
                    // require totals only on the cart page to avoid dependency issues
                    require(['Magento_Checkout/js/model/totals'], function(totalsService){
                        if (!totalsService.isLoading()) {
                            window.location.reload();
                            return;
                        }

                        // reload the page after totals reload
                        totalsService.isLoading.subscribe(function (isLoading) {
                            if (!isLoading) {
                                window.location.reload();
                            }
                        });
                    });
                }
            }.bind(this));
        },

        getSelectedMethod: function () {

            let shippingRate = checkoutData.getSelectedShippingRate();

            if (shippingRate) {
                if(shippingRate !== 'instore_pickup'){
                    return "home";
                }
                return shippingRate;
            }

            return customerData.get('bopis')()['preferred_method'] || false;
        },

        hasItemsInCart: function () {
            return customerData.get('cart')()['items'] && customerData.get('cart')()['items'].length || false;
        }
    };
});
