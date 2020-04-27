<?php
$cost_centre_class = new class_cost_centres;
$cost_centre = $cost_centre_class->getOne($_GET['uid']);

$orders_class = new class_orders;
$orders = $orders_class->all(null, $cost_centre['uid']);

?>

<h2><?php echo $cost_centre['name'];?></h2>

<?php
	$sql = "SELECT
				orders.uid,
				orders.date,
				orders.cost_centre,
				orders.value,
				cost_centres.department
			FROM orders, cost_centres
			WHERE orders.cost_centre = cost_centres.uid
			AND (orders.date BETWEEN '" . BUDGET_STARTDATE . "' AND '" . BUDGET_ENDDATE . "')
			AND orders.cost_centre = '" . $cost_centre['uid'] . "'
			ORDER BY orders.date ASC;";
	$ordersRunningTotal = $db->rawQuery($sql);
	$budgetTotal = $cost_centre['value'];
	$runningBudget["'" . BUDGET_STARTDATE . "'"] = "'" . ($budgetTotal) . "'";
	foreach ($ordersRunningTotal AS $order) {;

		$runningBudget["'" . $order['date'] . "'"] = "'" . ($budgetTotal - $order['value']) . "'";
		$budgetTotal = $budgetTotal - $order['value'];
	}
	$runningBudget["'" . BUDGET_ENDDATE . "'"] = "'" . ($budgetTotal) . "'";
?>

<canvas id="canvas" width="400" height="100"></canvas>
<script>
	var timeFormat = 'YYYY/MM/DD';

	var config = {
		type: 'line',
		data: {
			labels: [<?php echo implode(", ", array_keys($runningBudget));?>],
			datasets: [{
				label: '£',
				data: [<?php echo implode(",", $runningBudget); ?>],
			}]
		},
		options: {
			title: {
				text: 'Running Budget'
			},
			elements: {
				line: {
					tension: 0
				}
			},
			legend: {
				display: false
			},
			scales: {
				xAxes: [{
					type: 'time',
					time: {
						parser: timeFormat,
						// round: 'day'
						tooltipFormat: 'll'
					},
					scaleLabel: {
						display: false
					}
				}],
				yAxes: [{
					ticks: {
						suggestedMin: 0
					},
					scaleLabel: {
						display: true,
						labelString: '£'
					}
				}]
			},
		}
	};

	window.onload = function() {
		var ctx = document.getElementById('canvas').getContext('2d');
		window.myLine = new Chart(ctx, config);
	};
</script>

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
			if (isset($order['paid'])) {
				$class = "table-active";
			} else {
				$class = "";
			}
			$output  = "<tr class=\"" . $class . "\">";
			$output .= "<td scope=\"row\">" . date('Y-m-d', strtotime($order['date'])) . "</td>";
			$output .= "<td><a href=\"index.php?n=orders_unique&uid=" . $order['uid'] . "\">" . $order['po'] . "</a></td>";
			$output .= "<td><i class=\"fas fa-coins\" style=\"color: " . $cost_centre['colour'] . ";\"></i> " . $cost_centre['code'] . "</td>";
			$output .= "<td>" . $order['name'] . "</td>";
			$output .= "<td><a href=\"index.php?n=suppliers_unique&name=" . $order['supplier'] . "\">" . $order['supplier'] . "</a></td>";
			$output .= "<td class=\"text-right color-red\">£" . number_format($order['value']) . " <i class=\"fas fa-long-arrow-alt-right fa-sm\"></i></td>";
			$output .= "</tr>";

			echo $output;


		}
		?>

	</tbody>
</table>
