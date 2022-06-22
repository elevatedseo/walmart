/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'jquery',
    'bopisLocation',
    'Magento_Ui/js/modal/modal'
], function ($, bopisLocation) {
    'use strict';

    return function (config, element) {
        config.modalOptions.opened = function () {
            bopisLocation.updateStockInfo();
        };

        $(element).modal(config.modalOptions);

        $(document).on('openBopisModal', function () {
            $(element).modal('openModal');
        });
        
        $(document).on('closeBopisModal', function () {
            $(element).modal('closeModal');
        })
    };
});
