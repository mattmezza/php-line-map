<?php
declare(strict_types=1);

namespace Map;

class Text implements Map
{
    private $fileName;
    private $handle;
    private $string;
    private $lines;
    private $length;

    public function with(callable $eachRow) : Map
    {
        $lineNumber = 0;
        if ($this->handle) {
            while (!feof($this->handle)) {
                $line = trim(fgets($this->handle, $this->length));
                $newLine = $eachRow($line, $lineNumber);
                if ($newLine) {
                    $this->lines[] = $newLine;                
                }
                $lineNumber++;
            }
            fclose($this->handle);
        } else if ($this->string) {
            foreach (explode("\n", $this->string) as $line) {
                $newLine = $eachRow($line, $lineNumber);
                if ($newLine) {
                    $this->lines[] = $newLine;                
                }
                $lineNumber++;
            }
        }
        return $this;
    }

    public function toArray() : array
    {
        return $this->lines;
    }

    /**
     * Set the value of length
     *
     * @return  self
     */ 
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Set the value of string
     *
     * @return  self
     */ 
    public function setString($string)
    {
        $this->string = $string;

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
     * Set the value of fileName
     *
     * @return  self
     */ 
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

        /**
     * Builds an instance with a string
     */
    public static function string(string $string, int $limit = 2048) : self
    {
        return (new Text())->setString($string)
            ->setLength($limit);
    }

    /**
     * Builds an instance with a resource file
     */
    public static function file($handle, int $limit = 2048) : self
    {
        if (false === is_resource($handle)) {
            throw new InvalidArgumentException(sprintf('Argument must be a valid resource type. %s given.', gettype($handle)));
        }
        return (new Text())->setHandle($handle)
            ->setLength($limit);
    }

    /**
     * Builds an instance with a resource file
     */
    public static function fileNamed(string $filename, int $limit = 2048) : self
    {
        return (new Text())->setHandle(fopen($filename, "r"))
            ->setFilename($filename)
            ->setLength($limit);
    }
}
