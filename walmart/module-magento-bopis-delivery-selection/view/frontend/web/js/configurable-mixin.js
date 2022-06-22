/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
define([
    'jquery',
    'knockout',
    'bopisLocation',
    'deliveryMethods',
    'mage/url'
], function ($, ko, bopisLocation, deliveryMethods, urlBuilder) {
    'use strict';

    return function (Widget) {
        $.widget('mage.configurable', Widget, {
            options: {
                stockInfoUrl: 'wct-fulfillment/configurable/updateAttributes'
            },

            _init: function () {
                this.defaultAttributes = this.options.spConfig.attributes;

                this._super();
                ko.computed(function() {
                    bopisLocation.selectedLocation();
                    deliveryMethods.selectedMethod();
                    this._updateStock();
                }.bind(this));
            },

            _updateStock: function () {
                if (deliveryMethods.selectedMethod() == 'instore_pickup' && bopisLocation.selectedLocation()) {
                    $.ajax({
                        url: urlBuilder.build(this.options.stockInfoUrl),
                        type: 'POST',
                        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                        data: {
                            productId: this.options.spConfig.productId
                        },
                        cache: false,
                        success: function (response) {
                            this._Rebuild(response.attributes);
                        }.bind(this)
                    })
                } else {
                    this._Rebuild(this.defaultAttributes);
                }
            },

            _Rebuild: function (attributes) {
                this.options.spConfig.attributes = attributes;
                this._overrideDefaults();
                this._setChildSettings();
            }
        });

        return $.mage.configurable;
    }
});
