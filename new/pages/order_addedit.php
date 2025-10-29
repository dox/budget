<?php
if (!isset($_GET['action'])) {
	die('No action specified.');
}

$action = strtolower($_GET['action']);
$validActions = ['add', 'edit', 'clone'];

if (!in_array($action, $validActions, true)) {
	die('Invalid action.');
}

switch ($action) {
	case 'add':
		$actionLabel = 'Create';
		$order = new Order();
		break;

	case 'edit':
	case 'clone':
		$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
		if ($id === false || $id === null) {
			die('Invalid order ID.');
		}
		$order = new Order($id);
		$actionLabel = ucfirst($action);
		break;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2"><?php echo $actionLabel; ?> Purchase Order <?php echo "#" . $order->id; ?></h1>
</div>

<form method="POST" id="order_insert" class="needs-validation" novalidate>
	<!-- Order Details -->
	<div class="card mb-4">
	  <div class="card-header fw-bold">Order Details</div>
	  <div class="card-body">
		  <div class="row g-3">
		<div class="col-md-4">
		  <label for="orderNumber" class="form-label">Order Number</label>
		  <input type="text" class="form-control" id="order_num" name="order_num" value="PO-2025-017" readonly>
		</div>
		<div class="col-md-4">
		  <label for="orderDate" class="form-label">Order Date</label>
		  <input type="datetime-local" class="form-control" id="date_created" name="date_created" value="<?php echo date('Y-m-d H:i:s'); ?>" required>
		</div>
		<div class="col-md-4">
		  <label for="status" class="form-label">Status</label>
		  <select id="status" name="status" class="form-select" required>
			<option value="Pending">Pending</option>
			<option value="Approved" selected>Approved</option>
			<option value="Paid">Paid</option>
		  </select>
		</div>
	  </div>
	  
	  <div class="row g-3">
		  <div class="col">
			  <label for="cost_centre" class="form-label">Cost Centre</label>
			  <input type="text" class="form-control" id="cost_centre" name="cost_centre" value="1" required>
		  </div>
		  
		  <div class="col">
			  <label for="supplier" class="form-label">Supplier</label>
			  <input type="text" class="form-control" id="supplier" name="supplier" value="1" required>
		  </div>
	  </div>
	  
	  <div class="row g-3">
		  <div class="col">
			  <label for="value" class="form-label">Value</label>
			  <input type="text" class="form-control" id="value" name="value" value="99.99" required>
		  </div>
		  
		  <div class="col">
			  <label for="name" class="form-label">Name</label>
			  <input type="text" class="form-control" id="name" name="name" value="test" required>
		  </div>
	  </div>
	  
	  
	  </div>
	  
	</div>

	<!-- Line Items -->
	<div class="card mb-4">
	  <div class="card-header fw-bold d-flex justify-content-between align-items-center">
		<span>Line Items</span>
		<button type="button" class="btn btn-sm btn-outline-primary" id="addItem"><i class="bi bi-plus" aria-hidden="true"></i> Add Item</button>
	  </div>
	  <div class="card-body">
		<table class="table align-middle">
		  <thead>
			<tr>
			  <th>Description</th>
			  <th style="width: 100px;">Qty</th>
			  <th style="width: 150px;">Unit Price (£)</th>
			  <th style="width: 150px;">Total (£)</th>
			  <th style="width: 60px;"></th>
			</tr>
		  </thead>
		  <tbody id="itemTable">
			  <?php
			  if ($action = "edit") {
				  foreach ($order->items() AS $item) {
					  $output  = "<tr>";
					  $output .= "<td><input type=\"text\" name=\"itemName[]\" class=\"form-control\" value=\"" . $item['item_name'] . "\" required></td>";
					  $output .= "<td><input type=\"number\" name=\"itemQty[]\" class=\"form-control\" value=\"" . $item['item_qty'] . "\" min=\"1\" required></td>";
					  $output .= "<td><input type=\"number\" name=\"itemPrice[]\" class=\"form-control\" value=\"" . $item['item_value'] . "\" step=\"0.01\" required></td>";
					  $output .= "<td class=\"text-end fw-semibold align-middle\">" . ($item['item_value'] * $item['item_qty']) . "</td>";
					  $output .= "<td><button type=\"button\" class=\"btn btn-outline-danger btn-sm remove-item\">&times;</button></td>";
					  $output .= "</tr>";
					  
					  echo $output;
				  }
			  }
			  ?>
			
		  </tbody>
		</table>
	  </div>
	</div>

	<!-- Totals -->
	<div class="row justify-content-end mb-4">
	  <div class="col-md-4">
		<div class="card">
		  <div class="card-body">
			<div class="d-flex justify-content-between">
			  <span>Subtotal:</span>
			  <span>£700.00</span>
			</div>
			<div class="d-flex justify-content-between">
			  <span>VAT (20%):</span>
			  <span>£140.00</span>
			</div>
			<hr>
			<div class="d-flex justify-content-between fw-bold">
			  <span>Total:</span>
			  <span>£840.00</span>
			</div>
		  </div>
		</div>
	  </div>
	</div>

	<!-- Submit -->
	<div class="text-end">
	  <button type="submit" class="btn btn-primary">Save Changes</button>
	  <a href="orders.php" class="btn btn-secondary">Cancel</a>
	</div>
  </form>
  
  
<script>
// Add new line item row
document.getElementById('addItem').addEventListener('click', () => {
	const row = document.createElement('tr');
	row.innerHTML = `
	<td><input type="text" name="itemName[]" class="form-control" required></td>
	<td><input type="number" name="itemQty[]" class="form-control" value="1" min="1" required></td>
	<td><input type="number" name="itemPrice[]" class="form-control" value="0" step="0.01" required></td>
	<td class="text-end fw-semibold align-middle">0.00</td>
	<td><button type="button" class="btn btn-outline-danger btn-sm remove-item">&times;</button></td>`;
	document.getElementById('itemTable').appendChild(row);
});

// Remove item row
document.addEventListener('click', (e) => {
	if (e.target.classList.contains('remove-item')) {
		e.target.closest('tr').remove();
	}
});

// Submit form via XHR
document.getElementById('order_insert').addEventListener('submit', function(e) {
	e.preventDefault();
	const formData = new FormData(this);
	
	// Add the action manually
	formData.append('action', 'order_insert');

	fetch('actions/order.php', {
		method: 'POST',
		body: formData
	})
	.then(res => res.json())
	.then(data => {
		console.log('Server response:', data);
		if (data.success) alert('Order inserted!');
		else alert('Error: ' + (data.error || 'Unknown'));
	})
	.catch(err => console.error('XHR error:', err));
});
</script>