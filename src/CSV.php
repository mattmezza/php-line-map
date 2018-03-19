<?php
declare(strict_types=1);
namespace LineMap;

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
                // getting the header
                $this->header = $row;
                // skipping - will not call $func
                $rowNumber++;
                continue;
            }
            // setting the associative 
            $rowAssoc = [];
            for ($i = 0; $i < count($row); $i++) {
                $rowAssoc[$this->header[$i]] = $row[$i];
            }
            $newRow = $func($rowAssoc, $rowNumber, $this->header);
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

    public function toArray() : array
    {
        return $this->rows;
    }
}
