<?php
declare (strict_types = 1);

class Test extends PHPUnit\Framework\TestCase
{
    public function testTxt(): void
    {
        $expected = ["Matteo", "è", "un", "grandissimo!"];
        $actual = Map\Text::fileNamed(__DIR__ . "/prova.txt")->with(function ($line, $idx) {
            return $line;
        })->toArray();
        $this->assertEquals(4, count($actual));
        $this->assertEquals($expected, $actual);
    }

    public function testTxtFilename(): void
    {
        $expected = ["Matteo", "è", "un", "grandissimo!"];
        $actual = Map\Text::file(fopen(__DIR__ . "/prova.txt", "r"))->with(function ($line, $idx) {
            return $line;
        })->toArray();
        $this->assertEquals(4, count($actual));
        $this->assertEquals($expected, $actual);
    }

    public function testTxtString(): void
    {
        $expected = ["Matteo", "è", "un", "grandissimo!"];
        $string = "Matteo\nè\nun\ngrandissimo!";
        $actual = Map\Text::string($string)->with(function ($line, $idx) {
            return $line;
        })->toArray();
        $this->assertEquals(4, count($actual));
        $this->assertEquals($expected, $actual);
    }

    public function testCsv(): void
    {
        $expected = [["Matteo", "è", "un", "grandissimo!"]];
        $rows = Map\CSV::fileNamed(__DIR__ . "/prova.csv")->with(function ($row, $idx) {
            switch ($idx) {
                case 1:
                    $this->assertEquals("Matteo", $row["nome"]);
                    break;
                case 2:
                    $this->assertEquals("è", $row["verbo"]);
                    break;
                case 3:
                    $this->assertEquals("un", $row["articolo"]);
                    break;
                case 4:
                    $this->assertEquals("grandissimo!", $row["aggettivo"]);
                    break;
                default:
                    $this->assertTrue(false);
                    break;
            }
            return array_values($row);
        })->toArray();
        $this->assertEquals(1, count($rows));
        $this->assertEquals($expected, $rows);
    }

    public function testCsvHeader(): void
    {
        $expected = ["nome", "verbo", "articolo", "aggettivo"];
        $actual = Map\CSV::fileNamed(__DIR__ . "/prova.csv")->with(function ($row, $idx) {
            return $row;
        })->getHeader();
        $this->assertEquals(4, count($actual));
        $this->assertEquals($expected, $actual);
    }

    public function testCsvHeaderFile(): void
    {
        $expected = ["nome", "verbo", "articolo", "aggettivo"];
        $actual = Map\CSV::file(fopen(__DIR__ . "/prova.csv", "r"))->with(function ($row, $idx) {
            return $row;
        })->getHeader();
        $this->assertEquals(4, count($actual));
        $this->assertEquals($expected, $actual);
    }

    public function testCsvHeaderString(): void
    {
        $string = "nome,verbo,articolo,aggettivo\nMatteo,è,un,grandissimo!";
        $expected = ["nome", "verbo", "articolo", "aggettivo"];
        $actual = Map\CSV::string($string)->with(function($row, $idx) {
            return $row;
        })->getHeader();
        $this->assertEquals(4, count($actual));
        $this->assertEquals($expected, $actual);
    }

    public function testExample(): void
    {
        $map = Map\CSV::fileNamed(__DIR__ . "/example.csv");
        $header = $map->with(function ($row, $idx) {
            return $row;
        })->getHeader();
        $expected = explode(",", "Year,Make,Model,Description,Price");
        $this->assertEquals($expected, $header);
    }

    public function testExample2() : void
    {
        $tpl = "Hello {{name}},
        your username is {{username}}.";
        $expected1 = "Hello Matt,
        your username is mattmezza.";
        $expected2 = "Hello Mario,
        your username is m.rossi.";
        $logs = Map\CSV::fileNamed(__DIR__ . "/example2.csv")->with(function($row, $idx, $headers) use ($tpl) {
            $msg = $tpl;
            foreach ($headers as $header) {
                $msg = str_replace("{{".$header."}}", $row[$header], $msg);
            }
            return $this->sendEmail($row["email"], $msg);
        })->toArray();
        $this->assertEquals($expected1, $logs[0]);
        $this->assertEquals($expected2, $logs[1]);
    }

    private function sendEmail($email, $msg) : string
    {
        return $msg;
    }
}
