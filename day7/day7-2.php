<?php
// ans log
// 253187950 =  too low
// 252686427
// 253188840



define('JOKER_RULE', true);
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

    $FIVE_OF_A_KIND = 7;
    $FOUR_OF_A_KIND = 6;
    $FULL_HOUSE = 5;
    $THREE_OF_A_KIND = 4;
    $TWO_PAIR = 3;
    $ONE_PAIR = 2;
    $HIGH_CARD = 1;


    $value = 0;
    $uniqCardCount = count($uniqCards);
    if($uniqCardCount > 0){
        foreach($uniqCards as $card){
            $tmpVal = 0;
            $ofAKind = substr_count($hand, $card) . PHP_EOL;
            switch($ofAKind){
                case 5: $tmpVal =  $FIVE_OF_A_KIND; break;
                case 4: $tmpVal =  $FOUR_OF_A_KIND; break;
                case 3:
                    if($uniqCardCount == 2){
                        $tmpVal = $FULL_HOUSE;
                    }else{
                        $tmpVal = $THREE_OF_A_KIND;
                    }
                    break;
                case 2:
                    if($uniqCardCount == 3){
                        $tmpVal = $TWO_PAIR;
                    }else if($uniqCardCount == 4){
                        $tmpVal = $ONE_PAIR;
                    }
                    break;
                default: $tmpVal = $HIGH_CARD; break;
            }

            if($tmpVal > $value) $value = $tmpVal;
        }
    }


    /*
     * joker rule
     * ----------
     * Joker can morph into any card, so
     * 5555J => 55555
     * AAJJ2 => AAAA2
     * AAJ22 => AAA22
     * e.t.c
     **/
    if(JOKER_RULE && strpos($hand, 'J') !== FALSE){ // joker rule
        switch($value){
            case $FOUR_OF_A_KIND: //AAAJ
            case $FULL_HOUSE: //JJJAA
                $value = $FIVE_OF_A_KIND;
                break;
            case $THREE_OF_A_KIND: //JJJAA
                $value = $FOUR_OF_A_KIND;
                break;
            case $TWO_PAIR:
                if(substr_count($hand, 'J') == 2){ //JJAA2
                    $value = $FOUR_OF_A_KIND;
                }else{ // JAA22
                    $value = $FULL_HOUSE;
                }
                break;
            case $ONE_PAIR: //AAJ45
                $value = $THREE_OF_A_KIND; break;
            default: $value = $ONE_PAIR; break; //J1234
        }
    }

    return $value;
}

function curHandBeatsHand($c, $h){
    $map = array(
        'T' => 10,
        'J' => JOKER_RULE ? 1: 11,
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

