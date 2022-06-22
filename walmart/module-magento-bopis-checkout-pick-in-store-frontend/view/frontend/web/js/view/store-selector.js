/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'jquery',
    'uiComponent',
    'ko',
    'bopisLocation',
    'Walmart_BopisCheckoutPickInStoreFrontend/js/model/pickup-options-service',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
], function ($, Component, ko, bopisLocation, pickupOptionsService, setShippingInformationAction, stepNavigator, quote, customer) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Walmart_BopisCheckoutPickInStoreFrontend/store-selector',
            section: bopisLocation.section(),
            selectedLocation: ko.observable(bopisLocation.selectedLocation()),
            quoteIsVirtual: quote.isVirtual,
            loginFormSelector:
                '#store-selector form[data-role=email-with-possible-login]',
            modules: {
                pickupContactComponent: '${ $.name }.pickup-contact',
                pickupOptionsComponent: '${ $.name }.pickup_options'
            }
        },

        /**
         * @override
         */
        initialize: function () {
            this._super();

            this.selectedLocation = ko.computed(function () {
                return this.section()['preferred_location'];
            }.bind(this));
        },

        /**
         * Set shipping information handler
         */
        setPickupInformation: function () {
            if (!this.validatePickupInformation() || !this.pickupContactComponent()?.validatePickupContactForm()) {
                return false;
            }

            this.pickupContactComponent().setQuotePickupContactInformation();

            setShippingInformationAction().done(function () {
                stepNavigator.next();
            });
        },

        /**
         * @returns {Boolean}
         */
        validatePickupInformation: function () {
            var emailValidationResult,
                loginFormSelector = this.loginFormSelector;

            if (!customer.isLoggedIn()) {
                $(loginFormSelector).validation();
                emailValidationResult = $(loginFormSelector + ' input[name=username]').valid() ? true : false;

                if (!emailValidationResult) {
                    $(this.loginFormSelector + ' input[name=username]').trigger('focus');

                    return false;
                }
            }

            return true;
        }
    });
});
