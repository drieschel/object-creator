<?php


namespace Drieschel\ObjectCreator;


interface FooBarInterface
{
    /**
     * @param string $fooBar
     * @return FooBarInterface
     */
    public function setFooBar(string $fooBar): self;
}