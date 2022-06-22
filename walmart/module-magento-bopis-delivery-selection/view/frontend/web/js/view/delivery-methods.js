/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'jquery',
    'uiComponent',
    'ko',
    'deliveryMethods',
    'bopisLocation',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, Component, ko, deliveryMethods, bopisLocation, confirm, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            selectedMethod: ko.observable(deliveryMethods.selectedMethod()),
            selectedLocation: bopisLocation.selectedLocation,
            savedMethod: ko.observable(deliveryMethods.selectedMethod()),
            hasItemsModalContent: 'Do you want to continue?'
        },

        /**
         * @override
         */
        initialize: function () {
            this._super();

            bopisLocation.updateStockInfo();
            this._subscribe();
        },

        _subscribe: function () {
            var self = this;

            deliveryMethods.selectedMethod.subscribe(function (method){
                this.selectedMethod(method);
            }.bind(this));

            this.selectedMethod.subscribe(function (method) {
                if (deliveryMethods.hasItemsInCart() && method != deliveryMethods.getSelectedMethod()) {
                    confirm({
                        title: $t('Change delivery method'),
                        content: $t(self.hasItemsModalContent),
                        actions: {
                            confirm: function () {
                                deliveryMethods.setPreferredMethod(method);

                                if (method == 'instore_pickup' && bopisLocation.getSelectedLocationStock() && bopisLocation.getSelectedLocationStock()['out_of_stock_items'].length) {
                                    $(document).trigger('openBopisModal')
                                }
                            },
                            cancel: function () {
                                self.selectedMethod(deliveryMethods.getSelectedMethod());
                            }
                        },
                        buttons: [{
                            text: $t('Yes, Continue'),
                            class: 'action-primary action-accept',

                            /**
                             * Click handler.
                             */
                            click: function (event) {
                                this.closeModal(event, true);
                            }
                        }, {
                            text: $t('Cancel'),
                            class: 'action-secondary action-dismiss wct-action-button-as-link left-1',

                            /**
                             * Click handler.
                             */
                            click: function (event) {
                                this.closeModal(event);
                            }
                        }]
                    });
                } else {
                    deliveryMethods.setPreferredMethod(method);
                }
            });
        }
    });
});
