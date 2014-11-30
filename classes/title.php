<?php

class Title {
    public $id;
    public $title_id;
    public $name;
    public $type;
    public $year;
    public $season;
    public $imgurl;

    public function __construct($title_id, $name, $year, $season, $imgurl) {
        $this->title_id = $title_id;
        $this->name     = $name;
        $this->year     = $year;
        $this->season   = $season;
        $this->imgurl   = $imgurl;
    }

    public function install() {
    }
}
