<?php

/**
* @author     Allware Ltda. (http://www.allware.cl)
* @copyright  2017 Transbank S.A. (http://www.transbank.cl)
* @license    GNU LGPL
* @version    1.0
*/

namespace Transbank\Onepay\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Transbank\Onepay\Model\Libonepay\EwalletWebpay;
use Transbank\Onepay\Model\Libonepay\EWalletConfiguration;

class FinishTransaction
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $session
     ) {

        $this->_scopeConfig = $scopeConfig;
        $this->_session = $session;

    }

    public function getTransactionNumber(){

        $appKey      = $this->_scopeConfig->getValue('payment/ewallet/appKey');
        $apiKey      = $this->_scopeConfig->getValue('payment/ewallet/apiKey');
        $secret      = $this->_scopeConfig->getValue('payment/ewallet/secret');
        $environment = $this->_scopeConfig->getValue('payment/ewallet/environment');

        $config = new EWalletConfiguration($environment,$appKey,$apiKey,$secret);
        $ew = new EwalletWebpay($config);

        $occ = $this->_session->getOcc();
        $externalUniqueNumber = $this->_session->getExternalUniqueNumber();

        $result = $ew->getTransactionNumber($occ,$externalUniqueNumber);

        return $result;

    }


}
