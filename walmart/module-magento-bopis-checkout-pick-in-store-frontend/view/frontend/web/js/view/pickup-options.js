/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'uiComponent',
    'ko',
    'bopisLocation',
    'Walmart_BopisCheckoutPickInStoreFrontend/js/model/pickup-options-service'
], function (Component, ko, bopisLocation, pickupOptionsService) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Walmart_BopisCheckoutPickInStoreFrontend/pickup-options',
            section: bopisLocation.section(),
            selectedLocation: ko.observable(bopisLocation.selectedLocation()),
            selectedOption: ko.observable(bopisLocation.selectedLocation() ? bopisLocation.selectedLocation()['pickup_options'][0]['code'] : null)
        },

        /**
         * @override
         */
        initialize: function () {
            this._super();

            ko.computed(function (){
                pickupOptionsService.selectedPickupOption(this.selectedOption())
            }.bind(this));

            this.selectedLocation = ko.computed(function () {
                return this.section()['preferred_location'];
            }.bind(this));

            this.selectedLocation.subscribe(function (location){
                this.selectedOption(location['pickup_options'][0]['code']);
            }.bind(this));
        }
    });
});
