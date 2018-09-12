<?php

namespace Transbank\Onepay\Model;

/**
 * Pay In Store payment method model
 */
class Onepay extends \Magento\Payment\Model\Method\AbstractMethod {

    const CODE = 'transbank_onepay';

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Array of currency support
     */
    protected $_supportedCurrencyCodes = array('CLP');

    /**
     * Availability for currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode) {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }
}
