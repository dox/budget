<?php
class class_cost_centres {
	protected static $table_name = "cost_centres";
	public $uid;
	public $code;
	public $department;
	public $group_name;
	public $name;
	public $description;
	public $colour;
	public $value;

	public function groups() {
		global $db;

		$sql  = "SELECT group_name FROM " . self::$table_name;
		$sql .= " WHERE department = '" . $_SESSION['department'] . "'";
		$sql .= " GROUP BY group_name";
		
		echo $sql;

		$groups = $db->query($sql)->fetchAll();

		return $groups;
	}

	public function all() {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE department = '" . $_SESSION['department'] . "'";
		$sql .= " ORDER BY name";

		$costCentres = $db->query($sql)->fetchAll();

		return $costCentres;
	}

	public function summaryTable($date = null) {
		if ($date == null) {
			$date = date('Y-m-d');
		}

		$ordersThisMonth = class_orders::all($date);

		foreach ($ordersThisMonth AS $order) {
			$costCentreSpendByMonth[$order['cost_centre']] = $costCentreSpendByMonth[$order['cost_centre']] + $order['value'];
		}

		$output  = "<table class=\"table bg-white\">";
		$output .= "<thead>";
		$output .= "<tr>";
		$output .= "<th scope=\"col\" style=\"width: 140px;\">Code</th>";
		$output .= "<th scope=\"col\">Name</th>";
		$output .= "<th scope=\"col\" class=\"text-end\" style=\"width: 120px;\">Value</th>";
		$output .= "</tr>";
		$output .= "</thead>";

		foreach ($costCentreSpendByMonth AS $costCentre => $value) {
			$costCentreObject = new cost_centre($costCentre);

			$costCentreURL = "index.php?n=costcentres_unique&uid=" . $costCentreObject->uid;

			$output .= "<tr onclick=\"window.location='" . $costCentreURL . "';\">";
			$output .= "<td><a href=\"index.php?n=costcentres_unique&uid=" . $costCentreObject->uid . "\"><svg class=\"me-2\" width=\"16\" height=\"16\" style=\"color: " . $costCentreObject->colour . ";\"><use xlink:href=\"img/icons.svg#archive-fill\"/></svg> ". $costCentreObject->code . "</a></td>";
			$output .= "<td>" . $costCentreObject->name . "</td>";
			if ($value < 0) {
				$output .= "<td class=\"text-end colour-green\">£" . number_format($value, 2) . " <svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#arrow-left-short\"/></svg></td>";
			} else {
				$output .= "<td class=\"text-end colour-red\">£" . number_format($value, 2) . " <svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#arrow-right-short\"/></svg></td>";
			}

			$output .= "</tr>";
		}

		$output .=	"</table>";

		return $output;
	}
} //end CLASS
?>
