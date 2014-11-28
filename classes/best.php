<?php

class  Best {
    public $id;
    public $best_id;
    public $name;
    public $thankyou;

    /** @var $rank_list Rank[]  */
    public $rank_list;

    public function __construct($best_id = NULL, $name = NULL, $thankyou = NULL) {
        if (!isset($best_id)) {
            return;
        }
        $this->best_id = $best_id;
        $this->name = $name;
        $this->thankyou = $thankyou;
    }

    public function install($rec, $rank_list) {
        $this->id        = $rec[DB_CN_BESTS_ID];
        $this->best_id   = $rec[DB_CN_BESTS_ANI_BEST_ID];
        $this->name      = $rec[DB_CN_BESTS_BEST_NAME];
        $this->thankyou  = $rec[DB_CN_BESTS_THANKYOU];
        $this->rank_list = $rank_list;
    }

}
