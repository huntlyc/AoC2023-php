<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);

function printGrid(&$grid){
    foreach ($grid as $row){
        echo implode('', $row) . PHP_EOL;
    }
    echo PHP_EOL;
}

$grid = [];
foreach ($lines as $line){
    if(empty($line)) continue;

    $grid[] = str_split($line);
}

moveBoulders($grid);

// split array by rocks (#), then move all boulders (O) to front of array
function moveBoulders(&$grid){
    for($col = 0; $col < count($grid[0]); $col++){
        $row = array_column($grid, $col);

        $subRows = explode('#', implode('', $row));

        foreach ($subRows as &$subRow){
            $len = strlen($subRow);
            $boulderCount = substr_count($subRow, 'O');
            if($boulderCount > 0){
                $subRow = str_repeat('O', $boulderCount) . str_repeat('.', $len - $boulderCount);
            }
        }
        $subRows = implode('#', $subRows);



        // set grid col to new row
        for($row = 0; $row < count($grid); $row++){
            $grid[$row][$col] = $subRows[$row];
        }
    }

}

function getWeight(&$grid){
    $multiplier = count($grid);
    $sum = 0;
    foreach ($grid as $row){
        // count boulders 'O' in row
        $sum += substr_count(implode('', $row), 'O') * $multiplier;
        $multiplier--; // reduce multiplier as we head south
    }
    return $sum;
}
echo getWeight($grid) . PHP_EOL;
