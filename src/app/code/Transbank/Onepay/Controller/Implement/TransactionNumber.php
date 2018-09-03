<?php

/**
* @author     Allware Ltda. (http://www.allware.cl)
* @copyright  2017 Transbank S.A. (http://www.transbank.cl)
* @license    GNU LGPL
* @version    1.0
*/

namespace Transbank\Onepay\Controller\Implement;

class TransactionNumber extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Transbank\Onepay\Model\FinishTransaction $customer,
        \Magento\Checkout\Model\Session $session,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_customer = $customer;
        $this->_session  = $session;
        $this->_scopeConfig    = $scopeConfig;
        $this->_messageManager = $context->getMessageManager();

        parent::__construct($context);
    }

    public function execute()
    {
        if (isset($_POST['occ'])) {
            $occ = $_POST['occ'];
            $externalUniqueNumber = $_POST['externalUniqueNumber'];
            # code...
        } elseif (isset($_GET['occ'])) {
            $occ = $_GET['occ'];
            $externalUniqueNumber = $_GET['externalUniqueNumber'];
            # code...
        } else {
            $response = $this->failed();
            $this->_messageManager->addError(__($response));

            $order->setState($payError)->setStatus($payError);
            $order->save();

            $this->_session->restoreQuote();
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }


        $this->_session->setOcc($occ);
        $this->_session->setExternalUniqueNumber($externalUniqueNumber);

        $orderId = $this->_session->getLastOrderId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);

        $result   = $this->_customer->getTransactionNumber();
        $result['orderId'] = $orderId;

        $paySucefully = $this->_scopeConfig->getValue('payment/ewallet/sucefully_pay');
        $payError     = $this->_scopeConfig->getValue('payment/ewallet/error_pay');

        //$result = json_decode($result);
        if ($result['responseCode'] == 'OK') {
            $response = $this->Authorized($result);
            $this->_messageManager->addSuccess(__($response));
            $order->setState($paySucefully)->setStatus($paySucefully);
            $order->save();
            $this->_session->getQuote()->setIsActive(false)->save();
            return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success');
        } else {
            $order->addStatusHistoryComment("Onepay: ".$this->_session->getEntityId()." | Descripción: ".$result['description']." | Tu pago ha fallado. Vuelve a intentarlo más tarde");

            $order->setState($payError)->setStatus($payError);
            $order->save();

            $this->_session->restoreQuote();

            $this->_messageManager->addError(__('Tu pago ha fallado. Vuelve a intentarlo más tarde.'));

            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
    }

    public function Authorized($result)
    {
        $result['day'] = date('d-m-Y');
        $result['time'] = date('H:i:s');
        $result['amount'] = $this->_session->getGrandTotal();

        $message = "<h1>Detalle de Pago:</h1>
        <br><b><img src='https://web2desa.test.transbank.cl/tbk-ewallet-client-portal/static/images/logo-tarjetas.png'/></b>
        <br><b>Respuesta de Transacci&oacute;n:</b> {$result['responseCode']}
        <br><b>Fecha de Transacci&oacute;n: </b> {$result['day']}
        <br><b>Hora de Transacci&oacute;n: </b>{$result['time']}
        <br><b>Monto de la Compra: </b>{$result['amount']}
        <br><b>Orden de Compra: </b>{$result['orderId']}
        <br><b>C&oacute;digo de Autorizaci&oacute;n: </b>{$result['authorizationCode']}";


        return $message;
    }

    public function failed()
    {
        return "Error al comunicarse para obtener el numero de transaccion";
    }
}
