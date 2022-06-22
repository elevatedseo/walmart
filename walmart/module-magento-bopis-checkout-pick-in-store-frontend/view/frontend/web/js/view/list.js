/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'Walmart_BopisPreferredLocationFrontend/js/view/list',
    'ko',
    'bopisLocation',
    'deliveryMethods',
    'Magento_InventoryInStorePickupFrontend/js/model/pickup-locations-service',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/checkout-data-resolver'
], function (
    Component,
    ko,
    bopisLocation,
    deliveryMethods,
    pickupLocationsService,
    quote,
    checkoutData,
    checkoutDataResolver
) {
    'use strict';

    return Component.extend({
        /**
         * @override
         */
        initialize: function () {
            this._super();
            ko.computed(function () {
                if (deliveryMethods.selectedMethod() == 'instore_pickup' && bopisLocation.selectedLocation()) {
                    pickupLocationsService.selectForShipping(bopisLocation.selectedLocation());
                } else {
                    if (!quote.shippingAddress() || (quote.shippingAddress() && quote.shippingAddress().getKey().includes('store-pickup'))) {
                        var nonPickupShippingAddress = checkoutDataResolver.getShippingAddressFromCustomerAddressList();

                        if (nonPickupShippingAddress) {
                            checkoutData.setSelectedShippingAddress(nonPickupShippingAddress.getKey());
                        }
                    }
                }
            }.bind(this));
        }
    });
});
