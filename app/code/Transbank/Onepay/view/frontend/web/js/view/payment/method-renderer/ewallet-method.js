    /**
     * Copyright © 2015 Magento. All rights reserved.
     * See COPYING.txt for license details.
     */
    /*browser:true*/
    /*global define*/

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
        'Magento_Payment/js/view/payment/cc-form',
      ],

      function($,
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
            template: 'Transbank_Onepay/payment/ewallet'
          },

          placeOrder: function() {
            

            var self = this,
              placeOrder;
            this.isPlaceOrderActionAllowed(false);
            placeOrder = placeOrderAction(this.getData(), false, this.messageContainer);

            $.when(self).fail(function() {
              self.isPlaceOrderActionAllowed(true);
            }).done(this.afterPlaceOrder.bind(this));

          },

          afterPlaceOrder: function() {
            

            var result = window.checkoutConfig.createTransaction;
            

            result = JSON.parse(result);
            

            

            if (result.responseCode == "OK") {

              if (result.device == "Android") { // Redirección a aplicación Android

                var appScheme = 'ewallet';
                var appPackage = 'cl.ionix.ewallet';
                var action = appPackage + '.BROWSER_ACTION';

                // Url de Google Play de la aplicación
                var fallback = 'market://details?id=' + appPackage;

                // Construcción de la url de apertura de la aplicación
                var location = 'intent://#Intent' +
                  ';scheme=' + appScheme +
                  ';action=' + action +
                  ';package=' + appPackage +
                  ';S.occ=' + result.occ +
                  ';S.browser_fallback_url=' + fallback +
                  ';end';

                window.location = location;

              } else if (result.device == "iPhone") { // Redirección a aplicación iPhone

                var appName = 'onepay';
                var appStoreUrl = ' https://itunes.apple.com/cl/app/onepay/id12345678';
                var now = new Date().valueOf();

                setTimeout(function() {
                  if (new Date().valueOf() - now > 100) return;
                  window.open(appStoreUrl, "_self");
                }, 500);

                window.open(appName + '://?occ=' + result.occ, '_self');

              } else { // Redirección a MiniSitio
                //                var method = 'POST';
                var form = $('<form action="' + result.urlLogin + '" method="post">' +
                  '<input type="text" name="occ" value="' + result.occ + '" />' +
                  '<input type="text" name="apiKey" value="' + result.apiKey + '" />' +
                  '</form>');

                $('body').append(form);
                form.submit();


              }

            } else {

              window.location.href = result.callUrl + 'transbank/Implement/CancelOrder';

            }

          },

        });
      }
    );
