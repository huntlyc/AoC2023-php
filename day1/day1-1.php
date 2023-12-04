<?php
define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode("\n", $input);

$sum = 0;

foreach($lines as $line){
	if(empty(trim($line))) continue;

	$lineNums = preg_replace('/[^0-9]/', '', $line);

	$last = $first = $lineNums[0];

	if(strlen($lineNums) > 1) $last = $lineNums[strlen($lineNums)-1];

	$num = "{$first}{$last}";

	$sum += $num;
}

echo $sum;
