<?php

namespace Transbank\Onepay\Model;
use \Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface {

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
        $this->scopeConfig  = $scopeConfig;
    }

    public function getConfig() {
        return [
            'pluginConfig' => $this->getPluginConfig()
        ];
    }

    public function getPluginConfig() {

        $environment = $this->scopeConfig->getValue('payment/transbank_onepay/environment');
        $apiKey = $this->scopeConfig->getValue('payment/transbank_onepay/apiKey');
        $secret = $this->scopeConfig->getValue('payment/transbank_onepay/secret');
        $logoUrl = $this->scopeConfig->getValue('payment/transbank_onepay/logoUrl');

        $result = [
            'environment' => $environment,
            'apiKey' => $apiKey,
            'secret' => $secret,
            'logoUrl' => $logoUrl
        ];

        return json_encode($result);
    }
}
