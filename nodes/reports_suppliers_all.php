<?php
$orders_class = new class_orders;
$orders = $orders_class->all();	

foreach ($orders AS $order) {
	$supplierName = "'" . $order['supplier'] . "'";
	
	if (isset($suppliersArray[$supplierName])) {
		$suppliersArray[$supplierName] = $suppliersArray[$supplierName] + $order['value'];
	} else {
		$suppliersArray[$supplierName] = $order['value'];
	}
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
			<th scope="col" style="width: 120px;">Supplier</th>
			<th scope="col" style="width: 120px;">Total Spend</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($suppliersArray AS $supplier => $value) {
			$cost_centre_class = new class_cost_centres;
			$cost_centre = $cost_centre_class->getOne($order['cost_centre']);
			
			$output  = "<tr>";
			$output .= "<th scope=\"row\"><a href=\"index.php?n=suppliers_unique&name=" . str_replace("'", "", $supplier) . "\">" . str_replace("'", "", $supplier) . "</a></td>";
			$output .= "<th>£" . number_format($value,2) . "</td>";
			
			$output .= "</tr>";
			
			echo $output;
			
			
		}
		?>
		
	</tbody>
</table>