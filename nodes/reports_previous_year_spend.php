<?php
$cost_centre_class = new class_cost_centres;

if (isset($_POST['code'])) {
	$data = Array (
		"code" => $_POST['code'],
		"name" => $_POST['name'],
		"department" => $_POST['department'],
		"description" => $_POST['description'],
		"group_name" => $_POST['group_name'],
		"colour" => $_POST['colour'],
		"value" => $_POST['budget']
	);

	$costCentre = new cost_centre();
	$costCentre->create($data);

	$title = "Cost Centre Created";
	$message = "New cost centre '" . $_POST['name'] . "' created";
	echo toast($title, $message);
}

?>

<h2>Cost Centres <small class="text-muted"><?php echo "Budget Year: " . budgetStartDate(date('Y-m-d', strtotime("1 year ago"))) . " - " . budgetEndDate(date('Y-m-d', strtotime("1 year ago"))); ?></small></h2>

<table class="table bg-white">
	<thead>
		<tr>
			<th scope="col" style="width: 50px;"></th>
			<th scope="col" style="width: 120px;">Code</th>
			<th scope="col">Name</th>
			<th scope="col">Budget</th>
			<th scope="col">Remaining Budget</th>
			<th scope="col" style="width: 70px;"></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($cost_centre_class->groups() AS $group) {
			$orders = new class_orders();
			
			$output  = "<tr><td colspan='6'>" . ($group['group_name']) ."</td></tr>";

			foreach ($cost_centre_class->all() AS $cost_centre) {
				$cost_centre = new cost_centre($cost_centre['uid']);
				
				$output .= "";
				if ($cost_centre->group_name == $group['group_name']) {
					
					
					if (isset($cost_centre->description)) {
						$codeName = $cost_centre->name . "<br /><small class=\"text-muted\">" . $cost_centre->description . "</small>";
					} else {
						$codeName = $cost_centre->name;
					}
					
					$totalOrders = $orders->allBetweenDates(budgetStartDate(date('Y-m-d', strtotime("1 year ago"))), budgetEndDate(date('Y-m-d', strtotime("1 year ago"))), $cost_centre->uid);
					
					
					$totalSpend = 0;
					foreach($totalOrders AS $order) {
						$totalSpend = $totalSpend + $order['value'];
					}

					$output .= "<td scope=\"row\"><svg width=\"16\" height=\"16\" style=\"color: " . $cost_centre->colour . ";\"><use xlink:href=\"img/icons.svg#archive-fill\"/></svg></td>";
					$output .= "<td><a href=\"index.php?n=costcentres_unique&uid=" . $cost_centre->uid . "\">" . $cost_centre->code . "</td>";
					$output .= "<td>" . $codeName . "</td>";
					$output .= "<td>£" . number_format($totalSpend) . "</td>";
					$output .= "<td></td>";
					$output .= "<td></td>";
					$output .= "</tr>";
				}
			}
			echo $output;
		}
		?>
	</tbody>
</table>
