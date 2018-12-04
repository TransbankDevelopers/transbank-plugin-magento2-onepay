<?php

namespace Transbank\Onepay\Controller\Transaction;

use \Transbank\Onepay\OnepayBase;
use \Transbank\Onepay\ShoppingCart;
use \Transbank\Onepay\Item;
use \Transbank\Onepay\Transaction;
use \Transbank\Onepay\Options;
use \Transbank\Onepay\Exceptions\TransactionCreateException;
use \Transbank\Onepay\Exceptions\TransbankException;

use \Magento\Sales\Model\Order;

/**
 * Controller for commit transaction Onepay
 */
class CommitOnepay extends \Magento\Framework\App\Action\Action {

    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Checkout\Model\Cart $cart,
                                \Magento\Checkout\Model\Session $checkoutSession,
                                \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
                                \Transbank\Onepay\Model\Config\ConfigProvider $configProvider,
                                \Transbank\Onepay\Model\CustomLogger $log) {

        parent::__construct($context);

        $this->_cart = $cart;
        $this->_checkoutSession = $checkoutSession;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_messageManager = $context->getMessageManager();
        $this->_configProvider = $configProvider;
        $this->_log = $log;
    }

    public function execute() {

        //$orderStatusComplete = Order::STATE_COMPLETE;
        $orderStatusComplete = Order::STATE_PROCESSING;
        $orderStatusCanceled = Order::STATE_CANCELED;
        $orderStatusRejected = Order::STATE_CLOSED;

        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $occ = isset($_GET['occ']) ? $_GET['occ'] : null;
        $externalUniqueNumber = isset($_GET['externalUniqueNumber']) ? $_GET['externalUniqueNumber'] : null;

        $metadata = "<br><b>Estado:</b> {$status}
                     <br><b>OCC:</b> {$occ}
                     <br><b>N&uacute;mero de carro:</b> {$externalUniqueNumber}";

        if ($status == null || $occ == null || $externalUniqueNumber == null) {
            return $this->fail($orderStatusCanceled, 'Parametros inválidos', $metadata);
        }

        if ($status == 'PRE_AUTHORIZED') {
            try {

                $apiKey = $this->_configProvider->getApiKey();
                $sharedSecret = $this->_configProvider->getSharedSecret();
                $environment = $this->_configProvider->getEnvironment();

                OnepayBase::setApiKey($apiKey);
                OnepayBase::setSharedSecret($sharedSecret);
                OnepayBase::setCurrentIntegrationType($environment);

                $options = new Options($apiKey, $sharedSecret);

                if ($environment == 'LIVE') {
                    $options->setAppKey('F43FDB87-32BB-4184-AA46-40EA62A8E9F3');
                }

                $transactionCommitResponse = Transaction::commit($occ, $externalUniqueNumber, $options);

                if ($transactionCommitResponse->getResponseCode() == 'OK') {

                    $amount = $transactionCommitResponse->getAmount();
                    $buyOrder = $transactionCommitResponse->getBuyOrder();
                    $authorizationCode = $transactionCommitResponse->getAuthorizationCode();
                    $description = $transactionCommitResponse->getDescription();
                    $issuedAt = $transactionCommitResponse->getIssuedAt();
                    $dateTransaction = date('Y-m-d H:i:s', $issuedAt);

                    $message = "<h3>Detalles del pago con Onepay:</h3>
                                <br><b>Fecha de Transacci&oacute;n:</b> {$dateTransaction}
                                <br><b>OCC:</b> {$occ}
                                <br><b>N&uacute;mero de carro:</b> {$externalUniqueNumber}
                                <br><b>C&oacute;digo de Autorizaci&oacute;n:</b> {$authorizationCode}
                                <br><b>Orden de Compra:</b> {$buyOrder}
                                <br><b>Estado:</b> {$description}
                                <br><b>Monto de la Compra:</b> {$amount}";

                    $installmentsNumber = $transactionCommitResponse->getInstallmentsNumber();

                    if ($installmentsNumber == 1) {

                        $message = $message . "<br><b>N&uacute;mero de cuotas:</b> Sin cuotas";

                    } else {

                        $installmentsAmount = $transactionCommitResponse->getInstallmentsAmount();

                        $message = $message . "<br><b>N&uacute;mero de cuotas:</b> {$installmentsNumber}
                                               <br><b>Monto cuota:</b> {$installmentsAmount}";
                    }

                    $metadata2 = array('amount' => $amount,
                                    'authorizationCode' => $authorizationCode,
                                    'occ' => $occ,
                                    'externalUniqueNumber' => $externalUniqueNumber,
                                    'issuedAt' => $issuedAt);

                    return $this->success($orderStatusComplete, $message, $metadata2);
                } else {
                    return $this->fail($orderStatusRejected, 'Tu pago ha fallado. Vuelve a intentarlo más tarde.', $metadata);
                }

            } catch (TransbankException $transbank_exception) {
                $this->_log->error('Confirmación de transacción fallida: ' . $transbank_exception->getMessage());
                return $this->fail($orderStatusRejected, 'Error en el servicio de pago. Vuelve a intentarlo más tarde.', $metadata);
            }
        } else if($status == 'REJECTED') {
            return $this->fail($orderStatusRejected, 'Tu pago ha fallado. Pago rechazado', $metadata);
        } else {
            return $this->fail($orderStatusCanceled, 'Tu pago ha fallado. Compra cancelada', $metadata);
        }
    }

    private function success($orderStatus, $message, $metadata) {
        $order = $this->getOrder();
        $order->setState($orderStatus)->setStatus($orderStatus);
        $order->addStatusToHistory($order->getStatus(), $message);
        $order->addStatusToHistory($order->getStatus(), json_encode($metadata));

        $payment = $order->getPayment();

        $externalUniqueNumber = $metadata['externalUniqueNumber'];
        $payment->setLastTransId($externalUniqueNumber);
        $payment->setTransactionId($externalUniqueNumber);
        $payment->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array)$metadata]);

        $order->save();
        $this->_messageManager->addSuccess(__($message));
        $this->_checkoutSession->getQuote()->setIsActive(false)->save();
        return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success');
    }

    private function fail($orderStatus, $message, $metadata) {
        $order = $this->getOrder();
        $order->setState($orderStatus)->setStatus($orderStatus);
        $order->addStatusToHistory($order->getStatus(), $message . $metadata);
        $order->save();
        $this->_messageManager->addError(__($message));
        $this->_checkoutSession->restoreQuote();
        $this->_log->error($message . $metadata);
        return $this->resultRedirectFactory->create()->setPath('checkout/cart');
    }

    private function getOrder() {
        $orderId = $this->_checkoutSession->getLastOrderId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
    }
}
