<?php

# Seguridad
defined('INDEX_DIR') OR exit('Carwill software says .i.');
/**
 * Vedic Rishi Client for consuming Vedic Rishi Astro Web APIs
 * http://www.vedicrishiastro.com/astro-api/
 * Author: Chandan Tiwari
 * Date: 06/12/14
 * Time: 5:42 PM
 */

final class  AstroApi extends Models implements OCREND {

    private $userId = null;
    private $apiKey = null;
    private $apiEndPoint = "http://api.vedicrishiastro.com/v1";

    public function __construct() {
       parent::__construct();

       $this->userId =  600203;
       $this->apiKey = '683d23634e1376e7cccfaf6b9e16d84d';
  }


    private function getCurlReponse($resource, array $data)
    {
        $serviceUrl = $this->apiEndPoint.$resource;
        $authData = $this->userId.":".$this->apiKey;

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $serviceUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $header[] = 'Authorization: Basic '. base64_encode($authData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

 final  public function call($resourceName, $data)
    {
        $resData = $this->getCurlReponse($resourceName, $data);
        return $resData;
    }



//------------------------------------------------------------------------------
  public function __destruct() {
    parent::__destruct();
  }

}