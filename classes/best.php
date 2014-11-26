<?php

class  Best {
    public $id;
    public $best_id;
    public $name;
    public $thankyou;

    public function __construct($best_id, $name, $thankyou) {
        $this->best_id = $best_id;
        $this->name = $name;
        $this->thankyou = $thankyou;
    }

    public function install() {
    }
}
