<?php
class class_users {

public function getOne($usernameOrUID = null) {
	global $db;

	$user = $db->where("username", $usernameOrUID);
	$user = $db->orWhere ("uid", $usernameOrUID);
	$user = $db->getOne("users");
	
	return $user;
}

public function all() {
	global $db;

	$users = $db->orderBy('username', "DESC");
	$users = $db->get("users");

	return $meters;
}
} //end CLASS
?>
