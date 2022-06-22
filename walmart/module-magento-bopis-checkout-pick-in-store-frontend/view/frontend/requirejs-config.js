/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping-information': {
                'Magento_InventoryInStorePickupFrontend/js/view/shipping-information-ext': false,
                'Walmart_BopisCheckoutPickInStoreFrontend/js/view/shipping-information-ext': true
            },
            'Magento_InventoryInStorePickupFrontend/js/model/pickup-locations-service': {
                'Walmart_BopisCheckoutPickInStoreFrontend/js/model/pickup-locations-service-mixin': true
            },
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Walmart_BopisCheckoutPickInStoreFrontend/js/model/checkout-data-resolver-mixin': true
            },
            'Magento_Checkout/js/checkout-data': {
                'Walmart_BopisCheckoutPickInStoreFrontend/js/checkout-data-mixin': true
            },
            'Magento_Checkout/js/model/address-converter-mixin': {
                'Walmart_BopisCheckoutPickInStoreFrontend/js/model/address-converter-mixin': true
            }
        }
    }
}
