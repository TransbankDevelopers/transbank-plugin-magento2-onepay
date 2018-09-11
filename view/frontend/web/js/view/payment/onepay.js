define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component,
              rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'transbank_onepay',
                component: 'Transbank_Onepay/js/view/payment/method-renderer/onepay-method'
            }
        );
        // View logic goes here!
        return Component.extend({});
    }
);

