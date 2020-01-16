<?php declare(strict_types=1);
// Устанавливается для каждого сайта 
const DEFAULT_COMPANY_CODE = 'gms';

const SBER_CONFIG = [
    'test' => [
        'sberOptions'=>[
            'userName'   => 'maxlevel_ru-api',
            'password'   => 'maxlevel_ru',
            'apiUri'     => 'https://3dsec.sberbank.ru',
        ],
        'callbackToken'=>'vieb4rj8610efjrrgovqcpbrqq',
        'returnUrl' => 'https://www.maxlevel.ru/sbpay/success.php'
    ],
];
// id инфоблока счетов
const INVOICE_IBLOCK_ID = 61;
// код свойства инфоблока в котором хранится сумма счета
const INVOICE_AMOUNT_FIELD_CODE = 'INVOICE_AMOUNT';
// код свойста инфоблока в котором уазанно Юр лицо счета
const PS_ID_FIELD_CODE = 'PS_ID';
// код свойста инфоблока, флаг оплаты счета
const PAYED_FIELD_CODE  = 'PAYED';
// код свойста инфоблока, дата оплаты счета 
const PAY_DATE_FIELD_CODE  = 'PAY_DATE';
// соотношение списка Юр лиц в Битрикс с кодами для конфигурации 
// ключом является id значения в инфоблоке счетов
const COMPANY_CODE_BY_INVOICE_PS_ID = [
    '176' => 'motors',
    '177' => 'design', 
    '203' => 'eridan',
    '602' => 'maxlevel',
    '603' => 'aurum',
    '604' => 'interno',
    '605' => 'sanexpo',
    '606' => 'sella',
    '590' => 'gms',
    '000' => 'test'
];
const ORDER_TYPES = ['invoice', 'order'];
const LOGER_EMAIL     = 'dima731515@yandex.ru';
const LOGER_PATH      = '';    
const LOGER_FILE_NAME = 'info_sber.log';
