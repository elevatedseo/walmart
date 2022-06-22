/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'ko',
    'uiComponent',
    'deliveryMethods',
    'bopisLocation'
], function (ko, Component, deliveryMethods, bopisLocation) {
    'use strict';

    return Component.extend({
        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();

            return this;
        },

        initObservable: function () {
            this._super()
                .observe({
                    preferredMethodValue: deliveryMethods.getSelectedMethod() || '',
                    pickupLocationCode: bopisLocation.selectedLocation()['pickup_location_code'] || ''
                });

            bopisLocation.section().subscribe(({ preferred_location }) => {
                this.pickupLocationCode(preferred_location.pickup_location_code);
            })

            return this;
        },
    });
});
