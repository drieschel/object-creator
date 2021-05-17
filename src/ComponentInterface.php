<?php


namespace Drieschel\ObjectCreator;


interface ComponentInterface
{
    /**
     * @param string $className
     * @return boolean
     */
    public function supports(string $className): bool;

    /**
     * @return integer
     */
    public function getPriority(): int;
}