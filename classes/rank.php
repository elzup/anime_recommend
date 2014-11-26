<?php

class Rank {
    public $id;
    public $title_id;
    public $best_id;
    public $rank_num;

    public function __construct($title_id, $best_id, $rank_num) {
        $this->title_id = $title_id;
        $this->best_id = $best_id;
        $this->rank_num = $rank_num;
    }

    public function install() {
    }
}
