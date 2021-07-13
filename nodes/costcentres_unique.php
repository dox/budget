<?php
$cost_centre = new cost_centre($_GET['uid']);



$orders_class = new class_orders;

if (!$cost_centre->department == $_SESSION['department']) {
	echo "You have tried to access a cost centre that you are not authorised to view";
	exit;
}
?>

<h2><?php echo $cost_centre->name;?> <small class="text-muted"><?php echo $cost_centre->grouping;?></small></h2>

<?php
$budgetTotal = $cost_centre->yearlyBudget();

$ordersArray["'" . budgetEndDate() . "'"] = $budgetTotal;

foreach ($cost_centre->yearlyOrders() as $order) {
	$budgetTotal = $budgetTotal - $order['value'];

	$ordersArray["'" . $order['date'] . "'"] = $budgetTotal;
}
?>

<canvas id="canvas" width="400" height="100"></canvas>
<script>
	var timeFormat = 'YYYY/MM/DD';

	var config = {
		type: 'line',
		data: {
			labels: [<?php echo implode(", ", array_keys($ordersArray));?>],
			datasets: [{
				label: '£',
				borderColor: "<?php echo $cost_centre->colour;?>",
				backgroundColor: "<?php echo $cost_centre->colour;?>30",
				fill: true,
				data: [<?php echo implode(",", $ordersArray); ?>]
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
						display: false,
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

<div class="row row-deck row-cards mb-3">
  <div class="col-12 col-sm-12 col-lg-4 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="subheader">
          Breakdown by supplier
        </div>
        <div class="h1 mb-3">
          <?php

          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-12 col-lg-4 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="subheader">
          Other
        </div>
        <div class="h1 mb-3">
          <?php

          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-12 col-lg-4 mb-3">
    <div class="card">
      <div class="card-body">
				<h1 class="card-title pricing-card-title"><?php echo "£" . number_format($cost_centre->yearlyBudget()); ?></h1>
        <div class="subheader">
          <h5 class="text-muted fw-light">£<?php echo number_format($cost_centre->yearlySpend()); ?> spent / £<?php echo number_format($cost_centre->yearlyRemaining()); ?> remaining</h5>
        </div>
        <div class="h1 mb-3">
          <?php

          ?>
        </div>
      </div>
    </div>
  </div>
</div>
<hr />

<?php
echo $orders_class->table($cost_centre->yearlyOrders());
?>
