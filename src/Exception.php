<?php


namespace Drieschel\ObjectCreator;


class Exception extends \Exception
{
    public const
        INSTANTIATION_NOT_SUPPORTED = 10,
        SETTER_ARGUMENT_MISSING = 20,
        CONSTRUCTOR_ARGUMENTS_MISSING = 30,
        CLASS_NOT_FOUND = 40;


    /**
     * @param string $className
     * @return Exception
     */
    public static function instantiationNotSupported(string $className): self
    {
        return new self(sprintf('Instantiation of "%s" not supported', $className), self::INSTANTIATION_NOT_SUPPORTED);
    }

    /**
     * @param string $className
     * @param string $setterName
     * @return Exception
     */
    public static function setterArgumentMissing(string $className, string $setterName): self
    {
        return new self(sprintf('%s::%s does not have an argument', $className, $setterName), self::SETTER_ARGUMENT_MISSING);
    }

    /**
     * @param string $className
     * @param string $missingArg
     * @param string ...$moreMissingArgs
     * @return Exception
     */
    public static function constructorArgumentsMissing(string $className, string $missingArg, string ...$moreMissingArgs): self
    {
        $arguments = array_merge([$missingArg], $moreMissingArgs);
        $message = 'Cannot instantiate "%s". Argument%s missing (%s).';

        return new self(sprintf($message, $className, count($arguments) > 1 ? 's' : '', implode(', ', $arguments)), self::CONSTRUCTOR_ARGUMENTS_MISSING);
    }

    /**
     * @param string $className
     * @return Exception
     */
    public static function classNotExists(string $className): self
    {
        return new self(sprintf('Class %s not (auto-)loaded or not exists', $className), self::CLASS_NOT_FOUND);
    }
}