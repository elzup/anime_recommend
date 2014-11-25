<!DOCTYPE html>
<meta charset="UTF-8">
<title></title>
<pre>
<?php

class CrawlController {

    public function getTitles() {
        $this->login();
        $i = 1;
        while (TRUE) {
            $html = $this->getHtml(URL_ANICORE_RANK . $i);
            echo $html->find('title', 0)->innertext;
            echo "<br />";
            $i++;
//            exit;
            if ($i > 5) {
                break;
            }
        }
    }

    public $init = FALSE;
    public $ckfile;

    public function getHtml($url) {
        echo 'start getHTML';
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->ckfile); 
//        curl_setopt($ch, CURLOPT_POST, TRUE);
        $output = curl_exec($ch);
        curl_close($ch);
//        echo h($output) . PHP_EOL;

        return $html = str_get_html($output);
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
