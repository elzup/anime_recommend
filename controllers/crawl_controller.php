<?php

class CrawlController {

    public $init = FALSE;
    public $ckfile;
    public $anirecoDAO;

    public function updateDescriptions() {
        $this->anirecoDAO = new AnirecoModelPDO();
        $titles = $this->anirecoDAO->select_all_titles2();

        foreach ($titles as $title) {
            $html = $this->getHtml(URL_ANICORE_TITLEPAGE . $title[DB_CN_TITLES_ANI_TITLE_ID]);
            if ($html === FALSE) {
                echo 'exit';
                break;
            }
            $this->getDescriptionManagePage($html, $title[DB_CN_TITLES_ID]);
        }
    }

    public function getDescriptionManagePage($html, $title_id) {
        $title_list = array();
        $div = $html->find('.animeDetailTopIntroduceBody--story', 0);
        if (!$div) {
            return;
        }
        $block = $div->find('blockquote', 0);
        $text = $block->innertext;
        $this->anirecoDAO->update_titles_description($title_id, $text);
    }

    public function updateRanks() {
        $this->anirecoDAO = new AnirecoModelPDO();
        $this->anirecoDAO->update_ranks_best_id();
        echo '---------------------------------------------';
        $this->anirecoDAO->update_ranks_title_id();
    }

    public function getRanks() {
        $this->anirecoDAO = new AnirecoModelPDO();
        set_time_limit(24 * 60 * 60);
        $ids = $this->anirecoDAO->select_bests_ids_next();
//		var_dump($ids);
//		exit;
//        $ids = $this->anirecoDAO->select_bests_ids();
        $this->login();
        // debug
//        unset($ids[0]);

        $k = $ids[0][0];
        foreach ($ids as $ido) {
            $id = $ido[DB_CN_BESTS_ANI_BEST_ID];
            $html = $this->getHtml(URL_ANICORE_RANK . $id);
            $c = 0;
            while ($html === FALSE && $c < 5) {
                echo 'redirect' . PHP_EOL;
                $c++;
                $this->login();
                $html = $this->getHtml(URL_ANICORE_RANK . $id);
                if ($c > 5) {
                    echo 'count out exit';
                    break;
                }
            }
            $this->getRanksManagePage($html, $id);
            echo '*';
            if ($k * 50 < $id) {
                $k++;
                echo $k . PHP_EOL;
            }
        }
    }

    public function getRanksManagePage($html, $best_id) {
        $rank_list = array();
        foreach ($html->find('.myranking_detail_rank_title') as $i => $rankBox) {
            $atitle = $rankBox->find('a', 0);
            if (!$atitle) {
                continue;
            }
            preg_match('#/(?<id>[0-9]*)/?$#U', $atitle->href, $m);
            $title_id = $m['id'];
            $rank_list[] = new Rank($title_id, $best_id, $i + 1);
        }
        if (!$rank_list) {
            echo '404 skip' . PHP_EOL;
            return;
        }
        $this->uniqueRanks($rank_list);
        $this->anirecoDAO->regist_ranks($rank_list);
    }

    /**
     * 
     * @param Rank[] $ranks
     */
    function uniqueRanks(&$ranks) {
        $ids = array();
        $k = FALSE;
        foreach ($ranks as $i => $rank) {
            $id = $rank->title_id;
            if (in_array($id, $ids)) {
                unset($ranks[$i]);
                $k = TRUE;
                continue;
            }
            $ids[] = $id;
        }
        if ($k) {
            $ranks = array_values($ranks);
        }
    }

    public function getBests() {
        $this->anirecoDAO = new AnirecoModelPDO();
        set_time_limit(60 * 60);
        $this->login();
//        $end = 1;
        $start = 1;
        $end = 371;
        my_flush_prepare();

        foreach (range($start, $end) as $i) {
            $html = $this->getHtml(URL_ANICORE_BEST . $i);
            if ($html === FALSE) {
                echo 'exit';
                break;
            }
            $this->getBestsManagePage($html);
            echo $i . PHP_EOL;
            my_flush();
        }
    }

    public function getBestsManagePage($html) {
        $best_list = array();
        foreach ($html->find('.best_ranking_box') as $i => $rankBox) {
            echo '*';
            $atitle = $rankBox->find('div.best_ranking_box_title', 0)->find('a', 0);
            $name = $atitle->innertext;
            preg_match('#/(?<id>[0-9]*)/?$#U', $atitle->href, $m);
            $best_id = $m['id'];
            if ($rankingBox = $rankBox->find('.best_ranking_box_rank', 0)) {
                $thankyou = substr($rankingBox->find('img', 0)->src, -5, 1);
            } else {
                $thankyou = $rankBox->find('.best_ranking_box_rank2', 0)->find('span', 0)->innertext;
            }
            $best_list[] = new Best($best_id, $name, $thankyou);
        }
        $this->anirecoDAO->regist_bests($best_list);
    }

    public function getTitles2() {
        $this->anirecoDAO = new AnirecoModelPDO();
        set_time_limit(60 * 60);
        $this->login();
        $start = 1;
        $end = 2;
//        $end = 122;
        foreach (range(1, 3) as $i) {
            foreach (range(1, 45) as $j) {
                $html = $this->getHtml("http://www.anikore.jp/50on-{$i}-{$j}/");
                if ($html === FALSE) {
                    echo 'exit';
                    break;
                }
                $this->getTitlesManagePage2($html, $i);
                echo $i . PHP_EOL;
            }
        }
    }

    public function getTitlesManagePage2($html, $type) {
        $title_list = array();
        $rltas = $html->find('.rlta');
        if (!$rltas) {
            return;
        }
        foreach ($rltas as $i => $rankBox) {
            echo '*';
            $atitle = $rankBox->find('.rlta_ttl', 0)->find('a', 0);
            if (preg_match('#(.*)のレビュー・#U', $atitle->innertext, $m)) {
                $name = $m[1];
            }
            if (preg_match('#/(?<id>[0-9]*)/$#U', $atitle->href, $m)) {
                $title_id = $m['id'];
            }
            $divinfo = $rankBox->find('.rlta_exp', 0);
            if (@$divinfo->find('a', 0) && preg_match('#/(?<ye>[0-9]+)/(?<se>.*)/$#', $divinfo->find('a', 0)->href, $m)) {
                $year = $m['ye'];
                $season = season_to_num($m['se']);
            } else {
                $year = 0;
                $season = 4;
            }
            $imgurl = url_trim_param($rankBox->find('.rlta_img', 0)->find('img', 0)->src);
            $title = new Title($title_id, $name, $year, $season, $imgurl);
            $title->type = $type;
            $title_list[] = $title;
        }
        $this->anirecoDAO->regist_titles($title_list);
    }

    public function getTitles() {
        $this->anirecoDAO = new AnirecoModelPDO();
        set_time_limit(60 * 60);
        $this->login();
        $start = 1;
        $end = 2;
//        $end = 122;
        my_flush_prepare();
        foreach (range($start, $end) as $i) {
            $html = $this->getHtml(URL_ANICORE_TITLE . $i);
            if ($html === FALSE) {
                echo 'exit';
                break;
            }
            $this->getTitlesManagePage($html);
            echo $i . PHP_EOL;
            my_flush();
        }
    }

    public function getTitlesManagePage($html) {
        $title_list = array();
        foreach ($html->find('.rankingBox') as $i => $rankBox) {
            echo '*';
            $atitle = $rankBox->find('h3.rankingBoxTtl', 0)->find('a', 0);
            $name = $atitle->innertext;
            preg_match('#/(?<id>[0-9]*)/$#U', $atitle->href, $m);
            $title_id = $m['id'];
            $divinfo = $rankBox->find('.rankingBoxInfor', 0);
            if (@$divinfo->find('a', 0) && preg_match('#/(?<ye>[0-9]+)/(?<se>.*)/$#', $divinfo->find('a', 0)->href, $m)) {
                $year = $m['ye'];
                $season = season_to_num($m['se']);
            } else {
                $year = 0;
                $season = 4;
            }
            $imgurl = url_trim_param($rankBox->find('.rankingMainBox', 0)->find('img', 0)->src);
            $title_list[] = new Title($title_id, $name, $year, $season, $imgurl);
        }
        $this->anirecoDAO->regist_titles($title_list);
    }

    public function getHtml($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->ckfile);
        $output = curl_exec($ch);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            curl_close($ch);
            return FALSE;
        }
        curl_close($ch);
        return str_get_html($output);
    }

    public function login() {
        echo "get login \n";
        $html = file_get_html("http://www.anikore.jp/users/login");
        $input_hiddens = $html->find('input[type=hidden]');
        $token_key = $input_hiddens[1]->value;
        $token_field = $input_hiddens[2]->value;
        $f = $this->getLoginedHtml($token_key, $token_field);
        $html->clear();
        $html = str_get_html($f);
        return $html;
    }

    public function getLoginedHtml($token_key, $token_field) {
        $params = array(
            "data[_Token][key]" => $token_key,
            "data[_Token][field]" => $token_field,
            "data[User][email]" => ANICORE_MAIL,
            "data[User][password]" => ANICORE_PASS,
        );

        $this->ckfile = tempnam("/tmp", "CURLCOOKIE");
        $ch = curl_init("http://www.anikore.jp/users/login");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->ckfile);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

}
