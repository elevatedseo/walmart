/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'jquery',
    'uiComponent',
    'ko',
    'bopisLocation',
    'deliveryMethods',
    'mage/translate',
    'Magento_Ui/js/modal/confirm'
], function ($, Component, ko, bopisLocation, deliveryMethods, $t, confirm) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Walmart_BopisPreferredLocationFrontend/list',
            selectedLocation: ko.observable(bopisLocation.selectedLocation()),
            selectedLocationCode: ko.observable(false),
            storedLocation: bopisLocation.selectedLocation,
            locationsToShow: bopisLocation.locations,
            saveBtnText: $t('Select store for pickup'),
            locationsStockInfo: bopisLocation.locationsStockInfo,
            storeCode: 'all',
            locations: [],
            outOfStockConfirm: $t('Some items are out of stock at the new store location. Do you still want to select this store?'),
            itemsInCartConfirm: $t('You already have pickup items in your cart and only one store for pickup by order can be selected. Changing the pickup store now will apply to all cart items, and their availability may change. Do you want to proceed now?')
        },

        /**
         * @override
         */
        initialize: function () {
            this._super();

            // Set initial params to model
            bopisLocation.locations(this.locations);
            bopisLocation.storeCode(this.storeCode);
        },

        initObservable: function () {
            this._super();

            this.stockInfo = false;

            // preselect location if storedLocation exist
            ko.computed(function () {
                if (!this.selectedLocationCode() && this.storedLocation()) {
                    this.selectedLocationCode(this.storedLocation()['pickup_location_code']);
                }
            }.bind(this));

            // update selectedLocation when it was changed by user
            this.selectedLocationCode.subscribe(function (code) {
                this.selectedLocation(bopisLocation.getLocationByCode(code));
            }.bind(this));

            return this;
        },

        selectLocation: function () {
            var self = this,
                stock = bopisLocation.getStockByCode(this.selectedLocationCode());

            if (stock && stock['out_of_stock_items'].length) {
                confirm({
                    title: $t('Change store for pickup'),
                    content: $t(self.outOfStockConfirm),
                    actions: {
                        confirm: function () {
                            self.saveLocation();
                        },
                        cancel: function () {
                            self.selectedLocationCode(self.storedLocation()['pickup_location_code']);
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
                        class: 'action-secondary action-dismiss',

                        /**
                         * Click handler.
                         */
                        click: function (event) {
                            this.closeModal(event);
                        }
                    }]
                });
            } else if (deliveryMethods.selectedMethod() == 'instore_pickup' && deliveryMethods.hasItemsInCart() && (stock && !stock['out_of_stock_items'].length || !stock)) {
                if (window.location.href.indexOf('checkout/cart') == -1) {
                    confirm({
                        title: $t('Change store for pickup'),
                        content: $t(self.itemsInCartConfirm),
                        actions: {
                            confirm: function () {
                                self.saveLocation();
                            },
                            cancel: function () {
                                self.selectedLocationCode(self.storedLocation()['pickup_location_code']);
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
                            class: 'action-secondary action-dismiss',

                            /**
                             * Click handler.
                             */
                            click: function (event) {
                                this.closeModal(event);
                            }
                        }]
                    });
                } else {
                    this.saveLocation();
                }
            } else {
                this.saveLocation();
            }
        },

        saveLocation: function () {
            bopisLocation.setLocation(this.selectedLocationCode());
            bopisLocation.selectedLocationStock(bopisLocation.getSelectedLocationStock());
            $(document).trigger('closeBopisModal');
        },

        // TODO: find a better solution
        getStockInfo: function (location_code) {
            return this.stockInfo = ko.computed(function (){
                return bopisLocation.getStockByCode(location_code) || false;
            })
        }
    });
});
