<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use dima731515\SberPay\SberCallBack;


final class SberCallbackTest extends TestCase
{
    public function setUp(): void
    {
        $this->sberInvoiceJson = '{"checksum":"BB9A5DC898190E99B573C37AFA02E227F9E84141F9B87DAFD56E6DC6D197F2CF","orderNumber":"326180-invoice","mdOrder":"abba5cd4-2b19-7616-b9be-cd580014591f","operation":"deposited","status":"1"}';
        //$this->sberOrderJson = '{"checksum":"3E08174561913D9BF1F30D2F144E0B2EE99BCD5DCE0035383558777DBA694814","orderNumber":"design-326133-order","mdOrder":"0cf9e68a-1028-70c0-98ce-0fb001e0fe02","operation":"deposited","status":"1"}';
        $this->config = [];
        $this->config['callbackToken']='vieb4rj8610efjrrgovqcpbrqq';
    }

    public function tearDown(): void
    {
    }

    public function testConstruct(): void
    {
        $testObject = new SberCallback($this->sberInvoiceJson); 
        $result = $testObject->isPayed();
        $this->assertTrue($result);

    }
    public function testValidate(): void
    {
        $testObject = new SberCallback($this->sberInvoiceJson); 
        $result = $testObject->initConfig($this->config);
        $result = $testObject->validateCheckSum();
        $this->assertTrue($result);
        $this->assertTrue($testObject->isValid);
    }
    public function testInitByJson(): void
    {
        $testObject = new SberCallback(); 
        $result = $testObject->initByJson($this->sberInvoiceJson);
        $this->assertTrue($result);
    }
    public function testInitConfig(): void
    {
        $testObject = new SberCallback(); 
        $result = $testObject->initConfig($this->config);
        $this->assertTrue($result);
    }
    public function testGetExternald(): void
    {
        $testObject = new SberCallback(); 
        $testObject->initByJson($this->sberInvoiceJson);
        $result = $testObject->getExternalId();
        $this->assertEquals('326180-invoice', $result);
    }
    public function testIsPayed(): void
    {
        $testObject = new SberCallback(); 
        $testObject->initByJson($this->sberInvoiceJson);
        $result = $testObject->isPayed();
        $this->assertTrue($result);
    }
}
