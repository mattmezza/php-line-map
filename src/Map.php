<?php
declare(strict_types=1);

namespace Map;

/**
 * The interface each mapper has to implement
 */
interface Map
{

    /**
     * Maps each line with a callable
     * 
     * @param   callable    $func   The callable function to execute at each line
     * 
     * @return  Map     The self instance for chaining
     */
    public function with(callable $func) : self;

    /**
     * Returns the transformed array
     * 
     * @return  array   The transformed array
     */
    public function toArray() : array;
}
