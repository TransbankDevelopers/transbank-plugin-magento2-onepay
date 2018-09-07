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
                                \Magento\Checkout\Model\Session $session,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory) {

        parent::__construct($context);

        $this->cart = $cart;
        $this->session = $session;
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
    }
 
    public function execute() {

        $response = null;

        //NOTA: Esto solamente se usa para pruebas durante el desarrollo del plugin, despues se debe eliminar y 
        //usar la linea siguiente donde el valor por defecto es null
        $channel = isset($_POST['channel']) ? $_POST['channel'] : 'WEB';
        //$channel = isset($_POST['channel']) ? $_POST['channel'] : null;
       
        if (isset($channel)) {

            $configProvider = new ConfigProvider($this->scopeConfig);
            $apiKey = $configProvider->getApiKey();
            $sharedSecret = $configProvider->getSharedSecret();
            $environment = $configProvider->getEnvironment();

            OnepayBase::setApiKey($apiKey);
            OnepayBase::setSharedSecret($sharedSecret);
            OnepayBase::setCurrentIntegrationType('TEST');

            $carro = new ShoppingCart();

            //TODO crear el carro de compras con los items reales
            $objeto = new Item('Pelota de futbol', 1, 20000); 
            $carro->add($objeto);

            $externalUniqueNumber = $this->createExternalUniqueNumber();
            $transaction = Transaction::create($carro, $channel, $externalUniqueNumber);

            $response = array(
                'externalUniqueNumber' => $transaction->getExternalUniqueNumber(),
                'amount' => $carro->getTotal(),
                'qrCodeAsBase64' => $transaction->getQrCodeAsBase64(),
                'issuedAt' => $transaction->getIssuedAt(),
                'occ' => $transaction->getOcc(),
                'ott' => $transaction->getOtt(),
                'signature' => $transaction->getSignature()
            );

        } else {

            $response = array(
                'error' => 'Channel param is missing'
            );
        }

        $result = $this->resultJsonFactory->create();
        $result->setData($response);
        return $result;   
    }

    private function createExternalUniqueNumber() {
        $data = $this->cart->getQuote()->getData();
        $externalUniqueNumber = $data['store_id'] . '' . $data['entity_id'] . '' . $data['customer_id'] . '_' . rand(1, 10000000) . '_' . round(microtime(true) * 1000);
        return $externalUniqueNumber;
    }
}