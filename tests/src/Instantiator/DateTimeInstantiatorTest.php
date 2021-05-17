<?php

namespace Drieschel\ObjectCreator\Instantiator;

use Drieschel\ObjectCreator\Exception;
use PHPUnit\Framework\TestCase;

class DateTimeInstantiatorTest extends TestCase
{
    /**
     * @dataProvider instantiateProvider
     *
     * @param string $className
     * @param array $arguments
     * @param int $expectedTimestamp
     * @throws Exception
     */
    public function testInstantiate(string $className, array $arguments, int $expectedTimestamp)
    {
        $instantiator = new DateTimeInstantiator();
        /** @var \DateTimeInterface $object */
        $object = $instantiator->instantiate($className, $arguments);
        $this->assertInstanceOf($className, $object);
        $this->assertEquals($expectedTimestamp, $object->getTimestamp());
    }

    /**
     * @return array
     */
    public function instantiateProvider(): array
    {
        return [
            [\DateTime::class, [322816088], 322816088],
            [\DateTimeImmutable::class, [570524888], 570524888],
            [\DateTimeImmutable::class, [570524888, '+0400'], 570524888],
            [\DateTime::class, ['Sat Jan 30 1988 07:08:08 GMT', '+0600'], 570524888],
            [\DateTime::class, ['Sat Jan 30 1988 07:08:08+0000', '+0600'], 570524888],
            [\DateTime::class, ['Sat Jan 30 1988 07:08:08 GMT+0000', '+0600'], 570524888],
            [\DateTime::class, ['Sat Jan 30 1988 08:08:08', '+0100'], 570524888],
            [\DateTime::class, ['Sat Jan 30 1988 08:08:08', '+0100'], 570524888],
            [\DateTimeImmutable::class, ['Tue Mar 25 1980 07:08:08 GMT'], 322816088],
            [\DateTimeImmutable::class, ['Tue Mar 25 1980 07:08:08 +0000'], 322816088],
            [\DateTimeImmutable::class, ['Tue Mar 25 1980 07:08:08 GMT+0000'], 322816088],
            [\DateTimeImmutable::class, ['Tue Mar 25 1980 07:08:08', '+0000'], 322816088],
            [\DateTimeImmutable::class, ['Tue Mar 25 1980 08:08:08 GMT+0100', '-0500'], 322816088],
            [\DateTimeImmutable::class, ['Tue Mar 25 1980 08:08:08', '+0100'], 322816088],
        ];
    }

    /**
     * @throws Exception
     */
    public function testInstantiateMissingArgument()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::CONSTRUCTOR_ARGUMENTS_MISSING);

        $instantiator = new DateTimeInstantiator();
        /** @var \DateTimeInterface $object */
        $instantiator->instantiate(\DateTimeImmutable::class, []);
    }

    /**
     * @dataProvider supportsProvider
     *
     * @param string $className
     * @param bool $expected
     */
    public function testSupports(string $className, bool $expected)
    {
        $instantiator = new DateTimeInstantiator();
        $this->assertEquals($expected, $instantiator->supports($className));
    }

    /**
     * @return array[]
     */
    public function supportsProvider(): array
    {
        return [
            [\DateTimeImmutable::class, true],
            [\DateTime::class, true],
            [\ZipArchive::class, false],
            [\ReflectionClass::class, false],
            [\Reflection::class, false],
        ];
    }
}
