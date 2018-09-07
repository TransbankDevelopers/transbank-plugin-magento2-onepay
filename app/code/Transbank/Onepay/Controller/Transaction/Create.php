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

        $apiKey = $this->scopeConfig->getValue('payment/transbank_onepay/apiKey');
        $secret = $this->scopeConfig->getValue('payment/transbank_onepay/secret');
        $environment = $this->scopeConfig->getValue('payment/transbank_onepay/environment');

        //NOTA: Esto solamente se usa para pruebas durante el desarrollo del plugin, despues se debe eliminar y 
        //usar la linea siguiente donde el valor por defecto es null
        $channel = isset($_POST['channel']) ? $_POST['channel'] : 'WEB';
        //$channel = isset($_POST['channel']) ? $_POST['channel'] : null;
       
        if (isset($channel)) {

            $options = new Options($apiKey, $secret);

            $carro = new ShoppingCart();

            //TODO crear el carro de compras con los items reales

            $objeto = new Item('Pelota de futbol', 1, 20000); 
            $carro->add($objeto);

            $amount = 10000; //TODO obtener el amount real

            $transaction = Transaction::create($carro, $channel, $options);
            
            $response = array(
                'externalUniqueNumber' => $transaction->getExternalUniqueNumber(),
                'amount' => $amount,
                'qrCodeAsBase64' => $transaction->getQrCodeAsBase64(),
                'issuedAt' => $transaction->getIssuedAt(),
                'occ' => $transaction->getOcc(),
                'ott' => $transaction->getOtt()
            );

        } else {

            //NOTA: Esto solamente se usa para pruebas durante el desarrollo del plugin, despues se debe eliminar
            $response = array(
                'error' => 'Channel param is missing',
                'apiKey' => $apiKey,
                'secret' => $secret,
                'environment' => $environment,
                'channel' => $channel
            );
        }

        $result = $this->resultJsonFactory->create();
        $result->setData($response);
        return $result;   
    }
}