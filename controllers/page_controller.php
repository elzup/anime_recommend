<?php

class PageController {

    public function showIndex() {
    }

    public function cf_check() {
        $this->anirecoDAO = new AnirecoModelPDO();
        $id = 2;
        $best1 = $this->anirecoDAO->load_best($id);
        var_dump($best1);
    }

}

