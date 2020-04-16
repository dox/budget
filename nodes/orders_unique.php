<?php
$orders_class = new class_orders;

if (isset($_GET['paid'])) {
	if ($_GET['paid'] == "false") {
		$logMessage = "Marked invoice '" . $_GET['uid'] . "' as unpaid";
		$data = Array (
			'paid' => null
		);
	} elseif ($_GET['paid'] == "true") {
		$logMessage = "Marked invoice '" . $_GET['uid'] . "' as paid";
		$data = Array (
			'paid' => date('Y-m-d H:i:s')
		);
	}
	
$db->where ('uid', $_GET['uid']);
$db->update ('orders', $data);


$log = new class_logs;
$log->insert("update", $logMessage);
}

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
	
	$orders_class->update($_GET['uid'], $data);
	
	$log = new class_logs;
	$log->insert("update", $db->getLastQuery());
	
	$navBarMessagesClass = new class_navbar_messages;
	$navBarMessage = $navBarMessagesClass->create("1","success","Order '" . $_GET['uid'] . "' updated","0");
}

$order = $orders_class->getOne($_GET['uid']);

$users_class = new class_users;
$user = $users_class->getOne($order['username']);

$cost_centre_class = new class_cost_centres;
$cost_centre = $cost_centre_class->getOne($order['cost_centre']);
?>
<h1 class="text-right">Purchase Order</h1>
<h2 class="text-right">Date: <?php echo $order['date']; ?></h2>
<h2 class="text-right">PO: <?php echo $order['po']; ?></h2>
<h2 class="text-right">Supplier Order #: <?php echo $order['order_num']; ?></h2>
<h2 class="text-right">Cost Centre: <?php echo $cost_centre['code'] . " - " . $cost_centre['name']; ?></h2>
<h2 class="text-right">Order Created By: <?php echo $user['firstname'] . " " . $user['lastname']; ?></h2>

<?php
if (!isset($order['paid']) || empty($order['paid'])) {
	$output  = "<p class=\"text-right\">";
	$output .= "<a href=\"index.php?n=orders_unique&uid=" . $order['uid'] . "&paid=true\" class=\"btn btn-sm btn-success\">Mark as Paid</a> ";
	$output .= "<a href=\"index.php?n=orders_edit&uid=" . $order['uid'] . "\" class=\"btn btn-sm btn-secondary\">Edit Order</a></p>";
} else {
	$datePaid = date('Y-m-d', strtotime($order['paid']));
	$output  = "<p class=\"text-right\">";
	$output .= "<a href=\"index.php?n=orders_unique&uid=" . $order['uid'] . "&paid=false\" class=\"btn btn-sm btn-secondary\">Paid on " . $datePaid . " - Mark as Unpaid</a> ";
	$output .= "<a href=\"index.php?n=orders_edit&uid=" . $order['uid'] . "\" class=\"btn btn-sm btn-secondary\">Edit Order</a></p>";
}

echo $output;
?>
</p>

<h2>Supplier: </h2>
<h3><?php echo $order['supplier']; ?></h3>


<table class="table bg-white">
	<thead>
		<tr>
			<th scope="col" style="width: 120px;">Date</th>
			<th scope="col">Item</th>
			<th scope="col" style="width: 100px;">Value</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo date('Y-m-d', strtotime($order['date']));?></td>
			<td><?php echo $order['name'];
				if (isset($order['description'])) {
					echo "<br /><span class=\"text-muted\">" . $order['description'] . "</span>";
				}
				?>
			</td>
			<td>£<?php echo number_format($order['value'],2);?></td>
		</tr>
	</tbody>
</table>