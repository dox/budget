<?php
class class_logs {

public function all() {
	global $db;
	
	$meters = $db->orderBy('date', "DESC");
	$meters = $db->get("logs");
	
	return $meters;
}

public function purge($daysToKeep = 365) {
	global $db;
	
	$lastPurge = $db->where("type", "purge");
	$lastPurge = $db->where("DATE(date)", date('Y-m-d'));
	$lastPurge = $db->getOne("logs");
	
	if (empty($lastPurge)) {
		$db->where("UNIX_TIMESTAMP(date) < " . strtotime('-' . $daysToKeep . ' days'));
		$db->delete('logs');
		
		$log = new class_logs;
		$log->insert("purge", $db->getLastQuery());
	} else {
		// logs already purged today
	}
}

public function insert($type = null, $description = null) {
	global $db;
	
	if (isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
	} else {
		$username = null;
	}
	$data = Array (
		"date" => date('Y-m-d H:i:s'),
		"type" => $type,
		"description" => $description,
		"ip" => $_SERVER['REMOTE_ADDR'],
		"username" => $username
	);
	
	$id = $db->insert('logs', $data);
	if (!$id) {
		echo 'Log failed: ' . $db->getLastError();
	}
}	
} //end CLASS

$log = new class_logs;

?>