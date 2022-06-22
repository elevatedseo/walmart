/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'ko'
], function (ko) {
    'use strict';
    var altPickupContact = ko.observable({});

    return function (quote) {
        quote.altPickupContact = altPickupContact;

        return quote;
    };
});
