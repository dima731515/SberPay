<?php declare(strict_types=1);

namespace dima731515\tests;

use PHPUnit\Framework\TestCase as baseTestCase;

class TestCase extends baseTestCase
{

    public static function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
    protected static function invokeProperty(&$object, $propertyName, $setValue = false) 
    {
        $reflectionProp = new \ReflectionProperty(get_class($object), $propertyName);
        $reflectionProp->setAccessible(true);
        if($setValue !== false) $reflectionProp->setValue($object, $setValue);
        return $reflectionProp->getValue($object);
    }
}
