<?php
$budgetYear = BudgetYear::fromRequest();
$selectedYear = $budgetYear->year;
$status = $_GET['status'] ?? null;
$query = trim((string) ($_GET['q'] ?? ''));
$isLegacySearchPage = ($_GET['page'] ?? '') === 'order_search';
$isSearch = $query !== '' || $isLegacySearchPage;

$orders = new Orders();
$ordersAll = $isSearch ? ($query !== '' ? $orders->search($query) : []) : $orders->allForBudgetYear($budgetYear);
$chartOrders = $isSearch ? [] : $orders->all();

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
	<h1 class="h2"><?= $isSearch ? 'Order Search' : 'Orders' ?></h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group me-2">
			<a href="index.php?page=order_addedit&action=add" class="btn btn-sm btn-outline-secondary"><i class="bi bi-plus-circle" aria-hidden="true"></i> New</a>
		</div>
		<?php if (!$isSearch): ?>
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
		<?php endif; ?>
	</div>
</div>

<?php if ($status === 'deleted'): ?>
	<div class="alert alert-success" role="alert">Order deleted.</div>
<?php elseif ($status === 'error'): ?>
	<div class="alert alert-danger" role="alert">Unable to complete that order action.</div>
<?php endif; ?>

<form method="get" action="index.php" class="mb-4">
	<input type="hidden" name="page" value="orders">
	<label for="order_search_page" class="form-label">Search all orders</label>
	<div class="input-group">
		<input
			type="search"
			class="form-control"
			id="order_search_page"
			name="q"
			value="<?= htmlspecialchars($query) ?>"
			placeholder="PO, supplier, item, notes, value..."
			aria-label="Search all orders"
		>
		<button class="btn btn-outline-secondary" type="submit">
			<i class="bi bi-search" aria-hidden="true"></i>
			<span class="visually-hidden">Search</span>
		</button>
		<?php if ($isSearch): ?>
			<a href="index.php?page=orders" class="btn btn-outline-secondary">Clear</a>
		<?php endif; ?>
	</div>
</form>

<?php if (!$isSearch): ?>
	<div class="w-100 mb-4" style="height: 320px;">
	  <canvas id="myChart"></canvas>
	</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-2">
	<h2 class="<?= $isSearch ? 'h5' : '' ?> mb-0">
		<?php if ($isSearch): ?>
			<?= $query !== '' ? 'Results for &ldquo;' . htmlspecialchars($query) . '&rdquo;' : 'Search all orders' ?>
		<?php else: ?>
			Orders (<?= htmlspecialchars((string) $budgetYear) ?>)
		<?php endif; ?>
	</h2>
	<?php if ($isSearch && $query !== ''): ?>
		<span class="badge text-bg-secondary"><?= count($ordersAll) ?> found</span>
	<?php endif; ?>
</div>
<?php if ($isSearch && $query === ''): ?>
	<div class="alert alert-info" role="alert">Enter a search term to find orders across every budget year.</div>
<?php else: ?>
<div class="table-responsive small">
	
	<table class="table table-striped table-sm align-middle">
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
			<?php foreach ($ordersAll AS $order): ?>
				<?php
				$orderBudgetYear = BudgetYear::fromDate($order->date_created);
				$linkedCostCentre = $order->costCentreModel();
				$costCentreURL = $linkedCostCentre
					? "index.php?page=cost_centre&id=" . $linkedCostCentre->id . "&year=" . $orderBudgetYear->year
					: '#';
				$costCentreLabel = $linkedCostCentre ? $linkedCostCentre->code : (string) $order->cost_centre;
				$supplierLabel = trim((string) ($order->supplier ?? '')) !== '' ? (string) $order->supplier : 'Unknown supplier';
				$orderURL = "index.php?page=order&id=" . $order->id;
				?>
				<tr>
					<td><?= htmlspecialchars(date("Y-m-d H:i", strtotime($order->date_created))) ?></td>
					<td><a href="<?= htmlspecialchars($costCentreURL) ?>"><?= htmlspecialchars($costCentreLabel) ?></a></td>
					<td><?= htmlspecialchars($supplierLabel) ?></td>
					<td>
						<a href="<?= htmlspecialchars($orderURL) ?>">
							<?= $order->name() ?>
						</a>
					</td>
					<td><?= htmlspecialchars(formatMoney((float) $order->value)) ?></td>
					<td>
						<div class="action-icons">
							<a href="index.php?page=order_addedit&action=edit&id=<?= (int) $order->id ?>"><i class="bi bi-pencil"></i></a>
							<a href="index.php?page=order_addedit&action=clone&id=<?= (int) $order->id ?>"><i class="bi bi-copy"></i></a>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>

			<?php if ($ordersAll === []): ?>
				<tr>
					<td colspan="6" class="text-muted">
						<?= $isSearch ? 'No orders found across any budget year.' : 'No orders for this budget year.' ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<?php endif; ?>

<?php if (!$isSearch): ?>
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
<?php endif; ?>
