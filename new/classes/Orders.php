<?php
class Orders {
	protected $db;
	
	protected static string $table = 'new_orders';

	public function __construct() {
		$this->db = Database::getInstance();
	}

	public function all() {
		$rows = $this->db->fetchAll("SELECT * FROM " . static::$table . " ORDER BY date_created DESC");
		return array_map(fn($row) => new Order($row['id']), $rows);
	}
	
	public function allThisYear() {
		$budgetyear = new BudgetYear();
		
		$rows = $this->db->fetchAll("SELECT * FROM " . static::$table . " WHERE date_created BETWEEN '" . $budgetyear->getStart()->format('Y-m-d') . "' AND '" . $budgetyear->getEnd()->format('Y-m-d') . "' ORDER BY date_created DESC");
		return array_map(fn($row) => new Order($row['id']), $rows);
	}

	public function byUser($user_id) {
		$rows = $this->db->fetchAll("SELECT * FROM " . static::$table . " WHERE user_id = ? ORDER BY date_created DESC", [$user_id]);
		return array_map(fn($row) => new Order($row['id']), $rows);
	}

	public function byBudgetCode($code) {
		$rows = $this->db->fetchAll("SELECT * FROM " . static::$table . " WHERE budget_code = ? ORDER BY date_created DESC", [$code]);
		return array_map(fn($row) => new Order($row['id']), $rows);
	}

	public function getTotalSpend($code) {
		$row = $this->db->fetch("SELECT SUM(amount) AS total FROM " . static::$table . " WHERE budget_code = ?", [$code]);
		return $row ? (float) $row['total'] : 0;
	}

	public function count() {
		$row = $this->db->fetch("SELECT COUNT(*) AS c FROM " . static::$table);
		return $row ? (int) $row['c'] : 0;
	}
}