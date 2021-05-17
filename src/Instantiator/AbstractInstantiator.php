<?php


namespace Drieschel\ObjectCreator\Instantiator;

use Drieschel\ObjectCreator\ObjectInstantiatorInterface;

abstract class AbstractInstantiator implements ObjectInstantiatorInterface
{
    /**
     * @var integer
     */
    protected int $priority = 0;

    /**
     * @return integer
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return AbstractInstantiator
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }
}