<?php

namespace Transbank\Onepay\Controller\Transaction;

use \Magento\Framework\App\CsrfAwareActionInterface;
use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\App\Request\InvalidRequestException;


/**
 * Controller for create transaction Onepay
 */
if (interface_exists("\Magento\Framework\App\CsrfAwareActionInterface")) {
    class CreateOnepay extends CreateOnepayM22 implements \Magento\Framework\App\CsrfAwareActionInterface {

        public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
        {
            return null;
        }

        public function validateForCsrf(RequestInterface $request): ?bool
        {
            return true;
        }
    }
} else {
    class CreateOnepay extends CreateOnepayM22 {}
}
