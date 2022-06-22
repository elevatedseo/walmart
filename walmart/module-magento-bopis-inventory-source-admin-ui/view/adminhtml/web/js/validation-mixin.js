/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define(['Magento_Ui/js/lib/validation/validator', 'jquery'], function (validator, $) {
    'use strict';

    return function () {
        if (window.wctModuleEnabled) {
            validator.addRule(
                'bopis-custom-phone-validation',
                function (value) {
                    return /^[0-9]{10}$/.test(value);
                },
                $.mage.__('Please enter a 10-digit phone number without any spaces or special characters. Ex. 1112223333')
            );

            validator.addRule(
                'bopis-pickup-time-label-validation',
                function (value) {
                    return (value.indexOf("%1") >= 0 && value.indexOf("minutes") >= 0) || (!value);
                },
                $.mage.__("'%1 minutes' must be included in the label. Ex. Ready for Pickup in %1 minutes.")
            );

            $.validator.addMethod(
                'bopis-pickup-time-label-validation',
                function (value) {
                    return (value.indexOf("%1") >= 0 && value.indexOf("minutes") >= 0) || (!value);
                },
                $.mage.__("'%1 minutes' must be included in the label. Ex. Ready for Pickup in %1 minutes.")
            );

            validator.addRule(
                'bopis-unique-fields-validation',
                function (value, params) {
                    var isValid = false;
                    var field = params.field;
                    var source_code = params.source_code;

                    $.ajax({
                        url: window.validateSourceCodeUrl,
                        data: {field: field, value: value, source_code: source_code},
                        type: 'POST',
                        showLoader: true,
                        async: false,
                        success: function (response) {
                            isValid = response.success;
                        }
                    });

                    return isValid;
                },
                $.mage.__('Value is already in use.')
            );
        }
    }
});
