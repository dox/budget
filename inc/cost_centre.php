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
    global $db, $log;

    $sql  = "INSERT INTO " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      $sqlColumns[] = $updateItem;
      $sqlValues[] = "'" . $value . "' ";
    }

    $sql .= " (" . implode(",", $sqlColumns) . ") ";
    $sql .= " VALUES (" . implode(",", $sqlValues) . ")";

    $create = $db->query($sql);

		$log->insert("cost_centre", "Cost centre created with values [" . implode(",", $sqlUpdate) . "]");

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

		$log->insert("cost_centre", "Cost centre " . $this->uid . " updated with values [" . implode(",", $sqlUpdate) . "]");

    return $update;
  }

	public function yearlySpendSummary() {
		$budgetTotal = $this->yearlyBudget();

		$ordersArray["'" . budgetStartDate() . "'"] = $budgetTotal;

		foreach (array_reverse($this->yearlyOrders()) as $order) {
			$budgetTotal = $budgetTotal - $order['value'];

			$ordersArray["'" . $order['date'] . "'"] = $budgetTotal;
		}
		$ordersArray["'" . budgetEndDate() . "'"] = $budgetTotal;

		return $ordersArray;
	}

	public function spendBySupplier() {
		foreach ($this->yearlyOrders() AS $order) {
			$supplierArray[$order['supplier']] = $supplierArray[$order['supplier']] + $order['value'];
		}

		return $supplierArray;
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
