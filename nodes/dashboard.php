<?php
$orders_class = new class_orders;
$ordersThisMonth = $orders_class->all();

$YTDTotalSpend = 0;
$outstandingPayments = 0;
foreach ($ordersThisMonth AS $order) {

	if (date('Y-m',strtotime($order['date'])) == date('Y-m')) {
		$monthlyOrdersTotalArray[$order['cost_centre']] = $order['cost_centre'] + $order['value'];
		$YTDTotalSpend = $YTDTotalSpend + $order['value'];
	} else {
		$YTDTotalSpend = $YTDTotalSpend + $order['value'];
	}
	
	if (empty($order['paid'])) {
		$outstandingPayments = $outstandingPayments + $order['value'];
	}
}

$totalSpendMonthly = array_sum($monthlyOrdersTotalArray);
?>

<h2>Dashboard <small class="text-muted"><?php echo date('F, Y'); ?></small></h2>

<canvas id="myChart" width="400" height="100"></canvas>
<br />
<div class="container">
	<div class="row">
		<div class="col-sm">
			<div class="card card--blue">
				<h2 style="font-size: 20px;">&pound; <?php echo number_format($totalSpendMonthly);?></h2>
			<div class="mt-1" style="color: #A7AEBB;">Outgoings This Month</div>
		</div>
		</div>
		<div class="col-sm">
			<div class="card card--red">
				<h2 style="font-size: 20px;">&pound; <?php echo number_format($YTDTotalSpend);?></h2>
				<div class="mt-1" style="color: #A7AEBB;">Outgoings This Budget Year</div>
			</div>
		</div>
		<div class="col-sm">
			<div class="card card--green">
				<h2 style="font-size: 20px;">&pound; <?php echo number_format($outstandingPayments);?></h2>
				<div class="mt-1" style="color: #A7AEBB;">Unpaid Orders</div>
			</div>
		</div>
	</div>
</div>
<br />

<table class="table bg-white">
	<thead>
		<tr>
			<th scope="col">Cost Centre</th>
			<th scope="col">Value</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($monthlyOrdersTotalArray AS $ordersTotal => $value) {
			$cost_centre_class = new class_cost_centres;
			$cost_centre = $cost_centre_class->getOne($ordersTotal);
			
			$output  = "<tr>";
			$output .= "<td>" . "<i class=\"fas fa-coins\" style=\"color: " . $cost_centre['colour'] . ";\"></i> " . $cost_centre['code'] . " (" . $cost_centre['name'] .")</td>";
			$output .= "<th>£" . number_format($value) . "</th>";
			
			$output .= "</tr>";
			
			echo $output;
			
			
		}
		?>
		
	</tbody>
</table>


<?php
	$i = 0;
	$date = date('Y-m-d');
	do {
		$ordersThisMonth2 = $orders_class->allByMonth($date);
		
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