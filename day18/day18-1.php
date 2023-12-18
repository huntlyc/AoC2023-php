<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);

$instructions = [];
foreach ($lines as $line) {
    if(empty($line)) continue;
    list($dir, $numMoves, $hex) = explode(' ', $line);

    $instructions[] = [
        'dir' => $dir,
        'numMoves' => $numMoves,
        'hex' => $hex,
    ];
}


$pathCoords = [[0,0]];
$minX = 0;
$maxX = 0;
$minY = 0;
$maxY = 0;
foreach ($instructions as $instruction) {
    $x = $pathCoords[count($pathCoords) - 1][1];
    $y = $pathCoords[count($pathCoords) - 1][0];

    for($i = 0; $i < $instruction['numMoves']; $i++) {
        switch ($instruction['dir']) {
            case 'U':
                $y--;
                if($y < $minY) $minY = $y;
                break;
            case 'D':
                $y++;
                if($y > $maxY) $maxY = $y;
                break;
            case 'R':
                $x++;
                if($x > $maxX) $maxX = $x;
                break;
            case 'L':
                $x--;
                if($x < $minX) $minX = $x;
                break;
        }
        $pathCoords[] = [$y, $x];
    }
}

echo "MinX: {$minX}, MaxX: {$maxX}, MinY: {$minY}, MaxY: {$maxY}" . PHP_EOL;
$gridHeight = $maxY - $minY + 1;
$gridWidth = $maxX - $minX + 1;
echo "Grid size: {$gridWidth}x{$gridHeight}" . PHP_EOL;
$grid = array_fill(0, $gridHeight, array_fill(0, $gridWidth, '.'));

foreach ($pathCoords as $coord) {
    $grid[$coord[0] - $minY][$coord[1] - $minX] = '#';
}



function printGrid(&$grid) {
    foreach ($grid as &$row) {
        echo implode('', $row) . PHP_EOL;
    }
    echo PHP_EOL;
}

array_unshift($grid, array_fill(0, $gridWidth, '.'));
array_push($grid, array_fill(0, $gridWidth, '.'));
foreach ($grid as &$row) {
    array_unshift($row, '.');
    array_push($row, '.');
}

function floodFill(&$grid, $x, $y, $newColor, $oldColor) {
    $rows = count($grid);
    $cols = count($grid[0]);

    if ($x < 0 || $x >= $rows || $y < 0 || $y >= $cols || $grid[$x][$y] !== $oldColor || $grid[$x][$y] === $newColor) {
        return;
    }

    $grid[$x][$y] = $newColor;

    floodFill($grid, $x + 1, $y, $newColor, $oldColor);
    floodFill($grid, $x - 1, $y, $newColor, $oldColor);
    floodFill($grid, $x, $y + 1, $newColor, $oldColor);
    floodFill($grid, $x, $y - 1, $newColor, $oldColor);
}

floodFill($grid, 0, 0, ' ', '.');
printGrid($grid);

// Count the number of .'s
$counter = 0;
foreach ($grid as &$row) {
    $counter += strlen(str_replace(' ','', trim(implode('', $row))));
}


printGrid($grid);
echo "Number of .'s: {$counter}" . PHP_EOL;
