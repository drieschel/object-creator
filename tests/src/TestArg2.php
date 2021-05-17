<?php


namespace Drieschel\ObjectCreator;


class TestArg2
{
    /**
     * @var string|null
     */
    protected ?string $bar = null;

    /**
     * @return string|null
     */
    public function getBar(): ?string
    {
        return $this->bar;
    }

    /**
     * @param string|null $bar
     * @return $this
     */
    public function setBar(?string $bar): TestArg2
    {
        $this->bar = $bar;
        return $this;
    }
}