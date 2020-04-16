<?php
class class_navbar_messages {

public function getOne() {
	global $db;
	
	$message = $db->where("uid", $this->uid);
	$message = $db->getOne("navbar_messages");
	
	return $message;
}

public function all() {
	global $db;
	
	//$messages = $db->where("department", '2');
	$messages = $db->orderBy('date', "DESC");
	$messages = $db->get("navbar_messages");
	
	return $messages;
}

public function create($user = null, $type = null, $message = null, $persistent = null) {
	global $db;
	
	$data =	Array (
		'user' => $user,
		'type' => $type,
		'message' => $message,
		'persistent' => $persistent
		);
	
	$id = $db->insert ('navbar_messages', $data);
}

} //end CLASS
?>