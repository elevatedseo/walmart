define([
    'jquery',
    'mage/utils/wrapper',
    'Walmart_BopisCheckoutPickInStoreFrontend/js/model/pickup-options-service',
    'Magento_Checkout/js/model/address-converter',
    'Magento_InventoryInStorePickupFrontend/js/model/pickup-address-converter',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/checkout-data'
], function (
    $,
    wrapper,
    pickupOptionsService,
    addressConverter,
    pickupAddressConverter,
    selectShippingAddressAction,
    checkoutData
) {
    'use strict';

    return function (originalModule) {
        originalModule.selectForShipping = wrapper.wrapSuper(originalModule.selectForShipping, function (location) {
            const newMethods = checkoutData.hasOwnProperty('setSelectedPickupAddress');
            let street = newMethods ? location.street : [location.street];
            let address = "";
            let unformattedAddress = {
                firstname: location.name,
                lastname: 'Store',
                street: street,
                city: location.city,
                postcode: location.postcode,
                'country_id': location['country_id'],
                telephone: location.telephone ?? location.phone,
                'region_id': location['region_id'],
                'save_in_address_book': 0,
                'extension_attributes': {
                    'pickup_location_code': location['pickup_location_code'],
                    'pickup_option': pickupOptionsService.selectedPickupOption()
                }
            }

            if (!newMethods) {
              address = $.extend(
                    {},
                    addressConverter.formAddressDataToQuoteAddress(unformattedAddress),{
                        /**
                         * Is address can be used for billing
                         *
                         * @return {Boolean}
                         */
                        canUseForBilling: function () {
                            return false;
                        },

                        /**
                         * Returns address type
                         *
                         * @return {String}
                         */
                        getType: function () {
                            return 'store-pickup-address';
                        },
                        'extension_attributes': {
                            'pickup_location_code': location['pickup_location_code'],
                            'pickup_option': pickupOptionsService.selectedPickupOption()
                        }
                    }
                );
            }


            if (newMethods) {
                let formattedAddress = $.extend(
                    {},
                    addressConverter.formAddressDataToQuoteAddress(unformattedAddress));
                address = pickupAddressConverter.formatAddressToPickupAddress(formattedAddress);
            }

            this.selectedLocation(location);
            selectShippingAddressAction(address);
            checkoutData.setSelectedShippingAddress(address.getKey());
            checkoutData.setSelectedShippingRate('instore_pickup');

            if (newMethods) {
                checkoutData.setSelectedPickupAddress(
                    addressConverter.quoteAddressToFormAddressData(address)
                );
            }
        });

        return originalModule;
    };
});
