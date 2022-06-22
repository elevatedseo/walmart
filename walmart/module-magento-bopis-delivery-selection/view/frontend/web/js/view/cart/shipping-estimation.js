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
    'knockout',
    'jquery',
    'Magento_Checkout/js/view/cart/shipping-estimation',
    'deliveryMethods',
], function (ko, $, ShippingEstimation, deliveryMethods){
    'use strict';

    return ShippingEstimation.extend({
        defaults: {
            visible: ko.observable(true),
            template: 'Walmart_BopisDeliverySelection/cart/shipping-estimation',
            proceedToCheckoutBtn: '.proceed-to-checkout',
            shippingEstimationBlock: '#block-shipping'
        },

        initialize: function () {
            this._super();

            ko.computed(function () {
                this.visible(deliveryMethods.selectedMethod() != 'instore_pickup');
            }.bind(this));

            // show by default "estimate shipping and tax" block if quote has errors
            if ($(this.proceedToCheckoutBtn).length === 0
                && $(this.shippingEstimationBlock).length
            ) {
                $(this.shippingEstimationBlock).collapsible('activate');
            }
        }
    });
});
