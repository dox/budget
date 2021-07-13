<?php
class class_departments {
	protected static $table_name = "departments";
	public $uid;
	public $name;
	public $po_code;

	public function all() {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " ORDER BY name DESC";

		$departments = $db->query($sql)->fetchAll();

		return $departments;
	}
} //end CLASS
?>
