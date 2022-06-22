/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

define([
    'uiComponent',
    'ko',
    'bopisBrowserLocation',
    'bopisLocation'
], function (Component, ko, bopisBrowserLocation, bopisLocation) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Walmart_BopisPreferredLocationFrontend/map',
            selectedLocation: ko.observable(false),
            googleMapUrl: '',
            map: null,
            mapSrc: ko.observable(false),
            locationsToShow: bopisLocation.locations
        },

        /**
         * @override
         */
        initialize: function () {
            var self = this;
            this._super();

            // initialize Google Map
            self.locationsToShow.subscribe(function(locations) {
                if (locations.length !== 0 && document.getElementById('bopis-google-map')) {
                    if (self.map === null) {
                        self.map = new google.maps.Map(document.getElementById('bopis-google-map'), {
                            zoom: 8
                        });
                    }

                    if (!self.selectedLocation()) {
                        let firstLocation = locations[0];
                        let initialLocation = new google.maps.LatLng(firstLocation.latitude, firstLocation.longitude);
                        self.map.setCenter(initialLocation);
                    }

                    // add sources markers
                    self.locationsToShow().map((location) => {
                        const label = location.name;
                        new google.maps.Marker({
                            position: {
                                lat: parseFloat(location.latitude),
                                lng: parseFloat(location.longitude)
                            },
                            map: self.map,
                            title: label
                        });
                    });
                }
            });

            ko.computed(function () {
                if (self.selectedLocation()) {
                    // center map on selected location
                    let initialLocation = new google.maps.LatLng(self.selectedLocation().latitude, self.selectedLocation().longitude);
                    self.map.setCenter(initialLocation);
                } else {
                    bopisBrowserLocation.getLocation(
                        // Success CallBack
                        function (position) {
                            if (!self.selectedLocation()) {
                                let initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                                self.map.setCenter(initialLocation);
                            }
                        },
                        // Error CallBack
                        function () {
                            if (self.locationsToShow().length > 0 && !self.selectedLocation()) {
                                let initialLocation = new google.maps.LatLng(self.locationsToShow()[0]['latitude'], self.locationsToShow()[0]['longitude']);
                                self.map.setCenter(initialLocation);
                            }
                        }
                    );
                }
            });
        }
    });
});
