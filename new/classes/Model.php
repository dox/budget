<?php
abstract class Model {
	protected $db;
	protected static string $table;

	public function __construct() {
		$this->db = Database::getInstance();
	}

	public function getOne($id) {
		$query = "SELECT * FROM " . static::$table . " WHERE id = ?";
		$row = $this->db->fetch($query, [$id]);
		
		if ($row) return $row;
	}
	
	public function getAll() {
		$query = "SELECT * FROM " . static::$table;
		$rows = $this->db->fetchAll($query);
		return $rows;
	}
	
	/**
	 * Generic INSERT for all subclasses
	 *
	 * @param array $data  Associative array of column => value
	 * @return int|false   Inserted row ID or false on failure
	 */
	public function insert(array $data, bool $log = true) {
		if (empty($data)) {
			throw new InvalidArgumentException("Insert data cannot be empty.");
		}
	
		// JSON-encode arrays automatically
		$params = [];
		foreach ($data as $col => $val) {
			if (is_array($val)) {
				$val = json_encode($val); // <-- encode arrays
			}
			$params[":$col"] = $val;
		}
	
		$columns = array_keys($data);
		$placeholders = array_map(fn($c) => ':' . $c, $columns);
	
		$sql = sprintf(
			"INSERT INTO %s (%s) VALUES (%s)",
			static::$table,
			implode(', ', $columns),
			implode(', ', $placeholders)
		);
	
		$stmt = $this->db->query($sql, $params);
		$insertId = $stmt ? $this->db->lastInsertId() : false;
	
		// Optional logging
		if ($log && $insertId !== false && static::$table !== 'new_logs') {
			$this->logInsert($insertId, $data);
		}
	
		return $insertId;
	}
	
	private function logInsert(int $id, array $data): void {
		// We reference Log dynamically to avoid recursion
		$log = new Log();
	
		$summary = sprintf(
			'Inserted into %s (ID %d): %s',
			static::$table,
			$id,
			json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
		);
	
		$log->add($summary, Log::INFO);
	}
}

class Log extends Model {
	protected static string $table = 'new_logs';
	
	// Define standard log levels
	public const INFO    = 'INFO';
	public const WARNING = 'WARNING';
	public const ERROR   = 'ERROR';
	public const DEBUG   = 'DEBUG';
	
	public function add(string $event, string $type = self::INFO): bool {
		global $user;
		
		$type = strtoupper($type);
	
		$sql = "INSERT INTO " . static::$table . " (username, ip, event, type, date_created)
				VALUES (:username, :ip, :event, :type, NOW())";
	
		$params = [
			':username' => $user->getUsername(),
			':ip'       => $this->detectIp(),
			':event'    => $event,
			':type'     => $type,
		];
	
		$stmt = $this->db->query($sql, $params);
		return $stmt !== false;
	}
	
	/**
	 * Retrieve recent log entries.
	 *
	 * @param int $limit Number of entries to return.
	 * @return array
	 */
	public function getRecent(int $limit = 50): array {
		$sql = "SELECT * FROM " . static::$table . "
				ORDER BY date_created DESC
				LIMIT :limit";
	
		// Because PDO doesn’t allow named parameters for LIMIT with emulated prepares off,
		// we’ll use a positional placeholder instead.
		$sql = str_replace(':limit', '?', $sql);
	
		return $this->db->fetchAll($sql, [$limit]);
	}
	
	/**
	 * Detect client IP address.
	 *
	 * @return string
	 */
	private function detectIp(): string {
		return $_SERVER['REMOTE_ADDR'] 
			?? $_SERVER['HTTP_CLIENT_IP'] 
			?? $_SERVER['HTTP_X_FORWARDED_FOR'] 
			?? 'UNKNOWN';
	}
}

class Group extends Model {
	protected static string $table = 'new_groups';
	
	public static function findByOU($ou) {
		global $db;
		
		$query = "SELECT * FROM " . static::$table . " WHERE ou = ?";
		$row = $db->fetch($query, [$ou]);
		
		if ($row && isset($row['id'])) {
			return $row;
		}
	
		return null; // nothing found
	}
}

class CostCentres extends Model {
	protected static string $table = 'new_groups';
	
	public static function findByOU($ou) {
		global $db;
		
		$query = "SELECT * FROM " . static::$table . " WHERE ou = ?";
		$row = $db->fetch($query, [$ou]);
		
		if ($row && isset($row['id'])) {
			return $row;
		}
	
		return null; // nothing found
	}
}

class Settings extends Model {
	protected static string $table = 'new_settings';
	
}

class BudgetYear extends Model {
	private DateTime $today;
	protected static string $table = 'new_groups';
	
	public static function findByOU($ou) {
		global $db;
		
		$query = "SELECT * FROM " . static::$table . " WHERE ou = ?";
		$row = $db->fetch($query, [$ou]);
		
		if ($row && isset($row['id'])) {
			return $row;
		}
	
		return null; // nothing found
	}
	
	
	
	
	
	
	public function __construct(?DateTime $date = null) {
		// If no date supplied, use today
		$this->today = $date ?? new DateTime();
	}
	
	/**
	 * Get the start of the current budget year
	 */
	public function getStart(): DateTime {
		$year = (int)$this->today->format('Y');
	
		// Budget year starts August 1st
		if ($this->today >= new DateTime("$year-08-01")) {
			return new DateTime("$year-08-01");
		} else {
			return new DateTime(($year-1) . "-08-01");
		}
	}
	
	/**
	 * Get the end of the current budget year
	 */
	public function getEnd(): DateTime {
		$start = $this->getStart();
		$end = clone $start;
		$end->modify('+1 year')->modify('-1 day'); // July 31st next year
		return $end;
	}
	
	/**
	 * Get start and end dates formatted for SQL (YYYY-MM-DD)
	 */
	public function getSqlRange(): array {
		return [
			'start' => $this->getStart()->format('Y-m-d'),
			'end'   => $this->getEnd()->format('Y-m-d'),
		];
	}
}
