<?php

class Supplier {
	protected static string $table = 'suppliers';
	protected static ?array $columns = null;
	protected array $data = [];

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public static function findByName(string $supplierName): ?self
	{
		$supplierName = trim($supplierName);
		if ($supplierName === '' || !self::tableExists()) {
			return null;
		}

		$matchColumn = self::firstAvailableColumn([
			'name',
			'supplier',
			'company',
			'company_name',
			'trading_name',
		]);

		if ($matchColumn === null) {
			return null;
		}

		$db = Database::getInstance();
		$row = $db->fetch(
			'SELECT * FROM `' . static::$table . '` WHERE LOWER(TRIM(`' . $matchColumn . '`)) = LOWER(TRIM(:supplier_name)) LIMIT 1',
			[':supplier_name' => $supplierName]
		);

		return $row ? new self($row) : null;
	}

	public static function findByUid(int $uid): ?self
	{
		if ($uid <= 0 || !self::tableExists()) {
			return null;
		}

		$db = Database::getInstance();
		$row = $db->fetch(
			'SELECT * FROM `' . static::$table . '` WHERE `uid` = :uid LIMIT 1',
			[':uid' => $uid]
		);

		return $row ? new self($row) : null;
	}

	public static function create(array $data): ?self
	{
		self::validate($data);

		$db = Database::getInstance();
		$stmt = $db->query(
			'INSERT INTO `' . static::$table . '` (
				`name`,
				`account_number`,
				`address`,
				`telephone`,
				`mobile`,
				`email`,
				`website`
			) VALUES (
				:name,
				:account_number,
				:address,
				:telephone,
				:mobile,
				:email,
				:website
			)',
			[
				':name' => trim((string) ($data['name'] ?? '')),
				':account_number' => self::normaliseOptional($data['account_number'] ?? null),
				':address' => self::normaliseOptional($data['address'] ?? null),
				':telephone' => self::normaliseOptional($data['telephone'] ?? null),
				':mobile' => self::normaliseOptional($data['mobile'] ?? null),
				':email' => self::normaliseOptional($data['email'] ?? null),
				':website' => self::normaliseOptional($data['website'] ?? null),
			]
		);

		if ($stmt === false) {
			return null;
		}

		return self::findByUid((int) $db->lastInsertId());
	}

	public static function findOrCreateByName(string $supplierName): ?self
	{
		$supplierName = trim($supplierName);
		if ($supplierName === '') {
			return null;
		}

		$existing = self::findByName($supplierName);
		if ($existing) {
			return $existing;
		}

		return self::create(['name' => $supplierName]);
	}

	public static function uidsByNames(array $names): array
	{
		$names = array_values(array_filter(array_map(
			static fn($name): string => trim((string) $name),
			$names
		), static fn(string $name): bool => $name !== ''));

		if ($names === [] || !self::tableExists()) {
			return [];
		}

		$matchColumn = self::firstAvailableColumn([
			'name',
			'supplier',
			'company',
			'company_name',
			'trading_name',
		]);

		if ($matchColumn === null) {
			return [];
		}

		$placeholders = [];
		$params = [];

		foreach ($names as $index => $name) {
			$placeholder = ':name_' . $index;
			$placeholders[] = 'LOWER(TRIM(' . $placeholder . '))';
			$params[$placeholder] = $name;
		}

		$db = Database::getInstance();
		$rows = $db->fetchAll(
			'SELECT `uid`, `' . $matchColumn . '` AS supplier_name
			FROM `' . static::$table . '`
			WHERE LOWER(TRIM(`' . $matchColumn . '`)) IN (' . implode(', ', $placeholders) . ')'
		, $params);

		$uids = [];
		foreach ($rows as $row) {
			$name = trim((string) ($row['supplier_name'] ?? ''));
			$uid = (int) ($row['uid'] ?? 0);
			if ($name !== '' && $uid > 0) {
				$uids[strtolower($name)] = $uid;
			}
		}

		return $uids;
	}

	public static function updateByUid(int $uid, array $data): bool
	{
		if ($uid <= 0) {
			throw new InvalidArgumentException('Invalid supplier ID.');
		}

		self::validate($data);
		$db = Database::getInstance();
		$existingSupplier = self::findByUid($uid);

		if (!$existingSupplier) {
			throw new RuntimeException('Supplier not found.');
		}

		$existingName = trim((string) ($existingSupplier->name() ?? ''));
		$newName = trim((string) ($data['name'] ?? ''));

		$db->beginTransaction();

		try {
			$sql = 'UPDATE `' . static::$table . '`
				SET `name` = :name,
					`account_number` = :account_number,
					`address` = :address,
					`telephone` = :telephone,
					`mobile` = :mobile,
					`email` = :email,
					`website` = :website
				WHERE `uid` = :uid
				LIMIT 1';

			$stmt = $db->query($sql, [
				':uid' => $uid,
				':name' => $newName,
				':account_number' => self::normaliseOptional($data['account_number'] ?? null),
				':address' => self::normaliseOptional($data['address'] ?? null),
				':telephone' => self::normaliseOptional($data['telephone'] ?? null),
				':mobile' => self::normaliseOptional($data['mobile'] ?? null),
				':email' => self::normaliseOptional($data['email'] ?? null),
				':website' => self::normaliseOptional($data['website'] ?? null),
			]);

			if ($existingName !== '' && strcasecmp($existingName, $newName) !== 0) {
				$db->query(
					'UPDATE `orders`
					SET `supplier` = :new_name
					WHERE LOWER(TRIM(`supplier`)) = LOWER(TRIM(:existing_name))',
					[
						':new_name' => $newName,
						':existing_name' => $existingName,
					]
				);
			}

			$db->commit();

			return $stmt !== false;
		} catch (Throwable $e) {
			$db->rollBack();
			throw $e;
		}
	}

	public static function tableExists(): bool
	{
		return self::columns() !== [];
	}

	public static function columns(): array
	{
		if (self::$columns !== null) {
			return self::$columns;
		}

		$db = Database::getInstance();
		$rows = $db->fetchAll(
			'SELECT column_name
			FROM information_schema.columns
			WHERE table_schema = :table_schema
			  AND table_name = :table_name
			ORDER BY ordinal_position',
			[
				':table_schema' => DB_NAME,
				':table_name' => static::$table,
			]
		);

		self::$columns = array_values(array_filter(array_map(
			static function (array $row): string {
				foreach (['column_name', 'COLUMN_NAME', 'Field'] as $key) {
					if (isset($row[$key])) {
						return (string) $row[$key];
					}
				}

				$firstValue = reset($row);

				return $firstValue === false ? '' : (string) $firstValue;
			},
			$rows
		), static fn(string $column): bool => $column !== ''));

		return self::$columns;
	}

	protected static function firstAvailableColumn(array $candidates): ?string
	{
		$columns = self::columns();

		foreach ($candidates as $candidate) {
			if (in_array($candidate, $columns, true)) {
				return $candidate;
			}
		}

		return null;
	}

	protected function firstPopulatedValue(array $candidates): ?string
	{
		foreach ($candidates as $candidate) {
			$value = trim((string) ($this->data[$candidate] ?? ''));
			if ($value !== '') {
				return $value;
			}
		}

		return null;
	}

	public function name(): ?string
	{
		return $this->firstPopulatedValue([
			'name',
			'supplier',
			'company',
			'company_name',
			'trading_name',
		]);
	}

	public function uid(): int
	{
		return (int) ($this->data['uid'] ?? 0);
	}

	public function contactName(): ?string
	{
		return $this->firstPopulatedValue([
			'contact_name',
			'contact',
			'contact_person',
			'account_manager',
		]);
	}

	public function email(): ?string
	{
		return $this->firstPopulatedValue([
			'email',
			'email_address',
			'contact_email',
		]);
	}

	public function accountNumber(): ?string
	{
		return $this->firstPopulatedValue([
			'account_number',
			'account_no',
			'account',
		]);
	}

	public function telephone(): ?string
	{
		return $this->firstPopulatedValue([
			'telephone',
			'phone',
			'phone_number',
			'tel',
			'contact_number',
		]);
	}

	public function mobile(): ?string
	{
		return $this->firstPopulatedValue([
			'mobile',
			'mobile_number',
			'cell',
			'cellphone',
		]);
	}

	public function phone(): ?string
	{
		return $this->telephone() ?? $this->mobile();
	}

	public function website(): ?string
	{
		return $this->firstPopulatedValue([
			'website',
			'web',
			'url',
		]);
	}

	public function addressLines(): array
	{
		$genericAddress = $this->firstPopulatedValue(['address']);
		if ($genericAddress !== null) {
			return preg_split('/\r\n|\r|\n/', $genericAddress) ?: [$genericAddress];
		}

		$lines = [];
		foreach (['address1', 'address2', 'address3', 'city', 'county', 'postcode', 'country'] as $column) {
			$value = trim((string) ($this->data[$column] ?? ''));
			if ($value !== '') {
				$lines[] = $value;
			}
		}

		return $lines;
	}

	public function value(string $column): ?string
	{
		$value = $this->data[$column] ?? null;

		if ($value === null) {
			return null;
		}

		return (string) $value;
	}

	protected static function normaliseOptional(?string $value): ?string
	{
		$value = trim((string) $value);

		return $value === '' ? null : $value;
	}

	protected static function validate(array $data): void
	{
		if (trim((string) ($data['name'] ?? '')) === '') {
			throw new InvalidArgumentException('Supplier name is required.');
		}
	}
}
