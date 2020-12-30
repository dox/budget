<?php
$orders_class = new class_orders;
$orders = $orders_class->all(null, null, $_GET['name'], null);
$ordersPrevious = $orders_class->all_previous_years_by_supplier($_GET['name']);

$suppliers_class = new class_suppliers;

if (isset($_POST['name'])) {
	$data = Array (
		"name" => $_POST['name'],
		"account_number" => $_POST['account_number'],
		"address" => $_POST['address'],
		"telephone" => $_POST['telephone'],
		"mobile" => $_POST['mobile'],
		"email" => $_POST['email'],
		"website" => $_POST['website']
	);

	$suppliers_class->update($_POST['name'], $data);

	$title = "Supplier Updated";
	$message = "Supplier '" . $_POST['name'] . "' updated";
	echo toast($title, $message);
}

$supplier = $suppliers_class->getOne($_GET['name']);
$supplierURL = "index.php?n=suppliers_edit&name=" . urlencode($_GET['name']);
?>

<h2>Orders from Supplier: <a href="<?php echo $supplierURL;?>"><?php echo $_GET['name'];?></a></h2>

<?php
echo $orders_class->table($orders);
?>

<h2>Orders from Supplier: <a href="<?php echo $supplierURL;?>"><?php echo $_GET['name'];?></a> in previous years</h2>

<?php
echo $orders_class->table($ordersPrevious);
?>
