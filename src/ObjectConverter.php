<?php


namespace Drieschel\ObjectCreator;


use Jawira\CaseConverter\CaseConverterException;

class ObjectConverter implements ObjectConverterInterface
{
    /**
     * @var array<string, string, ObjectPropertyExtractorInterface>
     */
    protected array $propertyExtractors = [];

    /**
     * @var array<integer>
     */
    protected array $extractPropertyTypes = [
        \ReflectionProperty::IS_PROTECTED,
        \ReflectionProperty::IS_PUBLIC,
    ];

    /**
     * @var array
     */
    protected array $invalidPropertyValues = [];

    /**
     * @var array<integer>
     */
    protected static array $propertyTypes = [
        \ReflectionProperty::IS_PRIVATE,
        \ReflectionProperty::IS_PROTECTED,
        \ReflectionProperty::IS_PUBLIC,
    ];

    /**
     * @var ReflectionClassCollection
     */
    protected ReflectionClassCollection $reflectionClasses;

    /**
     * ObjectConverter constructor.
     * @param ReflectionClassCollection|null $reflectionClasses
     */
    public function __construct(ReflectionClassCollection $reflectionClasses = null)
    {
        if ($reflectionClasses === null) {
            $reflectionClasses = new ReflectionClassCollection();
        }

        $this->reflectionClasses = $reflectionClasses;
    }

    /**
     * @param object $instance
     * @param array $onlyProperties
     * @return array
     * @throws Exception
     * @throws \ReflectionException
     */
    public function toArray(object $instance): array
    {
        $data = [];
        $reflectionClass = $this->reflectionClasses->getByInstance($instance);
        $reflectionProperties = $reflectionClass->getProperties(self::determinePropertiesFilter($this->extractPropertyTypes));
        foreach ($reflectionProperties as $reflectionProperty) {
            if (!isset($this->propertyExtractors[$reflectionClass->getName()][$reflectionProperty->getName()])) {
                $this->propertyExtractors[$reflectionClass->getName()][$reflectionProperty->getName()] = new ObjectPropertyExtractor($reflectionProperty);
            }

            $propertyExtractor = $this->propertyExtractors[$reflectionClass->getName()][$reflectionProperty->getName()];

            $value = $propertyExtractor->getPropertyValue();
            if (is_object($value)) {
                $value = $this->toArray($value);
            }

            if (!in_array($value, $this->invalidPropertyValues, true)) {
                $data[$propertyExtractor->getNormalizedPropertyName()] = $value;
            }
        }

        return $data;
    }

    /**
     * @param string $className
     * @return bool
     * @throws \ReflectionException|Exception
     */
    public function supports(string $className): bool
    {
        return class_exists($className) && $this->reflectionClasses->get($className)->isInstantiable();
    }

    /**
     * @return integer
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * @param array<integer> $propertyTypes
     * @return integer
     */
    protected static function determinePropertiesFilter(array $propertyTypes): int
    {
        $result = 0;
        foreach ($propertyTypes as $propertyType) {
            $result |= $propertyType;
        }

        return $result;
    }

    /**
     * @param int $propertyType
     * @param int ...$morePropertyTypes
     * @return ObjectConverter
     * @throws Exception
     */
    public function setExtractPropertyTypes(int $propertyType, int ...$morePropertyTypes): self
    {
        $propertyTypes = func_get_args();
        foreach ($propertyTypes as $propertyType) {
            if (!self::isPropertyType($propertyType)) {
                throw Exception::valueIsNotAPropertyType($propertyType);
            }
        }

        $this->extractPropertyTypes = array_unique($propertyTypes);

        return $this;
    }

    /**
     * @param array $invalidPropertyValues
     * @return ObjectConverter
     */
    public function setInvalidPropertyValues(...$invalidPropertyValues): self
    {
        $this->invalidPropertyValues = $invalidPropertyValues;

        return $this;
    }

    /**
     * @param integer $propertyType
     * @return boolean
     */
    public static function isPropertyType(int $propertyType): bool
    {
        return in_array($propertyType, self::$propertyTypes, true);
    }

    /**
     * @return array<integer>
     */
    public static function getPropertyTypes(): array
    {
        return self::$propertyTypes;
    }
}