/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'mage/utils/wrapper',
    'Walmart_BopisCheckoutPickInStoreFrontend/js/model/pickup-options-service',
], function (wrapper, pickupOptionsService) {
    'use strict';

    return function (target) {
        return wrapper.wrap(target, function(originalFunction, formData) {
            if(formData['extension_attributes'] !== undefined &&
                formData['extension_attributes']['pickup_location_code'] !== undefined)
            {
                formData['extension_attributes']['pickup_option'] = pickupOptionsService.selectedPickupOption();
            }
            originalFunction(formData);
        });
    };
});
