<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);


$sum = 0;

foreach ($lines as $line){
    if(empty($line)) continue;
    list($springs, $groups) = explode(" ", $line);
    $groups = explode(',', $groups);


    $unfoldedSprings = '';
    // unfold the springs
    for($i = 0; $i < 5; $i++){
        $unfoldedSprings .= $springs . '?';
        if($i == 4){
            $unfoldedSprings = substr($unfoldedSprings, 0, -1);
        }
    }

    $unfoldedGroups = [];
    for($i = 0; $i < 5; $i++){
        foreach($groups as $group){
            $unfoldedGroups[] = $group;
        }
    }

    $result = findPosArrangements($unfoldedSprings, $unfoldedGroups);
    $sum += $result;
}

/**
 * Recusrive, Dynamic programming fn to find the number of posible arrangements
 * of springs
 *
 * Good intro to the topic: https://www.youtube.com/watch?v=oBt53YbR9Kk
 *
 * @param string $springs The springs to place
 * @param array $groups The remaining groups
 * @param array $memoCache The memoization cache
 * @return int The number of possible arrangements
 */
function findPosArrangements($springs, $groups, &$memoCache = []){
    $numArrangements = 0;

    // if we've already calculated this, return the cached result
    $memoKey = "$springs:".implode(",",$groups);
    if(isset($memoCache[$memoKey])){
        return $memoCache[$memoKey];
    }

    /**
     * Base cases:
     * 1. No more springs to place and no groups left
     * 2. No more groups to place and no more damaged springs
     */
    if($springs == ""){ // end of the line with no groups left?
        return (count($groups) == 0 ? 1 : 0);
    }

    if(count($groups) == 0){ // no remaining groups
        return (strpos($springs, "#") === false ? 1 : 0);
    }


    $curChar = $springs[0];
    $curGroupLength = $groups[0];



    /**
     * Deal with potential damaged springs
     **/
    if(in_array($curChar, ['.', '?'])){
        $remainingSprings = substr($springs, 1);
        $numArrangements += findPosArrangements($remainingSprings, $groups, $memoCache);
    }

    /**
     * Deal with actual damaged springs
     */
    if(in_array($curChar, ['#', '?'])){
        if(strlen($springs) >= $curGroupLength){
            $curGrouping = substr($springs, 0, $curGroupLength);
            if(!str_contains($curGrouping, ".")){
                $lastChar = '';
                if(isset($springs[$curGroupLength])){
                    $lastChar = $springs[$curGroupLength];
                }
                if(in_array($lastChar, ['', '?', '.'])){ // no more springs, potential damaged, or end of group
                    $groupEnd = $curGroupLength + 1; // plus one as theres'a '.' after the group
                    $remainingSprings = substr($springs, $groupEnd);
                    $remainingGroups = array_slice($groups, 1);
                    $numArrangements += findPosArrangements($remainingSprings, $remainingGroups, $memoCache);
                }
            }
        }
    }


    return $memoCache[$memoKey] = $numArrangements;
}

echo $sum . PHP_EOL;
