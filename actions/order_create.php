<?php
require_once('../inc/autoload.php');

$ordersClass = new class_orders;

if (isset($_POST['po'])) {
	if ($_POST['name'] == $_POST['description']) {
		$_POST['description'] = null;
	}
	$data = Array (
		"username" => $_SESSION['localUID'],
		"date" => $_POST['date'],
		"cost_centre" => $_POST['cost_centre'],
		"po" => $_POST['po'],
		"order_num" => $_POST['order_num'],
		"name" => $_POST['name'],
		"value" => $_POST['value'],
		"supplier" => $_POST['supplier'],
		"description" => $_POST['description']
	);

	if ($ordersClass->insert($data)) {
		$message = "New order '" . $_POST['name'] . "' created";
	} else {
		$message = "ERROR";
	}

  echo $message;
}
?>
