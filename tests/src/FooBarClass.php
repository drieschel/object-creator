<?php


namespace Drieschel\ObjectCreator;


class FooBarClass implements FooBarInterface
{
    /**
     * @var string
     */
    protected string $fooBar;

    /**
     * @return string
     */
    public function getFooBar(): string
    {
        return $this->fooBar;
    }

    /**
     * @param string $fooBar
     * @return FooBarClass
     */
    public function setFooBar(string $fooBar): self
    {
        $this->fooBar = $fooBar;
        return $this;
    }
}