/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define(['jquery'], function ($) {
    'use strict';

    return {
        serviceUrl: ':method/:storeCode/:version',
        method: 'rest',
        version: 'V1',

        /**
         * @param {String} url
         * @param {Object} params
         * @return {*}
         */
        createUrl: function (url, storeCode = 'all') {
            var completeUrl = this.serviceUrl + url,
                params = {
                    method: this.method,
                    storeCode: storeCode,
                    version: this.version,
                }

            return this.bindParams(completeUrl, params);
        },

        /**
         * @param {String} url
         * @param {Object} params
         * @return {*}
         */
        bindParams: function (url, params) {
            var urlParts;

            urlParts = url.split('/');
            urlParts = urlParts.filter(Boolean);

            $.each(urlParts, function (key, part) {
                part = part.replace(':', '');

                if (params[part] != undefined) { //eslint-disable-line eqeqeq
                    urlParts[key] = params[part];
                }
            });

            return urlParts.join('/');
        }
    };
});
