<?php
$cost_centre_class = new class_cost_centres;
$cost_centres = $cost_centre_class->all();
$groups = $cost_centre_class->groups();

$departments_class = new class_departments;
$department = $departments_class->getOne($_SESSION['department']);

$orders_class = new class_orders;
$orders = $orders_class->all();

foreach ($orders_class->recentSuppliers() AS $supplier) {
	$suppliersArray[] = $supplier['supplier'];
}
$suppliersArray = array_unique($suppliersArray);

if (isset($_GET['cloneUID'])) {
	$orderToClone = $orders_class->getOne($_GET['cloneUID']);
}
?>

<h2>Create New Order</h2>
<div id="andrew"></div>
<form method="POST" action="index.php?n=orders_all">
	<div class="row">
		<div class="col-sm">
			<div class="form-group">
				<label for="date">Date</label>
				<input type="text" class="form-control" id="date" name="date" placeholder="<?php echo date('Y-m-d H:i'); ?>" value="<?php echo date('Y-m-d H:i'); ?>">
			</div>
		</div>
		<div class="col-sm">
			<div class="form-group">
				<label for="po">Purchase Order #</label>
				<input type="text" class="form-control" id="po" name="po" placeholder="Purchase Order #" value="<?php echo $orders_class->nextOrderNumber(); ?>">
				<small id="emailHelp" class="form-text text-muted">This is an auto-generated number based on the last order.</small>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm">
			<div class="form-group">
				<label for="supplier">Supplier</label>
				<input class="form-control" list="datalistOptions" id="supplier" name="supplier" placeholder="Supplier" <?php if (isset($orderToClone['supplier'])) { echo "value=\"" . $orderToClone['supplier'] . "\"";}?>>
				<datalist id="datalistOptions">
					<?php


					foreach ($suppliersArray AS $supplier) {
						$output = "<option value=\"" . $supplier . "\">";

						echo $output;
					}
					?>
				</datalist>
			</div>
		</div>
		<div class="col-sm">
			<div class="form-group">
				<label for="order_num">Supplier Order #</label>
				<input type="text" class="form-control" id="order_num" name="order_num" <?php if (isset($orderToClone['order_num'])) { echo "value=\"" . $orderToClone['order_num'] . "\"";}?>placeholder="Supplier Order #">
			</div>
		</div>
	</div>
	<div class="form-group">
		<label for="name">Name</label>
		<input type="text" class="form-control" id="name" name="name" <?php if (isset($orderToClone['name'])) { echo "value=\"" . $orderToClone['name'] . "\"";}?>placeholder="Name">
	</div>
	<div class="form-group">
		<label for="cost_centre">Cost Centre</label>
		<select class="form-control" readonly id="cost_centre" name="cost_centre">
			<option></option>
			<?php
			foreach ($groups AS $group) {
				$output  = "<optgroup label=\"" . $group . "\">";

				foreach ($cost_centres AS $cost_centre) {
					if ($cost_centre['grouping'] == $group) {
						if ($cost_centre['uid'] == $orderToClone['cost_centre']) {
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
	<div class="form-group">
		<label for="value">Value (£)</label>
		<input type="number" step=".01" class="form-control" id="value" name="value" <?php if (isset($orderToClone['value'])) { echo "value=\"" . $orderToClone['value'] . "\"";}?>placeholder="Value (without £ or commas)">
	</div>
	<div class="form-group">
		<label for="description">Description</label>
		<textarea class="form-control" id="description" name="description" rows="3"><?php if (isset($orderToClone['description'])) { echo $orderToClone['description'];}?></textarea>
	</div>
	<button type="submit" class="btn btn-primary orderCreateButton">Submit</button>
</form>
