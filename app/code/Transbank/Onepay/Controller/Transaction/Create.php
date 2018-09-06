<?php
 
namespace Transbank\Onepay\Controller\Transaction;

use \Transbank\Onepay\ShoppingCart;
use \Transbank\Onepay\Item;
use \Transbank\Onepay\Transaction;
use \Transbank\Onepay\ChannelEnum;
use \Transbank\Onepay\Options;

/**
 * Test controller: http://localhost/checkout/transaction/create
 */
class Create extends \Magento\Framework\App\Action\Action {

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

        $response = null;

        $channel = isset($_POST['channel']) ? $_POST['channel'] : 'WEB';
       
        if (isset($channel)) {

            $apiKey = $this->scopeConfig->getValue('payment/transbank_onepay/apiKey');
            $secret = $this->scopeConfig->getValue('payment/transbank_onepay/secret');

            $options = new Options($apiKey, $secret);

            $carro = new ShoppingCart();

            # description, quantity, amount;
            $objeto = new Item('Pelota de futbol', 1, 20000); 
            $carro->add($objeto);

            $transaction = Transaction::create($carro, $channel, $options);

            //retorno para pruebas hasta que funcione...
            $response = array(
                'externalUniqueNumber' => '38bab443-c55b-4d4e-86fa-8b9f4a2d2d13',
                'amount' => 88000,
                'qrCodeAsBase64' => 'QRBASE64STRING',
                'issuedAt' => '1534216134',
                'occ' => '1808534370011282',
                'ott' => '89435749',
                'apiKey' => $apiKey,
                'secret' => $secret,
                'channel' => $channel,
                'transaction' => $transaction
            );
            /*
            $response = array(
                'externalUniqueNumber' => $transaction->getExternalUniqueNumber(),
                'amount' => 88000,
                'qrCodeAsBase64' => $transaction->getQrCodeAsBase64(),
                'issuedAt' => $transaction->getIssuedAt(),
                'occ' => $transaction->getOcc(),
                'ott' => $transaction->getOtt()
            );
            */

        } else {

            $response = array(
                'error' => 'Channel param is missing'
            );
        }

        $result = $this->resultJsonFactory->create();
        $result->setData($response);
        return $result;   
    }
}