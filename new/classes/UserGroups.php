<?php
class UserGroups {
	protected $db;

	public function __construct() {
		$this->db = Database::getInstance();
	}

	public function all() {
		$rows = $this->db->fetchAll("SELECT * FROM user_groups ORDER BY name DESC");
		return array_map(fn($row) => new UserGroup($row['uid']), $rows);
	}
}
