<?php
class class_orders {

public function getOne($uid = null) {
	global $db;

	$order = $db->where("uid", $uid);
	$order = $db->getOne("orders");

	return $order;
}

public function nextOrderNumber() {
	global $db;
	$departments_class = new class_departments;
	$department = $departments_class->getOne($_SESSION['department']);

	$strLen = strlen($department['po_code']);

	$order = $db->where("LEFT(po," . $strLen . ")", $department['po_code']);
	$order = $db->orderBy("po", "DESC");
	$order = $db->getOne("orders");

    if ( ! $order )
        // We get here if there is no order at all
        // If there is no number set it to 0, which will be 1 at the end.

        $number = 0;
    else
        $number = substr($order['po'], 3);

    // If we have ORD000001 in the database then we only want the number
    // So the substr returns this 000001

    // Add the string in front and higher up the number.
    // the %05d part makes sure that there are always 5 numbers in the string.
    // so it adds the missing zero's when needed.

    return $department['po_code'] . sprintf('%05d', intval($number) + 1);
}

public function all($previousDays = null, $costCentre = null) {
	global $db;

	$sql  = "SELECT
				orders.uid,
				orders.date,
				orders.cost_centre,
				orders.po,
				orders.order_num,
				orders.name,
				orders.value,
				orders.supplier,
				orders.description,
				orders.paid,
				cost_centres.code,
				cost_centres.department
			FROM orders, cost_centres
			WHERE orders.cost_centre = cost_centres.uid
			AND (orders.date BETWEEN '" . BUDGET_STARTDATE . "' AND '" . BUDGET_ENDDATE . "')";

			if (isset($previousDays)) {
				$sql .= " AND orders.date BETWEEN CURDATE() - INTERVAL " . $previousDays . " DAY AND CURDATE() ";
			}

			if (isset($costCentre)) {
				$sql .= " AND orders.cost_centre = '" . $costCentre . "' ";
			}
	$sql .=" AND cost_centres.department = '" . $_SESSION['department'] . "'
			ORDER BY orders.date DESC, orders.po DESC;";
	$orders = $db->rawQuery($sql);

	return $orders;
}

public function allBySearch($searchTerm = null) {
	global $db;

	$sql  = "SELECT
				orders.uid,
				orders.date,
				orders.cost_centre,
				orders.po,
				orders.order_num,
				orders.name,
				orders.value,
				orders.supplier,
				orders.description,
				orders.paid,
				cost_centres.code,
				cost_centres.department
			FROM orders, cost_centres
			WHERE orders.cost_centre = cost_centres.uid
			AND cost_centres.department = '" . $_SESSION['department'] . "'
			AND (orders.date BETWEEN '" . BUDGET_STARTDATE . "' AND '" . BUDGET_ENDDATE . "')
			AND (
				orders.name LIKE '%" . $searchTerm . "%' OR
				orders.supplier LIKE '%" . $searchTerm . "%' OR
				orders.order_num LIKE '%" . $searchTerm . "%' OR
				orders.po LIKE '%" . $searchTerm . "%' OR
				orders.description LIKE '%" . $searchTerm . "%')
			ORDER BY orders.date DESC, orders.po DESC;";
	$orders = $db->rawQuery($sql);

	return $orders;
}

public function allByMonth($date = null) {
	global $db;

	if ($date == null) {
		$date = date('Y-m-d');
	}

	$sql  = "SELECT
				orders.uid,
				orders.date,
				orders.cost_centre,
				orders.po,
				orders.order_num,
				orders.name,
				orders.value,
				orders.supplier,
				orders.paid,
				orders.description,
				cost_centres.code,
				cost_centres.department
			FROM orders, cost_centres
			WHERE orders.cost_centre = cost_centres.uid
			AND YEAR(orders.date) = '" . date('Y',strtotime($date)) . "'
			AND MONTH(orders.date) = '" . date('m',strtotime($date)) . "'
			AND cost_centres.department = '" . $_SESSION['department'] . "'
			ORDER BY orders.date DESC, orders.po DESC;";

	$orders = $db->rawQuery($sql);

	return $orders;
}

public function insert($data = null) {
	global $db;

	$id = $db->escape($db->insert('orders', $data));

	$log = new class_logs;
	$log->insert("create", $db->getLastQuery());

	if (!$id) {
		echo 'Log failed: ' . $db->getLastError();
	}
}

public function update($uid = null, $data = null) {
	global $db;

	$db->where ('uid', $uid);
	$id = $db->escape($db->update('orders', $data));

	$log = new class_logs;
	$log->insert("update", $db->getLastQuery());

	if (!$id) {
		echo 'Log failed: ' . $db->getLastError();
	}
}
} //end CLASS
?>
