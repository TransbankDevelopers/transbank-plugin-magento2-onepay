<?php

namespace Transbank\Onepay\Model;

/**
 * Custom logger
 */
class CustomLogger {

    public function __construct(\Transbank\Onepay\Model\Config\ConfigProvider $configProvider) {
        $writer = new \Zend\Log\Writer\Stream($configProvider->logfileLocation());
        $this->_logger = new \Zend\Log\Logger();
        $this->_logger->addWriter($writer);
    }

    public function info($msg) {
        $this->_logger->info($msg);
    }

    public function error($msg) {
        $this->_logger->err($msg);
    }
}