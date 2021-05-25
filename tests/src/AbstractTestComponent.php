<?php


namespace Drieschel\ObjectCreator;


abstract class AbstractTestComponent implements ComponentInterface
{
    /**
     * @var integer
     */
    protected int $priority = 0;

    /**
     * @var array<string>
     */
    protected array $supportedClasses = [];

    /**
     * TestComponent1 constructor.
     * @param array $supportedClasses
     * @param int $priority
     */
    public function __construct(array $supportedClasses, int $priority = 0)
    {
        $this->supportedClasses = $supportedClasses;
        $this->priority = $priority;
    }

    /**
     * @param string $className
     * @return boolean
     */
    public function supports(string $className): bool
    {
        return in_array($className, $this->supportedClasses, true);
    }

    /**
     * @param string ...$supportedClasses
     * @return AbstractTestComponent
     */
    public function setSupportedClasses(string ...$supportedClasses): AbstractTestComponent
    {
        $this->supportedClasses = $supportedClasses;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param integer $priority
     * @return AbstractTestComponent
     */
    public function setPriority(int $priority): AbstractTestComponent
    {
        $this->priority = $priority;
        return $this;
    }
}