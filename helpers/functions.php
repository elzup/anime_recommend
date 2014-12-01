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

function my_flush_prepare() {
    ob_start();
    echo str_pad(" ", 4096) . "<br />\n";
    ob_end_flush();
    ob_start('mb_output_handler');
}

function my_flush() {
    ob_flush();
    flush();
}

function calc_collaborative_filtering(Best $best1, Best $best2) {
    
}

function standard_deviation(Array $nums) {
    $count = count($nums);
    $aveg = array_sum($nums) / $count;
    $d = 0;
    foreach ($nums as $n) {
        $d += pow($n - $aveg, 2);
    }
    return sqrt($d / ($count - 1));
}

function array_average($arr) {
    return array_sum($nums) / count($nums);
}

/**
 * 
 * @param Best[] $best_list
 * @param type $title_ids
 */
function collect_patternA($best_list, $title_ids) {
    $points = array();
    foreach ($best_list as $best) {
        foreach ($best->rank_list as $rank) {
            if (in_array($rank->title_id, $title_ids)) {
                continue;
            }
            if (!isset($points[$rank->title_id])) {
                $points[$rank->title_id] = 0;
            }
            $points[$rank->title_id] += (10 - $rank->rank_num);
        }
    }
    arsort($points);
    foreach ($points as $key => $p) {
        return $key;
    }
}

function trim_negative($logs) {
    $list = array();
    foreach ($logs as $key => $case) {
        if ($case == 1 || $case == 2 || $case == 3) {
            $list[$key] = $case;
        }
    }
    return $list;
}
