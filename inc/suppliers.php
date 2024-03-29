<?php
class class_suppliers {
	protected static $table_name = "suppliers";
	public $uid;
	public $name;
	public $po_code;

	public function all() {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " ORDER BY name";

		$suppliers = $db->query($sql)->fetchAll();

		return $costCentres;
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

		$recentSuppliers = $db->query($sql)->fetchAll();

		foreach ($recentSuppliers AS $supplier) {
			$supplierArray[] = $supplier['supplier'];
		}

		$supplierArray = array_unique($supplierArray);

		return $supplierArray;
	}
} //end CLASS
?>
