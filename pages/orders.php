<?php
$currentBudgetYear = BudgetYear::current();
$budgetYear = BudgetYear::fromRequest();
$selectedYear = $budgetYear->year;
$status = $_GET['status'] ?? null;

$orders = new Orders();
$ordersAll = $orders->allForBudgetYear($budgetYear);
$chartOrders = $orders->all();

$yearOptions = BudgetYear::dropdownOptions();
$currentLabel = (new BudgetYear($selectedYear))->label();

$chartMonthLabels = [];
$chartMonthKeys = [];
$chartStartMonth = new DateTime('first day of this month 00:00:00');
$chartStartMonth->modify('-11 months');

for ($monthIndex = 0; $monthIndex < 12; $monthIndex++) {
	$monthDate = (clone $chartStartMonth)->modify('+' . $monthIndex . ' months');
	$chartMonthKeys[] = $monthDate->format('Y-m');
	$chartMonthLabels[] = $monthDate->format('M Y');
}

$spendByCostCentreAndMonth = [];
$costCentreLabelsById = [];

foreach ($chartOrders as $order) {
	$orderDate = new DateTime($order->date_created);
	$monthKey = $orderDate->format('Y-m');

	if (!in_array($monthKey, $chartMonthKeys, true)) {
		continue;
	}

	$costCentreId = (int) $order->cost_centre;
	if ($costCentreId <= 0) {
		continue;
	}

	$spendByCostCentreAndMonth[$costCentreId] ??= array_fill_keys($chartMonthKeys, 0.0);
	$spendByCostCentreAndMonth[$costCentreId][$monthKey] += (float) $order->value;

	if (!isset($costCentreLabelsById[$costCentreId])) {
		$linkedCostCentre = $order->costCentreModel();
		$costCentreLabelsById[$costCentreId] = $linkedCostCentre
			? $linkedCostCentre->code
			: 'Cost centre #' . $costCentreId;
	}
}

asort($costCentreLabelsById);

$chartPalette = [
	'#0d6efd',
	'#198754',
	'#fd7e14',
	'#dc3545',
	'#6f42c1',
	'#20c997',
	'#ffc107',
	'#0dcaf0',
	'#6610f2',
	'#adb5bd',
	'#6c757d',
	'#1982c4',
];

$stackedSpendDatasets = [];
$datasetIndex = 0;

foreach ($costCentreLabelsById as $costCentreId => $costCentreLabel) {
	$monthSpend = $spendByCostCentreAndMonth[$costCentreId] ?? array_fill_keys($chartMonthKeys, 0.0);
	$stackedSpendDatasets[] = [
		'label' => $costCentreLabel,
		'data' => array_map(
			fn(string $monthKey): float => round((float) ($monthSpend[$monthKey] ?? 0.0), 2),
			$chartMonthKeys
		),
		'backgroundColor' => $chartPalette[$datasetIndex % count($chartPalette)],
	];
	$datasetIndex++;
}
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

<div class="w-100 mb-4" style="height: 320px;">
  <canvas id="myChart"></canvas>
</div>

<h2>Orders (<?= htmlspecialchars((string) $budgetYear) ?>)</h2>
<div class="table-responsive small">
	
	<table class="table table-striped table-sm">
		<thead>
			<tr>
				<th scope="col">Date</th>
				<th scope="col">Cost Centre</th>
				<th scope="col">Supplier</th>
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
				$supplierLabel = trim((string) ($order->supplier ?? '')) !== '' ? (string) $order->supplier : 'Unknown supplier';
				$orderURL = "index.php?page=order&id=" . $order->id;
				
				$output  = "<tr>";
				$output .= "<td>" . date("Y-m-d H:i", strtotime($order->date_created)) . "</td>";
				$output .= "<td><a href=\"" . $costCentreURL . "\">" . htmlspecialchars($costCentreLabel) . "</a></td>";
				$output .= "<td>" . htmlspecialchars($supplierLabel) . "</td>";
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
const spendChartLabels = <?= json_encode($chartMonthLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
const spendChartDatasets = <?= json_encode($stackedSpendDatasets, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

const config = {
  type: 'bar',
  data: {
	labels: spendChartLabels,
	datasets: spendChartDatasets
  },
  options: {
	plugins: {
	  title: {
		display: false
	  },
	  legend: {
		position: 'top'
	  }
	},
	responsive: true,
	maintainAspectRatio: false,
	scales: {
	  x: {
		stacked: true
	  },
	  y: {
		stacked: true,
		beginAtZero: true,
		title: {
			display: true,
			text: 'Spend (£)'
		}
	  }
	}
  }
};

new Chart(
  document.getElementById('myChart'),
  config
);
</script>
