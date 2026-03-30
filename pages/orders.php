<?php
$currentBudgetYear = BudgetYear::current();
$budgetYear = BudgetYear::fromRequest();
$selectedYear = $budgetYear->year;
$status = $_GET['status'] ?? null;

$orders = new Orders();
$ordersAll = $orders->allForBudgetYear($budgetYear);

$yearOptions = [];
for ($year = $currentBudgetYear->year; $year >= $currentBudgetYear->year - 4; $year--) {
	$optionYear = new BudgetYear($year);
	$yearOptions[] = [
		'label' => $optionYear->label(),
		'year' => $optionYear->year,
	];
}

$currentLabel = (new BudgetYear($selectedYear))->label();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Orders</h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group me-2">
			<a href="index.php?page=order_addedit&action=add" class="btn btn-sm btn-outline-secondary"><i class="bi bi-plus-circle" aria-hidden="true"></i> New</a>
		</div>
		<div class="dropdown">
			<button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="bi bi-calendar3" aria-hidden="true"></i> <?= htmlspecialchars($currentLabel) ?>
			</button>
			<ul class="dropdown-menu dropdown-menu-end">
				<?php foreach ($yearOptions as $option): ?>
					<li>
						<form method="post" class="dropdown-item p-0">
							<input type="hidden" name="year" value="<?= $option['year'] ?>">
							<button type="submit" class="btn btn-link w-100 text-start px-3 py-1">
								<?= htmlspecialchars($option['label']) ?>
							</button>
						</form>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>

<?php if ($status === 'deleted'): ?>
	<div class="alert alert-success" role="alert">Order deleted.</div>
<?php elseif ($status === 'error'): ?>
	<div class="alert alert-danger" role="alert">Unable to complete that order action.</div>
<?php endif; ?>

<div>
  <canvas id="myChart"></canvas>
</div>

<h2>Orders (<?= htmlspecialchars((string) $budgetYear) ?>)</h2>
<div class="table-responsive small">
	
	<table class="table table-striped table-sm">
		<thead>
			<tr>
				<th scope="col">Date</th>
				<th scope="col">Cost Centre</th>
				<th scope="col">Name</th>
				<th scope="col">Value</th>
				<th scope="col"></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($ordersAll AS $order) {
				$orderBudgetYear = BudgetYear::fromDate($order->date_created);
				$linkedCostCentre = $order->costCentreModel();
				$costCentreURL = $linkedCostCentre
					? "index.php?page=cost_centre&id=" . $linkedCostCentre->id . "&year=" . $orderBudgetYear->year
					: '#';
				$costCentreLabel = $linkedCostCentre ? $linkedCostCentre->code : (string) $order->cost_centre;
				$orderURL = "index.php?page=order&id=" . $order->id;
				
				$output  = "<tr>";
				$output .= "<td>" . date("Y-m-d H:i", strtotime($order->date_created)) . "</td>";
				$output .= "<td><a href=\"" . $costCentreURL . "\">" . htmlspecialchars($costCentreLabel) . "</a></td>";
				$output .= "<td><a href=\"" . $orderURL . "\"><strong>" . $order->name() . "</a></td>";
				$output .= "<td>" . formatMoney($order->value) . "</td>";
				$output .= "<td>
					<div class=\"action-icons\">
						<a href=\"index.php?page=order_addedit&action=edit&id=" . $order->id . "\"><i class=\"bi bi-pencil\"></i></a>
						<a href=\"index.php?page=order_addedit&action=clone&id=" . $order->id . "\"><i class=\"bi bi-copy\"></i></a>
					</div>
				</td>";
				$output .= "";
				$output .= "</tr>";
				
				echo $output;
			}
			?>
		</tbody>
	</table>
</div>

<script>
// ==== Data ====
const data = {
  labels: ['January', 'February', 'March', 'April', 'May'],
  datasets: [
	{
	  label: 'Apples',
	  data: [12, 19, 3, 5, 2],
	},
	{
	  label: 'Bananas',
	  data: [2, 3, 20, 5, 1],
	},
	{
	  label: 'Cherries',
	  data: [3, 10, 13, 15, 22],
	}
  ]
};

// ==== Config ====
const config = {
  type: 'bar',
  data: data,
  options: {
	plugins: {
	  title: {
		display: false,
		text: 'Fruit Sales (Stacked Bar)'
	  },
	  legend: {
		position: 'top'
	  }
	},
	responsive: true,
	scales: {
	  x: {
		stacked: true
	  },
	  y: {
		stacked: true,
		beginAtZero: true
	  }
	}
  }
};

// ==== Render ====
new Chart(
  document.getElementById('myChart'),
  config
);
</script>
