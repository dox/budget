<?php
class class_users {
	protected static $table_name = "users";
	public $uid;
	public $username;
	public $type;
	public $department;
	public $firstname;
	public $lastname;
	public $email;

	public function all() {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " ORDER BY username";

		$users = $db->query($sql)->fetchAll();

		return $users;
	}
} //end CLASS

class user extends class_users {
	function __construct($userUIDorUsername = null) {
    global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE uid = '" . $userUIDorUsername . "'";
		$sql .= " OR USERNAME = '" . $userUIDorUsername . "'";

		$user = $db->query($sql)->fetchArray();

		foreach ($user AS $key => $value) {
			$this->$key = $value;
		}
  }
} //end CLASS
?>
