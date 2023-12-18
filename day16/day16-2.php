<?php

define('DEBUG', false);
global $beamCount;
$beamCount = 0;

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);

$grid = [];
foreach ($lines as $line) {
    if(empty($line)) continue;
    $grid[] = str_split($line);
}

class Vector {
    public int $x;
    public int $y;

    public function __construct($x, $y) {
        $this->x = intval($x);
        $this->y = intval($y);
    }

    public static function UP(): Vector {
        return new Vector(0, -1);
    }

    public static function DOWN(): Vector {
        return new Vector(0, 1);
    }

    public static function LEFT(): Vector {
        return new Vector(-1, 0);
    }

    public static function RIGHT(): Vector {
        return new Vector(1, 0);
    }

    public function toString(): string {
        switch($this){
            case Vector::UP():
                return 'UP';
            case Vector::DOWN():
                return 'DOWN';
            case Vector::LEFT():
                return 'LEFT';
            case Vector::RIGHT():
                return 'RIGHT';
            default:
                throw new Exception("Unknown vector: {$this->x},{$this->y}");
        }
    }
}

class Beam {
    public string $ID;
    public int $count;
    public int $x;
    public int $y;
    public Vector $direction;

    public function __construct(int $x, int $y, Vector $direction){
        $this->count = $GLOBALS['beamCount']++;
        $this->ID = "$x,$y,$direction->x,$direction->y";
        $this->x = $x;
        $this->y = $y;
        $this->direction = $direction;
    }
}


function printGrid(array $grid){
    foreach($grid as $row){
        foreach($row as $cell){
            echo $cell;
        }
        echo PHP_EOL;
    }
}

$res = [];

for($row = 0; $row < count($grid); $row++){
    for($col = 0; $col < count($grid[0]); $col++){
        if($row == 0 && $col == 0){
            $res[] = followBeam(new Beam($col, $row, Vector::DOWN()), $grid);
        }else if($row == 0){
            if($col == count($grid[0]) - 1){
                $res[] = followBeam(new Beam($col, $row, Vector::LEFT()), $grid);
            }else{
                $res[] = followBeam(new Beam($col, $row, Vector::DOWN()), $grid);
            }
        }else if($row == count($grid) - 1){
            if($col == 0){
                $res[] = followBeam(new Beam($col, $row, Vector::RIGHT()), $grid);
                $res[] = followBeam(new Beam($col, $row, Vector::UP()), $grid);
            }else if($col == count($grid[0]) - 1){
                $res[] = followBeam(new Beam($col, $row, Vector::LEFT()), $grid);
                $res[] = followBeam(new Beam($col, $row, Vector::UP()), $grid);
            }else{
                $res[] = followBeam(new Beam($col, $row, Vector::UP()), $grid);
            }
        }else if($col == 0){
            $res[] = followBeam(new Beam($col, $row, Vector::RIGHT()), $grid);
        }else if($col == count($grid[0]) - 1){
            $res[] = followBeam(new Beam($col, $row, Vector::LEFT()), $grid);
        }
    }
}




function inBounds(Beam $beam, array $grid): bool {
    return $beam->x >= 0 && $beam->x < count($grid[0]) && $beam->y >= 0 && $beam->y < count($grid);
}

function followBeam(beam $startBeam, array $grid = []): int {
    $cached = [];
    $beams = [];
    $beams[$startBeam->ID] = $startBeam;
    $visitedSquares = [];

    while(count($beams) > 0) {
        $count = 0;
        foreach($beams as &$beam){


            if(!inBounds($beam, $grid)){
                echo "Out of bounds: {$beam->count}($beam->x,$beam->y} [{$beam->ID}]" . PHP_EOL;
                // beam is out of bounds, remove
                unset($beams[$beam->ID]);
                continue;
            }

            $visitedSquares["$beam->y,$beam->x"] = true;

            //echo "vs: " . count($visitedSquares) . PHP_EOL;

            $curCell = $grid[$beam->y][$beam->x];
            echo "Beam {$beam->count} [{$beam->ID}] at ($beam->x,$beam->y), direction: ({$beam->direction->x},{$beam->direction->y}), cell: $curCell" . PHP_EOL;

            $count++;

            /**
             * The beam enters in the top-left corner from the left and heading to the right. Then, its behavior depends on what it encounters as it moves:
             * If the beam encounters empty space (.), it continues in the same direction.
             * If the beam encounters a mirror (/ or \), the beam is reflected 90 degrees depending on the angle of the mirror.
             * If the beam encounters the pointy end of a splitter (| or -), the beam passes through the splitter
             * If the beam encounters the flat side of a splitter (| or -), the beam is split into two beams going in each of the two directions the splitter's pointy ends are pointing.
             */
            switch($curCell){
            case '.':
                // continue on current vector
                break;
            case '/':
                switch($beam->direction){
                case Vector::RIGHT(): $beam->direction = Vector::UP(); break;
                case Vector::LEFT(): $beam->direction = Vector::DOWN(); break;
                case Vector::UP(): $beam->direction = Vector::RIGHT(); break;
                case Vector::DOWN(): $beam->direction = Vector::LEFT(); break;
                };
                break;
            case '\\':
                switch($beam->direction){
                case Vector::RIGHT(): $beam->direction = Vector::DOWN(); break;
                case Vector::LEFT(): $beam->direction = Vector::UP(); break;
                case Vector::DOWN(): $beam->direction = Vector::RIGHT(); break;
                case Vector::UP(): $beam->direction = Vector::LEFT(); break;
                };
                break;
            case '|':
                // split up and down
                if($beam->direction == Vector::RIGHT() || $beam->direction == Vector::LEFT()){

                    echo "Splitting {$beam->ID}, removing" . PHP_EOL;
                    unset($beams[$beam->ID]);

                    $newBeam = new Beam($beam->x, $beam->y, Vector::UP());
                    if(!isset($beams[$newBeam->ID]) && !isset($cached[$newBeam->ID])){
                        $newBeam->x += $newBeam->direction->x;
                        $newBeam->y += $newBeam->direction->y;
                        $beams[$newBeam->ID] = $newBeam;
                        $cached[$newBeam->ID] = true;
                        echo "Added: {$newBeam->count} {$newBeam->ID}" . PHP_EOL;
                    }else{
                        echo "Already exists: {$newBeam->ID}" . PHP_EOL;
                    }

                    $newBeam = new Beam($beam->x, $beam->y, Vector::DOWN());
                    if(!isset($beams[$newBeam->ID]) && !isset($cached[$newBeam->ID])){
                        $newBeam->x += $newBeam->direction->x;
                        $newBeam->y += $newBeam->direction->y;
                        $beams[$newBeam->ID] = $newBeam;
                        $cached[$newBeam->ID] = true;
                        echo "Added: {$newBeam->count} {$newBeam->ID}" . PHP_EOL;
                    }else{
                        echo "Already exists: {$newBeam->ID}" . PHP_EOL;
                    }
                }

                break;
            case '-':
                // split left and right
                if($beam->direction == Vector::UP() || $beam->direction == Vector::DOWN()){
                    echo "Splitting {$beam->ID}, removing" . PHP_EOL;
                    unset($beams[$beam->ID]);

                    $newBeam = new Beam($beam->x, $beam->y, Vector::LEFT());
                    if(!isset($beams[$newBeam->ID]) && !isset($cached[$newBeam->ID])){
                        $newBeam->x += $newBeam->direction->x;
                        $newBeam->y += $newBeam->direction->y;
                        $beams[$newBeam->ID] = $newBeam;
                        $cached[$newBeam->ID] = true;
                        echo "Added: {$newBeam->count} {$newBeam->ID}" . PHP_EOL;
                    }else{
                        echo "Already exists: {$newBeam->ID}" . PHP_EOL;
                    }

                    $newBeam = new Beam($beam->x, $beam->y, Vector::RIGHT());
                    if(!isset($beams[$newBeam->ID]) && !isset($cached[$newBeam->ID])){
                        $newBeam->x += $newBeam->direction->x;
                        $newBeam->y += $newBeam->direction->y;
                        $beams[$newBeam->ID] = $newBeam;
                        $cached[$newBeam->ID] = true;
                        echo "Added: {$newBeam->count} {$newBeam->ID}" . PHP_EOL;
                    }else{
                        echo "Already exists: {$newBeam->ID}" . PHP_EOL;
                    }
                }
                break;
            }

            // calculate next position
            $beam->x += $beam->direction->x;
            $beam->y += $beam->direction->y;
        }
    }

    return count($visitedSquares);
}


$sum = max($res);

echo $sum . PHP_EOL;
