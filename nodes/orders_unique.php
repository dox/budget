<?php
$orderObject = new order($_GET['uid']);

if (isset($_GET['paid'])) {
	if ($_GET['paid'] == "false") {
		$orderObject->markAsUnpaid();
	} elseif ($_GET['paid'] == "true") {
		$orderObject->markAsPaid();
	}

	$orderObject = new order($_GET['uid']);
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

	$orderObject->update($data);

	$orderObject = new order($_GET['uid']);

	$title = "Order Updated";
	$message = "Order '" . $_GET['uid'] . "' updated";
	echo toast($title, $message);
}

$supplierObject = new supplier($order->supplier);
$userObject = new user($order->username);
$costCentreObject = new cost_centre($orderObject->cost_centre);

$uploads_class = new class_uploads;
$uploads = $uploads_class->allByOrder($orderObject->uid);

if (!$costCentreObject->department == $_SESSION['department']) {
	echo "You have tried to access an order that you are not authorised to view";
	exit;
}
?>
<h1 class="text-right">Purchase Order <?php echo $orderObject->po; ?></h1>

<div class="row">
	<div class="col-md">
		<form action="" enctype="multipart/form-data" id="file-form" method="POST">
		  <div id="upup">
				<div class="input-group mb-3">
					<input type="file" class="form-control" name="file-select" id="file-select">
					<button type="submit" class="btn btn-primary" id="upload-button">Upload</button>
				</div>
		  </div>
			<input type="hidden" id="orderUID" value="<?php echo $orderObject->uid; ?>" />
		</form>
		<div class="progress">
			<div id="progressdiv" class="progress-bar" role="progressbar" style="width: 0%"></div>
		</div>

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
		<h4>Date: <?php echo date('Y-m-d H:i', strtotime($orderObject->date)); ?></h4>
		<h4>Supplier Order #: <?php echo $orderObject->order_num; ?></h4>
		<h4>Cost Centre: <?php echo $costCentreObject->code . " - " . $costCentreObject->name; ?></h4>
		<h4>Order Created By: <?php echo $userObject->firstname . " " . $userObject->lastname; ?></h4>

		<?php
		if (!isset($orderObject->paid) || empty($orderObject->paid)) {
			$button  = "<button type=\"button\" class=\"btn btn-sm btn-success\" onclick=\"location.href='index.php?n=orders_unique&uid=" . $orderObject->uid . "&paid=true'\">Mark As Paid</button>";
			$button .= "<button type=\"button\" class=\"btn btn-sm btn-success dropdown-toggle dropdown-toggle-split\" data-bs-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
		} else {
			$datePaid = date('Y-m-d', strtotime($orderObject->paid));

			$button  = "<button type=\"button\" class=\"btn btn-sm btn-secondary\">Paid on " . $datePaid . "</button>";
			$button .= "<button type=\"button\" class=\"btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split\" data-bs-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
		}
		?>

		<div class="btn-group">
			<?php echo $button; ?>
				<span class="visually-hidden">Toggle Dropdown</span>
			</button>
		  <div class="dropdown-menu">
				<a class="dropdown-item" href="index.php?n=orders_edit&uid=<?php echo $orderObject->uid; ?>"><i class="far fa-edit"></i> Edit Order</a>
				<a class="dropdown-item" href="index.php?n=orders_create&cloneUID=<?php echo $orderObject->uid; ?>"><i class="far fa-clone"></i> Clone Order</a>
				<?php
				if (isset($orderObject->paid) || !empty($orderObject->paid)) {
					echo "<div class=\"dropdown-divider\"></div>";
					echo "<a class=\"dropdown-item\" href=\"index.php?n=orders_unique&uid=" . $orderObject->uid . "&paid=false\"><i class=\"far fa-undo-alt\"></i> Mark as Unpaid</a>";
				}
				?>
		  </div>
		</div>
	</div>
</div>
<i class="bi bi-alarm-fill"></i>
<h2>Supplier: <a href="index.php?n=suppliers_edit&name=<?php echo $orderObject->supplier; ?>"><?php echo $orderObject->supplier; ?></a></h2>

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
			<td><?php echo date('Y-m-d', strtotime($orderObject->date));?></td>
			<td><?php echo $orderObject->name;
				if (isset($orderObject->description)) {
					echo "<br /><span class=\"text-muted\">" . $orderObject->description . "</span>";
				}
				?>
			</td>
			<td>£<?php echo number_format($orderObject->value,2);?></td>
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

    var progressdiv = document.getElementById('progressdiv');

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
					uploadButton.innerHTML = 'Upload '+percentage+'%';
					progressdiv.style["width"] = percentage + '%';
          console.log("percent " + percentage + '%' );
      }
      else{
        console.log("Unable to compute progress information since the total size is unknown");
      }
  }
</script>
