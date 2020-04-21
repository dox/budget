<?php
$orders_class = new class_orders;
$orders = $orders_class->all();

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

<table class="table bg-white">
	<thead>
		<tr>
			<th scope="col" style="width: 120px;">Date</th>
			<th scope="col" style="width: 120px;">PO</th>
			<th scope="col" style="width: 120px;">Cost Centre</th>
			<th scope="col">Item</th>
			<th scope="col">Supplier</th>
			<th scope="col" style="width: 100px;">Value</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($orders AS $order) {
			if ($order['supplier'] == $_GET['name']) {
				
			
			$cost_centre_class = new class_cost_centres;
			$cost_centre = $cost_centre_class->getOne($order['cost_centre']);
			
			$output  = "<tr>";
			$output .= "<th scope=\"row\">" . date('Y-m-d', strtotime($order['date'])) . "</th>";
			$output .= "<th><a href=\"index.php?n=orders_unique&uid=" . $order['uid'] . "\">" . $order['po'] . "</a></th>";
			$output .= "<td><i class=\"fas fa-coins\" style=\"color: " . $cost_centre['colour'] . ";\"></i> <a href=\"index.php?n=costcentres_unique&uid=" . $cost_centre['uid'] . "\">" . $cost_centre['code'] . "</a></td>";
			$output .= "<td>" . $order['name'] . "</td>";
			$output .= "<td>" . $order['supplier'] . "</td>";
			$output .= "<td class=\"text-right color-red\">£" . number_format($order['value']) . " <i class=\"fas fa-long-arrow-alt-right fa-sm\"></i></td>";
			$output .= "</tr>";
			
			echo $output;
			}
			
		}
		?>
		
	</tbody>
</table>