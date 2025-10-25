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

<form method="POST" action="save_order.php" class="needs-validation" novalidate>
	<!-- Order Details -->
	<div class="card mb-4">
	  <div class="card-header fw-bold">Order Details</div>
	  <div class="card-body row g-3">
		<div class="col-md-4">
		  <label for="orderNumber" class="form-label">Order Number</label>
		  <input type="text" class="form-control" id="orderNumber" name="orderNumber" value="PO-2025-017" readonly>
		</div>
		<div class="col-md-4">
		  <label for="orderDate" class="form-label">Order Date</label>
		  <input type="date" class="form-control" id="orderDate" name="orderDate" value="2025-10-25" required>
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
	</div>

	<!-- Company Information -->
	<div class="card mb-4">
	  <div class="card-header fw-bold">Company Information</div>
	  <div class="card-body row g-3">
		<div class="col-md-6">
		  <label for="companyName" class="form-label">Company Name</label>
		  <input type="text" class="form-control" id="companyName" name="companyName" value="Oxford Supplies Ltd" required>
		</div>
		<div class="col-md-6">
		  <label for="contactName" class="form-label">Contact Name</label>
		  <input type="text" class="form-control" id="contactName" name="contactName" value="John Carter">
		</div>
		<div class="col-12">
		  <label for="address" class="form-label">Address</label>
		  <textarea class="form-control" id="address" name="address" rows="2" required>23 Broad Street, Oxford, OX1 3BE</textarea>
		</div>
		<div class="col-md-6">
		  <label for="email" class="form-label">Email</label>
		  <input type="email" class="form-control" id="email" name="email" value="orders@oxfordsupplies.co.uk">
		</div>
		<div class="col-md-6">
		  <label for="phone" class="form-label">Phone</label>
		  <input type="tel" class="form-control" id="phone" name="phone" value="+44 1865 123456">
		</div>
	  </div>
	</div>

	<!-- Line Items -->
	<div class="card mb-4">
	  <div class="card-header fw-bold d-flex justify-content-between align-items-center">
		<span>Line Items</span>
		<button type="button" class="btn btn-sm btn-outline-primary" id="addItem">Add Item</button>
	  </div>
	  <div class="card-body">
		<table class="table table-bordered align-middle">
		  <thead class="table-light">
			<tr>
			  <th>Description</th>
			  <th style="width: 100px;">Qty</th>
			  <th style="width: 150px;">Unit Price (£)</th>
			  <th style="width: 150px;">Total (£)</th>
			  <th style="width: 60px;"></th>
			</tr>
		  </thead>
		  <tbody id="itemTable">
			<tr>
			  <td><input type="text" name="itemDesc[]" class="form-control" value="Network Switch (24-port)" required></td>
			  <td><input type="number" name="itemQty[]" class="form-control" value="2" min="1" required></td>
			  <td><input type="number" name="itemPrice[]" class="form-control" value="350" step="0.01" required></td>
			  <td class="text-end fw-semibold align-middle">700.00</td>
			  <td><button type="button" class="btn btn-outline-danger btn-sm remove-item">&times;</button></td>
			</tr>
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
		<td><input type="text" name="itemDesc[]" class="form-control" required></td>
		<td><input type="number" name="itemQty[]" class="form-control" value="1" min="1" required></td>
		<td><input type="number" name="itemPrice[]" class="form-control" value="0" step="0.01" required></td>
		<td class="text-end fw-semibold align-middle">0.00</td>
		<td><button type="button" class="btn btn-outline-danger btn-sm remove-item">&times;</button></td>
	  `;
	  document.getElementById('itemTable').appendChild(row);
	});
  
	// Remove item row
	document.addEventListener('click', (e) => {
	  if (e.target.classList.contains('remove-item')) {
		e.target.closest('tr').remove();
	  }
	});
  </script>
