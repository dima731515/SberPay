<?php declare(strict_types=1);

//use DateTime;
//use PHPUnit\Framework\TestCase;
use dima731515\tests\TestCase;
use dima731515\SberPay\SberPayClient;


final class OtherTest extends TestCase
{
    protected $logFile = '';

    public function setUp(): void
    {
        $this->logFile = '' . ( new DateTime() )->format('d-m-Y') . '_info_sber.log';
        $this->sberInvoiceJson = '{"checksum":"3E08174561913D9BF1F30D2F144E0B2EE99BCD5DCE0035383558777DBA694814","orderNumber":"motors-326133-invoice","mdOrder":"0cf9e68a-1028-70c0-98ce-0fb001e0fe02","operation":"deposited","status":"1"}';
        $this->sberOrderJson = '{"checksum":"3E08174561913D9BF1F30D2F144E0B2EE99BCD5DCE0035383558777DBA694814","orderNumber":"design-326133-order","mdOrder":"0cf9e68a-1028-70c0-98ce-0fb001e0fe02","operation":"deposited","status":"1"}';

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
    }
    public function testInitConfigByCodeTest(): void
    {
        $testObject = new SberPayClient(); 
        $testObject->decodeExternalId('test-123123-invoice');
        $result = $testObject->initConfigByCompanyCode();
        $this->assertTrue($result);
    }
    public function testInitConfigByNoExistenCode(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Метод для проверки реакции на регистрацию платежа счета
     * должен корректно отработать метод декодирования externald для счета
     * Объект должен быть инициализирован данными счета
     * Должны проставится оплата счета
     */
//    public function testCallbackMethodByInvoice(): void
//    {
//        $testObject = new SberPayClient($test = true);    
//        $result = $testObject->sberCallback($this->sberInvoiceJson);
//        $this->assertTrue($result);
//    }
//    public function testCallbackMethodByOrder(): void
//    {
//        $testObject = new SberPayClient($test = true);
//        $result = $testObject->sberCallback($this->sberOrderJson);
//        $this->assertTrue($result);
//    }



//    public function Build(): string 
//    {
//        return 'test';
//    }
//
//    /**
//     * @depends Build
//     */
//    public function testTestMethod(string $str): void
//    {
//        print_r($str);
//        $testObject = new SberPay($test = true);
//        $result = $testObject->test();
//        $this->assertEquals(null, $result);
//    
//    }
}
