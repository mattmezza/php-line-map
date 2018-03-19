PHP Line Map
======

[![Packagist](https://img.shields.io/packagist/v/mattmezza/line-map.svg)](https://github.com/mattmezza/php-line-map) [![PHP from Packagist](https://img.shields.io/packagist/php-v/mattmezza/line-map.svg)](https://github.com/mattmezza/php-line-map) [![GitHub license](https://img.shields.io/github/license/mattmezza/php-line-map.svg)](https://github.com/mattmezza/php-line-map/blob/master/license.md)

Maps a function through the lines of a file, line by line

Install it with `composer require mattmezza/line-map`

Use it like this:

```php
$map = new LineMap\CSV("example.csv");
$rows = $map->with(function ($row, $idx) {
    return array_merge([$idx + 1], $row);
})->get();
$header = $map->getHeader();
```

or

```php
(new Line("lines.txt"))->with(function ($line, $idx) {
    echo "$idx: $line";
});
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
$logs = (new LineMap\CSV("file.csv"))->with(function($row, $idx, $headers) use ($tpl) {
    $msg = $tpl;
    foreach ($headers as $header) {
        $msg = str_replace("{{$header}}", $row[$header], $msg);
    }
    return sendMail($row["email"]);
})->toArray();
```

###### Matteo Merola <mattmezza@gmail.com>