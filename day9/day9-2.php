<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);

/**
 * Quick and dirty fn to return true if 
 * all values in $haystack array match $needle
 * 
 * @param $haystack array to search 
 * @param $needle value to match with
 * @return bool true if **all** values in $haystack match $needle
 */
function array_all($haystack, $needle){
    foreach($haystack as $val){
        if($val !== $needle){
            return false;
        }
    }
    return true;
}

$sum = 0;
// Parse the input
foreach ($lines as $line) {
    if(empty($line)) continue;

    $start = explode(' ', $line);

    $nums = array();
    $nums[] = $start;

    $idx = 0;

    // start reducing nums by difference, repeating untill we get all zeros
    do{

        $nextNums = array();
        for($i = 1; $i < count($nums[$idx]); $i++){
            $nextNums[] = $nums[$idx][$i] - $nums[$idx][$i - 1];
        }
        $nums[] = $nextNums;
        ++$idx;
    }while(!array_all($nums[count($nums) - 1], 0));

    // working from zero up calculate next number
    $nums = array_reverse($nums);
    array_unshift($nums[0],  0); // next num in zero arr always zero
    for($i = 1; $i < count($nums); $i++){

        // next number in array is difference is last num in cur array + last num in last arr
        $cur = $nums[$i][0];
        $prev = $nums[$i - 1][0];

        array_unshift($nums[$i], $cur - $prev);
    }

    $sum += $nums[count($nums)-1][0];
}

echo $sum . PHP_EOL;
