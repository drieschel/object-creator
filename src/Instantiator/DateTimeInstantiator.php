<?php


namespace Drieschel\ObjectCreator\Instantiator;


use Drieschel\ObjectCreator\Exception;

class DateTimeInstantiator extends AbstractInstantiator
{
    /**
     * @param string $className
     * @param array $arguments
     * @return object
     * @throws Exception
     */
    public function instantiate(string $className, array $arguments = []): object
    {
        $datetime = null;
        $timezone = null;

        if(isset($arguments['datetime'])) {
            $arguments[0] = $arguments['datetime'];
        }

        if(isset($arguments['timezone'])) {
            $arguments[1] = $arguments['timezone'];
        }

        if (isset($arguments[0])) {
            $datetime = $arguments[0];
            if (is_numeric($datetime) && (int)$datetime === intval($datetime)) {
                $datetime = sprintf('@%d', $datetime);
            }
        }

        if (isset($arguments[1])) {
            if ($arguments[1] instanceof \DateTimeZone) {
                $timezone = $arguments[1];
            } elseif (is_string($arguments[1]) && $arguments[1] !== '') {
                $timezone = new \DateTimeZone($arguments[1]);
            }
        }

        if($datetime === null) {
            throw Exception::constructorArgumentsMissing($className, 'datetime');
        }

        return new $className($datetime, $timezone);
    }

    /**
     * @param string $className
     * @return boolean
     */
    public function supports(string $className): bool
    {
        return is_subclass_of($className, \DateTimeInterface::class);
    }
}