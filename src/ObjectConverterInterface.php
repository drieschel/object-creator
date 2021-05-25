<?php


namespace Drieschel\ObjectCreator;


interface ObjectConverterInterface extends ComponentInterface
{
    /**
     * @param object $instance
     * @return array
     */
    public function toArray(object $instance): array;
}