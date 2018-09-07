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
                                \Magento\Checkout\Model\Session $session,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory) {


        parent::__construct($context);
        
        $this->_cart = $cart;
        $this->_session = $session;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_messageManager = $context->getMessageManager();
    }
 
    public function execute() {
        /*
        callbackUrl : URL que invocara desde el SDK una vez que la transacción ha finalizado. En este callback el comercio debe hacer el confirmación de la transacción, 
            para lo cual dispone de 30 segundos desde que la transacción se autorizo, de lo contrario esta sera automáticamente reversada.

        El callback será invocado via GET e irán los parametros occ y externalUniqueNumber con los cuales podrás invocar la confirmación de la transacción desde tu 
        backend. Adicionalmente se envía el parámetro status el cual puede ser AUTHORIZED, CANCELLED_BY_USER o REJECTED.

        En caso que el págo falle por algúna razón será informado desde el modal y una vez que el usuario precione el boton ENTENDIDO se 
        invocara tu callback con el status de error correspondiente.

        //status (AUTHORIZED, CANCELLED_BY_USER o REJECTED)
        */

        /*
        $orderId = $this->_session->getLastOrderId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
        */
        //var_dump($this->_session->getLastRealOrder());exit;

        $occ = isset($_GET['occ']) ? $_GET['occ'] : null;
        $externalUniqueNumber = isset($_GET['externalUniqueNumber']) ? $_GET['externalUniqueNumber'] : null;

        if ($occ == null || $externalUniqueNumber == null) {
            return $this->fail('Parametros inválidos');
        }

        try {

            $configProvider = new ConfigProvider($this->_scopeConfig);
            $apiKey = $configProvider->getApiKey();
            $sharedSecret = $configProvider->getSharedSecret();
            $environment = $configProvider->getEnvironment();

            OnepayBase::setApiKey($apiKey);
            OnepayBase::setSharedSecret($sharedSecret);
            OnepayBase::setCurrentIntegrationType('TEST');

            $transactionCommitResponse = Transaction::commit($occ, $externalUniqueNumber);

            if ($transactionCommitResponse->getResponseCode() == 'OK') {
                return $this->success();
            } else {
                return $this->fail('Tu pago ha fallado. Vuelve a intentarlo más tarde.');
            }

        } catch (TransbankException $transbank_exception) {
            //die('Confirmación de transacción fallida: ' . $transbank_exception->getMessage());
            return $this->fail('Tu pago ha fallado. Vuelve a intentarlo más tarde.');
        }
    }

    private function success() {
        $this->_messageManager->addSuccess(__('Tu pago se ha realizado exitosamente'));
        return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success');
    }

    private function fail($error) {
        $this->_messageManager->addError(__($error));
        $this->_session->restoreQuote();
        return $this->resultRedirectFactory->create()->setPath('checkout/cart');
    }
}