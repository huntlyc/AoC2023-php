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
    return preg_match('/[^0-9]/', $char);
}

$validNumbers = array();

for($row = 0; $row < count($rows); $row++) {
    $curNumAsStr = '';
    $parsingNum = false;
    $validNumer = false;
    for($col = 0; $col < count($rows[$row]); $col++) {
        if(is_numeric($rows[$row][$col])) { // start parsing a number
            $parsingNum = true;
            $curNumAsStr .= $rows[$row][$col];
        }else if($parsingNum) { // stop parsing a number
            if(!empty($curNumAsStr)) {

                $isValid = false;
                $startCol = max(0, $col - strlen($curNumAsStr));
                $endCol = max(0, $col - 1);

                if($curNumAsStr == '617') {
                    echo "row: $row, col: $col, startCol: $startCol, endCol: $endCol" . PHP_EOL;
                }

                // look in all adjacent positions for a non-numeric char
                for($i = $startCol; $i <= $endCol; $i++) {
                    if($i == $startCol){

                        $l = $i - 1;
                        if($l < 0) continue;

                        // check left
                        if(isSymbol($rows[$row][$l])){
                            $isValid = true;
                            break;
                        }
                        // check up-left
                        if($row > 0 && isSymbol($rows[$row - 1][$l])){
                            $isValid = true;
                            break;
                        }
                        // check down-left
                        if($row < count($rows) - 1 && isSymbol($rows[$row + 1][$l])){
                            $isValid = true;
                            break;
                        }
                    }

                    if($i == $endCol){
                        $r = $i + 1;
                        if($r > count($rows[$row]) - 1) continue;
                        // check right
                        if(isSymbol($rows[$row][$r])){
                            $isValid = true;
                            break;
                        }
                        // check up-right
                        if($row > 0 && isSymbol($rows[$row - 1][$r])){
                            $isValid = true;
                            break;
                        }
                        // check down-right
                        if($row < count($rows) - 1 && isSymbol($rows[$row + 1][$r])){
                            $isValid = true;
                            break;
                        }
                    }

                    // check up
                    if($row > 0 && isSymbol($rows[$row - 1][$i])){
                        $isValid = true;
                        break;
                    }

                    // check down
                    if($row < count($rows) - 1 && isSymbol($rows[$row + 1][$i])){
                        $isValid = true;
                        break;
                    }
                }


                if($isValid) {
                    $validNumbers[] = $curNumAsStr;
                }

                $curNumAsStr = '';
            }
            $parsingNum = false;
        }
    }
}

echo array_sum($validNumbers) . PHP_EOL;
