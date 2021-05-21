<?php


namespace Drieschel\ObjectCreator;


class TestClass
{
    /**
     * @var TestArg1
     */
    protected TestArg1 $a;

    /**
     * @var string
     */
    protected string $b;

    /**
     * @var float
     */
    protected float $c;

    /**
     * @var bool
     */
    protected bool $d = false;

    /**
     * @var TestArg2|null
     */
    protected ?TestArg2 $e = null;

    /**
     * @var \DateTimeInterface|null
     */
    protected ?\DateTimeInterface $f = null;

    /**
     * @var FooBarInterface|null
     */
    protected ?FooBarInterface $g = null;


    /**
     * TestClass constructor.
     * @param TestArg1 $a
     * @param string $b
     * @param float $c
     */
    public function __construct(TestArg1 $a, string $b, float $c = 0.5)
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    /**
     * @return TestArg1
     */
    public function getA(): TestArg1
    {
        return $this->a;
    }

    /**
     * @return string
     */
    public function getB(): string
    {
        return $this->b;
    }

    /**
     * @return float
     */
    public function getC(): float
    {
        return $this->c;
    }

    /**
     * @return bool
     */
    public function isD(): bool
    {
        return $this->d;
    }

    /**
     * @param bool $d
     * @return TestClass
     */
    public function setD(bool $d): TestClass
    {
        $this->d = $d;
        return $this;
    }

    /**
     * @return TestArg2|null
     */
    public function getE(): ?TestArg2
    {
        return $this->e;
    }

    /**
     * @param TestArg2|null $e
     * @return TestClass
     */
    public function setE(?TestArg2 $e): TestClass
    {
        $this->e = $e;
        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getF(): ?\DateTimeInterface
    {
        return $this->f;
    }

    /**
     * @param \DateTimeInterface|null $f
     * @return TestClass
     */
    public function setF(?\DateTimeInterface $f): TestClass
    {
        $this->f = $f;
        return $this;
    }

    /**
     * @return FooBarInterface|null
     */
    public function getG(): ?FooBarInterface
    {
        return $this->g;
    }

    /**
     * @param FooBarInterface|null $g
     * @return TestClass
     */
    public function setG(?FooBarInterface $g): TestClass
    {
        $this->g = $g;
        return $this;
    }
}