/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

var config = {
    map: {
        '*': {
            deliveryMethods: 'Walmart_BopisDeliverySelection/js/model/delivery-methods'
        }
    },
    config: {
        mixins: {
            'Magento_ConfigurableProduct/js/configurable': {
                'Walmart_BopisDeliverySelection/js/configurable-mixin': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Walmart_BopisDeliverySelection/js/swatch-renderer-mixin': true
            },
            'Magento_Checkout/js/action/select-shipping-method': {
                'Walmart_BopisDeliverySelection/js/action/select-shipping-method-mixin': true
            }
        }
    }
};
