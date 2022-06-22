define([
    'jquery',
    'uiComponent',
    'Magento_GroupedProduct/js/product-ids-resolver',
    'mage/translate',
    'bopisUrlBuilder',
    'knockout',
    'bopisLocation',
    'deliveryMethods',
    'Walmart_BopisPreferredLocationFrontend/js/view/addtocart',
    'domReady!'
], ($, Component, groupProductsResolver, $t, urlBuilder, ko, bopisLocation, deliveryMethods, addToCart) => {
    'use strict';

    return Component.extend({
        defaults: {
            idSkuMap: [],
            outOfStockMessage: $t('Out of Stock'),
            checkHomeDeliveryAvailabilityUrl: urlBuilder.createUrl('/wct-fulfillment/inventory/product-availability/home-delivery/'),
            checkInStoreAvailabilityUrl: urlBuilder.createUrl('/wct-fulfillment/inventory/product-availability/store-pickup/')
        },

        initialize() {
            this._super();
            self = this;

            ko.computed(function () {
                const method = deliveryMethods.selectedMethod();
                const location = bopisLocation.selectedLocation();

                // Don't proceed if method is not set, or if location is not sent when method is set to instore_pickup
                if (!method || (method == 'instore_pickup' && !location)) {
                    return;
                }

                const productSkus = self.getProductSkus();

                let url = '';
                let body = {
                    skus: productSkus,
                };

                if(method === 'instore_pickup')
                {
                    url =  self.checkInStoreAvailabilityUrl;
                    body.sources = [location['pickup_location_code']];
                } else {
                    url =  self.checkHomeDeliveryAvailabilityUrl;
                }


                //Do Ajax Call to Custom Controller, send Array of Product Ids above
                // Custom Controller looks at each product ID and determines if it's in stock
                // at the location, it returns a JSON object with a key for each productId and a
                // Boolean value of whether or not it is in stock
                // When you have the data run the self.updateElements(data);
                $.ajax({
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(body),
                    url: url,
                    success(response) {
                        self.updateElements.bind(self)(response);
                    }
                });

            })

        },

        updateElements(data) {
            let disabledGroupedOptionsCount = 0;
            // Remove each of the existing out of stock messages.
            $('#super-product-table .walmart-bopis-out-of-stock').remove();
            // Enable all inputs.
            $('#super-product-table input[data-selector^="super_group"]')
                .removeAttr('disabled')
                .removeClass('disabled');

            // Iterate of the Products
            $.each(data[0], (sku, isAvailable) => {
                const productId = _.findKey(this.idSkuMap, e => e == sku);
                const $productInput = $(`input[data-selector="super_group[${productId}]"]`);

                if (!isAvailable) {
                    // Disable the input, add the disabled class, set the value to 0, insert out of stock message
                    // after the input element.
                    disabledGroupedOptionsCount ++;
                    $productInput
                        .addClass('disabled')
                        .prop('disabled', true)
                        .val(0)
                        .after(`
                            <div class="stock unavailable walmart-bopis-out-of-stock">${this.outOfStockMessage}</div>
                        `);
                }
            });

            if (disabledGroupedOptionsCount === Object.keys(data[0]).length) {
                addToCart().isEnabled(false);
            } else {
                addToCart().isEnabled(true);
            }
        },

        getProductSkus() {
            // Get Product Ids for current Grouped Products Table 'Magento_GroupedProduct/js/product-ids-resolver'
            // this might work but has not been tested.  You may need to make your own method.
            const ids = groupProductsResolver({}, '#super-product-table');
            return _.map(ids, id => this.idSkuMap[id]);
        }
    })
});
