<?php
class Log {
	protected $db;
	
	// Define standard log levels
	public const INFO    = 'INFO';
	public const WARNING = 'WARNING';
	public const ERROR   = 'ERROR';
	public const DEBUG   = 'DEBUG';
	
	public function __construct() {
		$this->db = Database::getInstance();
	}

	/**
	 * Record a new log entry.
	 *
	 * @param string $event Description of the event (e.g. "Login attempt", "Order created").
	 * @param string|null $username Optional username (if known).
	 * @param string|null $ip Optional IP address (auto-detected if null).
	 * @param string $level Log level (INFO, WARNING, ERROR, DEBUG)
	 */
	public function add(string $description, string $type = self::INFO): bool {
		global $user;
		
		$level = strtoupper($level);
	
		$sql = "INSERT INTO logs (username, ip, description, type, date)
				VALUES (:username, :ip, :description, :type, NOW())";
	
		$params = [
			':username' => $user->getUsername(),
			':ip'       => $this->detectIp(),
			':description'    => $description,
			':type'    => $type,
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
		$sql = "SELECT * FROM logs
				ORDER BY date DESC
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