<?php

namespace Drieschel\ObjectCreator;

use PHPUnit\Framework\TestCase;

class ComponentCollectionTest extends TestCase
{
    /**
     * @dataProvider componentClassesProvider
     *
     * @param string $componentClassName
     * @throws Exception
     */
    public function testConstructorSuccess(string $componentClassName)
    {
        $collection = new ComponentCollection($componentClassName);
        $reflectionClass = new \ReflectionClass($collection);
        $reflectionProperty = $reflectionClass->getProperty('componentClassName');
        $reflectionProperty->setAccessible(true);
        $givenValue = $reflectionProperty->getValue($collection);
        $this->assertEquals($componentClassName, $givenValue);
    }

    /**
     * @dataProvider noComponentClassesProvider
     *
     * @param string $componentClassName
     * @throws Exception
     */
    public function testConstructorFails(string $componentClassName)
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::CLASS_IS_NOT_SUBCLASS_OF);
        new ComponentCollection($componentClassName);
    }

    /**
     * @dataProvider componentClassesProvider
     *
     * @param string $componentClassName
     * @throws Exception
     */
    public function testSetOnlyOneInstancePerClass(string $componentClassName)
    {
        $mock1 = $this->createMock($componentClassName);
        $mock2 = $this->createMock($componentClassName);
        $collection = new ComponentCollection($componentClassName);
        $reflectionClass = new \ReflectionClass($collection);
        $reflectionProperty = $reflectionClass->getProperty('components');
        $reflectionProperty->setAccessible(true);
        $this->assertFalse($mock1 === $mock2);
        $this->assertEquals(get_class($mock1), get_class($mock2));
        $this->assertCount(0, $reflectionProperty->getValue($collection));
        $collection->set($mock1);
        $this->assertCount(1, $reflectionProperty->getValue($collection));
        $collection->set($mock2);
        $this->assertCount(1, $reflectionProperty->getValue($collection));
    }

    /**
     * @dataProvider componentObjectsProvider
     *
     * @param string $componentClassName
     * @param ...$components
     * @throws Exception
     */
    public function testSet(string $componentClassName, ...$components)
    {
        $expectedComponents = [];
        $collection = new ComponentCollection($componentClassName);
        $reflectionClass = new \ReflectionClass($collection);
        $reflectionProperty = $reflectionClass->getProperty('components');
        $reflectionProperty->setAccessible(true);
        foreach ($components as $component) {
            $expectedComponents[get_class($component)] = $component;
            $collection->set($component);
        }

        $this->assertEquals($expectedComponents, $reflectionProperty->getValue($collection));
    }

    /**
     * @dataProvider componentObjectsProvider
     *
     * @param string $componentClassName
     * @param ...$components
     * @throws Exception
     */
    public function testSetMany(string $componentClassName, ...$components)
    {
        $collection = $this->createPartialMock(ComponentCollection::class, ['set']);

        $collection
            ->expects($this->exactly(count($components)))
            ->method('set')
            ->withConsecutive(...array_map(function($component) { return [$component]; }, $components));

        $collection->setMany(...$components);
    }

    /**
     * @dataProvider componentObjectsProvider
     *
     * @param string $componentClassName
     * @param ...$components
     * @throws Exception
     */
    public function testHas(string $componentClassName, ...$components)
    {
        $collection = new ComponentCollection($componentClassName);
        foreach($components as $component) {
            $componentClassName = get_class($component);
            $this->assertFalse($collection->has($componentClassName));
            $collection->set($component);
            $this->assertTrue($collection->has($componentClassName));
        }
    }

    /**
     * @dataProvider getForProvider
     *
     * @param string $componentClassName
     * @param string $forClassName
     * @param object|null $expectedComponent
     * @param ...$components
     * @throws Exception
     */
    public function testGetFor(string $componentClassName, string $forClassName, ?object $expectedComponent, ...$components)
    {
        $collection = new ComponentCollection($componentClassName);
        foreach($components as $component) {
            $collection->set($component);
        }

        $this->assertEquals($expectedComponent, $collection->getFor($forClassName));
    }

    /**
     * @dataProvider toArrayProvider
     *
     * @param array $componentInstances
     * @throws Exception
     */
    public function testToArray(string $componentClassName, object ...$componentInstances)
    {
        $collection = new ComponentCollection($componentClassName);
        $collection->setMany(...$componentInstances);
        $givenComponentInstances = $collection->toArray();
        $this->assertEquals($componentInstances, $givenComponentInstances);
    }

    /**
     * @dataProvider toArrayProvider
     *
     * @param string $componentClassName
     * @param object ...$componentInstances
     * @throws Exception
     */
    public function testGet(string $componentClassName, object ...$componentInstances)
    {
        $collection = new ComponentCollection($componentClassName);
        $collection->setMany(...$componentInstances);
        foreach($componentInstances as $componentInstance) {
            $this->assertEquals($componentInstance, $collection->get(get_class($componentInstance)));
        }

        $this->assertNull($collection->get('fooooo'));
    }

    /**
     * @return array
     */
    public function componentClassesProvider(): array
    {
        return [
            [ArrayConverterInterface::class],
            [ObjectCreatorInterface::class],
            [ObjectCreator::class],
            [ObjectInstantiatorInterface::class],
            [ObjectInitializerInterface::class],
        ];
    }

    /**
     * @return array
     */
    public function noComponentClassesProvider(): array
    {
        return [
            [TestArg1::class],
            [TestArg2::class],
            [TestClass::class],
            [Exception::class],
            [\Throwable::class],
        ];
    }

    /**
     * @return array[]
     */
    public function componentObjectsProvider(): array
    {
        return [
            [ObjectInitializerInterface::class, new class implements ObjectInitializerInterface {
                public function supports(string $className): bool
                {
                    return true;
                }

                public function getPriority(): int
                {
                    return 0;
                }

                public function initialize(object $instance, array $data = []): void
                {

                }
            }, new class implements ObjectInitializerInterface {
                public function supports(string $className): bool
                {
                    return true;
                }

                public function getPriority(): int
                {
                    return 10;
                }

                public function initialize(object $instance, array $data = []): void
                {

                }
            }],
            [
                AbstractTestComponent::class,
                new class(['foo']) extends AbstractTestComponent {

                },
                new class(['bar']) extends AbstractTestComponent {

                },
                new class(['foobaz']) extends AbstractTestComponent {

                },
            ]
        ];
    }

    /**
     * @return array
     */
    public function getForProvider(): array
    {
        $components = [
            new class(['yes'], 4) extends AbstractTestComponent {

            },
            new class(['foo'], 0) extends AbstractTestComponent {

            },
            new class(['yes'], 1) extends AbstractTestComponent {

            },
            new class(['foo'], 1) extends AbstractTestComponent {

            },
            new class(['foo'], -1) extends AbstractTestComponent {

            },
        ];

        return [
            [AbstractTestComponent::class, 'foo', $components[3], ...$components],
            [AbstractTestComponent::class, 'bar', null, ...$components],
            [AbstractTestComponent::class, 'yes', $components[0], ...$components],
        ];
    }

    /**
     * @return array
     */
    public function toArrayProvider(): array
    {
        return [
            [AbstractTestComponent::class, new class([]) extends AbstractTestComponent {}, new class([]) extends AbstractTestComponent {}, new class([]) extends AbstractTestComponent {}],
            [AbstractTestComponent::class],
        ];
    }
}
