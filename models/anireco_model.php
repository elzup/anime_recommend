<?php

class AnirecoModelPDO extends PDO {

    public function __construct() {
        $this->engine = DB_ENGINE;
        $this->host = DB_HOST;
        $this->database = DB_NAME;
        $this->user = DB_USER;
        $this->pass = DB_PASSWORD;
        $dns = $this->engine . ':dbname=' . $this->database . ";host=" . $this->host;
        parent::__construct($dns, $this->user, $this->pass);
    }

    // ----------------- DB Manage Wrap ----------------- //

    public function regist_titles($titles) {
        $sql_head = 'INSERT INTO `' . DB_TN_TITLE . '` (`' . DB_CN_TITLE_ANI_TITLE_ID . '`, `' . DB_CN_TITLE_TITLE_NAME . '`, `' . DB_CN_TITLE_TITLE_YEAR . '`, `' . DB_CN_TITLE_TITLE_SEASON. '`, `' . DB_CN_TITLE_TITLE_IMGURL . '`) VALUES';
        $sql_values = array();
        for ($i = 0; $i < count($titles); $i++) {
            $sql_values[] = "(:ID$i, :NAME$i, :YEAR$i, :SEASON$i, :IMGURL$i)";
        }
        $sql = $sql_head . implode(',', $sql_values);
        $stmt = $this->prepare($sql);
        foreach ($titles as $i => $title) {
            $stmt->bindValue(":ID$i", $title->title_id);
            $stmt->bindValue(":NAME$i", $title->name);
            $stmt->bindValue(":YEAR$i", $title->year);
            $stmt->bindValue(":SEASON$i", $title->season);
            $stmt->bindValue(":IMGURL$i", $title->imgurl);
        }
        return $stmt->execute();
    }

//    public function regist_words(array $words) {
//        $sql = 'INSERT INTO ' . DB_TN_CACHES . ' (' . DB_CN_CACHES_WORD . ', ' . DB_CN_CACHES_TWITTER_ID . ', ' . DB_CN_CACHES_TIMESTAMP . ') VALUES ';
//        $sql_values = array();
//        for ($i = 0; $i < count($words); $i++) {
//            $sql_values[] = "(:WORD$i, :TID$i, :TS$i)";
//        }
//        $sql .= implode(',', $sql_values);
//        $stmt = $this->prepare($sql);
//        foreach ($words as $i => $word) {
//            $stmt->bindValue(":WORD$i", $word->word);
//            $stmt->bindValue(":TID$i", $word->twitter_id);
//            $stmt->bindValue(":TS$i", date(MYSQL_TIMESTAMP, $word->timestamp));
//        }
//        return $stmt->execute();
//    }
//
//    public function regist_procede_word($word) {
//        return $this->insert_procede_word($word);
//    }
//
//    private function insert_procede_word($word) {
//        return $this->insert_special_word($word, 'p');
//    }
//
//    private function insert_special_word($word, $type) {
//        $stmt = $this->prepare('INSERT INTO ' . DB_TN_SPECIALS . ' (' . DB_CN_SPECIALS_WORD . ', ' . DB_CN_SPECIALS_TYPE . ') VALUES (:WORD, :TYPE)');
//        $stmt->bindValue(':WORD', $word);
//        $stmt->bindValue(':TYPE', $type);
//        return $stmt->execute();
//    }
//
//    public function insert_logs($words) {
//        $dh = date(MYSQL_TIMESTAMP_DATEHOUR);
//        foreach ($words as $word => $point) {
//            $this->insert_log($word, $point, $dh);
//        }
//    }
//
//    public function insert_memorys($words) {
//        $date = date(MYSQL_TIMESTAMP_DATE);
//        foreach ($words as $word => $point) {
//            $this->insert_memory($word, $point, $date);
//        }
//    }
//
//    private function insert_log($word, $point, $datehour) {
//        $stmt = $this->prepare('INSERT INTO ' . DB_TN_LOGS . ' (' . DB_CN_LOGS_WORD . ', ' . DB_CN_LOGS_POINT . ', ' . DB_CN_LOGS_DATEHOUR . ') VALUES (:WORD, :POINT, :DATEHOUR)');
//        $stmt->bindValue(':WORD', $word);
//        $stmt->bindValue(':POINT', $point);
//        $stmt->bindValue(':DATEHOUR', $datehour);
//        return $stmt->execute();
//    }
//
//    private function insert_memory($word, $count, $date) {
//        $stmt = $this->prepare('INSERT INTO ' . DB_TN_MEMORYS . ' (' . DB_CN_MEMORYS_WORD . ', ' . DB_CN_MEMORYS_COUNT . ', ' . DB_CN_MEMORYS_DATE . ') VALUES (:WORD, :COUNT, :DATE) ON DUPLICATE KEY UPDATE ' . DB_CN_MEMORYS_COUNT . ' = ' . DB_CN_MEMORYS_COUNT . ' + :COUNT');
//        $stmt->bindValue(':WORD', $word);
//        $stmt->bindValue(':COUNT', $count);
//        $stmt->bindValue(':DATE', $date);
//        return $stmt->execute();
//    }
//
//    public function load_logs($time, $limit = NULL) {
//        $sql = 'SELECT * FROM ' . DB_TN_LOGS . ' WHERE ' . DB_CN_LOGS_DATEHOUR . ' = \'' . $time . '\' ORDER BY ' . DB_CN_LOGS_POINT . ' DESC';
//        if (isset($limit)) {
//            $sql .= ' LIMIT ' . $limit;
//        }
//        echo $sql;
//        $stmt = $this->query($sql);
//        return !!$stmt ? $stmt->fetchAll(PDO::FETCH_CLASS) : NULL;
//    }
//
//    public function load_caches() {
//        $words = $this->select_cache_top();
//        $this->delete_caches_all();
//        return $words;
//    }
//
//    public function load_logs_yesterday() {
//        $yesterday = date(MYSQL_TIMESTAMP_DATE, time() - (60 * 60 * 24));
//        return $this->select_logs_date($yesterday);
//    }
//
//    public function select_logs_date($date, $num = TREND_DAY_WORD_NUM) {
//        $stmt = $this->query('SELECT ' . DB_CN_LOGS_WORD . ', sum(' . DB_CN_LOGS_POINT . ') as point_sum FROM `' . DB_TN_LOGS . '` WHERE ' . DB_CN_LOGS_DATEHOUR . ' between \'' . $date . ' 00:00:00\' and \'' . $date . ' 23:59:59\' group by ' . DB_CN_LOGS_WORD . ' ORDER BY sum(point) DESC LIMIT ' . $num);
//        return $stmt->fetchAll(PDO::FETCH_CLASS);
//    }
//
//    public function delete_caches_all() {
//        $this->query('delete FROM ' . DB_TN_CACHES);
//    }
//
//    public function select_cache_top($limit = TOP_LIMIT) {
//        $stmt = $this->query('SELECT * FROM ' . DB_TN_CACHES . ' WHERE ' . DB_CN_CACHES_WORD . ' in ( select ' . DB_CN_CACHES_WORD . ' from( select ' . DB_CN_CACHES_WORD . ' from `tt_caches` group by ' . DB_CN_CACHES_WORD . ' order by count(' . DB_CN_CACHES_WORD . ') DESC limit ' . $limit . ' ) as t)');
//        return $stmt->fetchAll(PDO::FETCH_CLASS);
//    }
//
//    public function select_cache_all() {
//        $stmt = $this->query('SELECT * FROM ' . DB_TN_CACHES);
//        return $stmt->fetchAll();
//    }
//
//    public function get_special_words() {
//        $sql = 'SELECT * FROM ' . DB_TN_SPECIALS;
//        $stmt = $this->prepare($sql);
//        $stmt->execute();
//        return $this->stmt_to_row($stmt);
//    }
//
//    public function check_trendy($words) {
//
//    }
//
//    /**
//     * 単語リストがそれぞれ何度連続でトレンド入りしたのかをチェックする
//     * @param string[] $words
//     * @return int[]
//     */
//    public function check_chains($words) {
//        $h = 1;
//        $chains = array();
//        foreach ($words as $word => $p) {
//            $chains[$word] = 0;
//        }
//        //		echo "---\n";
//        //		echo json_encode($words);
//        while (TRUE) {
//            $time = date(MYSQL_TIMESTAMP_DATEHOUR, strtotime('-' . $h . 'hour'));
//            $logs = $this->load_logs($time);
//            //			echo json_encode($logs);
//            $k = FALSE;
//            foreach ($words as $word => $p) {
//                if (($h - 1) != $chains[$word]) {
//                    continue;
//                }
//                foreach ($logs as $i => $log) {
//                    if ($log->word != $word) {
//                        continue;
//                    }
//                    $chains[$word] ++;
//                    break;
//                }
//                $k = TRUE;
//            }
//            if (!$k) {
//                break;
//            }
//            $h++;
//        }
//        //		echo json_encode($chains);
//        //		echo "\n---\n";
//        return $chains;
//    }
//
//    public function count_memory($word) {
//        $date_after7 = date(MYSQL_TIMESTAMP_DATE, strtotime('-7day'));
//        $stmt = $this->prepare('SELECT sum(`' . DB_CN_MEMORYS_COUNT . '`) as "sum" FROM ' . DB_TN_MEMORYS . ' WHERE ' . DB_CN_MEMORYS_WORD . ' = :WORD AND ' . DB_CN_MEMORYS_DATE . '> ' . $date_after7);
//        $stmt->bindValue(':WORD', $word);
//        $stmt->execute();
//        $fetch = $stmt->fetchAll(PDO::FETCH_CLASS);
//        $res = $fetch[0];
//        return $res->sum;
//    }
//
//    private function stmt_to_row($stmt) {
//        $rows = array();
//        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//            $rows[] = $row;
//        }
//        return $rows;
//    }

}

