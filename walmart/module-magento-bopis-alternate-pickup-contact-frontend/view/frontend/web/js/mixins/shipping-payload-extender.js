/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function (wrapper, quote) {
    'use strict';

    return function (payloadExtender) {
        /**
         * Set altPickupContact field to Address information extension attributes if associated quote property exist.
         * If altPickupContact quote property doesn't exist, return init payload to not send
         * redundant data.
         */
        return wrapper.wrap(payloadExtender, function (originalAction, payload) {
            payload = originalAction(payload);

            if(!Object.keys(quote.altPickupContact()).length) {
                return payload;
            }

            payload.addressInformation.extension_attributes.altPickupContact = quote.altPickupContact();

            return payload;
        });
    };
});
