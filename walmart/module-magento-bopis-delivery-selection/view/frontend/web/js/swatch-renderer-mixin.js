/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
define([
    'jquery',
    'knockout',
    'bopisLocation',
    'deliveryMethods',
    'bopisUrlBuilder',
    'mage/utils/wrapper'
], function ($, ko, bopisLocation, deliveryMethods, urlBuilder, wrapper) {
    'use strict';

    return function (Widget) {
        $.widget('mage.SwatchRenderer', Widget, {
            options: {
                checkInStoreAvailabilityUrl: '/wct-fulfillment/inventory/product-availability/store-pickup',
                checkHomeDeliveryAvailabilityUrl: '/wct-fulfillment/inventory/product-availability/home-delivery',
            },

            _init: function () {
                // Keep text copy of the default value for rebuild purpose
                this.defaultAttributes = JSON.stringify(this.options.jsonConfig.attributes);

                this._super();

                this.instocks = [];

                if (!this.inProductList) {
                    ko.computed(function() {
                        bopisLocation.selectedLocation();
                        deliveryMethods.selectedMethod();
                        this._updateStock();
                    }.bind(this));
                }
            },

            _updateStock: function () {

                const method = deliveryMethods.selectedMethod();
                const location = bopisLocation.selectedLocation();

                let url = '';
                let body = {
                    skus: _.map(this.options.jsonConfig.idToSkuMap, x => x),
                };

                /**
                 * Don't proceed if method is not set, or if location is not sent when method is set to instore_pickup
                 */
                if (!method || (method == "instore_pickup" && !location)) {
                    return;
                }

                if (method === 'instore_pickup') {
                    if(bopisLocation.selectedLocation())
                        url = this.options.checkInStoreAvailabilityUrl;
                    body.sources = [location['pickup_location_code']];
                } else {
                    url = this.options.checkHomeDeliveryAvailabilityUrl;
                }

                $.ajax({
                    url: urlBuilder.createUrl(url, this.options.jsonConfig.storeCode),
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(body),
                    cache: false,
                    success: function (response) {

                        this.instock = _.reduce(this.options.jsonConfig.idToSkuMap, (result, current, idx) => {
                            if(response[0][current]) {
                                result.push(idx);
                            }
                            return result
                        }, []);
                        this._Rebuild();
                    }.bind(this)
                });
            },

            _CalcProducts: wrapper.wrapSuper(Widget._CalcProducts, function($skipAttributeId) {
                return _.intersection(this._super($skipAttributeId), this.instock);
            })
        });

        return $.mage.SwatchRenderer;
    }
});
