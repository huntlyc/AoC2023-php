<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);

/*
 * algorithm:
 * current value of 0. Then, for each character in the string starting from the beginning:

    Determine the ASCII code for the current character of the string.
    Increase the current value by the ASCII code you just determined.
    Set the current value to itself multiplied by 17.
    Set the current value to the remainder of dividing itself by 256.
*/
$labelHashMap = [];
$instructions = [];
foreach ($lines as $line) {
    $groups = explode(',', $line);


    foreach($groups as $goup){

        if(strpos($goup, '=') !== false){
            list($label,$lens) = explode('=', $goup);
            $instructions[] = [$label, $lens];
        }

        if(strpos($goup, '-') !== false){
            $label = explode('-', $goup)[0];
            $instructions[] = [$label, -1];
        }

        $hashValue = 0;
        for ($i = 0; $i < strlen($label); $i++) {
            $hashValue += ord($label[$i]);
            $hashValue *= 17;
            $hashValue %= 256;
        }

        $labelHashMap[$label] = $hashValue;
    }
}


$boxes = [];
foreach($instructions as $instruction){
    list($label, $lens) = $instruction;
    $lens = intval($lens);
    $hashValue = $labelHashMap[$label];

    if($lens == -1){
        if(isset($boxes[$hashValue]) && isset($boxes[$hashValue][$label])){
            $boxLenses = json_encode($boxes[$hashValue]);
            $idx = array_search($label, array_keys($boxes[$hashValue]));
            array_splice($boxes[$hashValue], $idx, 1);
        }
    } else {
        if(isset($boxes[$hashValue][$label])){
            $boxes[$hashValue][$label] = $lens;
        }else{
            $boxes[$hashValue][$label] = $lens;
        }
    }
}


$sum = 0;
foreach($boxes as $key => $box){
    $keyVal = $key + 1;
    $count = 0;
    foreach($box as $label => $lens){
        $val = $keyVal * ++$count * $lens;
        $sum += $val;
    }
}
echo $sum . PHP_EOL;
