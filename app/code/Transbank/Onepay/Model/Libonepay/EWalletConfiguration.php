<?php

/**
 * @author     Allware Ltda. (http://www.allware.cl)
 * @copyright  2017 Transbank S.A. (http://www.transbank.cl)
 * @license    GNU LGPL
 * @version    1.0
 */

namespace Transbank\Onepay\Model\Libonepay;

class EWalletConfiguration
{
    public $environment = null;
    public $appKey = null;
    public $apiKey = null;
    public $secretKey = null;

    /**
     * Constuctor
     * */
    public function __construct($environment, $appKey, $apiKey, $secretKey)
    {
        $this->environment = $environment;
        $this->appKey = $appKey;
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
    }

    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    public function setAppKey($appKey)
    {
        $this->appKey = $appKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public function getAppKey()
    {
        return $this->appKey;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getSecretKey()
    {
        return $this->secretKey;
    }
}
