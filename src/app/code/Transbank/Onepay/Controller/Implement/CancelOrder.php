<?php

/**
* @author     Allware Ltda. (http://www.allware.cl)
* @copyright  2017 Transbank S.A. (http://www.transbank.cl)
* @license    GNU LGPL
* @version    1.0
*/

namespace Transbank\Onepay\Controller\Implement;

class CancelOrder extends \Magento\Framework\App\Action\Action {

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

    public function execute() {

      
        $orderId = $this->_session->getLastOrderId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);

        $payError = $this->_scopeConfig->getValue('payment/ewallet/error_pay');

        $order->setState($payError)->setStatus($payError);
        $order->addStatusHistoryComment('Onepay: '.$this->_session->getEntityId().' | Tu pago ha fallado. Vuelve a intentarlo más tarde');
        $order->save();

        $this->_session->restoreQuote();
        $this->_session->clearQuote();
        $this->_session->clearStorage();
        $this->_session->clearHelperData();
        $this->_session->resetCheckout();

        $this->_messageManager->addError(__('Tu pago ha fallado. Vuelve a intentarlo más tarde.'));
        return $this->resultRedirectFactory->create()->setPath('checkout/cart');

    }
}
