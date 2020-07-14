<?php
if (isset($_GET['month'])) {
	$dateReference = $_GET['month'];
	$thisMonthText = "Outgoings For " . date('F, Y', strtotime($dateReference));
} else {
	$dateReference = date('Y-m-d');
	$thisMonthText = "Outgoings This Month";
}
$previousMonth = date('Y-m-d', strtotime($dateReference . ' -1 month'));
$nextMonth = date('Y-m-d', strtotime($dateReference . ' +1 month'));

$orders_class = new class_orders;
$ordersAll = $orders_class->all();

$ordersThisMonth = $orders_class->all($dateReference);

$YTDTotalSpend = 0;
$outstandingPayments = 0;
foreach ($ordersAll AS $order) {

	if (date('Y-m',strtotime($order['date'])) == date('Y-m')) {
		$YTDTotalSpend = $YTDTotalSpend + $order['value'];
	} else {
		$YTDTotalSpend = $YTDTotalSpend + $order['value'];
	}

	if (empty($order['paid'])) {
		$outstandingPayments = $outstandingPayments + $order['value'];
	}
}

foreach ($ordersThisMonth AS $order) {
	if (date('Y-m',strtotime($order['date'])) == date('Y-m', strtotime($dateReference))) {
		if (isset($monthlyOrdersTotalArray[$order['cost_centre']])) {
			$monthlyOrdersTotalArray[$order['cost_centre']] = $monthlyOrdersTotalArray[$order['cost_centre']] + $order['value'];
		} else {
			$monthlyOrdersTotalArray[$order['cost_centre']] = $order['value'];
		}
	}
}

if (empty($monthlyOrdersTotalArray)){
	$monthlyOrdersTotalArray = array();

}
$totalSpendMonthly = array_sum($monthlyOrdersTotalArray);
?>

<h2>Dashboard <small class="text-muted"><a href="index.php?n=dashboard&month=<?php echo $previousMonth;?>"><i class="fas fa-chevron-left"></i></a> <?php echo date('F, Y', strtotime($dateReference)); ?> <a href="index.php?n=dashboard&month=<?php echo $nextMonth;?>"><i class="fas fa-chevron-right"></i></a></small></h2>


<canvas id="myChart" width="400" height="100"></canvas>
<br />
<div class="row">
	<div class="col-sm">
		<?php
		$valueOfordersThisMonth = $orders_class->ordersTotalValueByMonth($dateReference);
		$valueOfordersPreviousMonth = $orders_class->ordersTotalValueByMonth($previousMonth);
		$percentageDifference = round((($valueOfordersThisMonth/$valueOfordersPreviousMonth) * 100)-100, 2);

		if ($percentageDifference > 0) {
			$arrow = "<div class=\"float-right\">" . $percentageDifference . "% <i class=\"fas fa-long-arrow-alt-up color-red\"></i></div>";
		} elseif ($percentageDifference < 0) {
			$arrow = "<div class=\"float-right\">" . $percentageDifference . "% <i class=\"fas fa-long-arrow-alt-down color-green\"></i></div>";
		} else {
			$arrow = "<div class=\"float-right\">" . $percentageDifference . "% <i class=\"fas fa-long-arrow-alt-right\"></i></div>";
		}
		?>
		<div class="card card--blue">
			<div class=\"clearfix\">
				<?php echo $arrow; ?>
				<h2 style="font-size: 20px;">&pound; <?php echo number_format($totalSpendMonthly);?></h2>
				<div class="mt-1" style="color: #A7AEBB;"><?php echo $thisMonthText; ?></div>
			</div>
		</div>
	</div>
	<div class="col-sm">
		<?php
		// this needs fixing to include the whole of the budget year!
		$valueOfordersThisYear = $orders_class->ordersTotalValueByYear(date('Y'));
		$valueOfordersPreviousYear = $orders_class->ordersTotalValueByYear(date('Y')-1);
		$percentageDifference = round((($valueOfordersThisMonth/$valueOfordersPreviousYear) * 100)-100, 2);

		if ($percentageDifference > 0) {
			$arrow = "<div class=\"float-right\">" . $percentageDifference . "% <i class=\"fas fa-long-arrow-alt-up color-red\"></i></div>";
		} elseif ($percentageDifference < 0) {
			$arrow = "<div class=\"float-right\">" . $percentageDifference . "% <i class=\"fas fa-long-arrow-alt-down color-green\"></i></div>";
		} else {
			$arrow = "<div class=\"float-right\">" . $percentageDifference . "% <i class=\"fas fa-long-arrow-alt-right\"></i></div>";
		}
		?>
		<div class="card card--red">
			<div class=\"clearfix\">
				<?php echo $arrow; ?>
			<h2 style="font-size: 20px;">&pound; <?php echo number_format($YTDTotalSpend);?></h2>
			<div class="mt-1" style="color: #A7AEBB;">Outgoings This Budget Year</div>
		</div>
	</div>
	</div>
	<div class="col-sm">
		<div class="card card--green">
			<div class=\"clearfix\">
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
			<th scope="col" style="width: 50px;"></th>
			<th scope="col" style="width: 120px;">Code</th>
			<th scope="col">Name</th>
			<th scope="col" style="width: 110px;">Value</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($monthlyOrdersTotalArray AS $ordersTotal => $value) {
			$cost_centre_class = new class_cost_centres;
			$cost_centre = $cost_centre_class->getOne($ordersTotal);

			$output  = "<tr>";
			$output .= "<td scope=\"row\" style=\"width: 50px;\"><div style=\"width: 15px; height: 15px; border-radius: 2px; background: " . $cost_centre['colour'] . ";\"></div></td>";
			$output .= "<td><a href=\"index.php?n=costcentres_unique&uid=" . $cost_centre['uid'] . "\">" . $cost_centre['code'] . "</td>";
			$output .= "<td>" . $cost_centre['name'] . "</td>";
			if ($value < 0) {
				$output .= "<td class=\"text-right color-green\">£" . number_format($value) . " <i class=\"fas fa-long-arrow-alt-left fa-sm\"></i></td>";
			} else {
				$output .= "<td class=\"text-right color-red\">£" . number_format($value) . " <i class=\"fas fa-long-arrow-alt-right fa-sm\"></i></td>";
			}


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
                },
                scaleLabel: {
					display: false,
					labelString: '£'
				}
            }]
        },
        legend: {
			display: false
		}
    }
});
</script>
