<?php
$orders_class = new class_orders;
$orders = $orders_class->all(null, null, $_GET['name'], null);

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
?>

<h2>Orders from Supplier: <a href="index.php?n=suppliers_edit&name=<?php echo $_GET['name'];?>"><?php echo $_GET['name'];?></a></h2>

<?php
echo $orders_class->table($orders);
?>
