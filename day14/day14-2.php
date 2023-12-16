<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);


$grid = [];
$cache = [];
$rowLen = 0;
foreach ($lines as $line){
    if(empty($line)) continue;

    $grid[] = str_split($line);
    if($rowLen == 0) $rowLen = strlen($line);
}

$cycleCache = [];
$cacheIdx = 0;
for($i = 0; $i < 1_000_000_000; $i++){
    // run a spin cycle
    flipNorth($grid, $cache);
    flipWest($grid, $cache);
    flipSouth($grid, $cache);
    flipEast($grid, $cache);

    $sg = serializeGrid($grid);
    $matchIdx = array_search($sg, $cycleCache);
    if($matchIdx !== false){
        $cacheIdx = $matchIdx;
        break;
    }
    $cycleCache[] = $sg;
}

/**
 * Util fn to serialize grid to string
 * @param $grid
 * @return string
 */
function serializeGrid(&$grid){
    $str = '';
    foreach ($grid as $row){
        $str .= implode('', $row);
    }
    return $str;
}

/**
 * Util fn to unserialize grid from string
 * @param $str
 * @param $rowLen
 * @return array
 */
function unserializeGrid($str, $rowLen){
    $grid = [];
    $rows = str_split($str, $rowLen);
    foreach ($rows as $row){
        $grid[] = str_split($row);
    }
    return $grid;
}


function flipNorth(&$grid, &$cache){
    moveBouldersVertically($grid, $cache, 'N');
}

function flipEast(&$grid, &$cache){
    moveBouldersHorizontally($grid, $cache, 'E');
}

function flipSouth(&$grid , &$cache){
    moveBouldersVertically($grid, $cache, 'S');
}

function flipWest(&$grid, &$cache){
    moveBouldersHorizontally($grid, $cache, 'W');
}


/**
 * Move boulders vertically along the array (N or S)
 * @param $grid
 * @param $cache
 * @param string $dir
 */
function moveBouldersVertically(&$grid, &$cache, $dir = 'N'){
    for($col = 0; $col < count($grid[0]); $col++){
        $row = array_column($grid, $col);

        $curRow = implode('', $row);

        if($dir == 'N') $cache[$curRow] = moveBouldersToStart($row);
        if($dir == 'S') $cache[$curRow] = moveBouldersToEnd($row);

        // set grid col to new configuration
        for($row = 0; $row < count($grid); $row++){
            $grid[$row][$col] = $cache[$curRow][$row];
        }
    }
}

/**
 * Move boulders horizontally along the array (W or E)
 * @param $grid
 * @param $cache
 * @param string $dir
 */
function moveBouldersHorizontally(&$grid, &$cache, $dir = 'E'){
    for($row = 0; $row < count($grid); $row++){
        $curRow = implode('', $grid[$row]);

        if(isset($cache[$curRow])){
            $grid[$row] = str_split($cache[$curRow]);
            continue;
        }

        if($dir == 'E') $cache[$curRow] = moveBouldersToEnd($grid[$row]);
        if($dir == 'W')  $cache[$curRow] = moveBouldersToStart($grid[$row]);

        // set grid row to new configuration
        $grid[$row] = str_split($cache[$curRow]);
    }
}

function moveBouldersToStart($arr){
    return arrayBouldersTo($arr, 'start');
}

function moveBouldersToEnd($arr){
    return arrayBouldersTo($arr, 'end');
}

function arrayBouldersTo($arr, $dir = 'start'){
    $subRows = explode('#', implode('', $arr));

    foreach ($subRows as &$subRow){
        $len = strlen($subRow);
        $boulderCount = substr_count($subRow, 'O');
        if($boulderCount > 0){
            if($dir == 'start'){
                $subRow = str_repeat('O', $boulderCount) . str_repeat('.', $len - $boulderCount);
            }else{
                $subRow = str_repeat('.', $len - $boulderCount).str_repeat('O', $boulderCount);
            }
        }
    }
    $subRows = implode('#', $subRows);
    return $subRows;
}

/**
 * Get the weight of boulders on the north side of the grid
 * @param $grid
 * @return int
 */
function getNorthCornerWeight(&$grid){
    $multiplier = count($grid);
    $sum = 0;
    foreach ($grid as $row){
        $sum += substr_count(implode('', $row), 'O') * $multiplier;
        $multiplier--;
    }
    return $sum;
}

$endStateIndex = count($cycleCache) - 1;
$loopLength = count($cycleCache) - $cacheIdx;
$endStateIndex = ((1_000_000_000 - ($cacheIdx + 1)) % $loopLength) + $cacheIdx;

$endState = unserializeGrid($cycleCache[$endStateIndex], $rowLen);

echo "Weight: " . getNorthCornerWeight($endState) . PHP_EOL; exit;

