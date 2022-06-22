/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'uiComponent',
    'ko',
    'deliveryMethods'
], function (Component, ko, deliveryMethods) {
    'use strict';

    return Component.extend({
        defaults: {
            home: false,
            instore: false,
            instoreVisible: false
        },

        initialize: function () {
            this._super();

            deliveryMethods.methodsConfig = {
                home: this.home,
                instore: this.instore,
                instoreVisible: this.instoreVisible
            }
        }
    });
});
