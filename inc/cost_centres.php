<?php
class class_cost_centres {

public $meterUID;

public function groups() {
	global $db;

	//$groups = $db->where("department", $_SESSION['department']);
	$groups = $db->where("department", $_SESSION['department']);
	$groups = $db->groupBy("grouping");
	$groups = $db->get("cost_centres", null, "grouping");

	foreach ($groups AS $group) {
		$returnArray[] = $group['grouping'];
	}

	return $returnArray;
}

public function totalSpendByCostCentre($uid = null) {
	global $db;

	$totalSpend = $db->where("cost_centre", $uid);
	$totalSpend = $db->where('date', Array (budgetStartDate(), budgetEndDate()), 'BETWEEN');
	$totalSpend = $db->getOne ("orders", "sum(value) AS value");

	return $totalSpend['value'];
}

public function getOne($uid = null) {
	global $db;

	$meter = $db->where("uid", $uid);
	$meter = $db->where("department", $_SESSION['department']);
	$meter = $db->getOne("cost_centres");

	return $meter;
}

public function all() {
	global $db;

	$costcentres = $db->where("department", $_SESSION['department']);
	$costcentres = $db->orderBy('grouping', "ASC");
	$costcentres = $db->orderBy('name', "ASC");
	$costcentres = $db->get("cost_centres");

	return $costcentres;
}

public function insert($data = null) {
	global $db;

	//$data = $db->escape ($data);
	$id = $db->escape($db->insert('cost_centres', $data));

	$log = new class_logs;
	$log->insert("create", $db->getLastQuery());

	if (!$id) {
		echo 'Log failed: ' . $db->getLastError();
	}
}

public function update($uid = null, $data = null) {
	global $db;

	$db->where ('uid', $uid);
	$id = $db->escape($db->update('cost_centres', $data));

	$log = new class_logs;
	$log->insert("update", $db->getLastQuery());

	if (!$id) {
		echo 'Log failed: ' . $db->getLastError();
	}
}
} //end CLASS
?>
