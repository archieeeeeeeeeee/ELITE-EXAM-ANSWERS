<?php

function findShortestWordLength(string $str): int
{
    return min(array_map('strlen', explode(' ', $str)));
}

echo findShortestWordLength("TRUE FRIENDS ARE ME AND YOU");

echo "\n";

echo findShortestWordLength("I AM THE LEGENDARY VILLAIN");

?>