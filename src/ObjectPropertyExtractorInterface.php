<?php


namespace Drieschel\ObjectCreator;


interface ObjectPropertyExtractorInterface
{
    /**
     * @return string
     */
    public function getPropertyName(): string;

    /**
     * @return string
     */
    public function getNormalizedPropertyName(): string;

    /**
     * @param object $instance
     * @return mixed
     */
    public function extractPropertyValue(object $instance);
}