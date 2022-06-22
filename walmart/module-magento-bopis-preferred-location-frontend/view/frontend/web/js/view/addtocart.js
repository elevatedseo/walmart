/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'uiComponent',
    'ko'
], function (Component, ko) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Walmart_BopisPreferredLocationFrontend/product/view/addtocart',
            isEnabled: ko.observable(false)
        },

        /**
         * @override
         */
        initialize: function () {
            this._super();
        }
    });
});
