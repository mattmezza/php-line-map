PHP Line Map
======

Maps a function through the lines of a file, line by line

Install it with `composer require mattmezza/line-map`

Use it like this:

```php
$map = new Map\CSV("example.csv");
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