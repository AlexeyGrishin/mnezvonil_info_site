<?php

function markedArray($array) {
    $marray = array();
    $index = 0;
    $count = count($array);
    foreach ($array as $value) {
        $even = $index%2==1;
        $element = array("value" => $value,
                          "index" => $index,
                          "odd" => !$even,
                          "even" => $even,
                          "first" => ($index == 0),
                          "last"=>($index == $count)
        );
        foreach ($value as $n=>$v) {
            $element[$n] = $v;
        }
        $marray[] = $element;
    }
    return $marray;
}

function h($value) {
    echo Templator::escape($value);
}

function n($number) {
    echo number_format($number, 0, ".", ",");
}

function e($value) {
    echo $value;
}

function m($mail) {
    echo "<a href='mailto:$mail'>$mail</a>";
}

function iff($condition, $html) {
    if ($condition) {
        echo $html;
    }
}