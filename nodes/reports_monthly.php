<?php
$orders_class = new class_orders;

	$i = 0;
	$date = date('Y-m-d');
	do {
		$ordersThisMonth2 = $orders_class->all($date);

		foreach ($ordersThisMonth2 AS $orders) {
			$arrayKeyValue = "'" . date('M',strtotime($orders['date'])) . "'";
			if (empty($totalOrdersByMonthArray[$arrayKeyValue])) {
				$totalOrdersByMonthArray[$arrayKeyValue] = $orders['value'];
			} else {
				$totalOrdersByMonthArray[$arrayKeyValue] = $totalOrdersByMonthArray[$arrayKeyValue] + $orders['value'];
			}
		}
		$date = date('Y-m-d', strtotime("-1 months", strtotime($date)));
		$i++;
	} while ($i < 12);
	$totalOrdersByMonthArray = array_reverse($totalOrdersByMonthArray);
?>


<canvas id="myChart" width="400" height="200"></canvas>
<script>
var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php echo implode(", ", array_keys($totalOrdersByMonthArray));?>],
        datasets: [{
            label: '£',
            data: [<?php echo implode(",", $totalOrdersByMonthArray); ?>]
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
