<?php

namespace Transbank\Onepay\Model\Config;

class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface {

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
        $this->scopeConfig  = $scopeConfig;
    }

    public function getConfig() {
        return [
            'pluginConfig' => $this->getPluginConfig()
        ];
    }

    public function getPluginConfig() {
        $result = [
            'environment' => $this->getEnvironment(),
            'apiKey' => $this->getApiKey(),
            'sharedSecret' => $this->getSharedSecret(),
            'logoUrl' => $this->getLogoUrl()
        ];
        return json_encode($result);
    }

    public function getEnvironment() { 
        return $this->scopeConfig->getValue('payment/transbank_onepay/environment');
    }

    public function getApiKey() {
        $environment = $this->getEnvironment();
        if ($environment == 'PRODUCCION') {
            return $this->scopeConfig->getValue('payment/transbank_onepay/apiKeyProduction');
        } else {
            return $this->scopeConfig->getValue('payment/transbank_onepay/apiKeyIntegration');
        }
    }

    public function getSharedSecret() {
        $environment = $this->getEnvironment();
        if ($environment == 'PRODUCCION') {
            return $this->scopeConfig->getValue('payment/transbank_onepay/sharedSecretProduction');
        } else {
            return $this->scopeConfig->getValue('payment/transbank_onepay/sharedSecretIntegration');
        }
    }

    public function getLogoUrl() { 
        return $this->scopeConfig->getValue('payment/transbank_onepay/logoUrl');
    }
}
