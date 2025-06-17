<?php

$the_list_of_words = ["I", "TWO", "FORTY", "THREE", 'JEN', "TWO", "tWo", "Two"];

$the_word_to_find = "TWO";

$found_at_indices = [];

for ($i = 0; $i < count($the_list_of_words); $i++) {

    $current_word = $the_list_of_words[$i];

    if ($current_word == $the_word_to_find) {
        $found_at_indices[] = $i;
    }
}

$output_text = "[";

for ($i = 0; $i < count($found_at_indices); $i++) {
    $output_text = $output_text . $found_at_indices[$i];

    if ($i < count($found_at_indices) - 1) {
        $output_text = $output_text . ",";
    }
}

$output_text = $output_text . "]";

echo $output_text;

?>