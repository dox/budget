<?php
class supplier extends class_suppliers {
	function __construct($supplierName = null) {
    global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE name = '" . $supplierName . "'";

		$supplier = $db->query($sql)->fetchArray();

		if (isset($supplier['uid'])) {
			foreach ($supplier AS $key => $value) {
				$this->$key = $value;
			}
		} else {
			$this->name = $supplierName;
		}
  }

	public function updateOrInsert($array = null) {
    global $db, $log;

		foreach ($array AS $updateItem => $value) {
			if ($updateItem != 'uid') {
				if ($value == '') {
					$sqlUpdate[] = $updateItem ." = NULL ";
				} else {
					$value = str_replace("'", "\'", $value);
					$sqlUpdate[] = $updateItem ." = '" . $value . "' ";
				}
			}
		}

		if (isset($this->uid)) {
			// supplier already esists, update it
			$sql  = "UPDATE " . self::$table_name;
	    $sql .= " SET " . implode(", ", $sqlUpdate);
	    $sql .= " WHERE uid = '" . $this->uid . "' ";
	    $sql .= " LIMIT 1";

			$log->insert("supplier", "Supplier " . $this->uid . " updated with values [" . implode(",", $sqlUpdate) . "]");

		} else {
			// supplier needs creating
			$sql  = "INSERT INTO " . self::$table_name;
	    $sql .= " SET " . implode(", ", $sqlUpdate);

			$log->insert("supplier", "Supplier created with values [" . implode(",", $sqlUpdate) . "]");
		}

    $update = $db->query($sql);

		$log->insert("update", $logMessage);

    return $update;
  }
} //end CLASS
?>
