<?php
 
namespace Transbank\Onepay\Controller\Transaction;

use \Transbank\Onepay\OnepayBase;
use \Transbank\Onepay\ShoppingCart;
use \Transbank\Onepay\Item;
use \Transbank\Onepay\Transaction;
use \Transbank\Onepay\Exceptions\TransactionCreateException;
use \Transbank\Onepay\Exceptions\TransbankException;

use \Transbank\Onepay\Model\Config\ConfigProvider;

/**
 * Test controller: http://localhost/checkout/transaction/create
 */
class Create extends \Magento\Framework\App\Action\Action {

    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Checkout\Model\Cart $cart,
                                \Magento\Checkout\Model\Session $checkoutSession,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
                                \Magento\Quote\Model\QuoteManagement $quoteManagement) {

        parent::__construct($context);

        $this->_cart = $cart;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_quoteManagement = $quoteManagement;
    }
 
    public function execute() {

        $response = null;

        //NOTA: Esto solamente se usa para pruebas durante el desarrollo del plugin, despues se debe eliminar y 
        //usar la linea siguiente donde el valor por defecto es null
        $channel = isset($_POST['channel']) ? $_POST['channel'] : 'WEB';
        //$channel = isset($_POST['channel']) ? $_POST['channel'] : null;
       
        if (isset($channel)) {

            $configProvider = new ConfigProvider($this->_scopeConfig);
            $apiKey = $configProvider->getApiKey();
            $sharedSecret = $configProvider->getSharedSecret();
            $environment = $configProvider->getEnvironment();

            OnepayBase::setApiKey($apiKey);
            OnepayBase::setSharedSecret($sharedSecret);
            OnepayBase::setCurrentIntegrationType($environment);

            $quote = $this->_cart->getQuote();

            //$quote->reserveOrderId();
            //$id = $quote->getReservedOrderId();

            $quote->getPayment()->importData(['method' => 'checkmo']);
            $quote->collectTotals()->save();
            $order = $this->_quoteManagement->submit($quote);

            $orderStatus = 'pending_payment';
            $order->setState($orderStatus)->setStatus($orderStatus);
            $order->save();

            $this->_checkoutSession->setLastQuoteId($quote->getId());
            $this->_checkoutSession->setLastSuccessQuoteId($quote->getId());
            $this->_checkoutSession->setLastOrderId($order->getId());
            $this->_checkoutSession->setLastRealOrderId($order->getIncrementId());
            $this->_checkoutSession->setLastOrderStatus($order->getStatus());
            $this->_checkoutSession->setGrandTotal(round($quote->getGrandTotal()));

            $carro = new ShoppingCart();

            $items = $quote->getAllVisibleItems();

            foreach($items as $qItem) {
                $item = new Item($qItem->getName(), intval($qItem->getQty()), intval($qItem->getPrice())); 
                $carro->add($item);
            }

            $shippingAmount = $quote->getShippingAddress()->getShippingAmount();

            if ($shippingAmount != 0) {
                $item = new Item("Costo por envio", 1, intval($shippingAmount));
                $carro->add($item);
            }

            $transaction = Transaction::create($carro, $channel);

            $response = array(
                'externalUniqueNumber' => $transaction->getExternalUniqueNumber(),
                'amount' => $carro->getTotal(),
                'qrCodeAsBase64' => $transaction->getQrCodeAsBase64(),
                'issuedAt' => $transaction->getIssuedAt(),
                'occ' => $transaction->getOcc(),
                'ott' => $transaction->getOtt()
            );

        } else {

            $response = array(
                'error' => 'Falta parámetro channel'
            );
        }

        $result = $this->_resultJsonFactory->create();
        $result->setData($response);
        return $result;   
    }
}