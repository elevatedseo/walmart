/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'jquery',
    'bopisLocation'
], function ($, bopisLocation) {
    'use strict';

    return function (config, element) {
        var $formEl = $(element);

        $formEl.on('change', setRequestItems)

        function setRequestItems () {
            bopisLocation.requestItems([{
                sku: config.sku,
                qty: $formEl.find('#qty').val() || 1000
            }])
        }

        setRequestItems();
    };
});
