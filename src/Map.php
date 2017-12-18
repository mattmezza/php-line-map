<?php

namespace Map;

interface Map
{

    public function __construct(string $file, int $limit = 2048);

    public function with(callable $func) : Map;

    public function get() : array;
}
