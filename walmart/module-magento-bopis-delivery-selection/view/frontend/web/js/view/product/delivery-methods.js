/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'Walmart_BopisDeliverySelection/js/view/delivery-methods',
    'ko',
    'deliveryMethods',
    'bopisLocation'
], function (Component, ko, deliveryMethods, bopisLocation) {
    'use strict';

    return Component.extend({
        initObservable: function () {
            this._super();

            this.isOutOfStock = ko.computed(function () {
                return bopisLocation.selectedLocationStock() && bopisLocation.selectedLocationStock()['status']['code'] == 'out_of_stock';
            }.bind(this));

            this.isAddToCartEnabled = ko.computed(function () {
                return deliveryMethods.getSelectedMethod() == 'home' && deliveryMethods.methodsConfig['home'] ||
                    deliveryMethods.getSelectedMethod() == 'instore_pickup' && deliveryMethods.methodsConfig['instore'] && this.selectedLocation() && !this.isOutOfStock();
            }.bind(this));

            return this;
        }
    });
});
