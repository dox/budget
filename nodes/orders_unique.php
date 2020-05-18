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

	$title = "Order Updated";
	$message = "Order '" . $_GET['uid'] . "' updated";
	echo toast($title, $message);
}

$order = $orders_class->getOne($_GET['uid']);

$suppliers_class = new class_suppliers;
$supplier = $suppliers_class->getOne($order['supplier']);

$users_class = new class_users;
$user = $users_class->getOne($order['username']);

$cost_centre_class = new class_cost_centres;
$cost_centre = $cost_centre_class->getOne($order['cost_centre']);

$uploads_class = new class_uploads;
$uploads = $uploads_class->all($order['uid']);

if (!$cost_centre['department'] == $_SESSION['department']) {
	echo "You have tried to access an order that you are not authorised to view";
	exit;
}
?>

<div class="float-left">
	<?php
	foreach ($uploads AS $upload) {
		$output  = "<div><a href=\"uploads/" . $upload['path'] . "\">";
		$output .="<i class=\"fas fa-paperclip\"> " . $upload['name'] . "</i>";
		$output .= "</a>";
		$output .= "<i id=\"" . $upload['uid'] . "\" class=\"fas fa-trash float-right deleteUpload\"></i></div>";

		echo $output;
	}
	?>
	<div id="upload_feedback">&nbsp;</div>
	<div class="custom-file">
		<input type="file" class="custom-file-input" name="fileToUpload" id="fileToUpload">
		<label class="custom-file-label" for="fileToUpload">Upload file</label>
	</div>
</div>

<h1 class="text-right">Purchase Order</h1>
<h2 class="text-right">Date: <?php echo date('Y-m-d H:i', strtotime($order['date'])); ?></h2>
<h2 class="text-right">PO: <?php echo $order['po']; ?></h2>
<h2 class="text-right">Supplier Order #: <?php echo $order['order_num']; ?></h2>
<h2 class="text-right">Cost Centre: <?php echo $cost_centre['code'] . " - " . $cost_centre['name']; ?></h2>
<h2 class="text-right">Order Created By: <?php echo $user['firstname'] . " " . $user['lastname']; ?></h2>

<?php
if (!isset($order['paid']) || empty($order['paid'])) {
	$button  = "<button type=\"button\" class=\"btn btn-sm btn-success\" onclick=\"location.href='index.php?n=orders_unique&uid=" . $order['uid'] . "&paid=true'\">Mark As Paid</button>";
	$button .= "<button type=\"button\" class=\"btn btn-sm btn-success dropdown-toggle dropdown-toggle-split\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
} else {
	$datePaid = date('Y-m-d', strtotime($order['paid']));

	$button  = "<button type=\"button\" class=\"btn btn-sm btn-secondary\">Paid on " . $datePaid . "</button>";
	$button .= "<button type=\"button\" class=\"btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
}
?>

<div class="btn-group float-right">
	<?php echo $button; ?>
		<span class="sr-only">Toggle Dropdown</span>
	</button>
  <div class="dropdown-menu">
		<a class="dropdown-item" href="index.php?n=orders_edit&uid=<?php echo $order['uid']; ?>"><i class="far fa-edit"></i> Edit Order</a>
		<a class="dropdown-item" href="index.php?n=orders_create&cloneUID=<?php echo $order['uid']; ?>"><i class="far fa-clone"></i> Clone Order</a>
		<?php
		if (isset($order['paid']) || !empty($order['paid'])) {
			echo "<div class=\"dropdown-divider\"></div>";
			echo "<a class=\"dropdown-item\" href=\"index.php?n=orders_unique&uid=" . $order['uid'] . "&paid=false\"><i class=\"far fa-undo-alt\"></i> Mark as Unpaid</a>";
		}
		?>
  </div>
</div>

<h2>Supplier: </h2>
<?php
if (isset ($supplier)) {
	$output  = "<h3><a href=\"index.php?n=suppliers_edit&name=" . $supplier['name'] . "\">" . $supplier['name'] . "</a></h3>";
	$output .= "<h3>" . str_replace("\n", "<br />", $supplier['address'])  . "</h3>";

	if (!empty($supplier['account_number'])) {
		$output .= "<h3>Account #: " . $supplier['account_number'] . "</h3>";
	}
	if (!empty($supplier['telephone'])) {
		$output .= "<h3>Telephone: " . $supplier['telephone'] . "</h3>";
	}
	if (!empty($supplier['email'])) {
		$output .= "<h3><a href=\"mailto:" . $supplier['email'] . "\">" . $supplier['email'] . "</a></h3>";
	}
	if (!empty($supplier['website'])) {
		$output .= "<h3><a href=\"" . tidyUrl($supplier['website']) . "\">" . $supplier['website'] . "</a></h3>";
	}
} else {
	$output  = "<h3><a href=\"index.php?n=suppliers_edit&name=" . $order['supplier'] . "\">" . $order['supplier'] . "</a></h3>";
}

echo $output;
?>




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

<script src="js/simpleUpload.min.js"></script>
<script>
	$('input[type=file]').change(function(){
	$(this).simpleUpload("/actions/file_upload.php?orderUID=<?php echo $order['uid'];?>", {
		start: function(file){
			//upload started
			console.log("upload started");
			$("#upload_feedback").html("Uploading File");
		},

		progress: function(progress){
			//received progress
			console.log("upload progress: " + Math.round(progress) + "%");

		},

		success: function(data){
			//upload successful
			console.log("upload successful!");
			console.log(data);
			$("#upload_feedback").html("File uploaded - please refresh this page");

		},

		error: function(error){
			//upload failed
			console.log("upload error: " + error.name + ": " + error.message);
			$("#upload_feedback").html(" error!");

		}
	});
});
</script>
