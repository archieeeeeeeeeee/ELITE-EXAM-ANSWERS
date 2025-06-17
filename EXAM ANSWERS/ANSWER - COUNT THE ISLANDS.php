<?php

function findIslands($map) {
    $rows = count($map);
    $cols = count($map[0]);
    $total = 0;

    for ($x = 0; $x < $rows; $x++) {
        for ($y = 0; $y < $cols; $y++) {
            if ($map[$x][$y] == 1) {
                checkIsland($map, $x, $y, $rows, $cols);
                $total++;
            }
        }
    }

    return $total;
}

function checkIsland(&$map, $x, $y, $rows, $cols) {
    if ($x < 0 || $y < 0 || $x >= $rows || $y >= $cols || $map[$x][$y] == 0) {
        return;
    }

    $map[$x][$y] = 0;

    checkIsland($map, $x + 1, $y, $rows, $cols);
    checkIsland($map, $x - 1, $y, $rows, $cols);
    checkIsland($map, $x, $y + 1, $rows, $cols);
    checkIsland($map, $x, $y - 1, $rows, $cols);
}

function showGrid($map) {
    $rows = count($map);
    $cols = count($map[0]);
    $result = [];

    for ($x = 0; $x < $rows; $x++) {
        $line = "";
        for ($y = 0; $y < $cols; $y++) {
            if ($map[$x][$y] == 1) {
                $line .= "X";
            } else {
                $line .= "-";
            }
        }
        $result[] = $line;
    }

    return $result;
}

$matrix = [
    [1, 1, 1, 1],
    [0, 1, 0, 0],
    [0, 1, 0, 1],
    [1, 1, 0, 0]
];

$copyMatrix = $matrix;

$lines = showGrid($matrix);
foreach ($lines as $l) {
    echo "\"$l\"\n";
}

echo "Islands: " . findIslands($copyMatrix) . "\n";
