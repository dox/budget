<?php
class CostCentres {
	protected $db;

	public function __construct() {
		$this->db = Database::getInstance();
	}

	public function all() {
		$rows = $this->db->fetchAll("SELECT * FROM cost_centres ORDER BY name DESC");
		return array_map(fn($row) => new Order($row['uid']), $rows);
	}
	
	public function allThisYear() {
		global $budgetyear;
		
		$range = $budgetyear->getSqlRange();
		$rows = $this->db->fetchAll("SELECT * FROM orders WHERE date BETWEEN '" . $range['start'] . "' AND '" . $range['end'] . "' ORDER BY date DESC");
		return array_map(fn($row) => new Order($row['uid']), $rows);
	}

	public function byUser($user_id) {
		$rows = $this->db->fetchAll("SELECT * FROM orders WHERE user_id = ? ORDER BY date DESC", [$user_id]);
		return array_map(fn($row) => new Order($row['uid']), $rows);
	}

	public function byBudgetCode($code) {
		$rows = $this->db->fetchAll("SELECT * FROM orders WHERE budget_code = ? ORDER BY date DESC", [$code]);
		return array_map(fn($row) => new Order($row['id']), $rows);
	}

	public function getTotalSpend($code) {
		$row = $this->db->fetch("SELECT SUM(amount) AS total FROM orders WHERE budget_code = ?", [$code]);
		return $row ? (float) $row['total'] : 0;
	}

	public function count() {
		$row = $this->db->fetch("SELECT COUNT(*) AS c FROM orders");
		return $row ? (int) $row['c'] : 0;
	}
}
