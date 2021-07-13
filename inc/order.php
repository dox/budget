<?php
class order extends class_orders {
	function __construct($orderUID = null) {
    global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $orderUID . "'";

		$order = $db->query($sql)->fetchArray();

		foreach ($order AS $key => $value) {
			$this->$key = $value;
		}
  }

	public function create($array = null) {
    global $db;

    $sql  = "INSERT INTO " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      $sqlColumns[] = $updateItem;
      $sqlValues[] = "'" . $value . "' ";
    }

    $sql .= " (" . implode(",", $sqlColumns) . ") ";
    $sql .= " VALUES (" . implode(",", $sqlValues) . ")";

    $create = $db->query($sql);

    //$logArray['category'] = "booking";
    //$logArray['result'] = "success";
    //$logArray['description'] = "[bookingUID:" . $create->lastInsertID() . "] made for " . $_SESSION['username'] . " for [mealUID:" . $array['meal_uid'] . "]";
    //$logsClass->create($logArray);

    return $create;
  }

	public function update($array = null) {
    global $db;

    $sql  = "UPDATE " . self::$table_name;

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

    $sql .= " SET " . implode(", ", $sqlUpdate);
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";
		
    $update = $db->query($sql);

		$log = new class_logs;
		$log->insert("update", "Order updated with values");

    return $update;
  }

	public function markAsPaid() {
		global $db;

		$data = Array (
			"paid" => date('Y-m-d H:i:s')
		);

		$this->update($data);
	}

	public function markAsUnpaid() {
		global $db;

		$data = Array (
			"paid" => ''
		);

		$this->update($data);
	}
}

?>
