<?php

/**
 * 30605 - too high
 * 26777 - too high
 * 24244 - too high
 * 23737 - incorrect
 * 22906 - correct
 **/
define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);


$sum = 0;
$grids = [];
$grid = [];

foreach ($lines as $line){
    if(empty($line)){ // start a new grid
        if(!empty($grid)){
            $grids[] = $grid;
            $grid = [];
        }
        continue;
    }
    $grid[] = str_split($line);
}


foreach($grids as $grid){
}


/**
 * Find the total diff count for rows from a start index
 * @param $grid - 2d array of chars
 * @param $curRowIdx - current row index
 * @return int - diff count
 */
function diff_row(&$grid, $curRowIdx){
    $remainRowCount = min($curRowIdx + 1, count($grid) - $curRowIdx - 1);

    $sum = 0;
    for($rowIdx = 0; $rowIdx < $remainRowCount; $rowIdx++){
        $rowBefore = $grid[$curRowIdx - $rowIdx];
        $rowAFter = $grid[$curRowIdx + $rowIdx + 1];

        $sum += count(array_diff_assoc($rowBefore, $rowAFter));
    }
    return $sum;
}


/**
 * Find the total diff count for cols from a start index
 * @param $grid - 2d array of chars
 * @param $curColIdx - current col index
 * @return int - diff count
 */
function diff_col(&$grid, $curColIdx){
    $remainColCount = min($curColIdx + 1, count($grid[0]) - $curColIdx - 1);

    $sum = 0;
    for($rowIdx = 0; $rowIdx < $remainColCount; $rowIdx++){
        $colBefore = array_column($grid, $curColIdx - $rowIdx);
        $colAfter = array_column($grid, $curColIdx + $rowIdx + 1);

        $sum += count(array_diff_assoc($colBefore, $colAfter));
    }
    return $sum;
}


/**
 * Find the reflection point in the grid
 *
 * @param $grid - 2d array of chars
 * @return int - score
 */
function calcReflecPointScore(&$grid){
    $row = false;
    for($rowIdx = 0; $rowIdx < count($grid) - 1; $rowIdx++){
        if(diff_row($grid, $rowIdx) == 1){
            $row = $rowIdx + 1;
            break;
        }
    }

    $col = false;
    for($colIdx = 0; $colIdx < count($grid[0]) - 1; $colIdx++){
        if(diff_col($grid, $colIdx) == 1){
            $col = $colIdx + 1;
            break;
        }
    }

    if($row) return $row * 100;
    if($col) return $col;

    throw new Exception("No reflection point found");
}



foreach($grids as $grid){
    $sum += calcReflecPointScore($grid);
}
echo "Sum: $sum". PHP_EOL;
