<?php
class class_orders {

	public function getOne($uid = null) {
		global $db;

		$order = $db->where("uid", $uid);
		$order = $db->getOne("orders");

		return $order;
	}

	public function recentSuppliers() {
		global $db;

		$sql  = "SELECT
					orders.date,
					orders.cost_centre,
					orders.supplier,
					cost_centres.code,
					cost_centres.department
				FROM orders, cost_centres
				WHERE orders.cost_centre = cost_centres.uid
				AND orders.date BETWEEN CURDATE() - INTERVAL 1 YEAR AND CURDATE()";

		$sql .=" AND cost_centres.department = '" . $_SESSION['department'] . "'
				ORDER BY orders.date DESC, orders.po DESC;";

		$orders = $db->rawQuery($sql);

		return $orders;
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
			AND (orders.date BETWEEN '" . budgetStartDate() . "' AND '" . budgetEndDate() . "')";

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

public function ordersTotalValueByCostCentreAndMonth($date = null, $costCentre = null) {
	global $db;

	$orders = $this->all($date, $costCentre);

	$totalValue = 0;
	foreach ($orders AS $order) {
		$totalValue = $totalValue + $order['value'];
	}

	return $totalValue;
}



public function ordersTotalValueByYear($date = null) {
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
			AND (orders.date BETWEEN '" . budgetStartDate($date) . "' AND '" . budgetEndDate($date) . "')
			AND cost_centres.department = '" . $_SESSION['department'] . "'
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
			AND orders.date < '" . budgetStartDate() . "' ";

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
	$output .=	"<table class=\"table \">";
	$output .=		"<thead>";
	$output .=			"<tr>";
	$output .=				"<th scope=\"col\" style=\"width: 115px;\">Date</th>";
	$output .=				"<th scope=\"col\" style=\"width: 115px;\">PO</th>";
	$output .=				"<th scope=\"col\" style=\"width: 120px;\">Cost Centre</th>";
	$output .=				"<th scope=\"col\">Item</th>";
	$output .=				"<th scope=\"col\">Supplier</th>";
	$output .=				"<th scope=\"col\" style=\"width: 120px;\">Value</th>";
	$output .=			"</tr>";
	$output .=		"</thead>";
	$output .=		"<tbody>";

	foreach ($orders AS $order) {
		$orderDateAge = date('U', strtotime($order['date'])) - date('U', strtotime('60 seconds ago'));

		$cost_centre_class = new class_cost_centres;
		$cost_centre = $cost_centre_class->getOne($order['cost_centre']);

		$uploads_class = new class_uploads;
		$uploads = $uploads_class->all($order['uid']);

		if ($orderDateAge > -10) {
			$class = "table-success";
		} else {
			if (isset($order['paid'])) {
				$class = "table-secondary";
			} else {
				$class = "";
			}
		}

		if (!empty($uploads)) {
			$uploadsOutput = " <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-paperclip\" viewBox=\"0 0 16 16\"><path d=\"M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0V3z\"/></svg>";
		} else {
			$uploadsOutput = "";
		}

		$output .= "<tr class=\"" . $class . "\">";
		$output .= "<td scope=\"row\">" . date('Y-m-d', strtotime($order['date'])) . "</td>";
		$output .= "<td><a href=\"index.php?n=orders_unique&uid=" . $order['uid'] . "\">" . $order['po'] . $uploadsOutput . "</a></td>";
		$output .= "<td style=\"color: " . $cost_centre['colour'] . ";\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-archive-fill\" viewBox=\"0 0 16 16\">
		  <path d=\"M12.643 15C13.979 15 15 13.845 15 12.5V5H1v7.5C1 13.845 2.021 15 3.357 15h9.286zM5.5 7h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1zM.8 1a.8.8 0 0 0-.8.8V3a.8.8 0 0 0 .8.8h14.4A.8.8 0 0 0 16 3V1.8a.8.8 0 0 0-.8-.8H.8z\"/>
		</svg> <a href=\"index.php?n=costcentres_unique&uid=" . $cost_centre['uid'] . "\">" . $cost_centre['code'] . "</a></td>";
		$output .= "<td>" . $order['name'] . "</td>";

		$supplierURL = "index.php?n=suppliers_unique&name=" . urlencode($order['supplier']);
		$output .= "<td><a href=\"" . $supplierURL . "\">" . $order['supplier'] . "</a></td>";

		if ($order['value'] < 0) {
			$output .= "<td class=\"text-right color-green\">£" . number_format($order['value'], 2) . " <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-arrow-left-short\" viewBox=\"0 0 16 16\">
			  <path fill-rule=\"evenodd\" d=\"M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z\"/>
			</svg></td>";
		} else {
			$output .= "<td class=\"text-right color-red\">£" . number_format($order['value'], 2) . " <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-arrow-right-short\" viewBox=\"0 0 16 16\">
			  <path fill-rule=\"evenodd\" d=\"M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z\"/>
			</svg></td>";
		}
		$output .= "</tr>";
	}

	$output .=	"</tbody>";
	$output .= "</table>";

	return $output;
}
} //end CLASS

function budgetStartDate($date = null) {
	if ($date == null) {
		$date = date('Y-m-d');
	}

	if (date('m-d', strtotime($date)) >= '01-01' && date('m-d', strtotime($date)) < BUDGET_STARTDATE) {
		$dateFrom = date('Y', strtotime($date))-1 . "-" . BUDGET_STARTDATE;
	} else {
		$dateFrom = date('Y', strtotime($date)) . "-" . BUDGET_STARTDATE;
	}

	return $dateFrom;
}



function budgetEndDate($date = null) {
	if ($date == null) {
		$date = date('Y-m-d');
	}

	if (date('m-d', strtotime($date)) >= '01-01' && date('m-d', strtotime($date)) <= BUDGET_ENDDATE) {
		$dateTo = date('Y', strtotime($date)) . "-" . BUDGET_ENDDATE;
	} else {
		$dateTo = date('Y', strtotime($date))+1 . "-" . BUDGET_ENDDATE;
	}

	return $dateTo;
}

?>
