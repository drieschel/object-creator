<?php

namespace Drieschel\ObjectCreator;

use Drieschel\ObjectCreator\Instantiator\AbstractInstantiator;
use PHPUnit\Framework\TestCase;

class ObjectCreatorTest extends TestCase
{
    /**
     * @dataProvider constructorArgsProvider
     *
     * @param string $className
     * @param array $arguments
     * @param object $expectedObject
     * @throws Exception
     * @throws \ReflectionException
     */
    public function testInstantiate(string $className, array $arguments, object $expectedObject)
    {
        $creator = new ObjectCreator();
        $givenObject = $creator->instantiate($className, $arguments);
        $this->assertInstanceOf($className, $givenObject);
        $this->assertEquals($expectedObject, $givenObject);
    }

    /**
     * @dataProvider missingConstructorArgsProvider
     *
     * @param string $className
     * @param array $arguments
     * @param Exception $expectedException
     * @throws Exception
     * @throws \ReflectionException
     */
    public function testInstantiateConstructorArgsMissing(string $className, array $arguments, Exception $expectedException)
    {
        $this->expectExceptionObject($expectedException);
        (new ObjectCreator())->instantiate($className, $arguments);
    }

    /**
     * @dataProvider initializationArgsProvider
     *
     * @param object $subject
     * @param array $data
     * @param object $expectedObject
     * @throws Exception
     * @throws \ReflectionException
     */
    public function testInitialize(object $subject, array $data, object $expectedObject)
    {
        (new ObjectCreator())->initialize($subject, $data);
        $this->assertEquals($expectedObject, $subject);
    }

    /**
     * @dataProvider constructorArgsProvider
     *
     * @param string $className
     * @param array $data
     * @param object $object
     * @throws Exception
     * @throws \ReflectionException
     */
    public function testInstantiateAndInitialize(string $className, array $data, object $object)
    {
        $creator = $this->createPartialMock(ObjectCreator::class, ['instantiate', 'initialize']);

        $creator
            ->expects($this->once())
            ->method('instantiate')
            ->with($className, $data)
            ->willReturn($object);

        $creator
            ->expects($this->once())
            ->method('initialize')
            ->with($object, $data);

        $creator->instantiateAndInitialize($className, $data);
    }

    /**
     * @dataProvider supportsProvider
     *
     * @param string $className
     * @param bool $expected
     * @throws \ReflectionException
     */
    public function testSupports(string $className, bool $expected)
    {
        $this->assertEquals($expected, (new ObjectCreator())->supports($className));
    }

    /**
     * @return array
     */
    public function constructorArgsProvider(): array
    {
        return [
            [TestArg1::class, ['foo' => 'bar'], new TestArg1('bar')],
            [TestArg1::class, ['bar'], new TestArg1('bar')],
            [TestArg2::class, [], new TestArg2()],
            [TestClass::class, ['a' => new TestArg1('foowhat'), 'b' => 'string', 'c' => 0.4], new TestClass(new TestArg1('foowhat'), 'string', 0.4)],
            [TestClass::class, ['a' => 'wtf', 'b' => 'string'], new TestClass(new TestArg1('wtf'), 'string')],
            [TestClass::class, ['something', 'in your mind', 42.], new TestClass(new TestArg1('something'), 'in your mind', 42.)],
            [TestClass::class, ['a' => new TestArg1('yolo'), 'b' => 'string'], new TestClass(new TestArg1('yolo'), 'string', 0.5)],
        ];
    }

    /**
     * @return array
     */
    public function missingConstructorArgsProvider(): array
    {
        return [
            [TestArg1::class, ['one', 'two'], Exception::constructorArgumentsMissing(TestArg1::class, 'foo')],
            [TestArg1::class, ['bar' => 'foobar'], Exception::constructorArgumentsMissing(TestArg1::class, 'foo')],
            [TestClass::class, ['a' => 'something'], Exception::constructorArgumentsMissing(TestClass::class, 'b')],
            [TestClass::class, ['b' => 'string'], Exception::constructorArgumentsMissing(TestClass::class, 'a')],
        ];
    }

    /**
     * @return array
     */
    public function initializationArgsProvider(): array
    {
        return [
            [new TestArg2(), ['bar' => 'foo'], (new TestArg2())->setBar('foo')],
            [new TestArg2(), ['some' => 'bla'], new TestArg2()],
            [new TestClass(new TestArg1('42'), 'a string'), ['d' => true, 'e' => ['bar' => 'foobaz']], (new TestClass(new TestArg1('42'), 'a string'))->setD(true)->setE((new TestArg2())->setBar('foobaz'))],
            [new TestClass(new TestArg1('what'), 'a string'), ['d' => true, 'e' => (new TestArg2())->setBar('lalala')], (new TestClass(new TestArg1('what'), 'a string'))->setD(true)->setE((new TestArg2())->setBar('lalala'))],
        ];
    }

    /**
     * @return array
     */
    public function supportsProvider(): array
    {
        return [
            ['foo', false],
            [AbstractInstantiator::class, false],
            [ComponentInterface::class, false],
            [\ReflectionClass::class, true],
            [TestClass::class, true],
        ];
    }
}
