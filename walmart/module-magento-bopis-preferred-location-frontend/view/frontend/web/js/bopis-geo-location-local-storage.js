/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([], function (){
    'use strict';

    return {
        setLocation: function (location){
            const locationInformation = {
                accuracy: location.coords.accuracy,
                altitude: location.coords.altitude,
                altitudeAccuracy: location.coords.altitudeAccuracy,
                heading: location.coords.heading,
                latitude: location.coords.latitude,
                longitude: location.coords.longitude
            }
            window.localStorage.setItem('bopis-geo-location', JSON.stringify(locationInformation));
        },
        getLocation: function(){
            return JSON.parse(window.localStorage.getItem('bopis-geo-location')) || {};
        }
    }
});
