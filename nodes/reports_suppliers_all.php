<?php
$orders_class = new class_orders;
$orders = $orders_class->all();
$YTDTotalSpend = 0;

foreach ($orders AS $order) {
	$supplierName = "'" . $order['supplier'] . "'";

	if (isset($suppliersArray[$supplierName])) {
		$suppliersArray[$supplierName] = $suppliersArray[$supplierName] + $order['value'];
	} else {
		$suppliersArray[$supplierName] = $order['value'];
	}

	$YTDTotalSpend = $YTDTotalSpend + $order['value'];
}



arsort($suppliersArray);
?>

<canvas id="myChart" width="400" height="200"></canvas>

<script>
var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php echo implode(", ", array_keys($suppliersArray));?>],
        datasets: [{
            label: '£',
            data: [<?php echo implode(",", $suppliersArray); ?>]
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
</script>

<table class="table bg-white">
	<thead>
		<tr>
			<th scope="col">Supplier</th>
			<th scope="col" style="width: 200px;">Total Spend</th>
			<th scope="col" style="width: 80px;">%</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($suppliersArray AS $supplier => $value) {
			$cost_centre = new cost_centre($order['cost_centre']);
			$supplierURL = "index.php?n=suppliers_unique&name=" . urlencode(str_replace("'", "", $supplier));

			$output  = "<tr>";
			$output .= "<td scope=\"row\"><a href=\"" . $supplierURL . "\">" . str_replace("'", "", $supplier) . "</a></td>";
			$output .= "<td>£" . number_format($value,2) . "</td>";
			$output .= "<td>" . number_format(($value/$YTDTotalSpend)*100,1) . "</td>";

			$output .= "</tr>";

			echo $output;


		}
		?>

	</tbody>
</table>
