/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'uiComponent',
    'ko',
    'jquery',
    'mage/translate'
], function (Component, ko, $, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Walmart_BopisLocationCheckInFrontend/checkin-field',
            fieldType: 'select',
            icon: '',
            isRequired: false,
            isVisible: true,
            hasOtherOption: false,
            fieldTitle: '',
            placeholder: '',
            otherOptionPlaceholder: '',
            options: [],
            savedValue: '',
            savedLabel: '',
            isStarted: false,
            updateIconColorVar: false,
            textareaMaxLength: 100,
            fixedBtnBodyClass: 'btn-fixed',
            requiredMessage: $t('This is a required field.')
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();

            if (this.hasOtherOption) {
                this.options.push({text: $t('Other'), value: 'other'});
            }

            $('body').toggleClass(this.fixedBtnBodyClass, !this.isStarted);
        },

        initObservable: function () {
            this._super();

            this.isUpdateActive = ko.observable(false);
            this.currentValue = ko.observable(this.savedValue);

            this.isUpdateActive.subscribe(function (newValue) {
                $('body').toggleClass(this.fixedBtnBodyClass, newValue);
            }, this);

            return this;
        },

        triggerUpdate: function () {
            this.isUpdateActive(true);
        }
    });
});
