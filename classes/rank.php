<?php

class Rank {
    public $id;
    public $title_id;
    public $best_id;
    public $rank_num;

    /** @var $title Title  */
    public $title;

    public function __construct($title_id = NULL, $best_id = NULL, $rank_num = NULL) {
        if (!isset($title_id)) {
            return;
        }
        $this->title_id = $title_id;
        $this->best_id = $best_id;
        $this->rank_num = $rank_num;
    }

    public function install($rec) {
        $this->id = $rec[DB_CN_RANKS_ID];
        $this->title_id = $rec[DB_CN_RANKS_TITLE_ID];
        $this->best_id = $rec[DB_CN_RANKS_BEST_ID];
        $this->rank_num = $rec[DB_CN_RANKS_RANK_NUM];
    }
}
