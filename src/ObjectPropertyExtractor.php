<?php


namespace Drieschel\ObjectCreator;


use Jawira\CaseConverter\CaseConverterException;
use Jawira\CaseConverter\Convert;

class ObjectPropertyExtractor implements ObjectPropertyExtractorInterface
{
    public const
        PROPERTY_NAME_FORMAT_KEEP = 'keep',
        PROPERTY_NAME_FORMAT_CAMEL = 'camel',
        PROPERTY_NAME_FORMAT_SNAKE = 'snake';

    /**
     * @var \ReflectionProperty
     */
    protected \ReflectionProperty $reflectionProperty;

    /**
     * @var string
     */
    protected string $propertyNameFormat = self::PROPERTY_NAME_FORMAT_KEEP;

    /**
     * @var array<string>
     */
    protected static array $propertyNameFormats = [
        self::PROPERTY_NAME_FORMAT_KEEP,
        self::PROPERTY_NAME_FORMAT_CAMEL,
        self::PROPERTY_NAME_FORMAT_SNAKE,
    ];

    /**
     * ObjectPropertyExtractor constructor.
     * @param \ReflectionProperty $reflectionProperty
     */
    public function __construct(\ReflectionProperty $reflectionProperty)
    {
        $this->reflectionProperty = $reflectionProperty;
    }

    /**
     * @return string
     */
    public function getPropertyName(): string
    {
        return $this->reflectionProperty->getName();
    }

    /**
     * @return string
     * @throws CaseConverterException
     */
    public function getNormalizedPropertyName(): string
    {
        $propertyName = $this->getPropertyName();
        if(!$this->propertyNameFormat !== self::PROPERTY_NAME_FORMAT_KEEP) {
            $convertMethod = sprintf('to%s', ucfirst($this->propertyNameFormat));
            $propertyName = (new Convert($propertyName))->$convertMethod();
        }

        return $propertyName;
    }

    /**
     * @param object $instance
     * @return mixed
     * @throws Exception
     */
    public function extractPropertyValue(object $instance)
    {
        $this->validateInstance($instance);
        $this->reflectionProperty->setAccessible(true);
        $propertyValue = $this->reflectionProperty->getValue($instance);
        $this->reflectionProperty->setAccessible(!$this->reflectionProperty->isPrivate() && !$this->reflectionProperty->isProtected());

        return $propertyValue;
    }

    /**
     * @param string $format
     * @return ObjectPropertyExtractor
     * @throws Exception
     */
    public function setPropertyNameFormat(string $format): self
    {
        if(!self::isPropertyNameFormat($format)) {
            throw Exception::invalidPropertyNameFormat($format);
        }

        $this->propertyNameFormat = $format;

        return $this;
    }

    /**
     * @param object $instance
     * @throws Exception
     */
    protected function validateInstance(object $instance): void
    {
        if(!$this->reflectionProperty->getDeclaringClass()->isInstance($instance)) {
            throw Exception::instanceIsNotA(get_class($instance), $this->reflectionProperty->getDeclaringClass()->getName());
        }
    }

    /**
     * @param string $format
     * @return boolean
     */
    public static function isPropertyNameFormat(string $format): bool
    {
        return in_array($format, self::$propertyNameFormats, true);
    }
}