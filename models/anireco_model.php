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

    /**
     * 
     * @param Title[] $titles
     * @return type
     */
	public function regist_titles(array $titles) {
		$sql_head = 'INSERT INTO `' . DB_TN_TITLES . '` (`' . DB_CN_TITLES_ANI_TITLE_ID . '`, `' . DB_CN_TITLES_TITLE_NAME . '`, `' . DB_CN_TITLES_TITLE_YEAR . '`, `' . DB_CN_TITLES_TITLE_SEASON . '`, `' . DB_CN_TITLES_TITLE_IMGURL . '`, `' . DB_CN_TITLES_TITLE_TYPE . '`) VALUES';
		$sql_values = array();
		for ($i = 0; $i < count($titles); $i++) {
			$sql_values[] = "(:ID$i, :NAME$i, :YEAR$i, :SEASON$i, :IMGURL$i, :TYPE$i)";
		}
		$sql = $sql_head . implode(',', $sql_values);
		$stmt = $this->prepare($sql);
		foreach ($titles as $i => $title) {
			$stmt->bindValue(":ID$i", $title->title_id);
			$stmt->bindValue(":NAME$i", $title->name);
			$stmt->bindValue(":YEAR$i", $title->year);
			$stmt->bindValue(":SEASON$i", $title->season);
			$stmt->bindValue(":IMGURL$i", $title->imgurl);
			$stmt->bindValue(":TYPE$i", $title->type);
		}
		$res = $stmt->execute();
		if (!$res) {
            echo 'x';
//			echo $titles[0]->title_id . '×';
		}
		return $res;
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
		$sql_head = 'INSERT INTO `' . DB_TN_RANKS . '` (`' . DB_CN_RANKS_ANI_BEST_ID . '`, `' . DB_CN_RANKS_ANI_TITLE_ID . '`, `' . DB_CN_RANKS_RANK_NUM . '`) VALUES';
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
		if (!$stmt->execute()) {
			var_dump($stmt);
			var_dump($ranks);
			var_dump($real_query);
			exit;
		}
	}

    public function load_bests($best_ids) {
        $best_list = array();
        foreach ($best_ids as $id) {
            $best_list[] = $this->load_best($id);
        }
        return $best_list;
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

	public function load_rand_title() {
        $sql = 'SELECT * FROM `ar_titles` WHERE `title_type` = 1 and `title_year` > 2000 and `is_rankin` = 1';
        $stmt = $this->query($sql);
        $data = $stmt->fetchAll();
        return $this->wrap_title($data[array_rand($data)]);
	}

    public function collect_titles(array $titles) {
        $title_list = array();
        foreach($titles as $title_id => $title) {
            foreach($this->pickup_bests($title_id) as $reco_title) {
                $title_list[] = $reco_title[DB_CN_RANKS_BEST_ID];
            }
        }
        return $title_list;
    }

    public function pickup_bests($title_id) {
		echo $sql = 'SELECT * from `' . DB_TN_RANKS . '` WHERE `' . DB_CN_RANKS_TITLE_ID . '` = ' . $title_id . ' ORDER BY `' . DB_CN_RANKS_RANK_NUM . '`';
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }

	private function wrap_ranks($rec_ranks) {
		$rank_list = array();
		foreach ($rec_ranks as $rec_rank) {
			$rank_list[] = $this->wrap_rank($rec_rank);
		}
		return $rank_list;
	}

    private function wrap_title($rec_title) {
        $title = new Title();
        $title->install($rec_title);
        return $title;
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

    public function update_titles_description($id, $description) {
		$sql = 'UPDATE `' . DB_TN_TITLES . '` SET `' . DB_CN_TITLES_TITLE_DESCRIPTION . '` = :DES WHERE `' . DB_CN_TITLES_ID . '` = ' . $id;
		$stmt = $this->prepare($sql);
        $stmt->bindValue(":DES", $description);
		return $stmt->execute();
    }

	public function select_ranks($best_id) {
		$sql = 'SELECT * from `' . DB_TN_RANKS . '` WHERE `' . DB_CN_RANKS_BEST_ID . '` = :ID ORDER BY `' . DB_CN_RANKS_RANK_NUM . '`';
		$stmt = $this->prepare($sql);
		$stmt->bindValue(":ID", $best_id);
		return $stmt->execute() ? $stmt->fetchAll() : FALSE;
	}

    public function load_title($title_id) {
        return $this->wrap_title($this->select_title($title_id));
    }

	public function select_title($title_id) {
        $sql = 'SELECT * from `' . DB_TN_TITLES . '` WHERE `' . DB_CN_TITLES_ID . '` = :ID';
		$stmt = $this->prepare($sql);
        $stmt->bindValue(':ID', $title_id);
		return $stmt->execute() ? $stmt->fetch() : FALSE;
    }

	public function select_best($best_id) {
		$sql = 'SELECT * from `' . DB_TN_BESTS . '` WHERE `' . DB_CN_BESTS_ID . '` = :ID';
		$stmt = $this->prepare($sql);
		$stmt->bindValue(":ID", $best_id);
		return $stmt->execute() ? $stmt->fetch() : FALSE;
	}

	public function select_bests_ids_next() {
		$next_id = $this->select_ranks_last_best_id() ? : 0;
		return $this->select_bests_ids($next_id);
	}

	function select_ranks_best_ids() {
		$sql = 'SELECT distinct `ani_best_id` FROM `ar_ranks`';
		$stmt = $this->query($sql);
		return $stmt->fetchAll();
	}

	public function select_ranks_ids($since = 0) {
		$sql = 'SELECT `' . DB_CN_BESTS_ANI_BEST_ID . '` from `' . DB_TN_RANKS . '`';
		if ($since != 0) {
			$sql .= ' WHERE `' . DB_CN_BESTS_ANI_BEST_ID . '` > ' . $since;
		}
		$stmt = $this->query($sql);
		return $stmt->fetchAll();
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
		$sql = 'SELECT `' . DB_CN_RANKS_ANI_BEST_ID . '` from `' . DB_TN_RANKS . '` ORDER BY `' . DB_CN_RANKS_ANI_BEST_ID . '` DESC LIMIT 1';
		$stmt = $this->query($sql);
		$res = $stmt->fetch();
		if ($res) {
			return $res[DB_CN_RANKS_ANI_BEST_ID];
		}
		return FALSE;
	}

	public function select_all_bests() {
		$sql = 'SELECT * FROM `ar_bests` where `ani_best_id` in (select `ani_best_id` from `ar_ranks` where `best_id` = 0)';
		$stmt = $this->query($sql);
		return $stmt->fetchAll();
	}

	public function update_ranks_best_id() {
		foreach ($this->select_all_bests() as $reco) {
			echo $sql = 'UPDATE `ar_ranks` SET `best_id` = ' . $reco[DB_CN_BESTS_ID] . ' WHERE `ani_best_id` = ' . $reco[DB_CN_BESTS_ANI_BEST_ID];
			$this->query($sql);
		}
	}

	public function select_all_titles() {
		$sql = 'SELECT * FROM `ar_titles` where `ani_title_id` in (select `ani_title_id` from `ar_ranks` where `title_id` = 0)';
		$stmt = $this->query($sql);
		return $stmt->fetchAll();
	}

	public function select_all_titles2() {
		$sql = 'SELECT `title_id`, `ani_title_id` FROM `ar_titles` WHERE `title_description` != 0';
		$stmt = $this->query($sql);
		return $stmt->fetchAll();
	}

	public function update_ranks_title_id() {
        $title_ids = $this->select_all_titles();
		foreach ($title_ids as $reco) {
			$sql = 'UPDATE `ar_ranks` SET `title_id` = ' . $reco[DB_CN_TITLES_ID] . ' WHERE `ani_title_id` = ' . $reco[DB_CN_TITLES_ANI_TITLE_ID];
			$this->query($sql);
		}
	}

}
