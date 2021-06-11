<?php

namespace Drieschel\ObjectCreator;

use PHPUnit\Framework\TestCase;

class ReflectionClassCollectionTest extends TestCase
{
    /**
     * @dataProvider objectProvider
     *
     * @param object $object
     * @throws \ReflectionException
     */
    public function testGetByObject(object $object)
    {
        $collection = new ReflectionClassCollection();
        $reflectionClass = $collection->getByInstance($object);
        $this->assertInstanceOf(\ReflectionClass::class, $reflectionClass);
        $this->assertEquals(get_class($object), $reflectionClass->getName());
    }

    /**
     * @dataProvider objectProvider
     *
     * @param object $object
     * @throws \ReflectionException
     */
    public function testGet(object $object)
    {
        $className = get_class($object);
        $collection = new ReflectionClassCollection();
        $reflectionClass = $collection->get($className);
        $this->assertInstanceOf(\ReflectionClass::class, $reflectionClass);
        $this->assertEquals($className, $reflectionClass->getName());
    }

    public function testGetUnknownClass()
    {
        $className = uniqid('unknown-class');
        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::CLASS_NOT_FOUND);
        (new ReflectionClassCollection())->get($className);
    }

    /**
     * @return array<object>
     */
    public function objectProvider(): array
    {
        return [
            [new \ZipArchive()],
            [new ObjectCreator()],
            [new ReflectionClassCollection()],
            [new \JsonException()],
        ];
    }
}
