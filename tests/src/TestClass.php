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
     * @var \DateTimeImmutable|null
     */
    protected ?\DateTimeImmutable $f = null;

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
    public function getF(): ?\DateTimeImmutable
    {
        return $this->f;
    }

    /**
     * @param \DateTimeImmutable|null $f
     * @return TestClass
     */
    public function setF(?\DateTimeImmutable $f): TestClass
    {
        $this->f = $f;
        return $this;
    }
}