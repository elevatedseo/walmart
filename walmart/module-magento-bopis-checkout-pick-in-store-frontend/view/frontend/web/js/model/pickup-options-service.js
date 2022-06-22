/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'knockout'
], function (
    $,
    ko
) {
    'use strict';

    return {
        selectedPickupOption: ko.observable(null)
    };
});
