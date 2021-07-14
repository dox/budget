<?php
$orders_class = new class_orders;
$ordersAll = $orders_class->all();

$ordersThisMonth = $orders_class->all($dateReference);



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
    $ordersByCostCentreByMonth = $orders_class->totalOrdersByCostCentreAndMonth($date, $costCentre['uid']);
    $data[] = round($ordersByCostCentreByMonth, 2);
    $i--;
  } while ($i >= 0);

  $outputByCostCentre .= "data: " . json_encode($data, JSON_NUMERIC_CHECK);
  $outputByCostCentre .= "}";
  $outputArray[] = $outputByCostCentre;
}

?>

<h2>Dashboard</h2>

<canvas id="canvas" width="400" height="100"></canvas>
<br />
<div class="row">
	<div class="col-sm mb-3">
		<div class="card card--blue">
			<?php echo $arrow; ?>
			<h2 >&pound;<?php echo number_format($orders_class->totalOrdersByMonth(date('Y-m')));?></h2>
			<small class="text-muted">Outgoings This Month</small>
		</div>
	</div>
	<div class="col-sm mb-3">
		<div class="card card--red">
			<?php echo $arrow; ?>
			<h2 >&pound;<?php echo number_format($orders_class->totalOrdersByYear());?></h2>
			<small class="text-muted">Outgoings This Budget Year</small>
		</div>
	</div>
	<div class="col-sm mb-3">
		<div class="card card--green">
			<?php echo $arrow; ?>
			<h2 >&pound;<?php echo number_format($orders_class->totalUnpaidOrders());?></h2>
			<small class="text-muted">Total Unpaid Orders</small>
		</div>
	</div>
</div>

<?php
echo $cost_centre_class->summaryTable(date('Y-m-d'));
?>

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
