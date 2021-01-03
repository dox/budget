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
	$output = "<div class=\"list-group card-list-group\">";

	foreach ($orders AS $order) {
		$orderDateAge = date('U', strtotime($order['date'])) - date('U', strtotime('60 seconds ago'));

		$cost_centre_class = new class_cost_centres;
		$cost_centre = $cost_centre_class->getOne($order['cost_centre']);

		$uploads_class = new class_uploads;
		$uploads = $uploads_class->all($order['uid']);

		if ($orderDateAge > -10) {
			$class = "list-group-item-success";
		} else {
			if (isset($order['paid'])) {
				$class = "list-group-item-secondary";
			} else {
				$class = "";
			}
		}

		if (!empty($uploads)) {
			$uploadsOutput = " <svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#paperclip\"/></svg>";
		} else {
			$uploadsOutput = " <svg width=\"16\" height=\"16\" class=\"invisible\"><use xlink:href=\"img/icons.svg#paperclip\"/></svg>";
		}
		/*
				<div class="col-auto lh-1">
					<div class="dropdown">
						<a href="#" class="link-secondary" data-bs-toggle="dropdown"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="5" cy="12" r="1"></circle><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle></svg>
						</a>
						<div class="dropdown-menu dropdown-menu-end">
							<a class="dropdown-item" href="#">
								Action
							</a>
							<a class="dropdown-item" href="#">
								Another action
							</a>
						</div>
					</div>
				</div>
			</div>
		*/
		$output .= "<div class=\"list-group-item " . $class . "\">";
			$output .= "<div class=\"row g-2 align-items-center\">";
				$output .= "<div class=\"col-auto text-h3\">" . date('Y-m-d', strtotime($order['date'])) . "</div>";
				$output .= "<div class=\"col-auto\"><a href=\"index.php?n=costcentres_unique&uid=" . $cost_centre['uid'] . "\"><svg width=\"16\" height=\"16\" style=\"color: " . $cost_centre['colour'] . ";\"><use xlink:href=\"img/icons.svg#archive-fill\"/></svg></a></div>";//$cost_centre['code']
				$output .= "<div class=\"col-auto\">" . $uploadsOutput . "</div>";
				$output .= "<div class=\"col\">";
					$output .= "<strong><a href=\"index.php?n=orders_unique&uid=" . $order['uid'] . "\">" . $order['po'] . "</strong></a> / <a href=\"index.php?n=suppliers_unique&name=" . urlencode($order['supplier']) . "\">" . $order['supplier'] . "</a> " . $order['name'];
					$output .= "<div class=\"text-muted\">";
						$output .= $order['description'];
					$output .= "</div>";
				$output .= "</div>";
				$output .= "<div class=\"col-auto text-muted\">";
					if ($order['value'] < 0) {
						$output .= "<span class=\"text-right colour-green\">£" . number_format($order['value'], 2) . " <svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#arrow-left-short\"/></svg></span>";
					} else {
						$output .= "<span class=\"text-right colour-red\">£" . number_format($order['value'], 2) . " <svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#arrow-right-short\"/></svg></span>";
					}
				$output .= "</div>";
				$output .= "<div class=\"col-auto\">";
					$output .= "<a href=\"index.php?n=orders_edit&uid=" . $order['uid'] . "\" class=\"link-secondary\">";
					//$output .= "<button class=\"switch-icon\" data-bs-toggle=\"switch-icon\">";
					$output .= "<span class=\"switch-icon-a text-muted\">";
					$output .= "<svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#pencil-square\"/></svg>";
					$output .= "</span>";
					$output .= "<span class=\"switch-icon-b text-red\">";
					//$output .= "<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-filled" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M19.5 13.572l-7.5 7.428l-7.5 -7.428m0 0a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572"></path></svg>";
					$output .= "</span>";
					//$output .= "</button>";
					$output .= "</a>";
				$output .= "</div>";

			$output .= "</div>";
		$output .= "</div>";

		/*
		$output .= "<tr class=\"" . $class . "\">";


		$supplierURL = "index.php?n=suppliers_unique&name=" . urlencode($order['supplier']);
		$output .= "<td><a href=\"" . $supplierURL . "\">" . $order['supplier'] . "</a></td>";
		*/
	}

	$output .=	"</div>";

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
