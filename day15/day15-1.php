<?php

define('DEBUG', true);

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
$sum = 0;
foreach ($lines as $line) {
    $groups = explode(',', $line);

    foreach($groups as $goup){
        $value = 0;
        for ($i = 0; $i < strlen($goup); $i++) {
            $value += ord($goup[$i]);
            $value *= 17;
            $value %= 256;
        }
        echo $value . PHP_EOL;
        $sum += $value;
    }
}

echo $sum . PHP_EOL;
