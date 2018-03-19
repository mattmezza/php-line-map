<?php
declare(strict_types=1);

namespace LineMap;

/**
 * The interface each mapper has to implement
 */
interface Map
{

    /**
     * Builds an instance of Map
     * 
     * @param   string  $file   The filename of the actual file to map on
     * @param   int     $limit  An optional limit for long lines (defaults to 2048)
     */
    public function __construct(string $file, int $limit = 2048);

    /**
     * Maps each line with a callable
     * 
     * @param   callable    $func   The callable function to execute at each line
     * 
     * @return  Map     The self instance for chaining
     */
    public function with(callable $func) : Map;

    /**
     * Returns the transformed array
     * 
     * @return  array   The transformed array
     */
    public function toArray() : array;
}
