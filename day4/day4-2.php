<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');

$lines = explode("\n", $input);
$winningTickets = array();
$repeatingCards = array();

$sum = 0;
// Parse the input into a 2D array
foreach ($lines as $line) {
    if(empty($line)) continue;

    list($gameID, $numbers) = explode(':', $line);
    $gameID = explode(' ', $gameID);
    $gameID = array_filter($gameID, 'is_numeric');
    $gameID = array_pop($gameID);

    list($winningNumbers, $ourNumbers) = explode('|', $numbers);

    $winningNumbers = explode(' ', trim($winningNumbers));
    $winningNumbers = array_filter($winningNumbers, 'is_numeric');

    $ourNumbers = explode(' ', trim($ourNumbers));
    $ourNumbers = array_unique(array_filter($ourNumbers, 'is_numeric'));

    $playRepeat = 1;
    if(isset($repeatingCards[$gameID])) {
        $playRepeat += $repeatingCards[$gameID]; // repeat num + initial play
    }

    for($i = 0; $i < $playRepeat; $i++) {
        $matchCount = 0;
        foreach($ourNumbers as $number) {
            if(in_array($number, $winningNumbers)) {
               ++$matchCount;
            }
        }

        for($r = 1; $r <= $matchCount; $r++){
            $rGID = $gameID + $r;
            if(!isset($repeatingCards[$rGID])) {
                $repeatingCards[$rGID] = 1;
            }else{
                $repeatingCards[$rGID] += 1;
            }
        }

        ++$sum;
    }
}

echo $sum . PHP_EOL;
