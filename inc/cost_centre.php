<?php
class cost_centre extends class_cost_centres {

	function __construct($costCentreUID = null) {
    global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $costCentreUID . "'";

		$costCentre = $db->query($sql)->fetchArray();

		foreach ($costCentre AS $key => $value) {
			$this->$key = $value;
		}
  }

	public function yearlyOrders() {
		$orders_class = new class_orders;
		$orders = $orders_class->all(null, $this->uid, null, null);

		return $orders;
	}

	public function yearlyBudget() {
		return $this->value;
	}

	public function yearlySpend() {
		$totalSpend = 0;

		foreach ($this->yearlyOrders() AS $order) {
			$totalSpend = $totalSpend + $order['value'];
		}

		return $totalSpend;
	}

	public function yearlyRemaining() {
		$remaining = $this->yearlyBudget() - $this->yearlySpend();

		return $remaining;
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
		$log->insert("update", "Cost centre updated with values");

    return $update;
  }



















public function totalSpendByCostCentre($uid = null) {
	global $db;

	$totalSpend = $db->where("cost_centre", $uid);
	$totalSpend = $db->where('date', Array (budgetStartDate(), budgetEndDate()), 'BETWEEN');
	$totalSpend = $db->getOne ("orders", "sum(value) AS value");

	return $totalSpend['value'];
}





} //end CLASS
?>
