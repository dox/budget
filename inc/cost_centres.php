<?php
class class_cost_centres {
	protected static $table_name = "cost_centres";
	public $uid;
	public $code;
	public $department;
	public $grouping;
	public $name;
	public $description;
	public $colour;
	public $value;

	public function groups() {
		global $db;

		$sql  = "SELECT grouping FROM " . self::$table_name;
		$sql .= " WHERE department = '" . $_SESSION['department'] . "'";
		$sql .= " GROUP BY grouping";

		$groups = $db->query($sql)->fetchAll();

		return $groups;
	}

	public function all() {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE department = '" . $_SESSION['department'] . "'";
		$sql .= " ORDER BY name";

		$costCentres = $db->query($sql)->fetchAll();

		return $costCentres;
	}
} //end CLASS
?>
