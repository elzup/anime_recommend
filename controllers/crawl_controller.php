<!DOCTYPE html>
<meta charset="UTF-8">
<title></title>
<pre>
<?php

class CrawlController {

    public $init = FALSE;
    public $ckfile;
    public $anirecoDAO;

    public function getRanks() {
        $this->anirecoDAO = new AnirecoModelPDO();
        set_time_limit(60 * 60 * 60);
        // TODO: get bests from db
        $ids = $this->anirecoDAO->select_bests_ids();
        $this->login();

        ob_start();
        echo str_pad(" ",4096)."<br />\n";
        ob_end_flush();
        ob_start('mb_output_handler');

        $k = 0;
        foreach ($ids as $ido) {
            $id = $ido[DB_CN_BESTS_ANI_BEST_ID];
            $html = $this->getHtml(URL_ANICORE_RANK . $id);
            echo  $id . ',';
            if ($html === FALSE) {
                echo 'exit';
                break;
            }
            $this->getRanksManagePage($html, $id);
            echo '*';
            if ($k * 50 > $id) {
                $k++;
                echo $k . PHP_EOL;
                ob_flush();
                flush();
            }
        }
    }

    public function getRanksManagePage($html, $best_id) {
        $rank_list = array();
        foreach($html->find('.myranking_detail_rank_title') as $i => $rankBox) {
            $atitle = $rankBox->find('a', 0);
            if (!$atitle) {
                continue;
            }
            preg_match('#/(?<id>[0-9]*)/?$#U', $atitle->href, $m);
            $title_id = $m['id'];
            $rank_list[] = new Rank($title_id, $best_id, $i + 1);
        }
        $this->anirecoDAO->regist_ranks($rank_list);
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
        foreach($html->find('.best_ranking_box') as $i => $rankBox) {
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

    public function getTitles() {
        $this->anirecoDAO = new AnirecoModelPDO();
        set_time_limit(60 * 60);
        $this->login();
        $end = 122;
        my_flush_prepare();

        foreach (range(1, $end) as $i) {
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
        foreach($html->find('.rankingBox') as $i => $rankBox) {
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
        $ch = curl_init ($url);
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
        $token_key   = $input_hiddens[1]->value;
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

        $this->ckfile = tempnam ("/tmp", "CURLCOOKIE");
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
