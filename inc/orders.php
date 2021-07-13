<?php
class class_orders {
	protected static $table_name = "orders";
	public $uid;
	public $username;
	public $date;
	public $cost_centre;
	public $po;
	public $order_num;
	public $name;
	public $value;
	public $supplier;
	public $description;
	public $paid;

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

		$orders = $db->query($sql)->fetchAll();

		return $orders;
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
		$orders = $db->query($sql)->fetchAll();

		return $orders;
	}

	public function nextOrderNumber() {
		global $db;

		$sql  = "SELECT orders.po FROM orders ";
		$sql .= "INNER JOIN cost_centres ";
		$sql .= "ON orders.cost_centre = cost_centres.uid ";
		$sql .= "WHERE cost_centres.department = '" . $_SESSION['department'] . "' ";
		$sql .= "ORDER BY orders.uid DESC ";
		$sql .= "LIMIT 1";

		$lastOrder = $db->query($sql)->fetchArray();

		$departmentObject = new department($_SESSION['department']);

		$strLen = strlen($department['po_code']);

		if (!$lastOrder) {
			// We get here if there is no order at all
			// If there is no number set it to 0, which will be 1 at the end.

			$number = 0;
		} else {
			$number = substr($lastOrder['po'], 3);

	    // If we have ORD000001 in the database then we only want the number
	    // So the substr returns this 000001

	    // Add the string in front and higher up the number.
	    // the %05d part makes sure that there are always 5 numbers in the string.
	    // so it adds the missing zero's when needed.
		}
	    return $departmentObject->po_code . sprintf('%05d', intval($number) + 1);
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

	$orders = $db->query($sql)->fetchArray();

	$totalValue = 0;
	foreach ($orders AS $order) {
		$totalValue = $totalValue + $order['value'];
	}

	return $totalValue;
}



public function all_previous_years_by_supplier($supplier = null) {
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
			AND orders.date < '" . budgetStartDate() . "'
			AND orders.supplier = '" . $supplier . "'
			AND cost_centres.department = '" . $_SESSION['department'] . "'
			ORDER BY orders.date DESC, orders.po DESC;";

	$orders = $db->query($sql)->fetchArray();

	return $orders;
}





public function table($orders = null) {
	$output  = "<table class=\"table bg-white\">";
	$output .= "<thead>";
	$output .= "<tr>";
	$output .= "<th scope=\"col\" style=\"width: 140px;\">Code</th>";
	$output .= "<th scope=\"col\" style=\"width: 120px;\">Date</th>";
	$output .= "<th scope=\"col\" style=\"width: 100px;\">Supplier</th>";
	$output .= "<th scope=\"col\">PO</th>";
	$output .= "<th scope=\"col\" class=\"text-end\" style=\"width: 120px;\">Value</th>";
	$output .= "</tr>";
	$output .= "</thead>";

	foreach ($orders AS $order) {
		if (isset($order['paid'])) {
			$trClass = "table-secondary";
		} else {
			$trClass = "";
		}
		$orderDateAge = date('U', strtotime($order['date'])) - date('U', strtotime('60 seconds ago'));

		$cost_centre = new cost_centre($order['cost_centre']);

		$uploads_class = new class_uploads;
		$uploads = $uploads_class->allByOrder($order['uid']);

		if ($orderDateAge > -10) {
			$class = "list-group-item-success";
		} else {
			if (isset($order['paid'])) {
				$class = "list-group-item-secondary";
			} else {
				$class = "";
			}
		}

		$orderName = "<strong>" . $order['po'] . "</strong>: " . $order['name'];
		$orderURL = "index.php?n=orders_unique&uid=" . $order['uid'];
		if (!empty($order['description'])) {
			$orderName = $orderName . "<br /><span class=\"text-muted\">" . $order['description'] . "</span>";
		}
		if (!empty($uploads)) {
			$orderName = $orderName . " <svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#paperclip\"/></svg>";
		}

		$output .= "<tr class=\"" . $trClass . "\" onclick=\"window.location='" . $orderURL . "';\">";
		$output .= "<td><a href=\"index.php?n=costcentres_unique&uid=" . $cost_centre->uid . "\"><svg class=\"me-2\" width=\"16\" height=\"16\" style=\"color: " . $cost_centre->colour . ";\"><use xlink:href=\"img/icons.svg#archive-fill\"/></svg> ". $cost_centre->code . "</a></td>";
		$output .= "<td>" . date('Y-m-d', strtotime($order['date'])) . "</td>";
		$output .= "<td><a href=\"index.php?n=suppliers_unique&name=" . urlencode($order['supplier']) . "\">" . $order['supplier'] . "</a></td>";
		$output .= "<td>" . $orderName . "</td>";
		if ($order['value'] < 0) {
			$output .= "<td class=\"text-end colour-green\">£" . number_format($order['value']) . " <svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#arrow-left-short\"/></svg></td>";
		} else {
			$output .= "<td class=\"text-end colour-red\">£" . number_format($order['value'], 2) . " <svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#arrow-right-short\"/></svg></td>";
		}

		$output .= "</tr>";
	}

	$output .=	"</table>";


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
