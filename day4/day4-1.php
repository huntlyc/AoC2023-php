<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');

$lines = explode("\n", $input);
$winningTickets = array(
);

$sum = 0;
// Parse the input into a 2D array
foreach ($lines as $line) {
    if(empty($line)) continue;

    list($gameID, $numbers) = explode(':', $line);
    list($winningNumbers, $ourNumbers) = explode('|', $numbers);

    $winningNumbers = explode(' ', trim($winningNumbers));
    $winningNumbers = array_filter($winningNumbers, 'is_numeric');

    $ourNumbers = explode(' ', trim($ourNumbers));
    $ourNumbers = array_unique(array_filter($ourNumbers, 'is_numeric'));


    $cardScore = 1;
    $gameTally = 0;
    $matchCount = 0;
    foreach($ourNumbers as $number) {
        if(in_array($number, $winningNumbers)) {
            $j = json_encode($winningNumbers);
            $gameTally += $cardScore;
            if(++$matchCount > 1) $cardScore *= 2;
        }
    }
    $sum += $gameTally;


}

echo $sum . PHP_EOL;
