<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);


$sum = 0;
$grids = [];
$grid = [];

foreach ($lines as $line){
    if(empty($line)){ // start a new grid
        if(!empty($grid)){
            $sum += findReflectionPoint($grid);
            $grid = [];
        }
        continue;
    }
    $grid[] = str_split($line);
}

// do last grid
if(!empty($grid)){
    $sum += findReflectionPoint($grid);
}


function findReflectionPoint($grid){
    $hit = 0;
    $horiz = [];
    for($row = 0; $row < count($grid); $row++){
        $horiz[] = [
            'row' => $grid[$row],
            'hash' => sha1(implode('',$grid[$row])),
        ];
    }

    $r = findHitPoint($horiz);
    if($r){
        echo "Found row hit point $r". PHP_EOL;
        $hit = $r * 100;
    }

    if($hit == 0){
        $verts = [];
        for($col = 0; $col < count($grid[0]); $col++){
            $verts[] = [
                'row' => array_column($grid, $col),
                'hash' => sha1(implode('', array_column($grid, $col)))
            ];
        }
        $c = findHitPoint($verts);
        if($c){
            echo "Found col hit point $c". PHP_EOL;
            $hit = $c;
        }
    }

    return $hit;
}

function findHitPoint($lines){
    print_r($lines);
    $hitPoint = 0;
    for($lineIdx = 1; $lineIdx < count($lines); $lineIdx++){
        //echo "$lineIdx: Checking {$lines[$lineIdx]} against {$lines[$lineIdx-1]}". PHP_EOL;
        if($lines[$lineIdx]['hash'] == $lines[$lineIdx-1]['hash']){
            // found hit point, now check back and forward
            $hitPoint = $lineIdx;
            $count = 1;
            for($j = $lineIdx-2; $j >= 0; $j--){
                if($lineIdx + $count >= count($lines)){
                    break;
                }
         //       echo "rmatch: Checking {$lines[$j]} against {$lines[$lineIdx+$count]}". PHP_EOL;
                if($lines[$j]['hash'] != $lines[$lineIdx+$count]['hash']){
                    $hitPoint = 0;
                    break;
                }
                $count++;
            }
            if($hitPoint){
                return $hitPoint;
            }
        }
    }

    return $hitPoint;
}



echo $sum . PHP_EOL;
