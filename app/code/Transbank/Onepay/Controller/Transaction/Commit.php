<?php
 
namespace Transbank\Onepay\Controller\Transaction;

use Transbank\Onepay\OnepayBase;
use Transbank\Onepay\ShoppingCart;
use Transbank\Onepay\Item;
use Transbank\Onepay\Transaction;
use \Transbank\Onepay\Exceptions\TransactionCreateException;
use \Transbank\Onepay\Exceptions\TransbankException;

use \Transbank\Onepay\Model\Config\ConfigProvider;

/**
 * Test controller: http://localhost/checkout/transaction/commit
 */
class Commit extends \Magento\Framework\App\Action\Action {

    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Checkout\Model\Cart $cart,
                                \Magento\Checkout\Model\Session $checkoutSession,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory) {


        parent::__construct($context);
        
        $this->_cart = $cart;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_messageManager = $context->getMessageManager();
    }
 
    public function execute() {

        $orderStatusComplete = 'complete';
        $orderStatusCanceled = 'canceled';
        $orderStatusRejected = 'closed';

        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $occ = isset($_GET['occ']) ? $_GET['occ'] : null;
        $externalUniqueNumber = isset($_GET['externalUniqueNumber']) ? $_GET['externalUniqueNumber'] : null;

        if ($status == null || $occ == null || $externalUniqueNumber == null) {
            return $this->fail($payError, 'Parametros inválidos');
        }

        if ($status == 'PRE_AUTHORIZED') {
            try {

                $configProvider = new ConfigProvider($this->_scopeConfig);
                $apiKey = $configProvider->getApiKey();
                $sharedSecret = $configProvider->getSharedSecret();
                $environment = $configProvider->getEnvironment();

                OnepayBase::setApiKey($apiKey);
                OnepayBase::setSharedSecret($sharedSecret);
                OnepayBase::setCurrentIntegrationType($environment);

                $transactionCommitResponse = Transaction::commit($occ, $externalUniqueNumber);

                if ($transactionCommitResponse->getResponseCode() == 'OK') {
                    return $this->success($orderStatusComplete, 'Tu pago se ha realizado exitosamente');
                } else {
                    return $this->fail($orderStatusRejected, 'Tu pago ha fallado. Vuelve a intentarlo más tarde.');
                }

            } catch (TransbankException $transbank_exception) {
                return $this->fail($orderStatusRejected, 'Error en el servicio de pago. Vuelve a intentarlo más tarde.');
            }
        } else if($status == 'REJECTED') {
            return $this->fail($orderStatusRejected, 'Tu pago ha fallado. Pago rechazado');
        } else {
            return $this->fail($orderStatusCanceled, 'Tu pago ha fallado. Compra cancelada');
        }
    }

    private function success($orderStatus, $message) {
        $order = $this->getOrder();
        $order->setState($orderStatus)->setStatus($orderStatus);
        $order->save();
        $this->_messageManager->addSuccess(__($message));
        $this->_checkoutSession->getQuote()->setIsActive(false)->save();
        return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success');
    }

    private function fail($orderStatus, $message) {
        $order = $this->getOrder();
        $order->setState($orderStatus)->setStatus($orderStatus);
        $order->save();
        $this->_messageManager->addError(__($message));
        $this->_checkoutSession->restoreQuote();
        return $this->resultRedirectFactory->create()->setPath('checkout/cart');
    }

    private function getOrder() {
        $orderId = $this->_checkoutSession->getLastOrderId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
    }
}