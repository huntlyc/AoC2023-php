<?php

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
        $seeds = explode(' ', $tmp[1]);
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
foreach($seeds as $seed) {
    $val = $seed;
    foreach($maps as $mapName => $curMap){
        foreach($curMap as $map){
            if($val >= $map['src'] && $val <= $map['src'] + $map['range']){
                $diff = $val - $map['src'];
                $val = $map['dest'] + $diff;
                break;
            }
        }
    }
    $locations[] = $val;
}


echo min($locations);
