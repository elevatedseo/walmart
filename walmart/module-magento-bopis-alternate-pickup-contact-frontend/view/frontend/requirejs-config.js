/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': {
                'Walmart_BopisAlternatePickupContactFrontend/js/mixins/shipping-payload-extender': true
            },
            'Magento_Checkout/js/model/quote': {
                'Walmart_BopisAlternatePickupContactFrontend/js/mixins/quote-mixin': true
            },
        }
    }
}
