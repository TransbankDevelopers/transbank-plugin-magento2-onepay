<?php
 
namespace Transbank\Onepay\Controller\Transaction;
 
/**
 * Test controller: http://localhost/checkout/transaction/commit
 */
class Commit extends \Magento\Framework\App\Action\Action {

    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Checkout\Model\Cart $cart,
                                \Magento\Checkout\Model\Session $session,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory) {

        $this->cart = $cart;
        $this->session = $session;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }
 
    public function execute() {
        die('Transaction Commit');
    }
}