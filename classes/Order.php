<?php

class OrderLine {
	public string $description;
	public float $amount;
	public CostCentre $CostCentre;
	public BudgetYear $BudgetYear;

	public function __construct(string $description, float $amount, CostCentre $CostCentre, BudgetYear $BudgetYear)
	{
		$this->description = $description;
		$this->amount = $amount;
		$this->CostCentre = $CostCentre;
		$this->BudgetYear = $BudgetYear;
	}
}

class Order extends Model {
	protected static ?bool $attachmentsColumnExists = null;

	public $id;
	public $username;
	public $date_created;
	public $cost_centre;
	public $po;
	public $order_num;
	public $name;
	public $items;
	public $value;
	public $supplier;
	public $notes;
	public $attachments;

	protected $db;
	
	protected static string $table = 'orders';

	public function __construct($id = null) {
		$this->db = Database::getInstance();

		if ($id !== null) {
			$this->getOne($id);
		}
	}

	public function getOne($id) {
		$query = "SELECT * FROM " . static::$table . " WHERE id = ?";
		$row = $this->db->fetch($query, [$id]);

		if ($row) {
			foreach ($row as $key => $value) {
				$this->$key = $value;
			}
		}
	}
	
	public function costCentre () {
		return $this->cost_centre;
	}

	public function costCentreModel(): ?CostCentre {
		$id = (int) $this->cost_centre;
		if ($id <= 0) {
			return null;
		}

		try {
			return new CostCentre($id, $this->budgetYear());
		} catch (RuntimeException) {
			return null;
		}
	}

	public function budgetYear(): BudgetYear {
		return isset($this->date_created)
			? BudgetYear::fromDate($this->date_created)
			: BudgetYear::current();
	}
	
	public function name() {
		$po = isset($this->po) ? $this->po : "No PO";
	
		$name = $this->name ?? (function() {
			$items = json_decode($this->items, true) ?: [];
			$itemNames = array_column($items, 'item_name');
			return $itemNames ? implode(", ", $itemNames) : "No name";
		})();
	
		return "<strong>{$po}</strong> {$name}";
	}

	public static function generatePoReference(int $orderId): string
	{
		if ($orderId <= 0) {
			throw new InvalidArgumentException('Order ID is required to generate a PO reference.');
		}

		return 'IT' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT);
	}

	public static function nextPoReferencePreview(): ?string
	{
		$db = Database::getInstance();
		$row = $db->fetch(
			"SELECT auto_increment
			FROM information_schema.tables
			WHERE table_schema = :table_schema
			  AND table_name = :table_name
			LIMIT 1",
			[
				':table_schema' => DB_NAME,
				':table_name' => static::$table,
			]
		);

		$nextId = (int) ($row['auto_increment'] ?? 0);

		return $nextId > 0 ? self::generatePoReference($nextId) : null;
	}

	public static function recentSuppliers(int $limit = 10): array {
		$db = Database::getInstance();
		$limit = max(1, $limit);
		$rows = $db->fetchAll(
			"SELECT supplier
			FROM " . static::$table . "
			WHERE supplier IS NOT NULL
			  AND supplier <> ''
			GROUP BY supplier
			ORDER BY MAX(date_created) DESC
			LIMIT {$limit}"
		);

		return array_map(
			fn(array $row): string => (string) $row['supplier'],
			$rows
		);
	}

	public static function formatDateTimeLocal(?string $value): string {
		if (!$value) {
			return date('Y-m-d\TH:i');
		}

		return date('Y-m-d\TH:i', strtotime($value));
	}
	
	public function save() {
		if (isset($this->id)) {
			// update
			$sql = "UPDATE " . static::$table . " 
					SET user_id = ?, budget_code = ?, amount = ?, description = ?, invoice_path = ? 
					WHERE id = ?";
			return $this->db->query($sql, [
				$this->user_id, $this->budget_code, $this->amount,
				$this->description, $this->invoice_path, $this->id
			]);
		} else {
			// insert
			$sql = "INSERT INTO " . static::$table . " (user_id, budget_code, amount, description, invoice_path, created_at) 
					VALUES (?, ?, ?, ?, ?, NOW())";
			$this->db->query($sql, [
				$this->user_id, $this->budget_code, $this->amount,
				$this->description, $this->invoice_path
			]);

			$this->id = $this->db->lastInsertId();
			return $this->id;
		}
	}
	
	public function items() {
		$itemsArray = [];
		
		if (isset($this->items)) {
			$items = json_decode($this->items, true);
			
			foreach ($items AS $item) {
				$itemsArray[] = $item;
			}
		}
		
		return $itemsArray;
	}

	public function attachments(): array
	{
		if (!self::hasAttachmentsColumn()) {
			return [];
		}

		if (!isset($this->attachments) || $this->attachments === null || $this->attachments === '') {
			return [];
		}

		$attachments = json_decode((string) $this->attachments, true);

		return is_array($attachments) ? $attachments : [];
	}

	public static function hasAttachmentsColumn(): bool
	{
		if (self::$attachmentsColumnExists !== null) {
			return self::$attachmentsColumnExists;
		}

		$db = Database::getInstance();
		$row = $db->fetch(
			"SELECT 1
			FROM information_schema.columns
			WHERE table_schema = :table_schema
			  AND table_name = :table_name
			  AND column_name = :column_name
			LIMIT 1",
			[
				':table_schema' => DB_NAME,
				':table_name' => static::$table,
				':column_name' => 'attachments',
			]
		);

		self::$attachmentsColumnExists = $row !== false;

		return self::$attachmentsColumnExists;
	}

	public static function storageRoot(): string
	{
		if (!defined('UPLOAD_DIR') || trim((string) UPLOAD_DIR) === '') {
			throw new RuntimeException('UPLOAD_DIR is not configured.');
		}

		return rtrim((string) UPLOAD_DIR, DIRECTORY_SEPARATOR);
	}

	public static function attachmentDirectoryForId(int $orderId): string
	{
		if ($orderId <= 0) {
			throw new InvalidArgumentException('Order ID is required for attachment storage.');
		}

		return self::storageRoot() . DIRECTORY_SEPARATOR . 'orders' . DIRECTORY_SEPARATOR . $orderId;
	}

	public static function ensureAttachmentDirectory(int $orderId): string
	{
		$directory = self::attachmentDirectoryForId($orderId);

		if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
			throw new RuntimeException('Unable to create order attachment directory.');
		}

		if (!is_writable($directory)) {
			throw new RuntimeException('Order attachment directory is not writable.');
		}

		return $directory;
	}

	public static function attachmentToken(): string
	{
		return bin2hex(random_bytes(16));
	}

	public static function sanitiseAttachmentName(string $name): string
	{
		$name = trim($name);
		$name = preg_replace('/[^A-Za-z0-9._ -]/', '_', $name) ?? 'attachment';
		$name = preg_replace('/\s+/', ' ', $name) ?? 'attachment';

		return $name !== '' ? $name : 'attachment';
	}

	public static function detectMimeType(string $path): string
	{
		if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			if ($finfo !== false) {
				$mimeType = finfo_file($finfo, $path) ?: null;
				finfo_close($finfo);

				if (is_string($mimeType) && $mimeType !== '') {
					return $mimeType;
				}
			}
		}

		if (function_exists('mime_content_type')) {
			$mimeType = mime_content_type($path);
			if (is_string($mimeType) && $mimeType !== '') {
				return $mimeType;
			}
		}

		return 'application/octet-stream';
	}

	public static function relativeAttachmentPath(int $orderId, string $storedName): string
	{
		return 'orders/' . $orderId . '/' . $storedName;
	}

	public static function absoluteAttachmentPath(array $attachment): ?string
	{
		if (!defined('UPLOAD_DIR') || trim((string) UPLOAD_DIR) === '') {
			return null;
		}

		$relativePath = (string) ($attachment['path'] ?? '');
		if ($relativePath === '') {
			return null;
		}

		return self::storageRoot() . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
	}

	public static function cleanupAttachmentDirectory(int $orderId): void
	{
		if (!defined('UPLOAD_DIR') || trim((string) UPLOAD_DIR) === '') {
			return;
		}

		$directory = self::attachmentDirectoryForId($orderId);
		if (!is_dir($directory)) {
			return;
		}

		$items = scandir($directory);
		if ($items === false) {
			return;
		}

		$remaining = array_diff($items, ['.', '..']);
		if ($remaining === []) {
			@rmdir($directory);
		}
	}

	public static function deleteAttachmentFiles(int $orderId, array $attachments): void
	{
		foreach ($attachments as $attachment) {
			$absolutePath = self::absoluteAttachmentPath($attachment);
			if ($absolutePath !== null && is_file($absolutePath)) {
				@unlink($absolutePath);
			}
		}

		self::cleanupAttachmentDirectory($orderId);
	}

	public function removeAttachment(string $token): bool
	{
		if (!isset($this->id) || !self::hasAttachmentsColumn()) {
			return false;
		}

		$token = trim($token);
		if ($token === '') {
			return false;
		}

		$remainingAttachments = [];
		$deletedAttachments = [];

		foreach ($this->attachments() as $attachment) {
			if (($attachment['token'] ?? '') === $token) {
				$deletedAttachments[] = $attachment;
				continue;
			}

			$remainingAttachments[] = $attachment;
		}

		if ($deletedAttachments === []) {
			return false;
		}

		$updated = $this->update([
			'id' => (int) $this->id,
			'attachments' => $remainingAttachments,
		]);

		if ($updated) {
			self::deleteAttachmentFiles((int) $this->id, $deletedAttachments);
			$this->attachments = json_encode($remainingAttachments);
		}

		return $updated;
	}

	public function delete() {
		if (!isset($this->id)) {
			return false;
		}

		$attachments = $this->attachments();
		$deleted = $this->db->query("DELETE FROM " . static::$table . " WHERE id = ?", [$this->id]);

		if ($deleted !== false) {
			self::deleteAttachmentFiles((int) $this->id, $attachments);
			self::cleanupAttachmentDirectory((int) $this->id);
		}

		return $deleted;
	}

	public function update(array $data): bool {
		$id = (int) ($data['id'] ?? 0);
		unset($data['id']);

		if ($id <= 0 || $data === []) {
			return false;
		}

		$assignments = [];
		$params = [':id' => $id];

		foreach ($data as $column => $value) {
			$assignments[] = $column . ' = :' . $column;
			$params[':' . $column] = is_array($value) ? json_encode($value) : $value;
		}

		$sql = "UPDATE " . static::$table . "
			SET " . implode(', ', $assignments) . "
			WHERE id = :id
			LIMIT 1";

		$stmt = $this->db->query($sql, $params);

		return $stmt !== false;
	}
}
