<?php


namespace Drieschel\ObjectCreator;


use Drieschel\ObjectCreator\Instantiator\DateTimeInstantiator;
use Drieschel\ObjectCreator\Instantiator\ReflectionInstantiator;

class ObjectCreator implements ObjectCreatorInterface
{
    /**
     * @var array<string, string>
     */
    protected array $classMappings = [];

    /**
     * @var array<ObjectInstantiatorInterface>
     */
    protected array $instantiators = [];

    /**
     * @var ReflectionClassCollection
     */
    protected ReflectionClassCollection $reflectionClasses;

    /**
     * AbstractObjectCreator constructor.
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
     * @param object $subject
     * @param array $data
     * @throws \ReflectionException|Exception
     */
    public function initialize(object $subject, array $data = []): void
    {
        $reflectionClass = $this->reflectionClasses->getByObject($subject);
        foreach ($data as $property => $value) {
            $setterName = sprintf('set%s', ucfirst($property));
            if (is_callable([$subject, $setterName])) {
                $reflectionMethod = $reflectionClass->getMethod($setterName);
                $reflectionParam = $reflectionMethod->getParameters()[0] ?? null;
                if ($reflectionParam === null) {
                    throw Exception::setterArgumentMissing($reflectionClass->getName(), $setterName);
                }

                if (!$reflectionParam->isVariadic()) {
                    $value = [$value];
                }

                $argumentReflectionClass = $reflectionParam->getClass();
                if ($argumentReflectionClass !== null) {
                    if (is_array($value)) {
                        $value = array_map(function ($argumentData) use ($argumentReflectionClass) {
                            $argumentClassName = $this->getArgumentClassName($argumentReflectionClass->getName());
                            if ($argumentData instanceof $argumentClassName) {
                                return $argumentData;
                            }

                            return $this->instantiateAndInitialize($argumentReflectionClass->getName(), is_array($argumentData) ? $argumentData : [$argumentData]);
                        }, $value);
                    }
                }

                $subject->{$setterName}(...$value);
            }
        }
    }

    /**
     * @param string $className
     * @param array $arguments
     * @return object
     * @throws Exception|\ReflectionException
     */
    public function instantiate(string $className, array $arguments = []): object
    {
        $instantiator = $this->getInstantiatorFor($className);
        if ($instantiator !== null) {
            return $instantiator->instantiate($className, $arguments);
        }

        $reflectionClass = $this->reflectionClasses->get($className);
        $reflectionConstructor = $reflectionClass->getConstructor();

        $preparedArguments = [];
        $missingArguments = [];

        if($reflectionConstructor !== null) {
            $reflectionConstructorParams = $reflectionConstructor->getParameters();
            $argumentsHaveNumericKeys = count($arguments) === count($reflectionConstructorParams) && count(array_filter(array_keys($arguments), 'is_string')) === 0;
            foreach ($reflectionConstructorParams as $i => $reflectionParam) {
                $argumentName = $reflectionParam->getName();
                if ($argumentsHaveNumericKeys) {
                    $arguments[$argumentName] = $arguments[$i];
                }

                if (isset($arguments[$argumentName])) {
                    if (!$reflectionParam->isVariadic()) {
                        $arguments[$argumentName] = [$arguments[$argumentName]];
                    }

                    $argumentClassName = null;
                    if ($reflectionParam->getClass() !== null) {
                        $argumentClassName = $this->getArgumentClassName($reflectionParam->getClass()->getName());
                    }

                    foreach ($arguments[$argumentName] as $argumentValue) {
                        if ($argumentClassName !== null && !$argumentValue instanceof $argumentClassName) {
                            $preparedArguments[] = $this->instantiateAndInitialize($argumentClassName, is_array($argumentValue) ? $argumentValue : [$argumentValue]);
                        } else {
                            $preparedArguments[] = $argumentValue;
                        }
                    }
                } elseif ($reflectionParam->isDefaultValueAvailable()) {
                    $preparedArguments[] = $reflectionParam->getDefaultValue();
                } else {
                    $missingArguments[] = $argumentName;
                }
            }
        }

        if (count($missingArguments) > 0) {
            throw Exception::constructorArgumentsMissing($className, ...$missingArguments);
        }

        return $reflectionClass->newInstanceArgs($preparedArguments);
    }

    /**
     * @param string $className
     * @param array $data
     * @return object
     * @throws Exception
     * @throws \ReflectionException
     */
    public function instantiateAndInitialize(string $className, array $data = []): object
    {
        $object = $this->instantiate($className, $data);
        $this->initialize($object, $data);
        return $object;
    }

    /**
     * @param string $className
     * @return boolean
     * @throws \ReflectionException
     */
    public function supports(string $className): bool
    {
        return class_exists($className) && $this->reflectionClasses->get($className)->isInstantiable();
    }

    /**
     * @param string $className
     * @return string|null
     */
    public function getArgumentClassName(string $className): string
    {
        return $this->classMappings[$className] ?? $className;
    }

    /**
     * @param string $fromClassName
     * @param string $toClassName
     * @return ObjectCreator
     */
    public function setClassMapping(string $fromClassName, string $toClassName): self
    {
        $this->classMappings[$fromClassName] = $toClassName;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getClassMappings(): array
    {
        return $this->classMappings;
    }

    /**
     * @param string[] $classMappings
     * @return ObjectCreator
     */
    public function setClassMappings(array $classMappings): self
    {
        foreach($classMappings as $fromClassName => $toClassName) {
            $this->setClassMapping($fromClassName, $toClassName);
        }

        return $this;
    }


    /**
     * @param string $className
     * @return ObjectInstantiatorInterface|null
     */
    public function getInstantiatorFor(string $className): ?ObjectInstantiatorInterface
    {
        $instantiators = [];
        foreach ($this->instantiators as $instantiator) {
            if ($instantiator->supports($className)) {
                $instantiators[] = $instantiator;
            }
        }

        if (count($instantiators) > 0) {
            usort($instantiators, function (ObjectInstantiatorInterface $a, ObjectInstantiatorInterface $b) {
                return $b->getPriority() - $a->getPriority();
            });

            return $instantiators[0];
        }

        return null;
    }

    /**
     * @param string $instantiatorClassName
     * @return ObjectInstantiatorInterface
     */
    public function getInstantiator(string $instantiatorClassName): ?ObjectInstantiatorInterface
    {
        return $this->instantiators[$instantiatorClassName] ?? null;
    }

    /**
     * @param string $instantiatorClassName
     * @return boolean
     */
    public function hasInstantiator(string $instantiatorClassName): bool
    {
        return $this->getInstantiator($instantiatorClassName) !== null;
    }

    /**
     * @param ObjectInstantiatorInterface $instantiator
     * @return ObjectCreator
     */
    public function registerInstantiator(ObjectInstantiatorInterface $instantiator): self
    {
        $this->instantiators[get_class($instantiator)] = $instantiator;

        return $this;
    }

    /**
     * @return ObjectInstantiatorInterface[]
     */
    public function getInstantiators(): array
    {
        return $this->instantiators;
    }

    /**
     * @param ObjectInstantiatorInterface ...$instantiators
     * @return ObjectCreator
     */
    public function registerInstantiators(ObjectInstantiatorInterface ...$instantiators): self
    {
        foreach ($instantiators as $instantiator) {
            $this->registerInstantiator($instantiator);
        }

        return $this;
    }

    /**
     * @return void
     */
    public function registerDefaultInstantiators(): void
    {
        $this->registerInstantiator(new DateTimeInstantiator());
    }

    /**
     * @return integer
     */
    public function getPriority(): int
    {
        return 0;
    }
}