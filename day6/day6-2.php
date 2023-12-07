<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode("\n", $input);

$time = 0;
$distance = 0;

// Parse the input into a 2D array
foreach ($lines as $line) {
    if(empty($line)) continue;

    $line = preg_replace('/\s+/', ' ', $line);

    if(str_starts_with($line, 'Time:')) $v = &$time;
    if(str_starts_with($line, 'Distance:')) $v = &$distance;

    list($_, $tmp) = explode(':', $line);

    $v = preg_replace('/\s+/', '', trim($tmp));
}



$winCount = 0;
for($t = 0; $t < $time; $t++){
    $speed = $t; // each sec charg ++ speed, linear
    $timeRemain = $time - $t;
    $dist = $timeRemain * $speed;


    if($dist > $distance){
        ++$winCount;
    }
}

echo $winCount. PHP_EOL;
