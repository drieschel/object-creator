<?php


namespace Drieschel\ObjectCreator;

interface ObjectInitializerInterface extends ComponentInterface
{
    /**
     * @param object $instance
     * @param array $data
     * @return void
     */
    public function initialize(object $instance, array $data = []): void;
}
