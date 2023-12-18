<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);

$dirMap = [
    '0' => 'R',
    '1' => 'D',
    '2' => 'L',
    '3' => 'U',
];
$area = 0; $parimeter = 0;
$x = 0; $y = 0;
$matches = [];
foreach ($lines as $line) {
    if(preg_match('/\(\#([0-9a-fA-F]+)\)$/', $line, $matches) !== 1) {
        continue;
    }

    $hex = $matches[1];
    $numMoves = hexdec(substr($hex, 0, 5));

    $dir = substr($hex, 5, 1);
    $dir = $dirMap[$dir];

    $parimeter += $numMoves;

        switch ($dir) {
            case 'U':
                $area += $x * $numMoves / 2;
                $y -= $numMoves;
                break;
            case 'D':
                $area += $x * -$numMoves / 2;
                $y += $numMoves;
                break;
            case 'R':
                $area += $y *  $numMoves / 2;
                $x += $numMoves;
                break;
            case 'L':
                $area += $y *  -$numMoves / 2;
                $x -= $numMoves;
                break;
        }
}

$area = abs($area);
$area = (abs($area) + $parimeter / 2 + 1);
echo $area. PHP_EOL;

die;
