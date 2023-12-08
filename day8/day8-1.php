<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);

$dir = array();
$locationMap = array();
$curLocation = null;
// Parse the input
foreach ($lines as $line) {
    if(empty($line)) continue;

    if(empty($dir)){
        $dir = str_split($line);
    }else if(preg_match('/=/', $line)){
        list($location,$lrLocations) = explode('=', $line);
        $location = trim($location);
        $matches = array();
        if(preg_match('/\(([A-Z]+), ([A-Z]+)\)/', $line, $matches)){
            $locationMap[$location] = array($matches[1], $matches[2]);
        }
        if(is_null($curLocation)){
            $curLocation = $location;
        }
    }
}

$steps = 0;
$dirIdx = 0;
while($curLocation !== 'ZZZ'){
    if($dir[$dirIdx] === 'L') $curLocation = $locationMap[$curLocation][0];
    if($dir[$dirIdx] === 'R') $curLocation = $locationMap[$curLocation][1];

    ++$dirIdx;
    if($dirIdx > count($dir) - 1){
        $dirIdx = 0;
    }
    ++$steps;
}

echo $steps . PHP_EOL;
