<?php


namespace Drieschel\ObjectCreator;


class ReflectionClassCollection
{
    /**
     * @var array<ReflectionClass>
     */
    protected array $reflectionClasses = [];

    /**
     * @param string $className
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    public function get(string $className): \ReflectionClass
    {
        if(!isset($this->reflectionClasses[$className])) {
            if(!class_exists($className)) {
                throw Exception::classNotExists($className);
            }

            $this->reflectionClasses[$className] = new \ReflectionClass($className);
        }

        return $this->reflectionClasses[$className];
    }

    /**
     * @param object $object
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    public function getByObject(object $object): \ReflectionClass
    {
        return $this->get(get_class($object));
    }
}