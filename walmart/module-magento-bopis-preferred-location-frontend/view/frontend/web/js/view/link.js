/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'uiComponent',
    'ko',
    'bopisLocation',
    'mage/translate'
], function (Component, ko, bopisLocation, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Walmart_BopisPreferredLocationFrontend/link',
            section: bopisLocation.section(),
            linkText: ko.observable(''),
            selectedStoreName: ko.observable(''),
            defaultText: $t('Select your store'),
            changeText: $t('Change store')
        },

        /**
         * @override
         */
        initialize: function () {
            this._super();

            this.linkText = ko.computed(function () {
                if (this.section()['preferred_location']) {
                    this.selectedStoreName(this.section()['preferred_location'].name);

                    return this.changeText;
                }

                this.selectedStoreName('');

                return this.defaultText;
            }.bind(this));
        }
    });
});
