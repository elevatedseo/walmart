/**
 * Original work
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Modified work
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */

define([
    'Magento_Checkout/js/model/quote',
    'bopisLocation',
    'Walmart_BopisCheckoutPickInStoreFrontend/js/view/pickup-options'
], function (quote, bopisLocation, pickupOptions) {
    'use strict';

    var storePickupShippingInformation = {
        defaults: {
            template: 'Magento_InventoryInStorePickupFrontend/shipping-information',
            pickupOptionsList:  bopisLocation.selectedLocation()['pickup_options']
        },

        /**
         * Get shipping method title based on delivery method.
         *
         * @return {String}
         */
        getShippingMethodTitle: function () {
            const shippingMethod = quote.shippingMethod();
            let locationName = '',
                title = '';

            if (!this.isStorePickup()) {
                return this._super();
            }

            if (this.pickupOptionsList && pickupOptions().selectedOption()) {
                var pickupOption = this.pickupOptionsList.find(element => element['code'] == pickupOptions().selectedOption());

                if (pickupOption['title'] && quote.shippingAddress().firstname !== undefined) {
                    return pickupOption['title'] + ' - ' + quote.shippingAddress().firstname;
                }
            }

            title = shippingMethod['carrier_title'] + ' - ' + shippingMethod['method_title'];

            if (quote.shippingAddress().firstname !== undefined) {
                locationName = quote.shippingAddress().firstname + ' ' + quote.shippingAddress().lastname;
                title += ' "' + locationName + '"';
            }

            return title;
        },

        /**
         * Get is store pickup delivery method selected.
         *
         * @returns {Boolean}
         */
        isStorePickup: function () {
            const shippingMethod = quote.shippingMethod();
            let isStorePickup = false;

            if (shippingMethod !== null) {
                isStorePickup = shippingMethod['carrier_code'] === 'instore' &&
                    shippingMethod['method_code'] === 'pickup';
            }

            return isStorePickup;
        }
    };

    return function (shippingInformation) {
        return shippingInformation.extend(storePickupShippingInformation);
    };
});
