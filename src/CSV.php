<?php

namespace Map;

class CSV implements Map
{
    private $file;
    private $handle;
    private $limit;
    private $rows;
    private $header;

    public function __construct(string $file, int $limit = 2048)
    {
        $this->file = $file;
        $this->handle = fopen($this->file, "r");
        $this->limit = $limit;
        $this->rows = [];
        $this->header = [];
    }

    public function with(callable $func) : Map
    {
        $rowNumber = 0;
        while (!feof($this->handle)) {
            $row = fgetcsv($this->handle, $this->limit);
            if ($rowNumber === 0) {
                $this->header = $row;
            }
            $newRow = $func($row, $rowNumber);
            if ($newRow) {
                $this->rows[] = $newRow;                
            }
            $rowNumber++;
        }
        return $this;
    }

    public function getHeader() : array
    {
        return $this->header;
    }

    public function get() : array
    {
        return $this->rows;
    }
}
