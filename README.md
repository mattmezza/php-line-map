PHP Line Map
======

[![Packagist](https://img.shields.io/packagist/v/mattmezza/line-map.svg)](https://github.com/mattmezza/php-line-map) [![PHP from Packagist](https://img.shields.io/packagist/php-v/mattmezza/line-map.svg)](https://github.com/mattmezza/php-line-map) [![GitHub license](https://img.shields.io/github/license/mattmezza/php-line-map.svg)](https://github.com/mattmezza/php-line-map/blob/master/license.md)

Maps a function through the lines of a file, line by line

Install it with `composer require mattmezza/line-map`

Use it like this:

```php
$map = Map\CSV::fileNamed("example.csv");
$rows = $map->with(function ($row, $idx) {
    return array_merge([$idx + 1], $row);
})->get();
$header = $map->getHeader();
```

or

```php
Map\Txt::fileNamed("lines.txt")->with(function ($line, $idx) {
    echo "$idx: $line";
});
```

# TXT
```php
// the callback function - can be also an anonymous closure
$doStuff = function($line, $lineNumber) {
    return $lineNumber . ": " . $line;
};

// from a string variable
$text = "line 1
line2
line3";
$newLines = Map\Txt::string($text)->with($doStuff)->toArray();

// from a filename
$filename = "./somelines.txt";
$newLines = Map\Txt::fileNamed($filename)->with($doStuff)->toArray();

// from a file resource handle
$file = fopen($filename, "r");
$newLines = Map\Txt::file($file)->with($doStuff)->toArray();
```
# CSV
```php
// the callback function - can be also an anonymous closure
$doStuff = function($row, $lineNumber, $headers) {
    echo $row[$headers[0]];
    return $row;
};

// from a string variable
$csv = "name,surname
John,Doe
Foo,Bar";
$newRows = Map\CSV::string($csv)->with($doStuff)->toArray();

// from a filename
$filename = "./somedata.csv";
$newRows = Map\CSV::fileNamed($filename)->with($doStuff)->toArray();

// from a file resource handle
$file = fopen($filename, "r");
$newRows = Map\CSV::file($file)->with($doStuff)->toArray();
```

##### example

Rapidly cycle through this CSV 

```csv
name,username,email
Matt,mattmezza,mattmezza@gmail.com
```

for each line generate custom message replacing values using the following template

```
Hello {{name}},
your username is {{username}}.
```

send each message to the right user and collect some logs

```php
$tpl = "Hello __name__,
your username is __username__.";
$logs = Map\CSV::fileNamed("file.csv")->with(function($row, $idx, $headers) use ($tpl) {
    $msg = $tpl;
    foreach ($headers as $header) {
        $msg = str_replace("{{".$header."}}", $row[$header], $msg);
    }
    return sendMail($row["email"]);
})->toArray();
```

###### Matteo Merola <mattmezza@gmail.com>
