<?php

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="csv-export.csv"');

$output = '';
$numSubscribers = count($this->hits);
$numFields = count($this->hits[0]);
for ($i = 0; $numSubscribers > $i; $i++) {
    if (0 === $i) {
        $j = 1;
        foreach ($this->hits[$i] as $key => $field) {
            $output .= $key;
            if ($j < $numFields) {
                $output .= ',';
            } else {
                $output .= "\n";
            }
            $j++;
        }
    }

    $j = 1;
    foreach ($this->hits[$i] as $field) {
        $output .= $field;
        if ($j < $numFields) {
            $output .= ',';
        } else {
            $output .= "\n";
        }
        $j++;
    }
}
echo $output;
