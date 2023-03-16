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
    global $db, $log;

		foreach ($array AS $updateItem => $value) {
			$value = str_replace("'", "\'", $value);
			$sqlUpdate[] = $updateItem ." = '" . $value . "' ";
		}

		$sql  = "INSERT INTO " . self::$table_name;
		$sql .= " SET " . implode(", ", $sqlUpdate);

    $create = $db->query($sql);

		$log->insert("order", "Order created with values [" . implode(",", $sqlUpdate) . "]");

    return $create;
  }

	public function update($array = null) {
    global $db, $log;

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

		$log->insert("order", "Order " . $this->uid . " updated with values [" . implode(",", $sqlUpdate) . "]");

    return $update;
  }

	public function markAsPaid() {
		global $db, $log;

		$data = Array (
			"paid" => date('Y-m-d H:i:s')
		);

		$this->update($data);

		$log->insert("order", "Order " . $this->uid . " marked as paid");
	}

	public function markAsUnpaid() {
		global $db, $log;

		$data = Array (
			"paid" => ''
		);

		$this->update($data);

		$log->insert("order", "Order " . $this->uid . " marked as unpaid");
	}

	public function uploads() {
		$uploads = new class_uploads;
		$uploads = $uploads->allByOrder($this->uid);

		return $uploads;
	}
}

?>
