/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'Magento_Ui/js/form/form',
    'Magento_Checkout/js/model/quote',
], function(Component, quote) {
    'use strict';
    return Component.extend({
        initialize: function () {
            this._super();

            return this;
        },

        initObservable: function () {
            this._super()
                .observe({
                    isFormVisible: false
                });

            return this;
        },

        validatePickupContactForm: function() {
            return !this.isFormVisible() ? true : this.validateFilledForm();
        },

        validateFilledForm: function () {
            this.source.set('params.invalid', false);
            this.source.trigger('pickupContactForm.data.validate');

            return !this.source.get('params.invalid');
        },

        setQuotePickupContactInformation: function () {
            if (!this.isFormVisible()) {
                quote.altPickupContact({});

                return false;
            }

            var formData = this.source.get('pickupContactForm');

            quote.altPickupContact(formData);
        }
    });
});
