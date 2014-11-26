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
        return $stmt->execute();
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
        $stmt = $this->prepare($sql);
        foreach ($ranks as $i => $rank) {
            $stmt->bindValue(":BI$i", $rank->best_id);
            $stmt->bindValue(":TI$i", $rank->title_id);
            $stmt->bindValue(":NUM$i", $rank->rank_num);
        }
        return $stmt->execute();
    }

    public function select_bests_ids($since = 0) {
        $sql = 'SELECT `' . DB_CN_BESTS_ANI_BEST_ID . '` from `' . DB_TN_BESTS . '`';
        if ($since != 0) {
            $sql .= ' WHERE `' . DB_CN_BESTS_ANI_BEST_ID . '` > ' . $since;
        }
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }

}

