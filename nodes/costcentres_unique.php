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

<div class="row row-deck row-cards mb-3">
  <div class="col-12 col-sm-12 col-lg-8 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="subheader">
          Breakdown by supplier
        </div>
				<canvas id="canvasSupplier" width="400" height="100"></canvas>
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

<script>
<?php
foreach ($costCentreObject->spendBySupplier() AS $supplier => $value) {
	$supplier = str_replace("'" , "", $supplier);
	$output  = "{";
	$output .= "label: '" . $supplier . "',";
	$output .= "data: [" . $value . "],";
	$output .= "backgroundColor: '" . textToRGB($supplier) . "'";
	$output .= "}";

	$datasets["'" . $supplier . "'"] = $output;
}
?>
const supplierLabels = ['Spend'];
const supplierData = {
  labels: supplierLabels,
  datasets: [<?php echo implode(", ", $datasets);?>],
};

const supplierConfig = {
	type: 'bar',
	data: supplierData,
	options: {
		indexAxis: 'y',
		plugins: {
			legend: {
				display: true
			}
		},
		scales: {
			x: {
				display: false,
				stacked: true,
			},
			y: {
				display: false,
				stacked: true,

			}
		}
	}
};

var supplierChart = new Chart(
	document.getElementById('canvasSupplier'),
	supplierConfig
);
</script>
