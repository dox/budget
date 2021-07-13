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

$outstandingPayments = 0;
foreach ($ordersAll AS $order) {
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


<?php
$cost_centre_class = new class_cost_centres;
$cost_centres = $cost_centre_class->all();

$date = date('Y-m-d');

$i = 11;
do {
  $date = date('Y-m-d', strtotime($i . " month ago"));
  $monthNames[] = "'" . date('F', strtotime($date)) . "'";

  $i--;
} while ($i >= 0);

// itterate through each cost centre
$outputArray = null;
foreach ($cost_centres AS $costCentre) {
  $data = null;
  $outputMonth = null;

  $outputByCostCentre  = "{";
  $outputByCostCentre .= "label: '" . str_replace("'", "\'", $costCentre['name']) . "', ";
  $outputByCostCentre .= "backgroundColor: '" . $costCentre['colour'] . "99', ";

  $i = 11;
  $data = array();
  do {
    $date = date('Y-m-d', strtotime($i . " month ago"));
    $ordersByCostCentreByMonth = $orders_class->ordersTotalValueByCostCentreAndMonth($date, $costCentre['uid']);
    $data[] = round($ordersByCostCentreByMonth, 2);
    $i--;
  } while ($i >= 0);

  $outputByCostCentre .= "data: " . json_encode($data, JSON_NUMERIC_CHECK);
  $outputByCostCentre .= "}";
  $outputArray[] = $outputByCostCentre;
}

?>

<h2>Dashboard <small class="text-muted"><a href="index.php?n=dashboard&month=<?php echo $previousMonth;?>"><svg width="16" height="16"><use xlink:href="img/icons.svg#chevron-left"/></svg></a> <?php echo date('F, Y', strtotime($dateReference)); ?> <a href="index.php?n=dashboard&month=<?php echo $nextMonth;?>"><svg width="16" height="16"><use xlink:href="img/icons.svg#chevron-right"/></svg></a></small></h2>

<canvas id="canvas" width="400" height="100"></canvas>
<br />
<div class="row">
	<div class="col-sm">
		<?php
		$valueOfordersThisMonth = $orders_class->ordersTotalValueByMonth($dateReference);
		$valueOfordersPreviousMonth = $orders_class->ordersTotalValueByMonth($previousMonth);

		if ($valueOfordersThisMonth > 0 && $valueOfordersPreviousMonth > 0) {
			$percentageDifference = round((($valueOfordersThisMonth/$valueOfordersPreviousMonth) * 100)-100, 2);
		} else {
			$percentageDifference = 0;
		}

		if ($percentageDifference > 0) {
			$arrow = $percentageDifference . "% <svg width=\"16\" height=\"16\" class=\"float-end colour-red\"><use xlink:href=\"img/icons.svg#graph-up\"/></svg>";
		} elseif ($percentageDifference < 0) {
			$arrow = $percentageDifference . "% <svg width=\"16\" height=\"16\" class=\"float-end colour-green\"><use xlink:href=\"img/icons.svg#graph-down\"/></svg>";
		} else {
			$arrow = $percentageDifference . "% <svg width=\"16\" height=\"16\" class=\"float-end\"><use xlink:href=\"img/icons.svg#arrow-right-short\"/></svg>";
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
		$valueOfordersPreviousYear = $orders_class->ordersTotalValueByYear(date('Y-m-d', strtotime('1 year ago')));

		if ($valueOfordersThisYear > 0 && $valueOfordersPreviousYear > 0) {
			$percentageDifference = round((($valueOfordersThisYear/$valueOfordersPreviousYear) * 100)-100, 2);
		} else {
			$percentageDifference = 0;
		}

		if ($percentageDifference > 0) {
			$arrow = $percentageDifference . "% <svg width=\"16\" height=\"16\" class=\"float-end colour-red\"><use xlink:href=\"img/icons.svg#graph-up\"/></svg>";
		} elseif ($percentageDifference < 0) {
			$arrow = $percentageDifference . "% <svg width=\"16\" height=\"16\" class=\"float-end colour-green\"><use xlink:href=\"img/icons.svg#graph-down\"/></svg>";
		} else {
			$arrow = $percentageDifference . "% <svg width=\"16\" height=\"16\" class=\"float-end\"><use xlink:href=\"img/icons.svg#arrow-right-short\"/></svg>";
		}
		?>
		<div class="card card--red">
			<div class=\"clearfix\">
				<?php echo $arrow; ?>
				<h2 style="font-size: 20px;">&pound; <?php echo number_format($valueOfordersThisYear);?></h2>
			<div class="mt-1" style="color: #A7AEBB;">Outgoings This Budget Year</div>
		</div>
	</div>
	</div>
	<div class="col-sm">
		<div class="card card--green">
			<div class=\"clearfix\">
				<?php echo $arrow; ?>
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
			$output .= "<td scope=\"row\" style=\"width: 50px;\"><a href=\"index.php?n=costcentres_unique&uid=" . $cost_centre['uid'] . "\"><svg width=\"16\" height=\"16\" style=\"color: " . $cost_centre['colour'] . ";\"><use xlink:href=\"img/icons.svg#archive-fill\"/></svg></a></td>";
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

<script>
var ctx = document.getElementById('canvas').getContext('2d');

var chart = new Chart(ctx, {
  // The type of chart we want to create
  type: 'bar',

  // The data for our dataset
  data: {
    labels: [<?php echo implode(",", $monthNames); ?>],
    datasets:[
      <?php echo implode(",", $outputArray); ?>
    ]
  },

  // Configuration options go here
  options: {
    legend: {
      display: false
    },
    scales: {
      xAxes: [{
        stacked: true
      }],
      yAxes: [{
        ticks: {
          min: 0
        },
        stacked: true
      }]
    }
  }
});
</script>
