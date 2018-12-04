<?php

namespace Transbank\Onepay\Model\Config;

class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface {

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
                               \Magento\Framework\Module\ModuleListInterface $moduleListInterface) {
        $this->_scopeConfigInterface = $scopeConfigInterface;
        $this->_moduleListInterface = $moduleListInterface;
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
        return $this->_scopeConfigInterface->getValue('payment/transbank_onepay/environment');
    }

    public function getApiKey() {
        $environment = $this->getEnvironment();
        if ($environment == 'LIVE') {
            return $this->_scopeConfigInterface->getValue('payment/transbank_onepay/apiKeyProduction');
        } else {
            return $this->_scopeConfigInterface->getValue('payment/transbank_onepay/apiKeyIntegration');
        }
    }

    public function getSharedSecret() {
        $environment = $this->getEnvironment();
        if ($environment == 'LIVE') {
            return $this->_scopeConfigInterface->getValue('payment/transbank_onepay/sharedSecretProduction');
        } else {
            return $this->_scopeConfigInterface->getValue('payment/transbank_onepay/sharedSecretIntegration');
        }
    }

    public function getLogoUrl() {
        return $this->_scopeConfigInterface->getValue('payment/transbank_onepay/logoUrl');
    }

    public function getVersion() {
        return $this->_moduleListInterface->getOne('Transbank_Onepay')['setup_version'];
    }

    public function getMagentoVersion() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        return $productMetadata->getVersion();
    }

    public function logfileLocation() {
        return BP . '/var/log/onepay-log.log';
    }
}
