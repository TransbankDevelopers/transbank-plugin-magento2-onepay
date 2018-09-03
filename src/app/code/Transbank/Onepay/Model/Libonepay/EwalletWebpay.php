<?php

/**
 * @author     Allware Ltda. (http://www.allware.cl)
 * @copyright  2017 Transbank S.A. (http://www.transbank.cl)
 * @license    GNU LGPL
 * @version    1.0
 */

namespace Transbank\Onepay\Model\Libonepay;

class EwalletWebpay
{
    public $appKey = null;
    public $apiKey = null;
    public $secret = null;
    public $urlCreate = null;
    public $urlGet = null;
    public $urlNull = null;
    public $urlPortal = null;


    public $urls = array(
          "INTEGRACION" => array(
              "create" => "https://onepay.ionix.cl/ewallet-plugin-api-services/services/transactionservice/sendtransaction",
              "get" => "https://onepay.ionix.cl/ewallet-plugin-api-services/services/transactionservice/gettransactionnumber",
              "nullify" => "https://onepay.ionix.cl/ewallet-plugin-api-services/services/transactionservice/nullifytransaction",
              "portal" => "https://onepay.ionix.cl/tbk-ewallet-payment-login/"
          ),

          "PRODUCCION" => array(
              "create" => "https://www.onepay.cl/ewallet-plugin-api-services/services/transactionservice/sendtransaction",
              "get" => "https://www.onepay.cl/ewallet-plugin-api-services/services/transactionservice/gettransactionnumber",
              "nullify" => "https://www.onepay.cl/ewallet-plugin-api-services/services/transactionservice/nullifytransaction",
              "portal" => "https://www.onepay.cl/tbk-ewallet-payment-login/"
          )
      );

    /**
     * Constuctor
     * */
    public function __construct($config)
    {
        $this->appKey = $config->getAppKey();
        $this->apiKey = $config->getApiKey();
        $this->secret = $config->getSecretKey();
        $this->urlCreate = $this->urls[$config->getEnvironment()]["create"];
        $this->urlGet = $this->urls[$config->getEnvironment()]["get"];
        $this->urlNull = $this->urls[$config->getEnvironment()]["nullify"];
        $this->urlPortal = $this->urls[$config->getEnvironment()]["portal"];
    }

    /**
     * @return string hash
     */
    private function toStringHasheable($params)
    {
        $string  = strlen($params['externalUniqueNumber']) . $params['externalUniqueNumber'];
        $string .= strlen($params['total'])                . $params['total'];
        $string .= strlen($params['itemsQuantity'])        . $params['itemsQuantity'];
        $string .= strlen($params['issuedAt'])             . $params['issuedAt'];
        $string .= strlen($params['callbackUrl'])          . $params['callbackUrl'];
        return $string;
    }

    private function toStringHasheableReturn($params)
    {
        $string = strlen($params['result']['occ'])                . $params['result']['occ'];
        $string .= strlen($params['result']['externalUniqueNumber']) . $params['result']['externalUniqueNumber'];
        $string .= strlen($params['result']['issuedAt'])             . $params['result']['issuedAt'];
        return $string;
    }

    private function toStringHasheableAuth($params)
    {
        $string = strlen($params['occ'])                . $params['occ'];
        $string .= strlen($params['externalUniqueNumber']) . $params['externalUniqueNumber'];
        $string .= strlen($params['issuedAt'])             . $params['issuedAt'];
        return $string;
    }

    private function toStringHasheableAuthReturn($params)
    {
        $string = strlen($params['result']['occ']) . $params['result']['occ'];
        $string .= strlen($params['result']['authorizationCode']) . $params['result']['authorizationCode'];
        $string .= strlen($params['result']['issuedAt']) . $params['result']['issuedAt'];
        $string .= strlen($params['result']['amount']) . $params['result']['amount'];
        //$string .= strlen($params['result']['transactionDesc']) . $params['result']['transactionDesc'];
        $string .= strlen($params['result']['installmentsAmount']) . $params['result']['installmentsAmount'];
        $string .= strlen($params['result']['installmentsNumber']) . $params['result']['installmentsNumber'];
        $string .= strlen($params['result']['buyOrder']) . $params['result']['buyOrder'];
        return $string;
    }

    private function toStringHasheableNull($params)
    {
        $string = strlen($params['occ'])                . $params['occ'];
        $string .= strlen($params['externalUniqueNumber']) . $params['externalUniqueNumber'];
        $string .= strlen($params['authorizationCode']) . $params['authorizationCode'];
        $string .= strlen($params['issuedAt'])             . $params['issuedAt'];
        $string .= strlen($params['nullifyAmount'])             . $params['nullifyAmount'];
        return $string;
    }

    private function toStringHasheableNullReturn($params)
    {
        $string = strlen($params['result']['occ']) . $params['result']['occ'];
        $string .= strlen($params['result']['externalUniqueNumber']) . $params['result']['externalUniqueNumber'];
        $string .= strlen($params['result']['reverseCode']) . $params['result']['reverseCode'];
        $string .= strlen($params['result']['issuedAt']) . $params['result']['issuedAt'];
        return $string;
    }


    /**
     * @param $key
     * @param $data
     * @return string signature
     */
    private function signData($key, $data)
    {
        $pre_signature = hash_hmac('sha256', $data, $key, true);
        $signature = base64_encode($pre_signature);
        return $signature;
    }

    public function redirect($url, $data)
    {
        echo  "<form action='" . $url . "' method='POST' name='webpayForm'>";
        foreach ($data as $name => $value) {
            echo "<input type='hidden' name='".htmlentities($name)."' value='".htmlentities($value)."'>";
        }
        echo  "</form>"
               ."<script language='JavaScript'>"
               ."document.webpayForm.submit();"
               ."</script>";
    }

    public function createTransaction($eun, $total, $quantity, $items, $channel, $callbackUrl)
    {
        if (!isset($items)) {
            $items = array(
                  array(
                      "description" => "Producto autogenerado",
                      "quantity" => $quantity,
                      "amount" => $total,
                      "additionalData" => null,
                      "expire" => null
                  )
              );
        }

        $data = array(
              "appKey" => $this->appKey,
              "apiKey" => $this->apiKey,
              "externalUniqueNumber" => $eun,
              "total" => $total,
              "itemsQuantity" => $quantity,
              "issuedAt" => time(),
              "signature" => "",
              "items" => $items,
              "callbackUrl" => $callbackUrl,
              "channel" => $channel
          );

        $str = $this->toStringHasheable($data);

        $signature = $this->signData($this->secret, $str);
        $data['signature'] = $signature;
        $data_string = json_encode($data);

        $result = file_get_contents($this->urlCreate, null, stream_context_create(array(
          'http' => array(
          'method' => 'POST',
          'header' => 'Content-Type: application/json' . "\r\n"
          . 'Content-Length: ' . strlen($data_string) . "\r\n",
          'content' => $data_string,
          ),
          )));


        $res = json_decode($result, true);
        $str2 = $this->toStringHasheableReturn($res);
        $signature2 = $this->signData($this->secret, $str2);


        if ($res['responseCode'] == "OK") {
            if ($signature2 == $res['result']['signature']) {
                $json['responseCode'] = $res['responseCode'];
                $json['description'] = $res['description'];
                $json['occ'] = $res['result']['occ'];
                $json['urlLogin'] = $this->urlPortal;
            } else {
                $json['responseCode'] = "ERROR";
                $json['description'] = "Firma de respuesta invalida";
            }
        } else {
            $json['responseCode'] = "ERROR";
            $json['description'] = $res['description'];
        }
        return $json;
    }

    public function getTransactionNumber($occ, $eun)
    {
        $data = array(
              "appKey" => $this->appKey,
              "apiKey" => $this->apiKey,
              "occ" => $occ,
              "externalUniqueNumber" => $eun,
              "issuedAt" => time(),
              "signature" => ""
          );

        $str = $this->toStringHasheableAuth($data);
        $signature = $this->signData($this->secret, $str);
        $data['signature'] = $signature;
        $data_string = json_encode($data);

        $result = file_get_contents($this->urlGet, null, stream_context_create(array(
          'http' => array(
          'method' => 'POST',
          'header' => 'Content-Type: application/json' . "\r\n"
          . 'Content-Length: ' . strlen($data_string) . "\r\n",
          'content' => $data_string,
          ),
          )));

        $res = json_decode($result, true);
        $str2 = $this->toStringHasheableAuthReturn($res);
        $signature2 = $this->signData($this->secret, $str2);

        if ($res['responseCode'] == "OK") {
            if ($signature2 == $res['result']['signature']) {
                $json['responseCode'] = $res['responseCode'];
                $json['description'] = $res['description'];
                $json['authorizationCode'] = $res['result']['authorizationCode'];
            } else {
                $json['responseCode'] = "ERROR";
                $json['description'] = "Firma de respuesta invalida";
            }
        } else {
            $json['responseCode'] = "ERROR";
            $json['description'] = $res['description'];
        }
        return $json;
    }

    public function nullifyTransaction($occ, $eun, $authCode, $total)
    {
        $data = array(
              "appKey" => $this->appKey,
              "apiKey" => $this->apiKey,
              "occ" => $occ,
              "externalUniqueNumber" => $eun,
              "authorizationCode" => $authCode,
              "nullifyAmount" => $total,
              "issuedAt" => time(),
              "signature" => ""
          );

        $str = $this->toStringHasheableNull($data);
        $signature = $this->signData($this->secret, $str);
        $data['signature'] = $signature;
        $data_string = json_encode($data);

        $result = file_get_contents($this->urlNull, null, stream_context_create(array(
          'http' => array(
          'method' => 'POST',
          'header' => 'Content-Type: application/json' . "\r\n"
          . 'Content-Length: ' . strlen($data_string) . "\r\n",
          'content' => $data_string,
          ),
          )));

        $res = json_decode($result, true);
        $str2 = $this->toStringHasheableNullReturn($res);
        $signature2 = $this->signData($this->secret, $str2);

        if ($signature2 == $res['result']['signature']) {
            $json['responseCode'] = $res['responseCode'];
            $json['description'] = $res['description'];
            $json['reverseCode'] = $res['result']['reverseCode'];
        } else {
            $json['responseCode'] = "ERROR";
            $json['description'] = "Firma de respuesta invalida";
        }
        return $json;
    }
}
