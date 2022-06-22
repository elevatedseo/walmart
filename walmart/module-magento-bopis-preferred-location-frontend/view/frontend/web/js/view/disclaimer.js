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
            template: 'Walmart_BopisPreferredLocationFrontend/disclaimer',
            message: ko.computed(function () {
                return bopisLocation.selectedLocation()['pickup_time_disclaimer'] || false;
            })
        }
    });
});
