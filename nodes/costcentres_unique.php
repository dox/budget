<?php
$costCentreObject = new cost_centre($_GET['uid']);

if (!$costCentreObject->department == $_SESSION['department']) {
	echo "You have tried to access a cost centre that you are not authorised to view";
	exit;
}
?>

<h2><?php echo $costCentreObject->name;?> <small class="text-muted"><?php echo $costCentreObject->grouping;?></small></h2>

<canvas id="canvas" width="400" height="100" class="mb-3"></canvas>

<script>
const labels = [<?php echo implode(", ", array_keys($costCentreObject->yearlySpendSummary()));?>];
const data = {
  labels: labels,
  datasets: [{
    label: 'Budget Remaining',
    backgroundColor: '<?php echo $costCentreObject->colour;?>30',
    borderColor: '<?php echo $costCentreObject->colour;?>',
		fill: 'origin',
    data: [<?php echo implode(",", $costCentreObject->yearlySpendSummary()); ?>],
  }],
};

const config = {
	type: 'line',
	data: data,
	options: {
		plugins: {
			legend: {
				display: false
			}
		},
    scales: {
      x: {
        type: 'time',
        time: {
          unit: 'month'
        }
      }
    }
	}
};

var costCentreChart = new Chart(
	document.getElementById('canvas'),
	config
);
</script>

<div class="row mb-3">
	<div class="col">
		<div class="card">
			<div class="card-body">
				<div class="subheader">
					Breakdown by supplier
				</div>
				<div class="progress" style="height: 70px;">
					<?php
					$otherValue = 0;
					$otherWidth = 0;
					$output = "";

					foreach ($costCentreObject->spendBySupplier() AS $supplier => $value) {
						$width = ($value / $costCentreObject->yearlySpend()) * 100;

						$supplierURL .= "index.php?n=suppliers_unique&name=" . urlencode($supplier);

						$name  = "<span><a href=\"" . $supplierURL . "\">" . $supplier . "</a> " . number_format($width, 0) . "%</span>";
						$name .= "£" . number_format($value, 0) . "</span>";

						if ($width >= 6) {
							$output .= "<div class=\"progress-bar\" role=\"progressbar\" style=\"width: " . $width . "%; background-color:" . textToRGB($supplier) . " !important;\" aria-valuenow=\"" . $width . "\" aria-valuemin=\"0\" aria-valuemax=\"100\">" . $name . "</div>";
						} else {
							$otherValue = $otherValue + $value;
							$otherWidth = $otherWidth + $width;
						}
					}

					if ($otherWidth > 0) {
						$otherName  = "<span>Other " . number_format($width, 0) . "%</span>";
						$otherName .= "£" . number_format($otherValue, 0);

						$output .= "<div class=\"progress-bar progress-bar-striped bg-light text-dark\" role=\"progressbar\" style=\"width: " . $otherWidth . "%;\" aria-valuenow=\"" . $otherWidth . "\" aria-valuemin=\"0\" aria-valuemax=\"100\">" . $otherName . "</div>";
					}

					echo $output;
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12 col-sm-12 col-lg-4 mb-3">
		<div class="card">
      <div class="card-body">
				<h1 class="card-title pricing-card-title"><?php echo "£" . number_format($costCentreObject->yearlyBudget()); ?></h1>
        <div class="subheader">
          <h5 class="text-muted fw-light">Yearly Budget</h5>
        </div>
			</div>
		</div>
  </div>
	<div class="col-12 col-sm-12 col-lg-4 mb-3">
		<div class="card">
      <div class="card-body">
				<h1 class="card-title pricing-card-title"><?php echo "£" . number_format($costCentreObject->yearlySpend()); ?></h1>
        <div class="subheader">
          <h5 class="text-muted fw-light">Yearly Spend</h5>
        </div>
			</div>
		</div>
  </div>
  <div class="col-12 col-sm-12 col-lg-4 mb-3">
    <div class="card">
      <div class="card-body">
				<?php
				if ($costCentreObject->yearlyRemaining() < 0) {
					$class = " text-danger";
				}
				?>
				<h1 class="card-title pricing-card-title <?php echo $class; ?>"><?php echo "£" . number_format($costCentreObject->yearlyRemaining()); ?></h1>
        <div class="subheader">
          <h5 class="text-muted fw-light">Yearly Remaining</h5>
        </div>
      </div>
    </div>
  </div>
</div>
<hr />

<?php
echo class_orders::table($costCentreObject->yearlyOrders());
?>
