<?php
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id'];
	$order = new Order($id);
} else {
	// Handle invalid or missing ID
	die('Invalid order ID.');
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Purchase Order <?php echo "#" . $order->id; ?></h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group me-2">
			<a href="index.php?page=order_addedit&action=edit&id=<?php echo $order->id; ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil" aria-hidden="true"></i> Edit</a>
			<a href="index.php?page=order_addedit&action=clone&id=<?php echo $order->id; ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-copy" aria-hidden="true"></i> Clone</a>
		</div>
	</div>
</div>

<div class="row mb-4">
	<div class="col text-end">
	  <h6 class="mb-0">Date</h6>
	  <p class="fw-semibold mb-0"><?php echo date("Y-m-d", strtotime($order->date_created)); ?></p>
	</div>
</div>

<div class="row mb-4">
	<div class="col-md-6">
	  <h6 class="text-uppercase text-muted">From</h6>
	  <p class="mb-1 fw-semibold">St Edmund Hall Design & Build Ltd</p>
	  <p class="mb-1">Queen’s Lane, Oxford OX1 4AR</p>
	  <p class="mb-1">help@seh.ox.ac.uk</p>
	  <p class="mb-1"><?php echo $order->username; ?></p>
	  <p class="mb-1"><?php echo $order->cost_centre; ?></p>
	</div>
	<div class="col-md-6 text-md-end">
	  <h6 class="text-uppercase text-muted">To</h6>
	  <p class="mb-1 fw-semibold"><?php echo $order->supplier; ?></p>
	  <p class="mb-1"><?php echo "Order #:" . $order->order_num; ?></p>
	  <p class="mb-1">123 Industrial Road, Reading RG1 2CD</p>
	  <p class="mb-0">sales@abcsupplies.co.uk</p>
	</div>
  </div>

  <!-- Item Table -->
  <div class="table-responsive mb-4">
	<table class="table table-bordered align-middle">
	  <thead >
		<tr>
		  <th scope="col">#</th>
		  <th scope="col">Description</th>
		  <th scope="col">Qty.</th>
		  <th scope="col" class="text-end">Price</th>
		  <th scope="col" class="text-end">Total</th>
		</tr>
	  </thead>
	  <tbody>
		
		  
		  <?php
		  $i = 1;
		  $items = json_decode($order->items, true);
		  
		  foreach ($items AS $item) {
			  $output  = "<tr>";
			  $output .= "<td>" . $i . "</td>";
			  $output .= "<td>" . $item['item_name'] . "</td>";
			  $output .= "<td>" . $item['item_qty'] . "</td>";
			  $output .= "<td>" . $item['item_value'] . "</td>";
			  $output .= "<td>" . $item['item_value'] . "</td>";
			  $output .= "</tr>";
			  
			  echo $output;
			  
			  $i++;
		  }
		  
		  echo $order->name;
		  if ($order->description) {
			  echo "<p class=\"small\">" . $order->description . "</p>";
		  };
		  ?>
	  </tbody>
	  <tfoot>
		<tr>
		  <th colspan="4" class="text-end">Total</th>
		  <th class="text-end"><?php echo formatMoney($order->value); ?></th>
		</tr>
	  </tfoot>
	</table>
  </div>

  <!-- Notes -->
  <div class="row">
	<div class="col">
	  <p class="mb-0">
	  	<?php
		  if ($order->notes) {
			  echo "<h6 class=\"text-uppercase text-muted\">Notes</h6>";
				echo "<p>" . $order->notes . "</p>";
			};
			?>
		</p>
	</div>
  </div>
