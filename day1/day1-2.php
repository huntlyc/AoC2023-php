<?php
define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input2.txt' : 'input.txt');
$lines = explode("\n", $input);

$sum = 0;

$numMap = [
	'one' => 'one1one',
	'two' => 'two2two',
	'three' => 'three3three',
	'four' => 'four4four',
	'five' => 'five5five',
	'six' => 'six6six',
	'seven' => 'seven7seven',
	'eight' => 'eight8eight',
	'nine' => 'nine9nine',
];

foreach($lines as $line){
	if(empty(trim($line))) continue;

	foreach($numMap as $k => $r){
		$line = str_replace($k, $r, $line);	
	}

	$lineNums = preg_replace('/[^0-9]/', '', $line);

	$last = $first = $lineNums[0];

	if(strlen($lineNums) > 1) $last = $lineNums[strlen($lineNums)-1];

	$num = "{$first}{$last}";


	$sum += $num;
}


echo $sum;
