<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input2.txt' : 'input.txt');

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
                $startCol = max(0, $col - strlen($curNumAsStr));

                if($numEndCol) { // End of row
                    $startCol++;
                }

                for($i = $startCol; $i < $col; $i++) {

                    // if not on col 0, check left
                    if($i == $startCol && ($i - 1) > 0){
                        $l = $i - 1;

                        // check left, left-up, left-down
                        if(isSymbol($rows[$row][$l]) ||
                          ($row > 0 && isSymbol($rows[$row - 1][$l])) ||
                          ($row < count($rows) - 1 && isSymbol($rows[$row + 1][$l]))){
                            $isValid = true;
                        }
                    }

                    // if not on row end, check right
                    if($i == ($col-1) && (($col + 1) < (count($rows[$row]) - 1))){
                        $r = $i + 1;
                        // check right, right up, right down
                        if(isSymbol($rows[$row][$r]) ||
                          ($row > 0 && isSymbol($rows[$row - 1][$r])) ||
                          ($row < count($rows) - 1 && isSymbol($rows[$row + 1][$r]))){
                            $isValid = true;
                        }
                    }

                    // check up if not on first row
                    if($row > 0 && isSymbol($rows[$row - 1][$i])) $isValid = true;

                    // check down if not on last row
                    if($row < (count($rows) - 1) && isSymbol($rows[$row + 1][$i])) $isValid = true;
                }

                if($isValid) {
                    $validNumbers[] = $curNumAsStr;
                }
            }

            // reset
            $curNumAsStr = '';
            $parsingNum = false;
            $shouldCheck = false;
        }
    }
}

echo array_sum($validNumbers) . PHP_EOL;
