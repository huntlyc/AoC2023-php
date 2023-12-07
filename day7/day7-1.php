<?php

define('DEBUG', true);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);

$winnings = 0;
$hands = array(); // cards, stake, rank

// Parse the input into a 2D array
foreach ($lines as $line) {
    if(empty($line)) continue;

    list($hand,$stake) = explode(' ', $line);


    $hands[] = array("hand" => $hand, 'value' => calcHandValue($hand), "stake" => $stake);
}

/*
    Five of a kind, where all five cards have the same label: AAAAA
    Four of a kind, where four cards have the same label and one card has a different label: AA8AA
    Full house, where three cards have the same label, and the remaining two cards share a different label: 23332
    Three of a kind, where three cards have the same label, and the remaining two cards are each different from any other card in the hand: TTT98
    Two pair, where two cards share one label, two other cards share a second label, and the remaining card has a third label: 23432
    One pair, where two cards share one label, and the other three cards have a different label from the pair and each other: A23A4
    High card, where all cards' labels are distinct: 23456
*/
function calcHandValue($hand) {
    $uniqCards = array_unique(str_split($hand));

    $value = 1;
    if(count($uniqCards) > 0){
        $maxOfAKind = 0;
        foreach($uniqCards as $card){
            $value += substr_count($hand, $card);
        }
    }

    echo "$uniqCards - $value\n";
    return $value;

}

function curHandBeatsHand($c, $h){
    $map = array(
        'T' => 10,
        'J' => 11,
        'Q' => 12,
        'K' => 13,
        'A' => 14,
    );
    if($c['value'] == $h['value']){
        // check first card
        $cf = $c['hand'][0];
        $hf = $c['hand'][0];
        if(!is_numeric($cf)) $cf = $map[$cf];
        if(!is_numeric($hf)) $hf = $map[$hf];

        return $cf > $hf;

    }else if($c['value'] > $h['value']){
        return true;
    }
    return false;
}

$sum = 0;
foreach($hands as $curHand) {
    $rank = 1;

    foreach($hands as $hand){
        if(curHandBeatsHand($curHand, $hand)){
            ++$rank;
        }else {
            break;
        }
    }
    $sum += $rank * $curHand["stake"];
}

echo $winnings . PHP_EOL;

