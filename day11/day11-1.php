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


// the universe is expanding...
function expandEmptyRowsAndCols($grid){
    // check for empty rows first
    for($row = 0; $row < count($grid); $row++) {
        if(strpos(implode('', $grid[$row]), '#') === false) { // inject and empty row before this row
            $emptyRow = array_fill(0, count($grid[0]), '.');
            array_splice($grid, $row, 0, [$emptyRow]);
            ++$row; // compensate for the new row
        }
    }

    // check for empty columns
    for($col = 0; $col < count($grid[0]); $col++) {
        $column = array_column($grid, $col);
        if(strpos(implode('', $column), '#') === false) { // inject an empty column before this column
            foreach($grid as &$row) {
                array_splice($row, $col, 0, '.');
            }
            ++$col; // compensate for the new column
        }
    }

    return $grid;
}
$grid = expandEmptyRowsAndCols($grid);


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

        }

        $sum += $steps;
    }
}

echo $sum . PHP_EOL;
