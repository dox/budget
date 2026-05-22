<?php
class Orders {
	protected $db;
	
	protected static string $table = 'orders';

	public function __construct() {
		$this->db = Database::getInstance();
	}

	public function all() {
		$rows = $this->db->fetchAll("SELECT * FROM " . static::$table . " ORDER BY date_created DESC");
		return array_map(fn($row) => new Order($row['id']), $rows);
	}
	
	public function allForBudgetYear(?BudgetYear $budgetYear = null): array {
		$budgetYear ??= BudgetYear::current();

		$sql = "SELECT * FROM " . static::$table . "
			WHERE date_created BETWEEN :start_date AND :end_date
			ORDER BY date_created DESC";

		$rows = $this->db->fetchAll($sql, [
			':start_date' => $budgetYear->startDate->format('Y-m-d H:i:s'),
			':end_date' => $budgetYear->endDate->format('Y-m-d H:i:s'),
		]);

		return array_map(fn($row) => new Order($row['id']), $rows);
	}

	public function allThisYear(): array {
		return $this->allForBudgetYear(BudgetYear::current());
	}

	public function search(string $query, int $limit = 100): array {
		$query = trim($query);

		if ($query === '') {
			return [];
		}

		$limit = max(1, min($limit, 250));
		$likeQuery = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $query) . '%';

		$sql = "SELECT * FROM " . static::$table . "
			WHERE CAST(id AS CHAR) LIKE ? ESCAPE '\\\\'
			   OR CAST(value AS CHAR) LIKE ? ESCAPE '\\\\'
			   OR DATE_FORMAT(date_created, '%Y-%m-%d') LIKE ? ESCAPE '\\\\'
			   OR po LIKE ? ESCAPE '\\\\'
			   OR order_num LIKE ? ESCAPE '\\\\'
			   OR name LIKE ? ESCAPE '\\\\'
			   OR supplier LIKE ? ESCAPE '\\\\'
			   OR notes LIKE ? ESCAPE '\\\\'
			   OR items LIKE ? ESCAPE '\\\\'
			ORDER BY date_created DESC
			LIMIT {$limit}";

		$rows = $this->db->fetchAll($sql, array_fill(0, 9, $likeQuery));

		return array_map(fn($row) => new Order($row['id']), $rows);
	}

	public function forCostCentre(BudgetYear $budgetYear, CostCentre $costCentre): array
	{
		return array_values(array_filter(
			$this->allForBudgetYear($budgetYear),
			fn(Order $order): bool => (int) $order->cost_centre === $costCentre->id
		));
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
