define([
    'mage/utils/wrapper',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/address-converter',
    'Magento_InventoryInStorePickupFrontend/js/model/pickup-address-converter',
    'underscore'
], function (
    wrapper,
    addressList,
    checkoutData,
    selectShippingAddress,
    addressConverter,
    pickupAddressConverter,
    _
) {
    'use strict';

    return function (checkoutDataResolver) {

        if(checkoutDataResolver.getShippingAddressFromCustomerAddressList === undefined) {

            checkoutDataResolver.resolveShippingAddress = wrapper.wrapSuper(
                checkoutDataResolver.resolveShippingAddress,
                function () {
                    var shippingAddress,
                        pickUpAddress;

                    if (checkoutData.getSelectedPickupAddress() && checkoutData.getSelectedShippingAddress()) {
                        shippingAddress = addressConverter.formAddressDataToQuoteAddress(
                            checkoutData.getSelectedPickupAddress()
                        );
                        pickUpAddress = pickupAddressConverter.formatAddressToPickupAddress(
                            shippingAddress
                        );

                        if (pickUpAddress.getKey() === checkoutData.getSelectedShippingAddress()) {
                            selectShippingAddress(pickUpAddress);

                            return;
                        }
                    }
                    this._super();
                });

            checkoutDataResolver.getShippingAddressFromCustomerAddressList = function () {
                var shippingAddress = _.find(
                    addressList(),
                    function (address) {
                        return checkoutData.getSelectedShippingAddress() == address.getKey() //eslint-disable-line
                    }
                );

                if (!shippingAddress) {
                    shippingAddress = _.find(
                        addressList(),
                        function (address) {
                            return address.isDefaultShipping();
                        }
                    );
                }

                if (!shippingAddress && addressList().length === 1) {
                    shippingAddress = addressList()[0];
                }

                return shippingAddress;
            }
        }

        return checkoutDataResolver;
    }

});
