<?php
class class_departments {

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