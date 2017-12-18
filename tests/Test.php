<?php

use Map\CSV;
use Map\Line;

class Test extends PHPUnit\Framework\TestCase
{
    public function testTxt() : void
    {
        $expected = ["Matteo", "è", "un", "grandissimo!"];
        $actual = (new Line(__DIR__ . "/prova.txt"))->with(function ($line, $idx) {
            return $line;
        })->get();
        $this->assertEquals(4, count($actual));
        $this->assertEquals($expected, $actual);
    }

    public function testCsv() : void
    {
        $expected = [["Matteo", "è", "un", "grandissimo!"]];
        $actual = (new CSV(__DIR__ . "/prova.csv"))->with(function ($row, $idx) {
            return $row;
        })->get();
        $this->assertEquals(1, count($actual));
        $this->assertEquals($expected, $actual);
    }

    public function testCsvHeader() : void
    {
        $expected = ["Matteo", "è", "un", "grandissimo!"];
        $actual = (new CSV(__DIR__ . "/prova.csv"))->with(function ($row, $idx) {
            return $row;
        })->getHeader();
        $this->assertEquals(4, count($actual));
        $this->assertEquals($expected, $actual);
    }

    public function testExample() : void
    {
        $map = new Map\CSV(__DIR__."/example.csv");
        $rows = $map->with(function ($row, $idx) {
            return array_merge([$idx + 1], $row);
        })->get();
        $header = $map->getHeader();
        $expected = explode(",", "Year,Make,Model,Description,Price");
        $this->assertEquals($expected, $header);
    }
}
