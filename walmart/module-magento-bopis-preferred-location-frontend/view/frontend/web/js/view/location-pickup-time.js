/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'uiComponent',
    'ko',
    'bopisLocation'
], function (Component, ko, bopisLocation) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Walmart_BopisPreferredLocationFrontend/text-block',
            defaultText: false
        },

        initObservable: function () {
            this._super();

            this.text = ko.computed(function () {
                return bopisLocation.selectedLocation()['estimated_pickup_time'] || this.defaultText;
            }.bind(this))

            return this;
        }
    });
});
