<?php
define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode("\n", $input);

$sum = 0;

$max = [
	'red' => 12,
	'green' => 13,
	'blue' => 14,
];

$curCount = 0;
foreach($lines as $line){
	if(empty(trim($line))) continue;


	list($gameID, $rounds) = explode(':', $line);
	$gameID = preg_replace('/[^0-9]/', '', $gameID);
	$rounds = explode(';', $rounds);

	$validGame = true;
	foreach($rounds as $round){
        $count = [
            'red' => 0,
            'green' => 0,
            'blue' => 0,
        ];

		$cubes = explode(',', $round);
		foreach($cubes as $cube){
			list($num,$colour) = explode(' ', trim($cube));
            $count[$colour] += $num;
		}

        if($count['red'] > $max['red'] ||
           $count['green'] > $max['green'] ||
           $count['blue'] > $max['blue'])
        {
            $validGame = false;
        }

	}

	if($validGame){
        $sum += $gameID;
	}
}

echo $sum;
