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
            template: 'Walmart_BopisPreferredLocationFrontend/search',
            searchTerm: ko.observable(''),
            websiteCode: 'base'
        },

        /**
         * @override
         */
        initialize: function () {
            this._super();
        },

        filterLocations: function () {
            bopisLocation.getLocations(this.searchTerm(), this.websiteCode);
        }
    });
});
