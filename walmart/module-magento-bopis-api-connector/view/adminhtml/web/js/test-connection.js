/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'jquery',
    'underscore',
    'mage/storage',
    'mage/translate'
], function ($, _, storage){
    'use strict';

    let appTestConnectionApp = function(){};

    appTestConnectionApp.prototype = {
        config: null,
        environments: {
            sandbox          : 'sandbox',
            production       : 'production',
        },
        selectors: {
            testSuccessBtnMessage       : $('#walmart-api-test-success-button-msg'),
            testErrorBtnMessage         : $('#walmart-api-test-error-button-msg'),
            consumerIdInput               : $('#bopis_account_credentials_consumer_id'),
            consumerSecretInput           : $('#bopis_account_credentials_consumer_secret'),
            sandboxConsumerIdInput        : $('#bopis_account_credentials_sandbox_consumer_id'),
            sandboxConsumerSecretInput    : $('#bopis_account_credentials_sandbox_consumer_secret'),
            environment                 : $('#bopis_account_credentials_environment'),
            testButtonIdentifier        : '#walmart_bopis_test_connection_button'
        },

        /**
         * Init App
         * @param config
         */
        init: function(config){
            const self = this;
            self.config = config;
            self.testApiConnection();
        },

        /**
         * Hide/Show error element
         */
        hideErrorAndSuccessMessage: function(){
            const self = this;
            self.selectors.testSuccessBtnMessage.addClass('hidden');
            self.selectors.testErrorBtnMessage.addClass('hidden');
        },

        /**
         * Get current environment selected
         *
         * @returns {*|string|jQuery}
         */
        getEnvironment: function(){
            const self = this;
            return self.selectors.environment.val();
        },

        /**
         * Get ClientId value
         * @returns {*|string|jQuery}
         */
        getConsumerId: function(){
            const self = this;
            let environment = self.getEnvironment();
            return ( environment == self.environments.sandbox )
                ? self.selectors.sandboxConsumerIdInput.val()
                : self.selectors.consumerIdInput.val();
        },

        /**
         * Get ClientSecret
         * @returns {*|string|jQuery}
         */
        getConsumerSecret: function(){
            const self = this;
            let environment = self.getEnvironment();
            return ( environment == self.environments.sandbox )
                ? self.selectors.sandboxConsumerSecretInput.val()
                : self.selectors.consumerSecretInput.val();
        },

        /**
         * Validate production credentials not empty (client Id, Client Secret)
         * @returns {boolean}
         */
        validateProductionCredentials: function(){
            const self = this;
            if( _.isEmpty($.trim(self.selectors.consumerIdInput.val())) ){
                self.selectors.testErrorBtnMessage.removeClass('hidden');
                self.selectors.testErrorBtnMessage.text($.mage.__('BOPIS Api credentials (Client Id) is not filled.'));
                return false;
            }

            if( _.isEmpty($.trim(self.selectors.consumerSecretInput.val())) ){
                self.selectors.testErrorBtnMessage.removeClass('hidden');
                self.selectors.testErrorBtnMessage.text($.mage.__('BOPIS Api credentials (Client Secret) is not filled.'));
                return false;
            }
            return true;
        },

        /**
         * Validate sandbox credentials not empty (Sandbox Client Id, Sandbox Client Secret)
         * @returns {boolean}
         */
        validateSandboxCredentials: function(){
            const self = this;
            if( _.isEmpty($.trim(self.selectors.sandboxConsumerIdInput.val())) ){
                self.selectors.testErrorBtnMessage.removeClass('hidden');
                self.selectors.testErrorBtnMessage.text($.mage.__('BOPIS Api credentials (Sandbox Client Id) is not filled.'));
                return false;
            }

            if( _.isEmpty($.trim(self.selectors.sandboxConsumerSecretInput.val())) ){
                self.selectors.testErrorBtnMessage.removeClass('hidden');
                self.selectors.testErrorBtnMessage.text($.mage.__('BOPIS Api credentials (Sandbox Client Secret) is not filled.'));
                return false;
            }
            return true;
        },

        /**
         * Event click to call EndPoint and validate connection
         */
        testApiConnection: function (){
            const self = this;
            $(self.selectors.testButtonIdentifier).on('click', function(){

                let environment = self.getEnvironment();
                self.hideErrorAndSuccessMessage()

                if( environment == self.environments.sandbox ){
                    if( !self.validateSandboxCredentials() ){
                        return false;
                    }
                }

                if( environment == self.environments.production ){
                    if( !self.validateProductionCredentials() ){
                        return false;
                    }
                }

                $(self.selectors.testButtonIdentifier).text(
                    $.mage.__("We're validating your credentials...")
                ).attr('disabled', true);

                $.ajax({
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({
                        environment     : environment,
                        clientId        : self.getConsumerId(),
                        clientSecret    : self.getConsumerSecret()
                    }),
                    beforeSend: function(xhr){},
                    url: self.config.endPointTestConnection,
                    cache: false,
                    success: function (response) {
                        let tokenData = JSON.parse(response);
                        if( !tokenData.error ){
                            self.selectors.testSuccessBtnMessage.removeClass('hidden');
                            self.selectors.testSuccessBtnMessage.text($.mage.__('Connection to Walmart API has been successfully established.'));
                        } else {
                            self.selectors.testErrorBtnMessage.removeClass('hidden');
                            self.selectors.testErrorBtnMessage.text(tokenData.message);
                        }
                    },
                    error: function () {
                        self.selectors.testErrorBtnMessage.removeClass('hidden');
                        self.selectors.testErrorBtnMessage.text($.mage.__(
                            'Please check your Data and try again. (Save it before validation)'
                        ));
                    },
                }).always(function (){
                    $(self.selectors.testButtonIdentifier).text(
                        $.mage.__("Validate Credentials")
                    ).attr('disabled', false);
                });
            })
        }
    };

    /**
     * Call init() method to control credential validations
     */
    return function (config){
        const AppTestConnection = new appTestConnectionApp();
        AppTestConnection.init(config);
    }
});
