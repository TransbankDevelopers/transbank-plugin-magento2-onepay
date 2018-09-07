define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url'
    ],
    function ($,
              Component,
              placeOrderAction,
              selectPaymentMethodAction,
              customer,
              checkoutData,
              additionalValidators,
              url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Transbank_Onepay/payment/onepay'
            },

            getCode: function() {
              return 'transbank_onepay';
            },
            isActive: function() {
                return true;
            },
            getTitle: function() {
                return "Transbank Onepay";
            },
            getMailingAddress: function () {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },

            placeOrder: function() {

                console.log("placeOrder", this);

                var config = JSON.parse(window.checkoutConfig.pluginConfig);
                console.log("config", config);

                var options = {
                    endpoint: './transaction/create',
                    commerceLogo: config.logoUrl || '',
                    callbackUrl: './transaction/commit'
                };
                Onepay.checkout(options);
            },

            afterPlaceOrder: function() {
                console.log("afterPlaceOrder", this);
            }
        })
    }
);
