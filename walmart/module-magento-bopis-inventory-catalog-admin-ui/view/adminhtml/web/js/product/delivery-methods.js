/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";

    return function (config, element) {
        const homeDelivery      = '[data-index="available_home_delivery"]';
        const inStoreDelivery   = '[data-index="available_store_pickup"]';

        $(document).on('change', homeDelivery + ', ' + inStoreDelivery, function (elem) {
            var elementName = $(elem.target).prop('name');

            if ($(elem.target).val() == 0) {
                if (elementName === 'product[available_home_delivery]') {
                    // Make In-Store Delivery selected
                    $(inStoreDelivery).find('input').prop('checked', true);
                }

                if (elementName === 'product[available_store_pickup]') {
                    // Make Home Delivery selected
                    $(homeDelivery).find('input').prop('checked', true);
                }
            }
        })
    };
});
