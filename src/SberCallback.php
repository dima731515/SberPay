<?php declare(strict_types=1);

namespace dima731515\SberPay;

use \Exception;

class SberCallback
{
    public  $json  = null;
    private $config = [];
    private $arData = [];
    private $checksum = null; 
    private $orderNumber = null;
    private $mdOrder = null;
    public $operation = null;
    public $status = null;
    public  $isValid = false;


    public function __construct(string $json = null)
    {
        if(!null == $json && !null == $json) $this->initByJson($json); 
    }

    public function initByJson(string $json): bool
    {
        try{
            $this->initByArray(json_decode($json, true)); 
        }catch(Exception $e){
            echo $e->getMessage('не удалось инициализировать ответ'); 
        }

       return true; 
    }

    public function initConfig(array $config): bool
    {
        if(!isset($config['callbackToken']) || empty($config['callbackToken']))
            Throw new Exception('Не верная конфигурация');

        $this->config = $config;
        return true;
    }
    public function getExternalId(): ?string
    {
        if($this->orderNumber)
            return $this->orderNumber; 

        return null;
    }
    public function isPayed(): bool
    {
        if(
           (isset($this->operation) && 'deposited' === $this->operation)
           && (isset($this->status) && '1' === $this->status)
        ){
            return true;
        }
        return false;
    }

    public function validateCheckSum(): bool 
    {
        unset($this->arData['checksum']);
        ksort($this->arData);
        $ar = array_map(function($k, $v){return "$k;$v";},array_keys($this->arData), $this->arData);
        $controlString = implode(';', $ar) . ';';
        $hmac = hash_hmac('sha256', $controlString, $this->config['callbackToken']);
        $hmac = strtoupper($hmac);
        // сравниваем переданный хеш с тем что мы сформировали с помощью токена, если суммы совподают то ок
        if($this->checksum === $hmac)
        {
            $this->isValid = true;
            return true;
        }
        //Throw new Exception('Контрольная сумма не прошла проверку!');
        return false;
    }

    public function initByArray(array $arr): void
    {
        $this->arData = $arr;
        foreach($arr as $key => $val){
            $this->{$key} = $val; 
        }
    }
}
