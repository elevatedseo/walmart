/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'mage/translate',
    'bopisBrowserLocationStorage'
], function ($t, browserLocationStorage) {
    'use strict';

    return {
        getLocation: function (successCallBack, errorCallBack) {
            var location = false;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function (pos){
                        browserLocationStorage.setLocation(pos);
                        successCallBack(pos);
                    },
                    function () {
                        console.log($t('Location detection is blocked by client.'));
                        errorCallBack();
                    },
                    {enableHighAccuracy: true}
                );
            } else {
                console.log($t('Geolocation is not supported by this browser.'));
            }

            return location;
        }
    };
});
