<?php


namespace Drieschel\ObjectCreator;


class ComponentCollection
{
    /**
     * @var string
     */
    protected string $componentClassName;

    /**
     * @var array<ComponentInterface>
     */
    protected array $components = [];

    /**
     * ComponentCollection constructor.
     * @param string $componentClassName
     * @throws Exception
     */
    public function __construct(string $componentClassName)
    {
        if(!is_subclass_of($componentClassName, ComponentInterface::class)) {
            throw Exception::classIsNotSubclassOf($componentClassName, ComponentInterface::class);
        }

        $this->componentClassName = $componentClassName;
    }

    /**
     * @param string $className
     * @return ComponentInterface|null
     */
    public function get(string $className): ?ComponentInterface
    {
        return $this->components[$className] ?? null;
    }

    /**
     * @param string $className
     * @return ComponentInterface|null
     */
    public function getFor(string $className): ?ComponentInterface
    {
        $components = [];
        foreach ($this->components as $component) {
            if ($component->supports($className)) {
                $components[] = $component;
            }
        }

        if (count($components) > 0) {
            usort($components, function (ComponentInterface $a, ComponentInterface $b) {
                return $b->getPriority() - $a->getPriority();
            });

            return $components[0];
        }

        return null;
    }

    /**
     * @param string $className
     * @return boolean
     */
    public function has(string $className): bool
    {
        return $this->get($className) !== null;
    }

    /**
     * @param ComponentInterface $instance
     * @return ComponentCollection
     * @throws Exception
     */
    public function set(ComponentInterface $instance): self
    {
        $instanceClassname = get_class($instance);
        if(!$instance instanceof $this->componentClassName) {
            throw Exception::instanceIsNotA($instanceClassname, $this->componentClassName);
        }

        $this->components[$instanceClassname] = $instance;

        return $this;
    }

    /**
     * @param ComponentInterface ...$instances
     * @return ComponentCollection
     * @throws Exception
     */
    public function setMany(ComponentInterface ...$instances): self
    {
        foreach($instances as $instance) {
            $this->set($instance);
        }

        return $this;
    }

    /**
     * @return array<ComponentInterface>
     */
    public function toArray(): array
    {
        return array_values($this->components);
    }
}