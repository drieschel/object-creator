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
     * @throws Exception
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
     * @param object $instance
     * @return \ReflectionClass
     * @throws \ReflectionException|Exception
     */
    public function getByInstance(object $instance): \ReflectionClass
    {
        return $this->get(get_class($instance));
    }
}