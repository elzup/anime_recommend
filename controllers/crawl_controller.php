<!DOCTYPE html>
<meta charset="UTF-8">
<title></title>
<pre>
<?php

class CrawlController {

    public function getTitles() {
        $i = 2;
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

    public function getHtml($url) {
        echo 'start getHTML';
        $html = file_get_html($url);
        echo 'end getHTML';
        if ($html->find('title', 0)->innertext == TITLE_REGIST) {
            $html->clear();
            $html = file_get_html(URL_ANICORE_LOGIN);
        }
        if ($html->find('title', 0)->innertext == TITLE_LOGIN) {
            echo "login page\n";
//            echo $html->find("[name=data%5B_Token%5D%5Bkey%5D]");
            $input_hiddens = $html->find('input[type=hidden]');
            $token_key   = $input_hiddens[1]->value;
            $token_field = $input_hiddens[2]->value;
            $f = $this->getLoginedHtml($token_key, $token_field, $url);
            echo h($f);
            // login manage
            $html->clear();
            $html = str_get_html($f);
        }
        return $html;
    }

    public function getLoginedHtml($token_key, $token_field, $url) {

        $params = array(
            "data%5B_Token%5D%5Bkey%5D" => $token_key,
            "data%5B_Token%5D%5Bfield%5D" => $token_field,
            "data%5BUser%5D%5Bemail%5D" => ANICORE_MAIL,
            "data%5BUser%5D%5Bpassword%5D" => ANICORE_PASS,
            "data[_Token][key]" => $token_key,
            "data[_Token][field]" => $token_field,
            "data[User][email]" => ANICORE_MAIL,
            "data[User][password]" => ANICORE_PASS,
        );

        $fp = fopen("tmp", "w");
        $ch = curl_init("http://www.anikore.jp/users/login");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_WRITEHEADER, $fp);
        $output = curl_exec($ch);
        fclose($fp);
//        var_dump(curl_getinfo($ch));
        curl_close($ch);
        return $output;
    }
}
