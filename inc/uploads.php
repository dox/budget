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

	public function allByOrder($orderUID = null) {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE order_uid = '" . $orderUID . "'";
		$sql .= " ORDER BY date_upload DESC";

		$uploads = $db->query($sql)->fetchAll();

		return $uploads;
	}

	public function insert($array = null) {
    global $db;

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

	public function delete($uploadUID = null) {
		global $db;

		// delete the file
		$existingUpload = self::getOne($uid);
		$target_file = UPLOAD_DIR . $existingUpload['path'];
		unlink($target_file);

		// delete record from database
		$sql  = "DELETE FROM " . self::$table_name . " ";
		$sql .= "WHERE uid = '" . $uploadUID . "' ";
		$sql .= "LIMIT 1";

		$delete = $db->query($sql);

		// log this!
		$log = new class_logs;
		$log->insert("file", "Deleted file '" . $existingUpload['name'] . "' for order " . $existingUpload['order_uid']);
	}
} //end CLASS

?>
