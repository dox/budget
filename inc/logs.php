<?php
class class_logs {
	protected static $table_name = "logs";
	public $uid;
	public $date;
	public $type;
	public $description;
	public $ip;
	public $username;

	public function all() {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " ORDER BY date DESC";

		$logs = $db->query($sql)->fetchAll();

		return $logs;
	}

	public function insert($type = null, $description = null) {
    global $db;

		$array['username'] = $_SESSION['username'];
		$array['date'] = date('Y-m-d H:i:s');
		$array['ip'] = $_SERVER['REMOTE_ADDR'];
		$array['type'] = $type;
		$array['description'] = $description;

    $sql  = "INSERT INTO " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      $sqlColumns[] = $updateItem;
      $sqlValues[] = "'" . $value . "' ";
    }

    $sql .= " (" . implode(",", $sqlColumns) . ") ";
    $sql .= " VALUES (" . implode(",", $sqlValues) . ")";

    $create = $db->query($sql);

    return $create;
  }

	public function purge($daysToKeep = 365) {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE type = 'purge' ";
		$sql .= "AND DATE(date) = '" . date('Y-m-d') . "' ";
		$sql .= "LIMIT 1";

		$lastPurge = $db->query($sql)->fetchArray();

		if (isset($lastPurge['uid'])) {
			// logs already purged today
		} else {
			$sql  = "DELETE FROM " . self::$table_name . " ";
			$sql .= "WHERE UNIX_TIMESTAMP(date) < " . strtotime('-' . $daysToKeep . ' days');

			$delete = $db->query($sql);

			$log = new class_logs;
			$log->insert("purge", $sql);
		}
	}
} //end CLASS

$log = new class_logs;

?>
