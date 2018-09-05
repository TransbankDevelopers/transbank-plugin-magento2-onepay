<?php

namespace Transbank\Onepay\Model;
use \Magento\Checkout\Model\ConfigProviderInterface;
use \Transbank\Onepay\Transaction;

class ConfigProvider implements  ConfigProviderInterface
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
        $appKey      = $this->_scopeConfig->getValue('payment/transbank_onepay/appKey');
        $apiKey      = $this->_scopeConfig->getValue('payment/transbank_onepay/apiKey');
        $secret      = $this->_scopeConfig->getValue('payment/transbank_onepay/secret');
        $environment = $this->_scopeConfig->getValue('payment/transbank_onepay/environment');

        $result = [
            'appKey' => $appKey,
            'apiKey' => $apiKey,
            'secret' => $secret,
            'environment' => $environment,
            'url' => Transaction::getServiceUrl()
        ];

        return json_encode($result);
    }









}
