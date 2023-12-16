<?php

define('DEBUG', true);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);


$sum = 0;
$grids = [];
$grid = [];

foreach ($lines as $line){
    if(empty($line)){ // start a new grid
        if(!empty($grid)){
            $grids[] = $grid;
            $grid = [];
        }
        continue;
    }
    $grid[] = str_split($line);
}

// do last grid
if(!empty($grid)){
    $rPoint = getReflectionDetails($grid);
    $sum += $rPoint['point'] * $rPoint['multiplier'];
    $grids[] = $grid;
    $grid = [];
}

foreach($grids as $grid){
    // pt 1 get reflection point
    $rPoint = getReflectionDetails($grid);
    echo "was\n";
    print_r($rPoint);


    // for pt2 get opposite reflection point, fixing smudge
    if($rPoint['type'] == 'row'){
        $rPoint = getReflectionDetails($grid, 'col');
    } else if($rPoint['type'] == 'col'){
        $rPoint = getReflectionDetails($grid, 'row');
    }
    echo "now\n";
    print_r($rPoint);
    echo "/now\n";
    if(is_null($rPoint)){
        paintGrid($grid);
        throw new Exception("No reflection point found");
        die;
    }else{
        $sum += $rPoint['point'] * $rPoint['multiplier'];
    }
}

function paintGrid($grid){
    foreach($grid as $row){
        echo implode('', $row) . PHP_EOL;
    }
    echo PHP_EOL;
}





function horizLines($grid){
    $horiz = [];
    for($row = 0; $row < count($grid); $row++){
        $horiz[] = [
            'row' => $grid[$row],
            'hash' => sha1(implode('',$grid[$row])),
        ];
    }
    return $horiz;
}

function vertLines($grid){
    $verts = [];
    for($col = 0; $col < count($grid[0]); $col++){
        $verts[] = [
            'row' => array_column($grid, $col),
            'hash' => sha1(implode('', array_column($grid, $col)))
        ];
    }
    return $verts;
}

/**
 * Find the reflection point in the grid
 * @param array $grid
 * @param string $dir - 'row' or 'col' to only look for that type of reflection
 * @return array|bool - array with keys 'point' and 'type' or false if no reflection point found
 */
function getReflectionDetails($grid, $dir = null){
    $hit = null;
    $fixSmudge = false;
    if(!is_null($dir)){
        $fixSmudge = true;
    }
    if(is_null($dir) || $dir == 'row'){
        $horiz = horizLines($grid);
        $r = findHitPoint($horiz, $fixSmudge);
        if($r){
            $hit = [
                'point' => $r,
                'type' => 'row',
                'multiplier' => 100,
            ];
            if($dir == 'row'){
                return $hit;
            }
        }
    }

    if((is_null($hit) && is_null($dir)) || $dir == 'col'){
        echo "checking col". PHP_EOL;
        $verts = vertLines($grid);
        $c = findHitPoint($verts, $fixSmudge);
        if($c){
            $hit = $c;
            $hit = [
                'point' => $c,
                'type' => 'col',
                'multiplier' => 1,
            ];
        }
    }


    return $hit;
}
function findHitPoint($lines, $fixSmudge = false){
    $hitPoint = 0;
    for($lineIdx = 1; $lineIdx < count($lines); $lineIdx++){
        if($lines[$lineIdx]['hash'] == $lines[$lineIdx-1]['hash']){
            $hitPoint = $lineIdx;
            $count = 1;
            for($j = $lineIdx-2; $j >= 0; $j--){
                if($lineIdx + $count >= count($lines)){
                    break;
                }
                if($lines[$j]['hash'] != $lines[$lineIdx+$count]['hash']){
                    // check diff is only 1 char
                    if($fixSmudge){
                        $curGrid = array_map(function($row){
                            return implode('', $row['row']);
                        }, $lines);
                        echo "curGrid: ". PHP_EOL;
                        print_r($curGrid);
                        $diff = 0;
                        for($i = 0; $i < count($lines[$j]['row']); $i++){
                            if($lines[$j]['row'][$i] != $lines[$lineIdx+$count]['row'][$i]){
                                $diff++;
                            }
                        }
                        if($diff == 1){
                            // change the char in the previous row to match current row
                            for($i = 0; $i < count($lines[$j]['row']); $i++){
                                if($lines[$j]['row'][$i] != $lines[$lineIdx+$count]['row'][$i]){
                                    $lines[$j]['row'][$i] = $lines[$lineIdx+$count]['row'][$i];
                                }
                            }
                            $newGrid = array_map(function($row){
                                return [
                                    'row' => $row['row'],
                                    'hash' => sha1(implode('', $row['row'])),
                                ];
                            }, $lines);

                            $curGrid = array_map(function($row){
                                return implode('', $row['row']);
                            }, $newGrid);

                            $newHit = findHitPoint($newGrid);
                            return $newHit;
                        }
                    }
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
