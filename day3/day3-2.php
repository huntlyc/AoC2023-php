<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');

$lines = explode("\n", $input);
$rows = array();

// Parse the input into a 2D array
foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) {
        continue;
    }

    $rows[] = str_split($line);
}

function isSymbol($char) {
    if($char == '.') return false;
    return !preg_match('/[0-9]/',$char);
}

$validNumbers = array();
$starMatches = array();
$gears = array();

for($row = 0; $row < count($rows); $row++) {
    $curNumAsStr = '';
    $parsingNum = false;
    $shouldCheck = false;
    $numEndCol = false;

    for($col = 0; $col < count($rows[$row]); $col++){

        if(is_numeric($rows[$row][$col])) { // Start parsing a number

            $parsingNum = true;
            $curNumAsStr .= $rows[$row][$col];

            if($col === count($rows[$row]) - 1) { // End of row
                $parsingNum = false;
                $shouldCheck = true;
                $numEndCol = true;
            }
        }else if($parsingNum) { // hit a symbol
            $parsingNum = false;
            $shouldCheck = true;
        }

        if($shouldCheck){
            if(!empty($curNumAsStr)) {
                $isValid = false;
                $pos = null;
                $startCol = max(0, $col - strlen($curNumAsStr));

                $symbol = '';

                if($numEndCol) { // End of row
                    $startCol++;
                }

                for($i = $startCol; $i < $col; $i++) {

                    // if not on col 0, check left
                    if($i == $startCol && ($i - 1) > 0){
                        $l = $i - 1;

                        // check left, left-up, left-down
                        if(isSymbol($rows[$row][$l])){
                            $isValid = true;
                            $symbol = $rows[$row][$l];
                            $pos = array($row, $l);
                        }

                        if($row > 0 && isSymbol($rows[$row - 1][$l])){
                            $isValid = true;
                            $symbol = $rows[$row - 1][$l];
                            $pos = array($row-1, $l);
                        }

                        if($row < count($rows) - 1 && isSymbol($rows[$row + 1][$l])){
                            $isValid = true;
                            $symbol = $rows[$row + 1][$l];
                            $pos = array($row+1, $l);
                        }
                    }

                    // if not on row end, check right
                    if($i == ($col-1) && (($col + 1) < (count($rows[$row]) - 1))){
                        $r = $i + 1;
                        // check right, right up, right down
                        if(isSymbol($rows[$row][$r])){
                            $isValid = true;
                            $symbol = $rows[$row][$r];
                            $pos = array($row, $r);
                        }

                        if($row > 0 && isSymbol($rows[$row - 1][$r])){
                            $isValid = true;
                            $symbol = $rows[$row - 1][$r];
                            $pos = array($row-1, $r);
                        }

                        if($row < count($rows) - 1 && isSymbol($rows[$row + 1][$r])){
                            $isValid = true;
                            $symbol = $rows[$row + 1][$r];
                            $pos = array($row + 1, $r);
                        }
                    }

                    // check up if not on first row
                    if($row > 0 && isSymbol($rows[$row - 1][$i])){
                        $isValid = true;
                        $symbol = $rows[$row - 1][$i];
                        $pos = array($row - 1, $i);
                    }

                    // check down if not on last row
                    if($row < (count($rows) - 1) && isSymbol($rows[$row + 1][$i])){
                        $isValid = true;
                        $symbol = $rows[$row + 1][$i];
                        $pos = array($row + 1, $i);
                    }
                }

                if($isValid) {
                    $validNumbers[] = $curNumAsStr;

                    $pos = implode(',', $pos);
                    if($symbol == '*'){
                        if(!isset($starMatches[$pos])){
                            $starMatches[$pos] = array();
                        }
                        $starMatches[$pos][] = $curNumAsStr;
                    }
                }
            }

            // reset
            $pos = null;
            $curNumAsStr = '';
            $parsingNum = false;
            $shouldCheck = false;
        }
    }
}


$sum = 0;
if(!empty($starMatches)){
    foreach($starMatches as $pos => $matches){
        if(count($matches) == 2){
            $sum += ($matches[0] * $matches[1]);
        }
    }
}

echo $sum . PHP_EOL;
