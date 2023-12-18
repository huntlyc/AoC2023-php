<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);

class DijkstraNoDiagonal {
    private $grid;

    public function __construct($grid) {
        $this->grid = $grid;
    }

    public function shortestPath($start, $end) {
        $rows = count($this->grid);
        $cols = count($this->grid[0]);
        $distances = [];

        // Initialize distances with infinity
        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $cols; $j++) {
                $distances[$i][$j] = INF;
            }
        }

        $distances[$start[0]][$start[1]] = 0;

        $visited = [];

        $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]]; // Up, Down, Left, Right

        $queue = new SplPriorityQueue();
        $queue->insert([$start[0], $start[1]], 0);

        while (!$queue->isEmpty()) {
            $current = $queue->extract();
            $row = $current[0];
            $col = $current[1];

            if ($row === $end[0] && $col === $end[1]) {
                return $queue;
            }

            if (!isset($visited[$row][$col])) {
                $visited[$row][$col] = true;

                foreach ($directions as $dir) {
                    for ($step = 1; $step <= 3; $step++) {
                        $newRow = $row + $dir[0] * $step;
                        $newCol = $col + $dir[1] * $step;

                        if ($newRow >= 0 && $newRow < $rows && $newCol >= 0 && $newCol < $cols) {
                            $weight = $this->grid[$newRow][$newCol];
                            $alt = $distances[$row][$col] + $weight;

                            if ($alt < $distances[$newRow][$newCol]) {
                                $distances[$newRow][$newCol] = $alt;
                                $queue->insert([$newRow, $newCol], -$alt);
                            }
                        } else {
                            break; // Stop moving in this direction if out of bounds
                        }
                    }
                }
            }
        }

        return false; // No path found
    }
}

class DijkstraFlexibleSteps {
    private $grid;

    public function __construct($grid) {
        $this->grid = $grid;
    }

    public function shortestPath($start, $end) {
        $rows = count($this->grid);
        $cols = count($this->grid[0]);
        $distances = [];

        // Initialize distances with infinity
        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $cols; $j++) {
                $distances[$i][$j] = INF;
            }
        }

        $distances[$start[0]][$start[1]] = 0;

        $visited = [];

        $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]]; // Up, Down, Left, Right

        $queue = new SplPriorityQueue();
        $queue->insert([$start[0], $start[1]], 0);

        while (!$queue->isEmpty()) {
            $current = $queue->extract();
            $row = $current[0];
            $col = $current[1];

            if ($row === $end[0] && $col === $end[1]) {
                return $distances[$row][$col];
            }

            if (!isset($visited[$row][$col])) {
                $visited[$row][$col] = true;

                foreach ($directions as $dir) {
                    for ($step = 1; $step <= 3; $step++) {
                        $newRow = $row + $dir[0] * $step;
                        $newCol = $col + $dir[1] * $step;

                        if ($newRow >= 0 && $newRow < $rows && $newCol >= 0 && $newCol < $cols) {
                            $weight = $this->grid[$newRow][$newCol];
                            $alt = $distances[$row][$col] + $weight;

                            if ($alt < $distances[$newRow][$newCol]) {
                                $distances[$newRow][$newCol] = $alt;
                                $queue->insert([$newRow, $newCol], -$alt);
                            }
                        } else {
                            break; // Stop moving in this direction if out of bounds
                        }
                    }
                }
            }
        }

        return false; // No path found
    }
}

function djirk($min, $max, &$grid){
    $dist = ['0,0,V' => 0, '0,0,H' => 0];
        $q = new MinPriorityQueue();
        $q->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
        $q->insert('0,0,H', 0);
        $q->insert('0,0,V', 0);

        foreach ($grid as $x => $row) {
            foreach ($row as $y => $value) {
                if (0 != $x || 0 != $y) {
                    foreach (['H', 'V'] as $d) {
                        $dist["$x,$y,$d"] = INF;
                        $q->insert("$x,$y,$d", INF);
                    }
                }
            }
        }

        $mh = count($grid) - 1;
        $mw = count($grid[0]) - 1;
        $moves = ['H' => [0, 1], 'V' => [1, 0]];

        while (!$q->isEmpty()) {
            ['data' => $u, 'priority' => $c] = $q->extract();

            if ($c != $dist[$u]) {
                continue;
            }

            [$ux, $uy, $ud] = explode(',', $u);

            if ($ux == $mh && $uy == $mw) {
                return $dist[$u];
            }

            $neighbors = [];
            $nd = 'H' == $ud ? 'V' : 'H';
            $sumUp = 0;
            $sumDown = 0;

            for ($i = 1; $i <= $max; ++$i) {
                if (isset($grid[(int) $ux + $moves[$nd][0] * $i][(int) $uy + $moves[$nd][1] * $i])) {
                    $sumUp += $grid[(int) $ux + $moves[$nd][0] * $i][(int) $uy + $moves[$nd][1] * $i];

                    if ($i >= $min) {
                        $neighbors[] = [
                            sprintf(
                                '%d,%d,%s',
                                (int) $ux + $moves[$nd][0] * $i,
                                (int) $uy + $moves[$nd][1] * $i,
                                $nd
                            ),
                            $sumUp,
                        ];
                    }
                }

                if (isset($grid[(int) $ux - $moves[$nd][0] * $i][(int) $uy - $moves[$nd][1] * $i])) {
                    $sumDown += $grid[(int) $ux - $moves[$nd][0] * $i][(int) $uy - $moves[$nd][1] * $i];

                    if ($i >= $min) {
                        $neighbors[] = [
                            sprintf(
                                '%d,%d,%s',
                                (int) $ux - $moves[$nd][0] * $i,
                                (int) $uy - $moves[$nd][1] * $i,
                                $nd
                            ),
                            $sumDown,
                        ];
                    }
                }
            }

            foreach ($neighbors as [$v, $cost]) {
                $alt = $dist[$u] + $cost;

                if ($alt < $dist[$v]) {
                    $dist[$v] = $alt;
                    $q->insert($v, $alt);
                }
            }
        }
}
class MinPriorityQueue extends \SplPriorityQueue
{
    public function compare(mixed $priority1, mixed $priority2): int
    {
        return $priority2 <=> $priority1;
    }
}

$grid = [];
foreach ($lines as $line) {
    if(empty($line)) continue;
    $grid[] = str_split($line);
}


$start = [0, 0];
$end = [count($grid) - 1, count($grid[0]) - 1];

$shortestPath = djirk(1, 3, $grid);
echo "Shortest path weight: " . $shortestPath; // Output: Shortest path weight: 9
echo PHP_EOL;
exit;
