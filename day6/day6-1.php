<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode("\n", $input);

$times = array();
$distances = array();
$sum = 0;
// Parse the input into a 2D array
foreach ($lines as $line) {
    if(empty($line)) continue;

    $line = preg_replace('/\s+/', ' ', $line);

    if(str_starts_with($line, 'Time:')) $arr = &$times;
    if(str_starts_with($line, 'Distance:')) $arr = &$distances;

    list($_, $tmp) = explode(':', $line);

    $arr = explode(' ', trim($tmp));
}

$winCounts = array();
for($g = 0; $g < count($times); $g++){
    $winCount = 0;
    // figure out how many ways we can beat the distance
    $minDist = $distances[$g];
    $maxTime = $times[$g];


    for($t = 0; $t < $maxTime; $t++){
        $speed = $t; // each sec charg ++ speed, linear
        $timeRemain = $maxTime - $t;
        $dist = $timeRemain * $speed;


        if($dist > $minDist){
            ++$winCount;
        }
    }

    if($winCount > 1){
        if($sum == 0) $sum = 1;
        echo "$sum * $winCount\n";
        $sum *= $winCount;
    }
}

echo $sum;
