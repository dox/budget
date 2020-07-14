<?php
$orders_class = new class_orders;
if (isset($_POST['po'])) {
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

	$orders_class->insert($data);

	$title = "Order Created";
	$message = "New order '" . $_POST['name'] . "' created";
	echo toast($title, $message);
}

$orders = $orders_class->all();
?>

<h2>Orders <small class="text-muted"><?php echo "Budget Year: " . budgetStartDate() . " - " . budgetEndDate(); ?></small></h2>

<?php
echo $orders_class->table($orders);
?>
