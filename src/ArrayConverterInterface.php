<?php


namespace Drieschel\ObjectCreator;


interface ArrayConverterInterface extends ComponentInterface
{
    /**
     * @param array $data
     * @return object
     */
    public function toObject(array $data): object;
}