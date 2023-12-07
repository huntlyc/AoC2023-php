<?php
/**
 * WARN: This takes 15m to run. It is not optimized.
 **/

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode("\n", $input);


$seeds = array();

$maps = array(
    'seed' => array(),
    'soil' => array(),
    'fert' => array(),
    'water' => array(),
    'light' => array(),
    'temp' => array(),
    'humid' => array(),
);
$ans = null;

$key = null;
// Parse the input into a 2D array
foreach ($lines as $line) {
    if(empty($line)) continue;

    // seeds and maps parsing
    if(str_starts_with($line, 'seeds:')) {
        $tmp = explode('seeds: ', $line);
        $tmp = explode(' ', $tmp[1]);

        for($i = 0; $i < count($tmp); $i+=2){
            $seeds[] = array(
                'start' => intval($tmp[$i]),
                'range' => intval($tmp[$i+1])
            );
        }
    }


    if(str_starts_with($line, 'seed-to-soil map')) $key = 'seed';
    if(str_starts_with($line, 'soil-to-fertilizer map')) $key = 'soil';
    if(str_starts_with($line, 'fertilizer-to-water map')) $key = 'fert';
    if(str_starts_with($line, 'water-to-light map')) $key = 'water';
    if(str_starts_with($line, 'light-to-temperature map')) $key = 'light';
    if(str_starts_with($line, 'temperature-to-humidity map')) $key = 'temp';
    if(str_starts_with($line, 'humidity-to-location map')) $key = 'humid';

    if(preg_match('/^([0-9]+) ([0-9]+) ([0-9])/', $line)){
        $tmp = explode(' ', $line);
        if(!is_null($key)){
            $maps[$key][] = array('dest' => $tmp[0], 'src' => $tmp[1], 'range' => $tmp[2]);
        }
    }
}

$locations = array();
$mapKeys = array_reverse(array_keys($maps));


$lowSeed = array();
$minValidLocation = PHP_INT_MAX;
// generate locations
$maxLoc = 0;
foreach($maps['humid'] as $h2l){
    $newMax = $h2l['dest'] + $h2l['range'];
    if($newMax > $maxLoc){
        $maxLoc = $newMax;
    }
}


// TODO: figure out a better way than brute force
for($i = 0; $i <= $maxLoc; $i++){
    $location = $i;
    $val = $location;

    foreach($mapKeys as $key){
        foreach($maps[$key] as $map){
            if($val >= $map['dest'] && $val <= $map['dest'] + $map['range']){

                $diff = $val - $map['dest'];
                $val = $map['src'] + $diff;

                break;
            }
        }

    }

    foreach($seeds as $seedRange){
        $start = $seedRange['start'];
        $end = $seedRange['start'] + $seedRange['range'];
        if($val >= $seedRange['start']  && $val <= $seedRange['start'] + $seedRange['range']){
            echo $location; exit;
        }
    }
}


echo $minValidLocation . PHP_EOL;
