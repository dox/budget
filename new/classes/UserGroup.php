<?php

class UserGroup {
	public $uid;
	public $name;
	public $ou;
	public $enabled;

	protected $db;

	public function __construct($uid = null, $ou = null) {
		$this->db = Database::getInstance();

		if ($uid !== null) {
			$this->getOne($uid);
		}
		
		if ($ou !== null) {
			$this->getOneByOU($ou);
		}
	}

	public function getOne($uid) {
		$query = "SELECT * FROM user_groups WHERE uid = ?";
		$row = $this->db->fetch($query, [$uid]);
	
		if ($row) {
			foreach ($row as $key => $value) {
				$this->$key = $value;
			}
		}
	}
	
	public function getOneByOU($ou) {
		$query = "SELECT * FROM user_groups WHERE ou = ?";
		$row = $this->db->fetch($query, [$ou]);
		
		if ($row) {
			foreach ($row as $key => $value) {
				$this->$key = $value;
			}
		}
	}	
}
