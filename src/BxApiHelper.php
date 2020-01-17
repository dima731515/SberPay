<?php declare(strict_types=1);

namespace dima731515\SberPay;

(!$_SERVER["DOCUMENT_ROOT"])?$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www":'';
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

require_once('/home/bitrix/.key.php');


class BxApiHelper
{
    public function __construct()
    {
        if(!\CModule::IncludeModule('iblock')) Throw new Exception('Не удалось подключить модуль Iblock для работы с Битикс');
        if(!\CModule::IncludeModule('sale')) Throw new Exception('Не удалось подключить модуль Iblock для работы с Битикс');
    }

    public static function getInvoiceById(): ?array
    {
        $result   = [];
        $arSelect = [
            'ID',
            'NAME',
            'DETAIL_TEXT',
            'DATE_ACTIVE_TO',
            'PROPERTY_'. INVOICE_AMOUNT_FIELD_CODE,
            'PROPERTY_' . PS_ID_FIELD_CODE, 'PROPERTY_'. PAYED_FIELD_CODE,
            'PROPERTY_' . PAY_DATE_FIELD_CODE
        ];
        $res = \CIBlockElement::getList(['ID'=>'ASC'], ['IBLOCK_ID'=>INVOICE_IBLOCK_ID,'ID'=>$invoiceId], false, ['nTopCount'=>1, 'nPageSize' => 1], $arSelect);

        while($row = $res->fetch()){
            return $row;
        }

        Throw new Exception('Не удалось инициализировать объект данными из Битрикс');
    }

    public static function getOrderById(int $orderId): ?array
    {
        $arOrder = \CSaleOrder::GetByID($orderId);    
        if(!$arOrder) Throw new Exception('Заказ не найден или не удалось загрузить');
        
        $arResult = [
            'ID' => $arOrder['ID'],
            'NAME' => $arOrder['ID'],
            'DETAIL_TEXT' => $arOrder['COMMENTS'],
            'DATE_ACTIVE_TO' => $arOrder['DATE_PAYED'],
            'PROPERTY_'. INVOICE_AMOUNT_FIELD_CODE => $arOrder['PRICE'],
            'PROPERTY_' . PS_ID_FIELD_CODE => 590,
            'PROPERTY_PS_ID_ENUM_ID' => 590,
            'PROPERTY_'. PAYED_FIELD_CODE . '_VALUE' => $arOrder['PAYED'],
            'PROPERTY_' . PAY_DATE_FIELD_CODE . '_VALUE' => $arOrder['DATE_PAYED']
        ];

        return $arResult; 
    }
    /**
     * сохраняет сылку на оплату полученную в сбер в счете Битрикс, чтобы повторно не запрашивать
     * @param string data, все, что прислал Сбер на запрос ссылки на оплату 
     * @return bool
     */
    public static function setPayLinkDataInBxInvoice(string $data) : bool
    {
        $el = new \CIBlockElement;
        $prop = ['DETAIL_TEXT'=>$data];
        $res = $el->Update($this->bxInvoiceData['ID'], $prop);
        return $res;
    }

    public static function setInvoicePayById(int $invoiceId): bool
    {
        $res = \CIBlockElement::SetPropertyValues($invoiceId, INVOICE_IBLOCK_ID, "Y", PAYED_FIELD_CODE);
        $resPayDate = \CIBlockElement::SetPropertyValues($invoiceId, INVOICE_IBLOCK_ID, (new DateTime())->format('d.m.Y H:i:s'), PAY_DATE_FIELD_CODE);
        return true;
    }
    public static function setOrderPayById(int $orderId): bool
    {
        if (!CSaleOrder::PayOrder($orderId, "Y", True, True, 0, array('DATE_PAYED' => new \DateTime() ))) 
            Throw new \Exception('Не удаллось проставить флаг оплаты Заказу с номером: ' . $orderId);
        return true;
    }
}
