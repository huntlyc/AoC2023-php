<?php

define('DEBUG', false);

$input = file_get_contents(DEBUG ? 'test-input.txt' : 'input.txt');
$lines = explode(PHP_EOL, $input);

function djirkShortestPath($min, $max, &$grid){
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


class MinPriorityQueue extends \SplPriorityQueue {
    public function compare(mixed $priority1, mixed $priority2): int {
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

$shortestPath = djirkShortestPath(4, 10, $grid);
echo "Shortest path weight: " . $shortestPath; // Output: Shortest path weight: 9
echo PHP_EOL;
exit;
