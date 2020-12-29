<?php
class class_uploads {

public function getOne($uid = null) {
	global $db;

	$upload = $db->where("uid", $uid);
	$upload = $db->getOne("uploads");

	return $upload;
}

public function all($orderUID = null) {
	global $db;

	$uploads = $db->where('order_uid', $orderUID);
	$uploads = $db->orderBy('date_upload', "DESC");
	$uploads = $db->get("uploads");

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
