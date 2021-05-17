<?php


namespace Drieschel\ObjectCreator;


class TestArg1
{
    protected string $foo;

    /**
     * TestArg1 constructor.
     * @param string $foo
     */
    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return string
     */
    public function getFoo(): string
    {
        return $this->foo;
    }
}