<?php
declare(strict_types=1);
namespace Map;

class CSV implements Map
{
    /**
     * The filename: used to fclose
     */
    private $filename;
    /**
     * The resource handle of the file
     */
    private $handle;
    /**
     * The return result of the actual map function
     */
    private $rows;
    /**
     * It is a CSV dude: it is the header columns array
     */
    private $header;
    /**
     * Sometimes it is good to map over a string: this is that string
     */
    private $csvString;
    /**
     * Max length of a line
     */
    private $length;
    /**
     * CSV,SCSV,TSV ????
     */
    private $delimiter;
    /**
     * Defaults to "
     */
    private $enclosure;
    /**
     * How are we escaping stuff?? Defaults \\
     */
    private $escape;

    public function with(callable $csvEachRow) : Map
    {
        $rowNumber = 0;
        if ($this->handle) {
            while (!feof($this->handle)) {
                $row = fgetcsv($this->handle, $this->length, $this->delimiter, $this->enclosure, $this->escape);
                if ($rowNumber === 0) {
                    // getting the header
                    $this->header = $row;
                    // skipping - will not call $csvEachRow
                    $rowNumber++;
                    continue;
                }
                // setting the associative 
                $rowAssoc = [];
                for ($i = 0; $i < count($row); $i++) {
                    $rowAssoc[$this->header[$i]] = $row[$i];
                }
                $newRow = $csvEachRow($rowAssoc, $rowNumber, $this->header);
                if ($newRow) {
                    $this->rows[] = $newRow;                
                }
                $rowNumber++;
            }
        } else if ($this->csvString) {
            $headerAndRest = explode("\n", $this->csvString, 2);
            $this->header = str_getcsv($headerAndRest[0], $this->delimiter, $this->enclosure, $this->escape);
            $this->rows = Text::string($headerAndRest[1], $this->length, $this->delimiter, $this->enclosure, $this->escape)
                ->with(function ($line, $number) use ($csvEachRow) {
                    $row = str_getcsv($line, $this->delimiter, $this->enclosure, $this->escape);
                    // setting the associative 
                    $rowAssoc = [];
                    for ($i = 0; $i < count($row); $i++) {
                        $rowAssoc[$this->header[$i]] = $row[$i];
                    }
                    $newRow = $csvEachRow($rowAssoc, $number, $this->header);
                    return $newRow;
                });
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

    /**
     * Set the value of filename
     *
     * @return  self
     */ 
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Set the value of handle
     *
     * @return  self
     */ 
    public function setHandle($handle)
    {
        $this->handle = $handle;

        return $this;
    }

    /**
     * Set the value of limit
     *
     * @return  self
     */ 
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Set the value of csvString
     *
     * @return  self
     */ 
    public function setCsvString($csvString)
    {
        $this->csvString = $csvString;

        return $this;
    }

    /**
     * Set how are we escaping stuff?? Defaults \\
     *
     * @return  self
     */ 
    public function setEscape($escape)
    {
        $this->escape = $escape;

        return $this;
    }

    /**
     * Set enclosure defaults to "
     *
     * @return  self
     */ 
    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * Set the delimiter cSV,SCSV,TSV ????
     *
     * @return  self
     */ 
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * Set max length of a line
     *
     * @return  self
     */ 
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

        /**
     * Builds an instance for working on a string
     * 
     * @param   string    $str
     * The csv string
     * 
     * @param   int     $length
     * Must be greater than the longest line (in characters) to be found in the CSV file (allowing for trailing line-end characters). Otherwise the line is split in chunks of length characters, unless the split would occur inside an enclosure.
     * Omitting this parameter (or setting it to 0 in PHP 5.1.0 and later) the maximum line length is not limited, which is slightly slower.
     * 
     * @param   string  $delimiter
     * The optional delimiter parameter sets the field delimiter (one character only).
     * 
     * @param   string  $enclosure
     * The optional enclosure parameter sets the field enclosure character (one character only).
     * 
     * @param   string  $escape
     * The optional escape parameter sets the escape character (one character only).
     * 
     * @see http://php.net/manual/en/function.str_getcsv.php
     * 
     * @return  self
     */
    public static function string(string $str, int $length = 0, string $delimiter = ",", string $enclosure = '"', string $escape = "\\") : self
    {
        return (new CSV())->setCsvString($str)
            ->setLength($length)
            ->setDelimiter($delimiter)
            ->setEnclosure($enclosure)
            ->setEscape($escape);
    }

    /**
     * Builds an instance for working on a string
     * 
     * @param   string    $str
     * The csv string
     * 
     * @param   int     $length
     * Must be greater than the longest line (in characters) to be found in the CSV file (allowing for trailing line-end characters). Otherwise the line is split in chunks of length characters, unless the split would occur inside an enclosure.
     * Omitting this parameter (or setting it to 0 in PHP 5.1.0 and later) the maximum line length is not limited, which is slightly slower.
     * 
     * @param   string  $delimiter
     * The optional delimiter parameter sets the field delimiter (one character only).
     * 
     * @param   string  $enclosure
     * The optional enclosure parameter sets the field enclosure character (one character only).
     * 
     * @param   string  $escape
     * The optional escape parameter sets the escape character (one character only).
     * 
     * @see http://php.net/manual/en/function.fgetcsv.php
     * 
     * @return  self
     */
    public static function file($handle, int $length = 0, string $delimiter = ",", string $enclosure = '"', string $escape = "\\") : self
    {
        if (false === is_resource($handle)) {
            throw new InvalidArgumentException(sprintf('Argument must be a valid resource type. %s given.', gettype($handle)));
        }
        return (new CSV())->setHandle($handle)
            ->setLength($length)
            ->setDelimiter($delimiter)
            ->setEnclosure($enclosure)
            ->setEscape($escape);
    }

    /**
     * Builds an instance for working on a string
     * 
     * @param   string    $str
     * The csv string
     * 
     * @param   int     $length
     * Must be greater than the longest line (in characters) to be found in the CSV file (allowing for trailing line-end characters). Otherwise the line is split in chunks of length characters, unless the split would occur inside an enclosure.
     * Omitting this parameter (or setting it to 0 in PHP 5.1.0 and later) the maximum line length is not limited, which is slightly slower.
     * 
     * @param   string  $delimiter
     * The optional delimiter parameter sets the field delimiter (one character only).
     * 
     * @param   string  $enclosure
     * The optional enclosure parameter sets the field enclosure character (one character only).
     * 
     * @param   string  $escape
     * The optional escape parameter sets the escape character (one character only).
     * 
     * @see http://php.net/manual/en/function.fgetcsv.php
     * 
     * @return  self
     */
    public static function fileNamed(string $filename, int $length = 0, string $delimiter = ",", string $enclosure = '"', string $escape = "\\") : self
    {
        return (new CSV())->setFilename($filename)
            ->setHandle(fopen($filename, "r"))
            ->setLength($length)
            ->setDelimiter($delimiter)
            ->setEnclosure($enclosure)
            ->setEscape($escape);
    }
}
