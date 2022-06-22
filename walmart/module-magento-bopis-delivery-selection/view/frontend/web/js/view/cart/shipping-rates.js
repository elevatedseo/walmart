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
    'ko',
    'Magento_Checkout/js/view/cart/shipping-rates',
    'deliveryMethods'
], function(ko, Component, deliveryMethods){
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();

            if (deliveryMethods.getSelectedMethod() == 'instore_pickup') {
                this.selectShippingMethod({
                    'carrier_code': 'instore',
                    'method_code': 'pickup'
                });
            }
        }
    });
});
