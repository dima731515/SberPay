<?php declare(strict_types=1);

//use PHPUnit\Framework\TestCase;
use dima731515\tests\TestCase;
use dima731515\SberPay\SberPayClient;
use dima731515\SberPay\SberCallback;

final class SberHowUseTest extends TestCase
{
    public  $mock = null;

    /**
     * @var array
     * массив ответа битрикс при запросе счета
     */
    private $bxInvoiceArray = [
        'ID' => '1',
        'NAME' => '12123',
        'DETAIL_TEXT' => '{"orderId":"06e08e8d-170f-79e1-8325-cd7b0014591f","formUrl":"https:\/\/securepayments.sberbank.ru\/payment\/merchants\/sbersafe_id\/payment_ru.html?mdOrder=06e08e8d-170f-79e1-8325-cd7b0014591f"}',
        'DATE_ACTIVE_TO' => '',
        'PROPERTY_INVOICE_AMOUNT_VALUE' => '1200.20',
        'PROPERTY_PS_ID_VALUE' => '12',
        'PROPERTY_PAYED_VALUE' => 'N',
        'PROPERTY_PAY_DATE_VALUE' => '',
//        'COMPANY_CODE' => 'test',
        'PROPERTY_PS_ID_ENUM_ID' => 000,
    ];

    /**
     * @var string 
     * json объект ответа Сбербанка 
     */
//    private $sberJsonCallBack = '{"checksum":"BB9A5DC898190E99B573C37AFA02E227F9E84141F9B87DAFD56E6DC6D197F2CF","orderNumber":"326180-invoice","mdOrder":"abba5cd4-2b19-7616-b9be-cd580014591f","operation":"deposited","status":"1"}';
//  private $sberJsonCallBack = '{"checksum":"3E08174561913D9BF1F30D2F144E0B2EE99BCD5DCE0035383558777DBA694814","orderNumber":"326133-invoice","mdOrder":"0cf9e68a-1028-70c0-98ce-0fb001e0fe02","operation":"deposited","status":"1"}';
//    private $sberJsonCallBack1 = '{"checksum":"3E08174561913D9BF1F30D2F144E0B2EE99BCD5DCE0035383558777DBA694814","orderNumber":"1-invoice","mdOrder":"0cf9e68a-1028-70c0-98ce-0fb001e0fe02","operation":"deposited","status":"1"}';

    public function setUp() : void
    {
        $this->mock = $this->getMockBuilder(dima731515\SberPay\SberPayClient::class)
             ->setMethods(['getBxInvoiceById', 'pay'])
             ->getMock();

        $this->mock
             ->method('getBxInvoiceById')
             ->willReturn($this->bxInvoiceArray);

        $this->mock
             ->method('pay')
             ->willReturn( true );

        $this->logFile = '' . ( new DateTime() )->format('d-m-Y') . '_info_sber.log';
        $this->sberInvoiceJson = '{"checksum":"BB9A5DC898190E99B573C37AFA02E227F9E84141F9B87DAFD56E6DC6D197F2CF","orderNumber":"326180-invoice","mdOrder":"abba5cd4-2b19-7616-b9be-cd580014591f","operation":"deposited","status":"1"}';

//        $this->sberInvoiceJson = '{"checksum":"3E08174561913D9BF1F30D2F144E0B2EE99BCD5DCE0035383558777DBA694814","orderNumber":"test-326133-invoice","mdOrder":"0cf9e68a-1028-70c0-98ce-0fb001e0fe02","operation":"deposited","status":"1"}';
//        $this->sberNoOrderNumberInvoiceJson = '{"checksum":"3E08174561913D9BF1F30D2F144E0B2EE99BCD5DCE0035383558777DBA694814","orderNumber":"","mdOrder":"0cf9e68a-1028-70c0-98ce-0fb001e0fe02","operation":"deposited","status":"1"}';
//        $this->sberJsonCallBack = '{"checksum":"3E08174561913D9BF1F30D2F144E0B2EE99BCD5DCE0035383558777DBA694814","orderNumber":"326133-invoice","mdOrder":"0cf9e68a-1028-70c0-98ce-0fb001e0fe02","operation":"deposited","status":"1"}';
    }

    public function tearDown(): void
    {
       if( file_exists($this->logFile) ) unlink($this->logFile);
    }

    public function testConstruct(): void
    {
        $testObject = new SberPayClient(); 
        $this->assertInstanceOf('Monolog\Logger', $testObject->logger);
    }

    public function testLoger(): void
    {
        $testObject = new SberPayClient(); 
        $testObject->logger->info('test message');
        $this->assertFileExists($this->logFile);
    }
    /**
     * test decodeExternalId()
     */
    public function testDecodeExternalId(): void
    {
        $testObject = new SberPayClient();
        $str = 'motors-123132-invoice';
        $result = $testObject->decodeExternalId($str);

        $this->assertIsArray($result); 
        $this->assertEquals(3, count($result));
        $this->assertEquals('123132', $this->invokeProperty($testObject, 'orderNum') );
        $this->assertEquals('motors', $this->invokeProperty($testObject, 'orderCompanyCode') );
    }
    public function testEncodeExternalId(): void
    {
        $testObject = new SberPayClient();
        $this->invokeProperty($testObject, 'orderNum', '123132');
        $this->invokeProperty($testObject, 'orderType', 'invoice');
        $result  = $this->invokeMethod($testObject, 'encodeExternalId', []);

        $this->assertEquals('123132-invoice', $result);
    }
    public function testEncodeExternalIdCompanyCode(): void
    {
        $testObject = new SberPayClient();
        $this->invokeProperty($testObject, 'orderCompanyCode', 'motors');
        $this->invokeProperty($testObject, 'orderNum', '123132');
        $this->invokeProperty($testObject, 'orderType', 'invoice');
        $result  = $this->invokeMethod($testObject, 'encodeExternalId', []);

        $this->assertEquals('motors-123132-invoice', $result);
    
    }
    public function testEncodeExternalIdNoTypeAndCompany(): void
    {
        $this->expectException(\Exception::class);

        $testObject = new SberPayClient();
        $this->invokeProperty($testObject, 'orderNum', '123132');
        //$this->invokeProperty($testObject, 'orderType', 'invoice');
        $result  = $this->invokeMethod($testObject, 'encodeExternalId', []);
    }

    public function testDecodeExternalIdNoCompany(): void
    {
        $testObject = new SberPayClient();
        $str = '123132-invoice';
        $result = $testObject->decodeExternalId($str);

        $this->assertIsArray($result); 
        $this->assertEquals(2, count($result));
        $this->assertEquals('123132', $this->invokeProperty($testObject, 'orderNum') );
        $this->assertEquals('gms', $this->invokeProperty($testObject, 'orderCompanyCode') );
    }
    public function testInitConfigByCodeTest(): void
    {
        $testObject = new SberPayClient(); 
        $testObject->decodeExternalId('test-123123-invoice');
        $result  = $this->invokeMethod($testObject, 'initConfigByCompanyCode', []);

        $this->assertTrue($result);
        $this->assertEquals('test', $this->invokeProperty($testObject, 'orderCompanyCode') );
    }

    // callback.php
    public function testCallback(): void
    {
        $testObject = $this->mock;
        $result = $testObject->sberCallback($this->sberInvoiceJson);
        $this->assertTrue($result);
    }

    //////////////////////////////////////////////
    /**
     * paylink
     */
    public function testPayLinkByInvoice(): void
    {
        $testObject = $this->mock; 
        $testObject->initByInvoiceId('1');
        $result = $testObject->getPayLink();

        $this->assertEquals(
            'https://securepayments.sberbank.ru/payment/merchants/sbersafe_id/payment_ru.html?mdOrder=06e08e8d-170f-79e1-8325-cd7b0014591f',
            $result
        );
    }


//    public function testCallbackNoOrderNum(): void
//    {
////        $testObject = new SberPayClient();
////        $result = $testObject->SberCallback($this->sberNoOrderNumberInvoiceJson);
//        $this->assertTrue(true);//$testObject->isTestServer);
//    }
//
//    /**
//     * Тестирование Инициализация Объекта данными из счета битрикс
//     */
//    public function testInitByInvoiceId() : void
//    {
//        $this->mock->initByInvoiceId('1');
//        $payLink = $this->mock->getPayLink();
//        $this->assertEquals(
//            'https://securepayments.sberbank.ru/payment/merchants/sbersafe_id/payment_ru.html?mdOrder=06e08e8d-170f-79e1-8325-cd7b0014591f',
//            $payLink
//        );
//    }
//    /**
//     * тестировние метода для запроса счета из Битрикс 
//     */
//    public function testGetBxInvoice() : void
//    {
//        $result = $this->invokeMethod($this->mock, 'getBxInvoiceById', ['invoiceId'=>1]);
//        $this->assertIsArray($result);
//        //$this->assertTrue(true);
//    }
//    /**
//     * Тестирования неудачной инициализации объекта Клиента Сбербанк
//     */
//    public function testFailInitClient() : void
//    {
////        $this->expectExceptionMessage('Не удалось создать объект Client');
////        $testObject = new SberPayClient(); 
////        $this->invokeMethod($testObject, 'initClient', ['sdfsf'=>'sdfsf']);
//          $this->assertTrue(true);
//    }
//
//    // index.php | paylink.php
//    public function testPayLink(): void
//    {
//        // $invoiceId = $this->_request->get("oid");
//        // $invoiceId = $this->_request->get("order_id");
//        // $sberPay->initByInvoiceId($invoiceId);
//        // $sberPay->initByOrder($invoiceId);
//        // $payUrl = $sberPay->getPayLink();
//        $this->mock->initByInvoiceId('1');
//        $payLink = $this->mock->getPayLink();
//        $this->assertEquals(
//            'https://securepayments.sberbank.ru/payment/merchants/sbersafe_id/payment_ru.html?mdOrder=06e08e8d-170f-79e1-8325-cd7b0014591f',
//            $payLink
//        );
//        // gms:
//        // \Perfekto\AtolOnline\Main::OnSalePayOrderHandler($elementId);
//        // \Perfekto\AtolOnline\Main::OnSalePayInvoice( $paySystemId, ['ID' => $elementId]);
//    }
//
//    public function testSuccess(): void
//    {
//        // echo 'успешная оплата';
//        $this->assertTrue(true); 
//    }
//
//    public function testFail(): void
//    {
//        // echo 'Оплата не проведена';
//        $this->assertTrue(true); 
//    }
}
