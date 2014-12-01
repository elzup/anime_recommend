<?php

class Title {
    public $id;
    public $title_id;
    public $name;
    public $type;
    public $year;
    public $season;
    public $imgurl;
    public $description;

    public $point;

    public function __construct($title_id = NULL, $name = NULL, $year = NULL, $season = NULL, $imgurl = NULL) {
        $this->title_id = $title_id;
        $this->name     = $name;
        $this->year     = $year;
        $this->season   = $season;
        $this->imgurl   = $imgurl;
    }

    public function install($rec) {
        $this->id          = $rec[DB_CN_TITLES_ID];
        $this->title_id    = $rec[DB_CN_TITLES_ANI_TITLE_ID];
        $this->name        = $rec[DB_CN_TITLES_TITLE_NAME];
        $this->year        = $rec[DB_CN_TITLES_TITLE_YEAR];
        $this->season      = $rec[DB_CN_TITLES_TITLE_SEASON];
        $this->imgurl      = $rec[DB_CN_TITLES_TITLE_IMGURL];
        $this->description = $rec[DB_CN_TITLES_TITLE_DESCRIPTION];
    }
}
