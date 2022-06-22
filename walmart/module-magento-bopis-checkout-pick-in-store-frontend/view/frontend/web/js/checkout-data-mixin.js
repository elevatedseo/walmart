/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Customer/js/customer-data'
], function (
    storage
) {
    'use strict';

    return function (checkoutData) {

        if(checkoutData.setSelectedPickupAddress !== undefined && checkoutData.getSelectedPickupAddress !== undefined)
            return checkoutData;

        var cacheKey = 'checkout-data',

            /**
             * @param {Object} data
             */
            saveData = function (data) {
                storage.set(cacheKey, data);
            },

            /**
             * @return {Object}
             */
            getData = function () {
                //Makes sure that checkout storage is initiated (any method can be used)
                checkoutData.getSelectedShippingAddress();

                return storage.get(cacheKey)();
            };

        /**
         * Get the pickup address from persistence storage
         *
         * @return {*}
         */
        checkoutData.getSelectedPickupAddress = function () {
            return getData().selectedPickupAddress || null;
        };

        return checkoutData;
    };
});
