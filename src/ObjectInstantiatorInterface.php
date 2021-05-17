<?php


namespace Drieschel\ObjectCreator;


interface ObjectInstantiatorInterface extends ComponentInterface
{
    /**
     * @param string $className
     * @param array $arguments
     * @return object
     */
    public function instantiate(string $className, array $arguments = []): object;
}