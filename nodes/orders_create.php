<?php
$orders_class = new class_orders;

if (isset($_GET['cloneUID'])) {
	$orderToClone = new order($_GET['cloneUID']);
}
?>

<h2>Create New Order</h2>
<form method="POST" id="create_order_form" action="index.php?n=orders_all" class="needs-validation" novalidate>
	<div class="row">
		<div class="col-sm">
			<div class="form-group">
				<label for="date">Date</label>
				<input type="text" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d H:i'); ?>" required>
				<div class="invalid-feedback">Please provide a valid date.</div>
			</div>
		</div>
		<div class="col-sm">
			<div class="form-group">
				<label for="po">Purchase Order #</label>
				<input type="text" class="form-control" id="po" name="po" placeholder="Purchase Order #" value="<?php echo $orders_class->nextOrderNumber(); ?>" required>
				<small id="emailHelp" class="form-text text-muted">This is an auto-generated number based on the last order</small>
				<div class="invalid-feedback">Please provide a valid PO.</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm">
			<div class="mb-3">
				<label for="supplier">Supplier</label>
				<input class="form-control" list="datalistOptions" id="supplier" name="supplier" <?php if (isset($orderToClone->supplier)) { echo "value=\"" . $orderToClone->supplier . "\"";}?>>
				<datalist id="datalistOptions">
					<?php
					foreach (class_suppliers::recentSuppliers() AS $supplier) {
						$output = "<option value=\"" . $supplier . "\">";

						echo $output;
					}
					?>
				</datalist>
			</div>
		</div>
		<div class="col-sm">
			<div class="mb-3">
				<label for="order_num">Supplier Order #</label>
				<input type="text" class="form-control" id="order_num" name="order_num" <?php if (isset($orderToClone->order_num)) { echo "value=\"" . $orderToClone->order_num . "\"";}?>placeholder="Supplier Order #">
			</div>
		</div>
	</div>
	<div class="mb-3">
		<label for="name">Name</label>
		<input type="text" class="form-control" id="name" name="name" <?php if (isset($orderToClone->name)) { echo "value=\"" . $orderToClone->name . "\"";}?> placeholder="Name" required>
		<div class="invalid-feedback">Please provide a valid order name.</div>
	</div>
	<div class="row">
		<div class="col-sm">
			<div class="mb-3">
				<label for="cost_centre">Cost Centre</label>
				<select class="form-select" id="cost_centre" name="cost_centre" required>
					<option></option>
					<?php
					foreach (class_cost_centres::groups() AS $group) {
						$output  = "<optgroup label=\"" . $group['grouping'] . "\">";

						foreach (class_cost_centres::all() AS $cost_centre) {
							if ($cost_centre['grouping'] == $group['grouping']) {

								// if cloning, make sure the right cost centre is pre-selected
								if ($cost_centre['uid'] == $orderToClone->cost_centre) {
									$selected = " selected";
								} else {
									$selected = "";
								}

								$output .= "<option value=\"" .  $cost_centre['uid'] . "\" " . $selected . ">" . $cost_centre['code'] . " - " .$cost_centre['name'] . "</option>";
							}
						}
						$output .= "</optgroup>";
						echo $output;
					}
					?>
				</select>
				<div class="invalid-feedback">Please provide a Cost Centre.</div>
			</div>
		</div>
		<div class="col-sm">
			<div class="mb-3">
				<label for="value">Value (£)</label>
				<input type="number" step=".01" class="form-control" id="value" name="value" <?php if (isset($orderToClone->value)) { echo "value=\"" . $orderToClone->value . "\"";}?> placeholder="Value (without £ or commas)" required>
				<div class="invalid-feedback">Please provide a order value.</div>
			</div>
		</div>
	</div>
	<div class="mb-3">
		<label for="description">Description</label>
		<textarea class="form-control" id="description" name="description" rows="3"><?php if (isset($orderToClone->description)) { echo $orderToClone->description;}?></textarea>
	</div>
	<div class="form-group">
		<a href="#" id="test" class="btn btn-primary" onclick="createOrder(this.id)">Submit</a>
	</div>
</form>

<script>
var fp = flatpickr("#date", {
  enableTime: true,
  time_24hr: true
})
</script>
