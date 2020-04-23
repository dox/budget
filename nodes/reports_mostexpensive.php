<?php
$orders_class = new class_orders;
$orders = $orders_class->all();

$valueColumn  = array_column($orders, 'value');
array_multisort($valueColumn, SORT_DESC, $orders);
$orders = array_slice($orders, 0, 20);
?>

<h2>Top 20 Most Expensive Orders <small class="text-muted"><?php echo "Budget Year: " . BUDGET_STARTDATE . " - " . BUDGET_ENDDATE; ?></small></h2>

<table class="table bg-white">
	<thead>
		<tr>
			<th scope="col" style="width: 120px;">Date</th>
			<th scope="col" style="width: 120px;">PO</th>
			<th scope="col" style="width: 120px;">Cost Centre</th>
			<th scope="col">Item</th>
			<th scope="col">Supplier</th>
			<th scope="col" style="width: 100px;">Value</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($orders AS $order) {
			$cost_centre_class = new class_cost_centres;
			$cost_centre = $cost_centre_class->getOne($order['cost_centre']);
			
			$output  = "<tr>";
			$output .= "<th scope=\"row\">" . date('Y-m-d', strtotime($order['date'])) . "</th>";
			$output .= "<th><a href=\"index.php?n=orders_unique&uid=" . $order['uid'] . "\">" . $order['po'] . "</a></th>";
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