<?php

define('DEBUG', false);

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

    $value = 0;
    if(count($uniqCards) > 0){
        foreach($uniqCards as $card){
            $tmpVal = 0;
            $ofAKind = substr_count($hand, $card) . PHP_EOL;
            if($ofAKind == 5){
                $tmpVal =  7; //5 of a kind
            }else if($ofAKind == 4){
                $tmpVal =  6; // 4 of a kind
            }else if($ofAKind == 3 && count($uniqCards) == 2){
                $tmpVal = 5; // full house
            }else if($ofAKind == 3){
                $tmpVal = 4;// 3 of a kind
            }else if($ofAKind == 2 && count($uniqCards) == 3){
                $tmpVal = 3; // two pair
            }else if($ofAKind == 2 && count($uniqCards) == 4){
                $tmpVal = 2; // one pair
            }else {
                $tmpVal = 1; // nothing, high card
            }
            if($tmpVal > $value) $value = $tmpVal;
        }
    }

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
        // start checking the highest card, starting with the first card until we find a winner
        foreach(str_split($c['hand']) as $i => $_card){
            $cCard = $c['hand'][$i];
            $hCard = $h['hand'][$i];

            if(!is_numeric($cCard)) $cCard = $map[$cCard];
            if(!is_numeric($hCard)) $hCard = $map[$hCard];

            if($cCard > $hCard){
                return true;
            }else if($cCard < $hCard){
                return false;
            }
        }

    }else if($c['value'] > $h['value']){
        return true;
    }

    return false;
}

$sum = 0;
foreach($hands as &$curHand) {
    $rank = 1;

    foreach($hands as $hand){
        if($curHand['hand'] == $hand['hand']) continue;

        if(curHandBeatsHand($curHand, $hand)){
            ++$rank;
        }
    }
    $curHand['rank'] = $rank;
    $sum += $curHand['rank'] * $curHand["stake"];
}

echo $sum . PHP_EOL;

