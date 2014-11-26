<?php

function h($str) {
    return htmlspecialchars($str);
}

function season_to_num($str) {
    $lib = array(
        'spring' => 0,
        'summer' => 1,
        'autumn' => 2,
        'winter' => 3,
    );
    return @$lib[$str];
}

function url_trim_param($url) {
    if (($s = strpos($url, '?')) !== FALSE) {
        return substr($url, 0, $s);
    }
    return $url;
}

function my_flush(){
    flush();
    ob_end_flush();
    ob_start();
}

