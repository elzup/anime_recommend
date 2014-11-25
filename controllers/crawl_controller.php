<!DOCTYPE html>
<meta charset="UTF-8">
<title></title>
<pre>
<?php

class CrawlController {


    public $init = FALSE;
    public $ckfile;

    public function getTitles() {
        set_time_limit(1000);
        $this->login();
        $end = 1;
//        $end = 122;
        foreach (range(1, $end) as $i) {
            $html = $this->getHtml(URL_ANICORE_RANK . $i);
            if ($html === FALSE) {
                echo 'exit';
                break;
            }
            $this->getTitlesManagePage($html);
//            exit;
        }
    }

    public function getTitlesManagePage($html) {
        $title_list = array();
        foreach($html->find('.rankingBox') as $i => $rankBox) {
            $atitle = $rankBox->find('h3.rankingBoxTtl', 0)->find('a', 0);
            $name = $atitle->innertext;
            preg_match('#/(?<id>[0-9]*)/$#U', $atitle->href, $m);
            $title_id = $m['id'];
            preg_match('#/(?<ye>[0-9]+)/(?<se>.*)/$#', $rankBox->find('.rankingBoxInfor', 0)->find('a', 0)->href, $m);
            $year = $m['ye'];
            $season = $m['se'];
            $imgurl = $rankBox->find('.rankingMainBox', 0)->find('img', 0)->src;
            $title_list[] = new Title($title_id, $name, $year, $season, $imgurl);
        }
        var_dump($title_list);
    }

    public function getHtml($url) {
        echo 'start getHTML';
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->ckfile); 
//        curl_setopt($ch, CURLOPT_POST, TRUE);
        $output = curl_exec($ch);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            curl_close($ch);
            return FALSE;
        }
        curl_close($ch);
//        echo h($output) . PHP_EOL;

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
