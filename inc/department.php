<?php
class department extends class_departments {
	function __construct($costCentreUID = null) {
    global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $costCentreUID . "'";

		$department = $db->query($sql)->fetchArray();

		foreach ($department AS $key => $value) {
			$this->$key = $value;
		}
  }

public function getOne($uid = null) {
	global $db;

	$department = $db->where("uid", $uid);
	$department = $db->getOne("departments");

	return $department;
}

public function all() {
	global $db;

	$departments = $db->orderBy('name', "DESC");
	$departments = $db->get("departments");

	return $departments;
}

} //end CLASS
?>
