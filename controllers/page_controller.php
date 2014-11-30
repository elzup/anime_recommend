<?php

class PageController {

    public function showIndex() {
    }

    public function showReco() {
		$page_title = 'アニレコ - アニメ推薦システム';
		$title = new Title(3, 'テストタイトル', 5, 0, 'http://img.anikore.jp/images/anime/7/4/3/7743/7743.jpg');
		$sub_title = 'アニレコ(推薦モード)';
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

