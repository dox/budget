<?php
$orderObject = new order($_GET['uid']);
?>

<h2>Edit Order '<?php echo $orderObject->name;?>'</h2>

<form method="POST" action="index.php?n=orders_unique&uid=<?php echo $orderObject->uid; ?>">
	<div class="row">
		<div class="col-sm">
			<div class="mb-3">
				<label for="date">Date</label>
				<input type="text" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d H:i', strtotime($orderObject->date)); ?>">
			</div>
		</div>
		<div class="col-sm">
			<div class="mb-3">
				<label for="po">Purchase Order #</label>
				<input type="text" class="form-control" id="po" name="po" placeholder="Purchase Order #" value="<?php echo $orderObject->po; ?>">
				<small id="emailHelp" class="form-text text-muted">This is an auto-generated number based on the last order.</small>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm">
			<div class="mb-3">
				<label for="supplier">Supplier</label>
				<input class="form-control" list="datalistOptions" id="supplier" name="supplier" value="<?php echo $orderObject->supplier; ?>">
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
				<input type="text" class="form-control" id="order_num" name="order_num" placeholder="Supplier Order #" value="<?php echo $orderObject->order_num; ?>">
			</div>
		</div>
	</div>
	<div class="mb-3">
		<label for="name">Name</label>
		<input type="text" class="form-control" id="name" name="name" placeholder="Name" value="<?php echo $orderObject->name; ?>">
	</div>
	<div class="row">
		<div class="col-sm">
			<div class="mb-3">
				<label for="cost_centre">Cost Centre</label>
				<select class="form-select" id="cost_centre" name="cost_centre" required>
					<?php
					foreach (class_cost_centres::groups() AS $group) {
						$output  = "<optgroup label=\"" . $group['grouping'] . "\">";

						foreach (class_cost_centres::all() AS $cost_centre) {
							if ($cost_centre['grouping'] == $group['grouping']) {
								if ($cost_centre['uid'] == $orderObject->cost_centre) {
									$selected = " selected";
								} else {
									$selected = "";
								}
							$output .= "<option " . $selected . " value=\"" .  $cost_centre['uid'] . "\">" . $cost_centre['code'] . " - " .$cost_centre['name'] . "</option>";

							}
						}
						$output .= "</optgroup>";
						echo $output;
					}
					?>
				</select>
			</div>
		</div>
		<div class="col-sm">
			<div class="mb-3">
				<label for="value">Value (£)</label>
				<input type="text" class="form-control" id="value" name="value" placeholder="Value (without £ or commas)" value="<?php echo $orderObject->value; ?>">
			</div>
		</div>
	</div>
	<div class="mb-3">
		<label for="description">Description</label>
		<textarea class="form-control" id="description" name="description" rows="3"><?php echo $orderObject->description;?></textarea>
	</div>
	<button type="submit" class="btn btn-primary">Update</button>
</form>

<script>
var fp = flatpickr("#date", {
  enableTime: true,
  time_24hr: true
})
</script>
