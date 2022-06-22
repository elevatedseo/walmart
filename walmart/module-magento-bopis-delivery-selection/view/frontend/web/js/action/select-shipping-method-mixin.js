define([
    'mage/utils/wrapper',
    'deliveryMethods',
    'Magento_Checkout/js/checkout-data'
], function (wrapper, deliveryMethods, checkoutData) {
    'use strict';

    return function (originalModule) {
        return wrapper.wrap(originalModule, function (original, shippingMethod) {
            if (shippingMethod && shippingMethod['carrier_code'] === 'instore') {
                let pickupAddress = checkoutData.getSelectedPickupAddress();
                let sourceCode = pickupAddress !== null ? pickupAddress.extension_attributes.pickup_location_code : null
                deliveryMethods.setPreferredMethod(
                    'instore_pickup',
                    shippingMethod,
                    sourceCode
                );
            } else {
                deliveryMethods.setPreferredMethod('home', shippingMethod);
            }

            let moduleData = original();
            return moduleData;
        });
    };
})
