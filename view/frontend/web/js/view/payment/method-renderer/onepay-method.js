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
        'Magento_Checkout/js/model/quote'
    ],
    function ($,
              Component,
              placeOrderAction,
              selectPaymentMethodAction,
              customer,
              checkoutData,
              additionalValidators,
              url,
              quote) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Transbank_Onepay/payment/onepay'
            },

            getCode: function() {
              return 'transbank_onepay';
            },
            getTitle: function() {
                return "Transbank Onepay";
            },
            getMailingAddress: function () {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },
            placeOrder: function() {

                var config = JSON.parse(window.checkoutConfig.pluginConfig);

                var endpoint = './transaction/create';

                if (quote.guestEmail) {
                    endpoint+='?guestEmail=' + encodeURIComponent(quote.guestEmail);
                }

                var options = {
                    endpoint: endpoint,
                    commerceLogo: config.logoUrl || '',
                    callbackUrl: './transaction/commit'
                };
                Onepay.checkout(options);
            }
        })
    }
);
