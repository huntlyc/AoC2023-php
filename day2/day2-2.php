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

    $minCount = [
        'red' => 0,
        'green' => 0,
        'blue' => 0,
    ];
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

        foreach($count as $colour => $count){
            if($count > $minCount[$colour]){
                $minCount[$colour] = $count;
            }
        }
	}

    var_dump($minCount);

    $pow = $minCount['red'] * $minCount['green'] * $minCount['blue'];

    $sum += $pow;
}

echo $sum;
