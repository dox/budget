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
<h1 class="text-right">Purchase Order <?php echo $order['po']; ?></h1>

<div class="row">
	<div class="col-md">
		<form action="" enctype="multipart/form-data" id="file-form" method="POST">
		  <div id="upup">
		    <p id="progressdiv"><progress max="100" value="0" id="progress" style="display: none;"></progress></p>
		    <input type="file" class="form-control" name="file-select" id="file-select"><br />
		    <button type="submit" class="btn btn-primary w-100" id="upload-button">Upload</button>
		  </div>
			<input type="hidden" id="orderUID" value="<?php echo $order['uid']; ?>" />
		</form>

		<br />

		<?php
		foreach ($uploads AS $upload) {
			$output  = "<div id=\"uploadLine_" . $upload['uid'] . "\" class=\"d-flex justify-content-between\">";
			$output .= "<a href=\"uploads/" . $upload['path'] . "\">";
			$output .="<svg width=\"16\" height=\"16\"><use xlink:href=\"img/icons.svg#paperclip\"/></svg> " . $upload['name'] . "</i>";
			$output .= "</a>";
			$output .= "<svg id=\"" . $upload['uid'] . "\" width=\"16\" height=\"16\" onclick=\"uploadDelete(this.id)\"><use xlink:href=\"img/icons.svg#trash\"/></svg>";
			$output .= "</div>";

			echo $output;
		}
		?>
	</div>
	<div class="col-md">
		<h2>Date: <?php echo date('Y-m-d H:i', strtotime($order['date'])); ?></h2>
		<h2>Supplier Order #: <?php echo $order['order_num']; ?></h2>
		<h2>Cost Centre: <?php echo $cost_centre['code'] . " - " . $cost_centre['name']; ?></h2>
		<h2>Order Created By: <?php echo $user['firstname'] . " " . $user['lastname']; ?></h2>

		<?php
		if (!isset($order['paid']) || empty($order['paid'])) {
			$button  = "<button type=\"button\" class=\"btn btn-sm btn-success\" onclick=\"location.href='index.php?n=orders_unique&uid=" . $order['uid'] . "&paid=true'\">Mark As Paid</button>";
			$button .= "<button type=\"button\" class=\"btn btn-sm btn-success dropdown-toggle dropdown-toggle-split\" data-bs-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
		} else {
			$datePaid = date('Y-m-d', strtotime($order['paid']));

			$button  = "<button type=\"button\" class=\"btn btn-sm btn-secondary\">Paid on " . $datePaid . "</button>";
			$button .= "<button type=\"button\" class=\"btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split\" data-bs-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
		}
		?>

		<div class="btn-group">
			<?php echo $button; ?>
				<span class="visually-hidden">Toggle Dropdown</span>
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
	</div>
</div>
<i class="bi bi-alarm-fill"></i>
<h2>Supplier: <a href="index.php?n=suppliers_edit&name=<?php echo $order['supplier']; ?>"><?php echo $order['supplier']; ?></a></h2>

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




<script type="text/javascript">
  var form = document.getElementById('file-form');
  var fileSelect = document.getElementById('file-select');
	var uploadButton = document.getElementById('upload-button');
	var orderUID = document.getElementById('orderUID').value;

  form.onsubmit = function(event) {
    event.preventDefault();

    var progress = document.getElementById('progress');
    var progressdiv = document.getElementById('progressdiv');

    progress.style.display = "block";
    uploadButton.innerHTML = 'Uploading...';


    var files = fileSelect.files;
    var formData = new FormData();
    var file = files[0];
		formData.append('upfile', file, file.name);
		formData.append('orderUID', orderUID);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'actions/file_upload.php', true);
    xhr.upload.onprogress = function (e) {
      update_progress(e);
    }
    xhr.onload = function (e) {
      if (xhr.status === 200) {
        uploadButton.innerHTML = 'Upload';
        progressdiv.innerHTML = "<h3>Sucess</h3>";
				location.reload();
      } else {
        alert('An error occurred!');
      }
    };
    xhr.send(formData);
  }

  function update_progress(e){
      if (e.lengthComputable){
          var percentage = Math.round((e.loaded/e.total)*100);
          progress.value = percentage;
          uploadButton.innerHTML = 'Upload '+percentage+'%';
          console.log("percent " + percentage + '%' );
      }
      else{
        console.log("Unable to compute progress information since the total size is unknown");
      }
  }
</script>
