<?php

namespace Transbank\Onepay\Model;

use \Transbank\Onepay\OnepayBase;
use \Transbank\Onepay\Refund;
use \Transbank\Onepay\Exceptions\RefundCreateException;

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

    protected $_isGateway = true;
    protected $_canCapture = true;
    //protected $_canCapturePartial = true;
    protected $_canRefund = true;
    //protected $_canRefundInvoicePartial = true;
    protected $_canAuthorize = true;

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

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount) {

        if (!$this->canCapture()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The capture action is not available.'));
        }

        $metadata = $payment->getData()['additional_information'][\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS];
        $payment->setTransactionId($metadata['externalUniqueNumber']);
        $payment->setIsTransactionClosed(0);

        return $this;
    }

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount) {

        if (!$this->canAuthorize()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The authorize action is not available.'));
        }

        return $this;
    }

    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount) {

        if (!$this->canRefund()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The refund action is not available.'));
        }

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/onepay-log.log');
        $log = new \Zend\Log\Logger();
        $log->addWriter($writer);

        try {

            $metadata = $payment->getData()['additional_information'][\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS];

            $log->info('refund req: ' . $amount . ', ' . json_encode($metadata));

            $occ = $metadata['occ'];
            $externalUniqueNumber = $metadata['externalUniqueNumber'];
            $authorizationCode = $metadata['authorizationCode'];

            $apiKey = $this->getApiKey();
            $sharedSecret = $this->getSharedSecret();
            $environment = $this->getEnvironment();

            OnepayBase::setApiKey($apiKey);
            OnepayBase::setSharedSecret($sharedSecret);
            OnepayBase::setCurrentIntegrationType($environment);

            $refund = Refund::create($amount, $occ, $externalUniqueNumber, $authorizationCode);

            $log->info('refund resp: ' . $amount . ', ' . json_encode($refund));

            if ($refund->getResponseCode() != 'OK') {
                throw new RefundCreateException('Error en anular transacción: ' . json_encode($refund));
            }

            $payment->setIsTransactionClosed(1);

        } catch (RefundCreateException $transbank_exception) {
            $log->err('Anulacion de transacción fallida: ' . $transbank_exception->getMessage());
            $payment->setIsTransactionClosed(0);
            throw new \Magento\Framework\Exception\LocalizedException(__($transbank_exception->getMessage()));
        }

        return $this;
    }

    private function getEnvironment() {
        return $this->_scopeConfig->getValue('payment/transbank_onepay/environment');
    }

    private function getApiKey() {
        $environment = $this->getEnvironment();
        if ($environment == 'PRODUCCION') {
            return $this->_scopeConfig->getValue('payment/transbank_onepay/apiKeyProduction');
        } else {
            return $this->_scopeConfig->getValue('payment/transbank_onepay/apiKeyIntegration');
        }
    }

    private function getSharedSecret() {
        $environment = $this->getEnvironment();
        if ($environment == 'PRODUCCION') {
            return $this->_scopeConfig->getValue('payment/transbank_onepay/sharedSecretProduction');
        } else {
            return $this->_scopeConfig->getValue('payment/transbank_onepay/sharedSecretIntegration');
        }
    }
}
