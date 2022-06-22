/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";

    function main(config) {
        var PICKUP_LOCATION_SOURCE_SELECTOR = '#pickup_location_source';

        $(document).ready(function () {
            $(document.body).on('change', PICKUP_LOCATION_SOURCE_SELECTOR, function () {
                updatePickupOptions(config.updatePickupOptionsUrl, this.value);
            });
        });
    }

    function updatePickupOptions(updatePickupOptionsUrl, sourceCode) {
        var PICKUP_OPTIONS_SELECTOR = '#pickup_option';

        $('body').trigger('processStart');

        $.ajax({
            context: PICKUP_OPTIONS_SELECTOR,
            url: updatePickupOptionsUrl,
            type: "POST",
            data: {source_code: sourceCode},
        }).done(function (data) {
            $(PICKUP_OPTIONS_SELECTOR).html(data.output);
            $('body').trigger('processStop');
            return true;
        });
    }

    return main;
});
