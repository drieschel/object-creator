<?php


namespace Drieschel\ObjectCreator;

interface ObjectInitializerInterface extends ComponentInterface
{
    /**
     * @param object $subject
     * @param array $data
     * @return void
     */
    public function initialize(object $subject, array $data = []): void;
}
