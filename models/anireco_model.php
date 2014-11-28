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
        $sql_head = 'INSERT INTO `' . DB_TN_TITLES . '` (`' . DB_CN_TITLES_ANI_TITLE_ID . '`, `' . DB_CN_TITLES_TITLE_NAME . '`, `' . DB_CN_TITLES_TITLE_YEAR . '`, `' . DB_CN_TITLES_TITLE_SEASON. '`, `' . DB_CN_TITLES_TITLE_IMGURL . '`) VALUES';
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
        $res = $stmt->execute();
        if (!$res) {
            echo $titles[0]->title_id . '×';
        }
        return  $res;
    }

    public function regist_bests($bests) {
        $sql_head = 'INSERT INTO `' . DB_TN_BESTS . '` (`' . DB_CN_BESTS_ANI_BEST_ID . '`, `' . DB_CN_BESTS_BEST_NAME . '`, `' . DB_CN_BESTS_THANKYOU . '`) VALUES';
        $sql_values = array();
        for ($i = 0; $i < count($bests); $i++) {
            $sql_values[] = "(:ID$i, :NAME$i, :THANK$i)";
        }
        $sql = $sql_head . implode(',', $sql_values);
        $stmt = $this->prepare($sql);
        foreach ($bests as $i => $best) {
            $stmt->bindValue(":ID$i", $best->best_id);
            $stmt->bindValue(":NAME$i", $best->name);
            $stmt->bindValue(":THANK$i", $best->thankyou);
        }
        return $stmt->execute();
    }

    public function regist_ranks($ranks) {
        $sql_head = 'INSERT INTO `' . DB_TN_RANKS . '` (`' . DB_CN_RANKS_BEST_ID . '`, `' . DB_CN_RANKS_TITLE_ID . '`, `' . DB_CN_RANKS_RANK_NUM . '`) VALUES';
        $sql_values = array();
        for ($i = 0; $i < count($ranks); $i++) {
            $sql_values[] = "(:BI$i, :TI$i, :NUM$i)";
        }
        $sql = $sql_head . implode(',', $sql_values);
        $real_query = $sql;
        $stmt = $this->prepare($sql);
        foreach ($ranks as $i => $rank) {
            $stmt->bindValue(":BI$i", $rank->best_id);
            $stmt->bindValue(":TI$i", $rank->title_id);
            $stmt->bindValue(":NUM$i", $rank->rank_num);
            $real_query = str_replace(array(":BI$i", ":TI$i", ":NUM$i"), array($rank->best_id, $rank->title_id, $rank->rank_num), $real_query);
        }
        var_dump($stmt);
        var_dump($ranks);
        var_dump($real_query);
        exit;
        return $stmt->execute();
    }

    public function load_best($best_id) {
        $rec_best = $this->select_best($best_id);
        if ($rec_best === FALSE) {
            return NULL;
        }
        $rec_ranks = $this->select_ranks($best_id);
        $rank_list = $this->wrap_ranks($rec_ranks);
        $best = new Best();
        $best->install($rec_best, $rank_list);
        return $best;
    }

    private function wrap_ranks($rec_ranks) {
        $rank_list = array();
        foreach ($rec_ranks as $rec_rank) {
            $rank_list[] = $this->wrap_rank($rec_rank);
        }
        return $rank_list;
    }

    private function wrap_rank($rec_rank) {
        $rank = new Rank();
        $rank->install($rec_rank);
        return $rank;
    }

    public function wrap_best($reco) {
        if ($reco === FALSE) {
            return FALSE;
        }
        $best = new Best();
        $best->install($reco, NULL);
        return $best;
    }

    public function load_title($title_id) {
    }

    public function select_ranks($best_id) {
        $sql = 'SELECT * from `' . DB_TN_RANKS . '` WHERE `' . DB_CN_RANKS_BEST_ID . '` = :ID ORDER BY `' . DB_CN_RANKS_RANK_NUM . '`';
        $stmt = $this->prepare($sql);
        $stmt->bindValue(":ID", $best_id);
        return $stmt->execute() ? $stmt->fetchAll() : FALSE;
    }

    public function select_best($best_id) {
        $sql = 'SELECT * from `' . DB_TN_BESTS . '` WHERE `' . DB_CN_BESTS_ANI_BEST_ID . '` = :ID';
        $stmt = $this->prepare($sql);
        $stmt->bindValue(":ID", $best_id);
        return $stmt->execute() ? $stmt->fetch() : FALSE;
    }

    public function select_bests_ids_next() {
        $next_id = $this->select_ranks_last_best_id() ?: 0;
        return $this->select_bests_ids($next_id);
    }

    public function select_bests_ids($since = 0) {
        $sql = 'SELECT `' . DB_CN_BESTS_ANI_BEST_ID . '` from `' . DB_TN_BESTS . '`';
        if ($since != 0) {
            $sql .= ' WHERE `' . DB_CN_BESTS_ANI_BEST_ID . '` > ' . $since;
        }
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }

    public function select_ranks_last_best_id() {
        $sql = 'SELECT `' . DB_CN_RANKS_BEST_ID . '` from `' . DB_TN_RANKS . '` ORDER BY `' . DB_CN_RANKS_BEST_ID . '` DESC LIMIT 1';
        $stmt = $this->query($sql);
        if ($res = $stmt->fetch()) {
            return $res[DB_CN_RANKS_BEST_ID];
        }
        return FALSE;
    }

}

