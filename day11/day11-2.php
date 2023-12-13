<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);

// util fn
function printGrid($grid) {
    foreach($grid as $row) {
        echo implode('', $row) . PHP_EOL;
    }
    echo PHP_EOL;
    echo PHP_EOL;
}

$sum = 0;
$grid = [];
// Parse the input
foreach ($lines as $line) {
    if(empty($line)) continue;

    $grid[] = str_split($line);
}


function isEmptyRow($grid,$row){
    return strpos(implode('', $grid[$row]), '#') === false;
}

// the universe is expanding...
function isEmptyCol($grid, $col){
    $column = array_column($grid, $col);
    return strpos(implode('', $column), '#') === false;
}


function getGalaxies($grid){
    $galaxies = [];
    for($row = 0; $row < count($grid); $row++) {
        for($col = 0; $col < count($grid[0]); $col++) {
            if($grid[$row][$col] === '#') {
                $galaxies[] = [$col, $row];
            }
        }
    }
    return $galaxies;
}

$galaxies = getGalaxies($grid);

$expandCount = 999999;

$pairCheck = 0;
for($i = 0; $i < count($galaxies); $i++) {
    $start = $galaxies[$i];
    for($j = $i + 1; $j < count($galaxies); $j++) {
        ++$pairCheck;
        $end = $galaxies[$j];

        // travesting the grid, get the ammount of steps between the two galaxies
        $steps = 0;
        $x = $start[0];
        $y = $start[1];
        while($x !== $end[0] || $y !== $end[1]) {
            if($x < $end[0]) {
                $x++;
                $steps++;
            } else if($x > $end[0]) {
                $x--;
                $steps++;
            }


            if($y < $end[1]) {
                $y++;
                $steps++;
            }


            if(isEmptyRow($grid, $y)){
                $steps += $expandCount;
            }
            if(isEmptyCol($grid, $x)){
                $steps += $expandCount;
            }
        }

        $sum += $steps;
    }
}


echo $sum . PHP_EOL;
