<?php
class supplier extends class_suppliers {
	function __construct($supplierName = null) {
    global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE name = '" . $supplierName . "'";

		$supplier = $db->query($sql)->fetchArray();

		foreach ($supplier AS $key => $value) {
			$this->$key = $value;
		}
  }
} //end CLASS
?>
