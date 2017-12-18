<?php

namespace Map;

class Line implements Map
{
    private $file;
    private $handle;
    private $limit;
    private $lines;

    public function __construct(string $file, int $limit = 2048)
    {
        $this->file = $file;
        $this->handle = fopen($this->file, "r");
        $this->limit = $limit;
        $this->lines = [];
    }

    public function with(callable $func) : Map
    {
        $lineNumber = 0;
        while (!feof($this->handle)) {
            $line = trim(fgets($this->handle, $this->limit));
            $newLine = $func($line, $lineNumber);
            if ($newLine) {
                $this->lines[] = $newLine;                
            }
            $lineNumber++;
        }
        return $this;
    }

    public function get() : array
    {
        return $this->lines;
    }
}
