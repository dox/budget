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

public function all($date = null, $costCentre = null, $supplier = null, $search = null) {
	global $db;

	$sql  = "SELECT
				orders.uid,
				orders.date,
				orders.cost_centre,
				orders.po,
				orders.order_num,
				orders.name,
				orders.username,
				orders.value,
				orders.supplier,
				orders.description,
				orders.paid,
				cost_centres.code,
				cost_centres.department
			FROM orders, cost_centres
			WHERE orders.cost_centre = cost_centres.uid
			AND (orders.date BETWEEN '" . BUDGET_STARTDATE . "' AND '" . BUDGET_ENDDATE . "')";


			if (isset($date)) {
				$sql .= " AND YEAR(orders.date) = '" . date('Y',strtotime($date)) . "'
				AND MONTH(orders.date) = '" . date('m',strtotime($date)) . "' ";
			}

			if (isset($costCentre)) {
				$sql .= " AND orders.cost_centre = '" . $costCentre . "' ";
			}

			if (isset($supplier)) {
				$sql .= " AND orders.supplier = '" . $supplier . "' ";
			}

			if (isset($search)) {
				$sql .= " AND (
					orders.name LIKE '%" . $search . "%' OR
					orders.supplier LIKE '%" . $search . "%' OR
					orders.order_num LIKE '%" . $search . "%' OR
					orders.po LIKE '%" . $search . "%' OR
					orders.description LIKE '%" . $search . "%') ";
			}
	$sql .=" AND cost_centres.department = '" . $_SESSION['department'] . "'
			ORDER BY orders.date DESC, orders.po DESC;";
	$orders = $db->rawQuery($sql);

	return $orders;
}

public function ordersTotalValueByMonth($date = null) {
	global $db;

	$orders = $this->all($date);

	$totalValue = 0;
	foreach ($orders AS $order) {
		$totalValue = $totalValue + $order['value'];
	}

	return $totalValue;
}

public function ordersTotalValueByYear($year = null) {
	global $db;

	$sql  = "SELECT
				orders.uid,
				orders.date,
				orders.cost_centre,
				orders.po,
				orders.order_num,
				orders.name,
				orders.username,
				orders.value,
				orders.supplier,
				orders.description,
				orders.paid,
				cost_centres.code,
				cost_centres.department
			FROM orders, cost_centres
			WHERE orders.cost_centre = cost_centres.uid";

	$sql .= " AND YEAR(orders.date) = '" . date('Y',strtotime($year)) . "'";

	$sql .=" AND cost_centres.department = '" . $_SESSION['department'] . "'
			ORDER BY orders.date DESC, orders.po DESC;";

	$orders = $db->rawQuery($sql);

	$totalValue = 0;
	foreach ($orders AS $order) {
		$totalValue = $totalValue + $order['value'];
	}

	return $totalValue;
}

public function all_previous_years($search = null) {
	global $db;

	$sql  = "SELECT
				orders.uid,
				orders.date,
				orders.cost_centre,
				orders.po,
				orders.order_num,
				orders.name,
				orders.username,
				orders.value,
				orders.supplier,
				orders.description,
				orders.paid,
				cost_centres.code,
				cost_centres.department
			FROM orders, cost_centres
			WHERE orders.cost_centre = cost_centres.uid
			AND orders.date < '" . BUDGET_STARTDATE . "' ";

			if (isset($search)) {
				$sql .= " AND (
					orders.name LIKE '%" . $search . "%' OR
					orders.supplier LIKE '%" . $search . "%' OR
					orders.order_num LIKE '%" . $search . "%' OR
					orders.po LIKE '%" . $search . "%' OR
					orders.description LIKE '%" . $search . "%') ";
			}
	$sql .=" AND cost_centres.department = '" . $_SESSION['department'] . "'
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

public function table($orders = null) {
	$output = "";
	$output .=	"<table class=\"table bg-white\">";
	$output .=		"<thead>";
	$output .=			"<tr>";
	$output .=				"<th scope=\"col\" style=\"width: 120px;\">Date</th>";
	$output .=				"<th scope=\"col\" style=\"width: 120px;\">PO</th>";
	$output .=				"<th scope=\"col\" style=\"width: 120px;\">Cost Centre</th>";
	$output .=				"<th scope=\"col\">Item</th>";
	$output .=				"<th scope=\"col\">Supplier</th>";
	$output .=				"<th scope=\"col\" style=\"width: 110px;\">Value</th>";
	$output .=			"</tr>";
	$output .=		"</thead>";
	$output .=		"<tbody>";

	foreach ($orders AS $order) {
		$cost_centre_class = new class_cost_centres;
		$cost_centre = $cost_centre_class->getOne($order['cost_centre']);

		$uploads_class = new class_uploads;
		$uploads = $uploads_class->all($order['uid']);

		if (isset($order['paid'])) {
			$class = "table-active";
		} else {
			$class = "";
		}

		if (!empty($uploads)) {
			$uploadsOutput = " <i class=\"fas text-muted fa-paperclip\"></i>";
		} else {
			$uploadsOutput = "";
		}

		$output .= "<tr class=\"" . $class . "\">";
		$output .= "<td scope=\"row\">" . date('Y-m-d', strtotime($order['date'])) . "</td>";
		$output .= "<td><a href=\"index.php?n=orders_unique&uid=" . $order['uid'] . "\">" . $order['po'] . $uploadsOutput . "</a></td>";
		$output .= "<td><i class=\"fas fa-coins\" style=\"color: " . $cost_centre['colour'] . ";\"></i> <a href=\"index.php?n=costcentres_unique&uid=" . $cost_centre['uid'] . "\">" . $cost_centre['code'] . "</a></td>";
		$output .= "<td>" . $order['name'] . "</td>";

		$supplierURL = "index.php?n=suppliers_unique&name=" . urlencode($order['supplier']);
		$output .= "<td><a href=\"" . $supplierURL . "\">" . $order['supplier'] . "</a></td>";

		if ($order['value'] < 0) {
			$output .= "<td class=\"text-right color-green\">£" . number_format($order['value']) . " <i class=\"fas fa-long-arrow-alt-left fa-sm\"></i></td>";
		} else {
			$output .= "<td class=\"text-right color-red\">£" . number_format($order['value']) . " <i class=\"fas fa-long-arrow-alt-right fa-sm\"></i></td>";
		}
		$output .= "</tr>";
	}

	$output .=	"</tbody>";
	$output .= "</table>";

	return $output;
}
} //end CLASS
?>
