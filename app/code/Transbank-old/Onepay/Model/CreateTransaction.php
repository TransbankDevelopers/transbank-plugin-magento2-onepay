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

class CreateTransaction implements ConfigProviderInterface
{
    public function __construct(
      \Magento\Checkout\Model\Cart $cart,
      \Magento\Framework\App\Action\Context $context,
      \Magento\Checkout\Model\Session $session,
      \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
      \Magento\Store\Model\StoreManagerInterface $storeManager
        ) {
        $this->_scopeConfig  = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_cart    = $cart;
        $this->_session = $session;
    }

    public function getConfig()
    {
        return [
             'createTransaction' => $this->getCreateTransaction(),
        ];
    }

    public function getCreateTransaction()
    {
        $appKey      = $this->_scopeConfig->getValue('payment/ewallet/appKey');
        $apiKey      = $this->_scopeConfig->getValue('payment/ewallet/apiKey');
        $secret      = $this->_scopeConfig->getValue('payment/ewallet/secret');
        $environment = $this->_scopeConfig->getValue('payment/ewallet/environment');

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('[ALLWARE] TEEEEEEEEEEEEEEEEEEEEEST');

        $config = new EWalletConfiguration($environment, $appKey, $apiKey, $secret);
        $ew     = new EwalletWebpay($config);

        $iPhone    = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
        $Android   = stripos($_SERVER['HTTP_USER_AGENT'], "Android");
        $userAgent = "WEB";

        if ($iPhone || $Android) {
            $userAgent = "MOBILE";
        }

        $getData       = $this->_cart->getQuote()->getData();
        $getGrandTotal = round($this->_cart->getQuote()->getGrandTotal());
        $n = null;
        for ($i = 0; $i<3; $i++) {
            $n .= mt_rand(0, 9);
        }

        $entity_id = (string)$getData["entity_id"]."_".$n;

        $this->_session->setGrandTotal($getGrandTotal);
        $this->_session->setEntityId($entity_id);

        $request = array(
            "externalUniqueNumber" => $entity_id,
            "total" => (string)$getGrandTotal,
            "quantity" => "1",
            "items" => null,
            "channel" => $userAgent,
            "callbackUrl" => $this->_storeManager->getStore()->getBaseUrl()."transbank/Implement/TransactionNumber"
        );

        $result = $ew->createTransaction($request['externalUniqueNumber'], $request['total'], $request['quantity'], $request['items'], $request['channel'], $request['callbackUrl']);

        $logger->info(json_encode($result));

        $result["base_grand_total"] = (string)$this->_cart->getQuote()->getGrandTotal();
        $result["apiKey"] = $apiKey;

        if ($iPhone) {
            $result["device"] = "iPhone";
        } elseif ($Android) {
            $result["device"] = "Android";
        } else {
            $result["device"] = "Web";
        }

        $result["orderId"] = $entity_id;
        $result["callUrl"] = $this->_storeManager->getStore()->getBaseUrl();

        $resultToArray = (array) $result;
        $resultToJson = json_encode($resultToArray);

        return $resultToJson;
    }
}
