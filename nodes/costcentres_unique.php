<?php
$costCentreObject = new cost_centre($_GET['uid']);

if (!$costCentreObject->department == $_SESSION['department']) {
	echo "You have tried to access a cost centre that you are not authorised to view";
	exit;
}
?>

<h2><?php echo $costCentreObject->name;?> <small class="text-muted"><?php echo $costCentreObject->grouping;?></small></h2>

<canvas id="canvas" width="400" height="100"></canvas>
<script>
	var timeFormat = 'YYYY/MM/DD';

	var config = {
		type: 'line',
		data: {
			labels: [<?php echo implode(", ", array_keys($costCentreObject->yearlySpendSummary()));?>],
			datasets: [{
				label: '£',
				borderColor: "<?php echo $costCentreObject->colour;?>",
				backgroundColor: "<?php echo $costCentreObject->colour;?>30",
				fill: true,
				data: [<?php echo implode(",", $costCentreObject->yearlySpendSummary()); ?>]
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
				<h1 class="card-title pricing-card-title"><?php echo "£" . number_format($costCentreObject->yearlyBudget()); ?></h1>
        <div class="subheader">
          <h5 class="text-muted fw-light">£<?php echo number_format($costCentreObject->yearlySpend()); ?> spent / £<?php echo number_format($costCentreObject->yearlyRemaining()); ?> remaining</h5>
        </div>
      </div>
    </div>
  </div>
</div>
<hr />

<?php
echo class_orders::table($costCentreObject->yearlyOrders());
?>
