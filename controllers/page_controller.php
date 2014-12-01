<?php

class PageController {
    /* @var AnirecoModelPDO */

    public $anirecoDAO;

    public function showIndex() {
        
    }

    public function showReco() {
        $page_title = 'アニレコ - アニメ推薦システム';
        $sub_title = 'アニレコ(推薦モード)';
        $this->anirecoDAO = new AnirecoModelPDO();

        $case = @$_POST['case'];
        $id = @$_POST['id'];
        $logs = @$_COOKIE['logs'];
        $log_num = count($logs);
        if (!$logs) {
            $logs = array();
        } else {
            $logs = unserialize($logs);
            var_dump($logs);
        }
        if ($case) {
            $logs[$id] = $case;
            foreach($logs as $key => $log) {
                if ($key == "") {
                    unset($logs[$key]);
                }
            }
            setcookie('logs', serialize($logs));
            $ids = $this->anirecoDAO->collect_titles($logs);
            $best_list = $this->anirecoDAO->load_bests($ids);
            $logs = trim_negative($logs);
            $title_id = collect_patternA($best_list, array_keys($logs));
            $title = $this->anirecoDAO->load_title($title_id);
        } else {
            $title = $this->anirecoDAO->load_rand_title();
        }

        include_once './views/head.php';
        include_once './views/header.php';
        include_once './views/reco.php';
        include_once './views/foot.php';
    }

    public function cf_check() {
        $this->anirecoDAO = new AnirecoModelPDO();
        $id = 2;
        $best1 = $this->anirecoDAO->load_best($id);
        var_dump($best1);
    }

}
