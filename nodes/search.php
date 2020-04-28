<?php
if (isset($_POST['search'])) {
} else {
	$_POST['search'] = "";
}

$orders_class = new class_orders;
$orders = $orders_class->allBySearch($_POST['search']);
?>

<h2>Search Results for '<?php echo $_POST['search']; ?>' <small class="text-muted"><?php echo "Budget Year: " . BUDGET_STARTDATE . " - " . BUDGET_ENDDATE; ?></small></h2>

<table class="table bg-white">
	<thead>
		<tr>
			<th scope="col" style="width: 120px;">Date</th>
			<th scope="col" style="width: 120px;">PO</th>
			<th scope="col" style="width: 120px;">Cost Centre</th>
			<th scope="col">Item</th>
			<th scope="col">Supplier</th>
			<th scope="col" style="width: 110px;">Value</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($orders AS $order) {
			$cost_centre_class = new class_cost_centres;
			$cost_centre = $cost_centre_class->getOne($order['cost_centre']);

			if (isset($order['paid'])) {
				$class = "table-active";
			} else {
				$class = "";
			}
			$output  = "<tr class=\"" . $class . "\">";
			$output .= "<td scope=\"row\">" . date('Y-m-d', strtotime($order['date'])) . "</td>";
			$output .= "<td><a href=\"index.php?n=orders_unique&uid=" . $order['uid'] . "\">" . $order['po'] . "</a></td>";
			$output .= "<td><i class=\"fas fa-coins\" style=\"color: " . $cost_centre['colour'] . ";\"></i> <a href=\"index.php?n=costcentres_unique&uid=" . $cost_centre['uid'] . "\">" . $cost_centre['code'] . "</a></td>";
			$output .= "<td>" . $order['name'] . "</td>";
			$output .= "<td><a href=\"index.php?n=suppliers_unique&name=" . $order['supplier'] . "\">" . $order['supplier'] . "</a></td>";
			$output .= "<td class=\"text-right color-red\">£" . number_format($order['value']) . " <i class=\"fas fa-long-arrow-alt-right fa-sm\"></i></td>";
			$output .= "</tr>";

			echo $output;


		}
		?>

	</tbody>
</table>
