<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input2.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);

$directions = array();
$locationMap = array();

// Parse the input
foreach ($lines as $line) {
    if(empty($line)) continue;

    if(empty($directions)){
        $directions = str_split($line);
    }else if(preg_match('/=/', $line)){
        list($location,$lrLocations) = explode('=', $line);
        $location = trim($location);
        $matches = array();
        if(preg_match('/\(([0-9A-Z]+), ([0-9A-Z]+)\)/', $line, $matches)){
            $locationMap[$location] = array($matches[1], $matches[2]);
        }
    }
}


function stepsToDest($start, $locationMap, $directions){
    $steps = 0;
    $dirIdx = 0;
    $curLocation = $start;
    while(!str_ends_with($curLocation, 'Z') && $steps < 4206900){
        if($directions[$dirIdx] === 'L') $curLocation = $locationMap[$curLocation][0];
        if($directions[$dirIdx] === 'R') $curLocation = $locationMap[$curLocation][1];

        ++$dirIdx;
        if($dirIdx > count($directions) - 1){
            $dirIdx = 0;
        }
        ++$steps;
    }
    return $steps;
}

// greatest common devisor
function gcd($x, $y){
    if($y==0)
        return $x;
    return gcd($y,$x%$y);
}

// lowest common multiple
function lcm($x, $y){
    if($x>$y){
        return ($x/gcd($x,$y))*$y;
    }else{
        return ($y/gcd($x,$y))*$x;    
    }
} 


$startLocations = array_filter(array_keys($locationMap), function($loc){
    return preg_match('/A$/', $loc);
});


$stepsArr = array();
foreach($startLocations as $start){
    $stepsArr[] = stepsToDest($start, $locationMap, $directions);
}


/**
 * Taking the example input 2, our $stepsArr is [2,3]
 * We want to find out given these results, whats the lowest common multiple which would
 * equalise all step counts to match each other.
 * 
 * 6 would give us 2 repeating 3 times, and 3 repeating twice to get both to match at 6 steps
 * 
 */
$steps = array_reduce($stepsArr, function($carry, $cur){
    if(is_null($carry)) return $cur;
    return lcm($carry, $cur);
});

echo $steps . PHP_EOL;