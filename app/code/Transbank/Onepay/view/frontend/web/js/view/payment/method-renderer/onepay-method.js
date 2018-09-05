define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url',
        'Magento_Payment/js/view/payment/cc-form'
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
                console.log("placeOrder");
                console.log(this);
                var result = window.checkoutConfig.createTransaction;


                result = JSON.parse(result);
                console.log("RESULTADO PLACEORDER");
                console.log(result);

            },

            afterPlaceOrder: function() {
                console.log("after place order");
                console.log(this);








            }
        })
    }
);
