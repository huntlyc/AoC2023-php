<?php

define('DEBUG', false);
$testInputs = [
    'test2-input.txt',
    'test2-input2.txt',
    'test2-input3.txt',
];

$input = file_get_contents(DEBUG ? $testInputs[2] : 'input.txt');
$lines = explode(PHP_EOL, $input);
// Parse the input into 2d array
$grid = array();
foreach ($lines as $line) {
    if(empty($line)) continue;

    $grid[] = str_split($line);
}

$startPos = [0,0];
for($row = 0; $row < count($grid); $row++){
    $sp = null;
    for($col = 0; $col < count($grid[$row]); $col++){
        if($grid[$row][$col] == 'S'){
            $sp = [$row,$col];
            break;
        }
    }
    if(!is_null($sp)){
        $startPos = $sp;
        break;
    }
}


// Mappings for directions in x,y co-ords as movement vectors
$validUp = [
    '|' => [0,-1],
    '7' => [-1,-1],
    'F' => [1,-1],
];
$validDown = [
    '|' => [0,1],
    'J' => [-1,1],
    'L' => [1,1],
];
$validRight = [
    '-' => [1,0],
    '7' => [1,1],
    'J' => [1,-1],
];
$validLeft = [
    '-' => [-1,0],
    'L' => [-1,-1],
    'F' => [-1,1],
];

function isValidUp($cc, $nc){
    return in_array($cc, ['L','J','|']) && in_array($nc, ['|', '7', 'F']);
}

function isValidDown($cc, $nc){
    return in_array($cc, ['|', '7', 'F']) && in_array($nc, ['|', 'J', 'L']);
}

function isValidRight($cc,$nc){
    return in_array($cc, ['-', 'L', 'F']) && in_array($nc, ['-', '7', 'J']);
}

function isValidLeft($cc,$nc){
    return in_array($cc, ['-', '7', 'J']) && in_array($nc, ['-', 'L', 'F', '7']);
}

function calcStartChar($p, $grid) {
    // start must have 2 valid chars in certian directions to make it valid
    $chars = ['|', '-', 'L', 'J', '7', 'F'];

    $py = $p[0];
    $px = $p[1];
    foreach($chars as $c){
        switch($c){
            case '|':
                if($py > 0 && $py < count($grid)){
                    if(in_array( $grid[$py-1][$px], ['|', '7', 'F']) && in_array( $grid[$py+1][$px], ['|', 'J', 'L'])){
                        return $c;
                    }
                }
                break;
            case '-':
                if($px > 0 && $px < count($grid[$py])){
                    if(in_array( $grid[$py][$px + 1],['-', '7', 'J']) && in_array( $grid[$py][$py - 1],['-', 'L', 'F'])) {
                        return $c;
                    }
                }
                break;
            case 'L':
                if($py > 0 && $px < count($grid[$py])){
                    if(in_array( $grid[$py][$px + 1],['-', '7', 'J']) && in_array( $grid[$py - 1][$px],['-', '7', 'J'])) {
                        return $c;
                    }
                }
                break;
            case 'J':
                if($py > 0 && $px > 0){
                    if(in_array( $grid[$py][$px - 1],['-', 'L', 'F']) && in_array( $grid[$py - 1][$px],['-', '7', 'J'])) {
                        return $c;
                    }
                }
                break;
            case '7':
                if($px < 0 && $py < count($grid)){
                    if(in_array( $grid[$py][$px - 1],['-', 'L', 'F']) && in_array( $grid[$py + 1][$px],['|', 'J', 'L'])) {
                        return $c;
                    }
                }
                break;
            case 'F':
                if($px < count($grid[$py]) && $py < count($grid)){
                    if(in_array( $grid[$py][$px + 1],['-', '7', 'J']) && in_array( $grid[$py + 1][$px],['|', 'J', 'L'])) {
                        return $c;
                    }
                }
                break;
        }
    }

    return null;
}

$startChar = calcStartChar($startPos, $grid);
if(is_null($startChar)){
    throw New Error('Could not calc start char');
}

$grid[$startPos[0]][$startPos[1]] = $startChar;


$maxDist = $steps = 0;
$curPos = [
    $startPos[0],
    $startPos[1],
];
$newVel = [0,0];
$steps = [];
// walk through maze counting steps untill we're back at our starting position
do{
    $cy = $curPos[0];
    $cx = $curPos[1];

    $validMove = false;

    $c = $grid[$cy][$cx];

    // up
    if(!$validMove && $newVel[1] != 1 && ($cy - 1) >= 0 && isValidUp($c, $grid[$cy - 1][$cx])){
        $nextSq = [$cy -1, $cx];
        if(empty($steps) || $steps[count($steps) -1] !== "{$nextSq[0]},{$nextSq[1]}"){ // don't go back
            $nextChar = $grid[$cy - 1][$cx];
            $newVel = $validUp[$nextChar];
            $curPos = $nextSq;
            $validMove = true;
        }
    }

    // right
    if(!$validMove && $newVel[0] != -1 && ($cx + 1) < count($grid[$cy]) && isValidRight($c, $grid[$cy][$cx + 1])){
        $nextSq = [$cy, $cx + 1];
        if(empty($steps) || $steps[count($steps) -1] !== "{$nextSq[0]},{$nextSq[1]}"){ // don't go back
            $nextChar = $grid[$cy][$cx + 1];
            $newVel = $validRight[$nextChar];
            $curPos = $nextSq;
            $validMove = true;
        }
    }

    // down
    if(!$validMove && $newVel[1] != -1 && ($cy + 1) < count($grid) && isValidDown($c, $grid[$cy + 1][$cx])){
        $nextSq = [$cy + 1, $cx];
        if(empty($steps) || $steps[count($steps) -1] !== "{$nextSq[0]},{$nextSq[1]}"){ // don't go back
            $nextChar = $grid[$cy + 1][$cx];
            $newVel = $validDown[$nextChar];
            $curPos = $nextSq;
            $validMove = true;
        }
    }

    // left
    if(!$validMove && $newVel[0] != 1 && ($cx - 1) >= 0 && isValidLeft($c, $grid[$cy][$cx - 1])){
        $nextSq = [$cy, $cx - 1];
        if(empty($steps) || $steps[count($steps) -1] !== "{$nextSq[0]},{$nextSq[1]}"){ // don't go back
            $nextChar = $grid[$cy][$cx - 1];
            $newVel = $validLeft[$nextChar];
            $curPos = $nextSq;
            $validMove = true;
        }
    }

    if(!$validMove) throw new Error('No valid move!');

    //echo "next: $nextChar ($curPos[0],$curPos[1])\n";


    $steps[] = "{$curPos[0]},{$curPos[1]}";
}while(count($steps) < 20000 && "{$curPos[0]},{$curPos[1]}" !== "{$startPos[0]},{$startPos[1]}");


/**
 * Drawing algo to fill in the empty spaces up to walls
 * We'll use it to remove all the outer empty spaces
 * leaving us with dots only inside the loop
 * @param $grid
 * @param $x
 * @param $y
 * @param $newColor
 * @param $oldColor
 */
function floodFill(&$grid, $x, $y, $newColor, $oldColor) {
    $rows = count($grid);
    $cols = count($grid[0]);

    if ($x < 0 || $x >= $rows || $y < 0 || $y >= $cols || $grid[$x][$y] !== $oldColor || $grid[$x][$y] === $newColor) {
        return;
    }

    $grid[$x][$y] = $newColor;

    floodFill($grid, $x + 1, $y, $newColor, $oldColor);
    floodFill($grid, $x - 1, $y, $newColor, $oldColor);
    floodFill($grid, $x, $y + 1, $newColor, $oldColor);
    floodFill($grid, $x, $y - 1, $newColor, $oldColor);
}


// prep grid by removing junk chars
for($row = 0; $row < count($grid); $row++){
    for($col = 0; $col < count($grid[$row]); $col++){
        if(!in_array("$row,$col",$steps)){
            $grid[$row][$col] = '.';
        }
    }
}


// remove u an n bends, and change snake bends to pipes
for($row = 0; $row < count($grid); $row++){
    $tmp = implode('', $grid[$row]);
    $tmp = str_replace('-', '', $tmp); // remove these to collapse potential snake bends
    $tmp = str_replace('FJ', '|', $tmp);
    $tmp = str_replace('L7', '|', $tmp);
    $tmp = str_replace('LJ', '', $tmp);
    $tmp = str_replace('F7', '', $tmp);
    $grid[$row] = str_split($tmp);
}


// loop grid and figure out inner spaces
$count = 0;
$outside = true;
for($row = 0; $row < count($grid); $row++){
    for($col = 0; $col < count($grid[$row]); $col++){
        if($grid[$row][$col] == '|'){
            $outside = !$outside;
        }else{
            if(!$outside){
                $count++;
            }
        }
    }
}

echo $count .PHP_EOL;

exit;
