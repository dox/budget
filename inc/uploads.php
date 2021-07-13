<?php
class class_uploads {
	protected static $table_name = "uploads";
	public $uid;
	public $date_uploaded;
	public $order_uid;
	public $name;
	public $path;

	public function getOne($uid) {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE uid = '" . $uid . "'";

		$upload = $db->query($sql)->fetchArray();

		return $upload;
	}

	public function allByOrder($orderUID) {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE order_uid = '" . $orderUID . "'";
		$sql .= " ORDER BY date_upload DESC";

		$uploads = $db->query($sql)->fetchAll();

		return $uploads;
	}






	

public function insert($data = null) {
	global $db;

	$id = $db->insert('uploads', $data);
}

public function delete($uid = null) {
	global $db;

	$upload = self::getOne($uid);

	$target_file = UPLOAD_DIR . $upload['path'];
	unlink($target_file);

	$db->where ('uid', $uid);
	$id = $db->delete('uploads');

	$log = new class_logs;
	$log->insert("file", "Deleted file '" . $upload['name'] . "' for order " . $upload['order_uid']);
}
} //end CLASS

?>
