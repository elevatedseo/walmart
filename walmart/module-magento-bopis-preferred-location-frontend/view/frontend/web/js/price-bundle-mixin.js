/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'jquery',
    'mage/translate',
    'bopisUrlBuilder',
    'knockout',
    'bopisLocation',
    'deliveryMethods',
    'Walmart_BopisPreferredLocationFrontend/js/view/addtocart',
    'domReady!',
], function ($, $t, bopisUrlBuilder, ko, bopisLocation, deliveryMethods, addToCart) {
    'use strict';

    return function (originalWidget) {
        $.widget('mage.priceBundle', originalWidget, {
            checkInStoreAvailabilityUrl: '/wct-fulfillment/inventory/product-availability/store-pickup',
            checkHomeDeliveryAvailabilityUrl: '/wct-fulfillment/inventory/product-availability/home-delivery',

            _init: function initPriceBundle() {
                this._super();

                /**
                 * Message displayed after inputs that are out of stock
                 * Message displays as a prefix to select options that are out of stock
                 *
                 * @type {mage.priceBundle}
                 */
                const self = this;
                this.outOfStockMessage = $t('Out of Stock');
                this.selectOutOfStockMessage = $t('[Out of Stock] ');
                this.bundleIdToSkuMap = this.options.optionConfig['idToSkuMap'];
                this.storeCode = this.options.optionConfig['storeCode']

                ko.computed(function () {
                    const method = deliveryMethods.selectedMethod();
                    const location = bopisLocation.selectedLocation();

                    /**
                     * Don't proceed if method is not set, or if location is not sent when method is set to instore_pickup
                     */
                    if (!method || (method === 'instore_pickup' && !location)) {
                        return;
                    }

                    const productSkus = self._getBundleProductIds(self.bundleIdToSkuMap);

                    let url = '';
                    let body = {
                        skus: productSkus,
                    };

                    if (method === 'instore_pickup') {
                        url = self.checkInStoreAvailabilityUrl;
                        body.sources = [location['pickup_location_code']];
                    } else {
                        url = self.checkHomeDeliveryAvailabilityUrl;
                    }

                    /**
                     * Do Ajax Call to Custom Controller, send Array of Product SKUs above Custom Controller looks at
                     * each Product SKU and determines if it's in stock at the location, it returns a JSON object with
                     * a key for each ProductSKU and a Boolean value of whether or not it is in stock
                     * When you have the data run the self._updateBundledElements(data);
                     */
                    $.ajax({
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(body),
                        url: bopisUrlBuilder.createUrl(url, self.storeCode),
                        success(response) {
                            self._updateBundledElements.bind(self)(response);
                        }
                    });
                })
            },

            /**
             * Gather the Bundle Product Ids and Bundle Options
             *
             * @returns {*}
             * @private
             */
            _getBundleProductIds: function getBundleProductIds(bundleIdToSkuMap) {
                const self = this;
                self.productSkus = [];

                $.each(bundleIdToSkuMap, (optionIdx, option) => {
                    $.each(option, (selectionIdx, sku) => {
                        self.productSkus.push(sku);
                    });
                });

                return self.productSkus;
            },

            /**
             * Update all Bundle Elements
             *
             * @param data
             * @private
             */
            _updateBundledElements: function updateBundledElements(data) {
                const self = this;

                /**
                 * Remove each of the existing out of stock messages.
                 */
                $('#product-options-wrapper .walmart-bopis-out-of-stock').remove();

                /**
                 * Remove all the Out of Stock messages from select options
                 */
                $.each($('#product-options-wrapper select option'), (idx, selectOption) => {
                    $(selectOption).html($(selectOption).html().replace(self.selectOutOfStockMessage, ''));
                });

                /**
                 * Enable all inputs
                 */
                $('#product-options-wrapper select, #product-options-wrapper select option, #product-options-wrapper input')
                    .removeAttr('disabled')
                    .removeClass('disabled');

                /**
                 * Iterate over all of the options
                 */
                $.each(self.bundleIdToSkuMap, (optionIdx, option) => {
                    /**
                     * Iterate over all the selections (product ids)
                     */
                    $.each(option, (selectionIdx, sku) => {
                        /**
                         * Update Bundle Options
                         */
                        self._updateBundleInputElements(optionIdx, selectionIdx, data[0][sku]);
                    });
                });

                this.element.trigger('updateProductSummary', {
                    config: this.options.optionConfig
                });
            },

            /**
             * Update all Bundle Input Elements
             *
             * @param optionId
             * @param selectionId
             * @param isAvailable
             * @private
             */
            _updateBundleInputElements: function updateBundleInputElements(optionId, selectionId, isAvailable) {
                const self = this;
                const $productInputs = $(`input[name^="bundle_option[${optionId}]"], select[name^="bundle_option[${optionId}]"]`);

                $.each($productInputs, (idx, productInput) => {
                    const $productInput = $(productInput);
                    const inputType = $productInput.prop('nodeName');

                    if (inputType === 'SELECT') {
                        self._updateBundleSelectElement($productInput, selectionId, isAvailable);
                    } else {
                        self._updateBundleInputElement($productInput, optionId, selectionId, isAvailable)
                    }
                });
            },

            /**
             * Update Bundle Input Element
             *
             * @param $productInput
             * @param optionId
             * @param selectionId
             * @param isAvailable
             * @private
             */
            _updateBundleInputElement: function updateBundleInputElement($productInput, optionId, selectionId, isAvailable) {
                const isHidden = $productInput.attr('type') === 'hidden';

                /**
                 * Uncheck Inputs
                 */
                if (!isHidden) {
                    $productInput.removeAttr('checked');
                    delete this.options.optionConfig.selected[optionId];
                }

                if (!isAvailable) {
                    if ($productInput.val() === selectionId) {
                        $productInput
                            .addClass('disabled')
                            .prop('disabled', true)
                            .after(`
                            <div class="stock unavailable walmart-bopis-out-of-stock">${this.outOfStockMessage}</div>
                        `)

                        /**
                         * If one of options is out of stock and it’s required - disable “Add To Cart” button
                         */
                        if ($productInput.attr('aria-required')) {
                            addToCart().isEnabled(false);
                        }
                    }
                } else {
                    addToCart().isEnabled(true);
                }
            },

            /**
             * Update Bundle Select Element
             *
             * @param $productInput
             * @param selectionId
             * @param isAvailable
             * @private
             */
            _updateBundleSelectElement: function updateBundleSelectElement($productInput, selectionId, isAvailable) {
                const self = this;

                /**
                 * Unselect all options
                 */
                $productInput.val([]);

                /**
                 * Disable individual option
                 */
                if (!isAvailable) {
                    const $currentOption = $productInput.find(`option[value="${selectionId}"]`);
                    $currentOption
                        .prop('disabled', true)
                        .html(`${self.selectOutOfStockMessage}${$currentOption.html()}`);
                }
            }
        });

        return $.mage.priceBundle;
    }
})
