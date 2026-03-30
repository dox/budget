<?php
class CostCentre {
	public int $id;
	public BudgetYear $budgetYear;

	public string $code;
	public string $name;
	public ?string $description = null;

	public float $budgetValue = 0.0;
	public bool $hasBudget = false;

	public function __construct(int $id, BudgetYear $budgetYear)
	{
		$this->id = $id;
		$this->budgetYear = $budgetYear;

		$this->loadBaseData();
		$this->loadBudgetValue();
	}

	protected function loadBaseData(): void
	{
		global $db;

		$sql = "
			SELECT code, name, description
			FROM cost_centres
			WHERE id = :id
			LIMIT 1
		";

		$row = $db->fetch($sql, ['id' => $this->id]);

		if (!$row) {
			throw new RuntimeException("Cost centre {$this->id} not found");
		}

		$this->code        = $row['code'];
		$this->name        = $row['name'];
		$this->description = $row['description'];
	}

	protected function loadBudgetValue(): void
	{
		global $db;

		$sql = "
			SELECT budget_value
			FROM cost_centre_budgets
			WHERE cost_centre_id = :id
			  AND budget_year_start = :budget_year_start
			LIMIT 1
		";

		$row = $db->fetch($sql, [
			'id' => $this->id,
			'budget_year_start' => $this->budgetYear->startDate->format('Y-m-d')
		]);

		$this->budgetValue = $row
			? (float) $row['budget_value']
			: 0.0;
		$this->hasBudget = $row !== false;
	}

	public static function all(BudgetYear $budgetYear): array
	{
		global $db;

		$rows = $db->fetchAll("SELECT id FROM cost_centres ORDER BY code");
		$costCentres = [];

		foreach ($rows as $row) {
			$costCentres[] = new self((int) $row['id'], $budgetYear);
		}

		return $costCentres;
	}

	public static function withBudget(BudgetYear $budgetYear): array
	{
		return array_values(array_filter(
			self::all($budgetYear),
			fn(self $costCentre): bool => $costCentre->hasBudget
		));
	}

	public static function budgetOptionsByYear(): array
	{
		$db = Database::getInstance();
		$rows = $db->fetchAll(
			"SELECT
				ccb.budget_year_start,
				cc.id,
				cc.code,
				cc.name,
				ccb.budget_value
			FROM cost_centre_budgets ccb
			INNER JOIN cost_centres cc
				ON cc.id = ccb.cost_centre_id
			ORDER BY ccb.budget_year_start DESC, cc.code ASC"
		);

		$optionsByYear = [];

		foreach ($rows as $row) {
			$year = (int) date('Y', strtotime((string) $row['budget_year_start']));
			$optionsByYear[$year][] = [
				'id' => (int) ($row['id'] ?? 0),
				'code' => (string) $row['code'],
				'name' => (string) $row['name'],
				'budgetValue' => (float) $row['budget_value'],
			];
		}

		return $optionsByYear;
	}

	public static function create(array $data): int|false
	{
		self::validate($data);
		$model = new class extends Model {
			protected static string $table = 'cost_centres';
		};

		return $model->insert([
			'code' => trim($data['code']),
			'name' => trim($data['name']),
			'description' => self::normaliseDescription($data['description'] ?? null),
		]);
	}

	public static function updateById(int $id, array $data): bool
	{
		self::validate($data);
		$db = Database::getInstance();

		$sql = "UPDATE cost_centres
			SET code = :code,
				name = :name,
				description = :description
			WHERE id = :id
			LIMIT 1";

		$stmt = $db->query($sql, [
			':id' => $id,
			':code' => trim($data['code']),
			':name' => trim($data['name']),
			':description' => self::normaliseDescription($data['description'] ?? null),
		]);

		return $stmt !== false;
	}

	public static function deleteById(int $id): bool
	{
		$db = Database::getInstance();
		$db->beginTransaction();

		try {
			$db->query(
				"DELETE FROM cost_centre_budgets WHERE cost_centre_id = :id",
				[':id' => $id]
			);
			$db->query(
				"DELETE FROM cost_centres WHERE id = :id LIMIT 1",
				[':id' => $id]
			);
			$db->commit();

			return true;
		} catch (Throwable $e) {
			$db->rollBack();
			throw $e;
		}
	}

	public static function saveBudget(int $costCentreId, BudgetYear $budgetYear, float $budgetValue): bool
	{
		$db = Database::getInstance();
		$params = [
			':cost_centre_id' => $costCentreId,
			':budget_year_start' => $budgetYear->startDate->format('Y-m-d'),
			':budget_value' => $budgetValue,
		];

		$existing = $db->fetch(
			"SELECT cost_centre_id
			FROM cost_centre_budgets
			WHERE cost_centre_id = :cost_centre_id
			  AND budget_year_start = :budget_year_start
			LIMIT 1",
			[
				':cost_centre_id' => $costCentreId,
				':budget_year_start' => $budgetYear->startDate->format('Y-m-d'),
			]
		);

		if ($existing) {
			$stmt = $db->query(
				"UPDATE cost_centre_budgets
				SET budget_value = :budget_value
				WHERE cost_centre_id = :cost_centre_id
				  AND budget_year_start = :budget_year_start
				LIMIT 1",
				$params
			);

			return $stmt !== false;
		}

		$stmt = $db->query(
			"INSERT INTO cost_centre_budgets (cost_centre_id, budget_year_start, budget_value)
			VALUES (:cost_centre_id, :budget_year_start, :budget_value)",
			$params
		);

		return $stmt !== false;
	}

	public static function deleteBudget(int $costCentreId, BudgetYear $budgetYear): bool
	{
		$db = Database::getInstance();
		$stmt = $db->query(
			"DELETE FROM cost_centre_budgets
			WHERE cost_centre_id = :cost_centre_id
			  AND budget_year_start = :budget_year_start
			LIMIT 1",
			[
				':cost_centre_id' => $costCentreId,
				':budget_year_start' => $budgetYear->startDate->format('Y-m-d'),
			]
		);

		return $stmt !== false;
	}

	protected static function normaliseDescription(?string $description): ?string
	{
		$description = trim((string) $description);

		return $description === '' ? null : $description;
	}

	protected static function validate(array $data): void
	{
		if (trim((string) ($data['code'] ?? '')) === '') {
			throw new InvalidArgumentException('Cost centre code is required.');
		}

		if (trim((string) ($data['name'] ?? '')) === '') {
			throw new InvalidArgumentException('Cost centre name is required.');
		}
	}
}
