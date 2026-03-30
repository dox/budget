<?php
$budgetYear = BudgetYear::current();
$costCentres = CostCentre::all($budgetYear);
$orders = (new Orders())->allForBudgetYear($budgetYear);

$totalBudget = array_reduce(
	$costCentres,
	fn(float $carry, CostCentre $costCentre): float => $carry + $costCentre->budgetValue,
	0.0
);

$ytdSpend = array_reduce(
	$orders,
	fn(float $carry, Order $order): float => $carry + (float) $order->value,
	0.0
);

$today = new DateTime();
$ytdCutoff = clone $budgetYear->endDate;
$elapsedRatio = 1.0;

if ($today <= $budgetYear->startDate) {
	$ytdCutoff = clone $budgetYear->startDate;
	$elapsedRatio = 0.0;
} elseif ($today < $budgetYear->endDate) {
	$ytdCutoff = $today;

	$totalBudgetYearSeconds = max(
		1,
		$budgetYear->endDate->getTimestamp() - $budgetYear->startDate->getTimestamp()
	);
	$elapsedBudgetYearSeconds = max(
		0,
		$today->getTimestamp() - $budgetYear->startDate->getTimestamp()
	);

	$elapsedRatio = min(1, $elapsedBudgetYearSeconds / $totalBudgetYearSeconds);
}

$remainingBudget = $totalBudget - $ytdSpend;
$totalOrders = count($orders);

$spendByCostCentre = [];
foreach ($costCentres as $costCentre) {
	$spendByCostCentre[$costCentre->id] = 0.0;
}

foreach ($orders as $order) {
	foreach ($costCentres as $costCentre) {
		if ((int) $order->cost_centre === $costCentre->id) {
			$spendByCostCentre[$costCentre->id] += (float) $order->value;
			break;
		}
	}
}

$chartCostCentres = array_values(array_filter(
	$costCentres,
	fn(CostCentre $costCentre): bool => $costCentre->budgetValue > 0 || $spendByCostCentre[$costCentre->id] > 0
));

usort(
	$chartCostCentres,
	fn(CostCentre $a, CostCentre $b): int => strcmp($a->code, $b->code)
);

$chartLabels = array_map(
	fn(CostCentre $costCentre): string => $costCentre->code,
	$chartCostCentres
);

$chartBudgetData = array_map(
	fn(CostCentre $costCentre): float => round($costCentre->budgetValue, 2),
	$chartCostCentres
);

$chartActualData = array_map(
	fn(CostCentre $costCentre): float => round($spendByCostCentre[$costCentre->id], 2),
	$chartCostCentres
);

$recentOrders = array_slice($orders, 0, 5);
$pieChartData = [
	round(max($ytdSpend, 0), 2),
	round(max($remainingBudget, 0), 2),
];
?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
	<div class="col-md-3">
		<div class="card bg-success card-summary p-3">
			<div>Total Budget</div>
			<div class="fs-4"><?= htmlspecialchars(formatMoney($totalBudget)) ?></div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card bg-primary card-summary p-3">
			<div>YTD Spend</div>
			<div class="fs-4"><?= htmlspecialchars(formatMoney($ytdSpend)) ?></div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card bg-warning card-summary p-3">
			<div>Remaining Budget</div>
			<div class="fs-4"><?= htmlspecialchars(formatMoney($remainingBudget)) ?></div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card bg-purple card-summary p-3" style="background-color: #6f42c1;">
			<div>Total Orders</div>
			<div class="fs-4"><?= htmlspecialchars((string) $totalOrders) ?> Orders</div>
		</div>
	</div>
</div>

<!-- Main Dashboard -->
<div class="row">
	<!-- Cost Centre Table -->
	<div class="col-lg-6">
			<div class="card">
			<div class="card-body">
				<h5 class="card-title">Cost Centre Overview</h5>
				<p class="card-text">
					<table class="table table-striped mt-3">
						<thead>
							<tr>
								<th>Cost Centre</th>
								<th>Budget</th>
								<th>YTD Spend</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($chartCostCentres as $costCentre): ?>
								<?php
								$costCentreYtdBudget = $costCentre->budgetValue * $elapsedRatio;
								$costCentreYtdSpend = $spendByCostCentre[$costCentre->id] ?? 0.0;
								$isOverBudget = $costCentreYtdSpend > $costCentreYtdBudget;
								$statusClass = $isOverBudget ? 'text-bg-danger' : 'text-bg-success';
								$statusLabel = $isOverBudget ? 'Over budget' : 'Under budget';
								$costCentreUrl = 'index.php?page=cost_centre&id=' . $costCentre->id . '&year=' . $budgetYear->year;
								?>
								<tr>
									<td><a href="<?= htmlspecialchars($costCentreUrl) ?>"><?= htmlspecialchars($costCentre->code) ?></a></td>
									<td><?= htmlspecialchars(formatMoney($costCentre->budgetValue)) ?></td>
									<td><?= htmlspecialchars(formatMoney($costCentreYtdSpend)) ?></td>
									<td><span class="badge <?= $statusClass ?>"><?= htmlspecialchars($statusLabel) ?></span></td>
								</tr>
							<?php endforeach; ?>
							<?php if ($chartCostCentres === []): ?>
								<tr>
									<td colspan="4" class="text-muted">No budget or spend data yet for this budget year.</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</p>
			</div>
		</div>
	</div>
	
	<!-- Budget vs Actual Chart -->
	<div class="col-lg-6">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title">Budget vs Actual (Year to Date)</h5>
				<p class="card-text">
					<canvas id="budgetChart" height="200"></canvas>
				</p>
			</div>
		</div>
	</div>
</div>

<div class="row g-4 mt-1">
	<!-- Recent Orders -->
	<div class="col-lg-6">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title">Recent Orders</h5>
				<p class="card-text">
					<table class="table table-striped mt-3">
						<thead>
							<tr>
								<th>Order ID</th>
								<th>Description</th>
								<th>Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($recentOrders as $order): ?>
								<tr>
									<td><?= htmlspecialchars($order->order_num ?: ('#' . $order->id)) ?></td>
									<td><a href="index.php?page=order&id=<?= $order->id ?>"><?= strip_tags($order->name()) ?></a></td>
									<td><?= htmlspecialchars(formatMoney((float) $order->value)) ?></td>
								</tr>
							<?php endforeach; ?>
							<?php if ($recentOrders === []): ?>
								<tr>
									<td colspan="3" class="text-muted">No orders yet for this budget year.</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</p>
			</div>
		</div>
	</div>
	
	<!-- Budget Utilization Pie Chart -->
	<div class="col-lg-6">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title">Budget Utilisation</h5>
				<p class="card-text">
					<canvas id="pieChart" height="200"></canvas>
				</p>
			</div>
		</div>
	</div>
</div>

<script>
	  const budgetChartLabels = <?= json_encode($chartLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
	  const budgetChartBudgetData = <?= json_encode($chartBudgetData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
	  const budgetChartActualData = <?= json_encode($chartActualData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
	  const pieChartData = <?= json_encode($pieChartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

	  // Budget vs Actual Bar Chart
	  const ctx = document.getElementById('budgetChart').getContext('2d');
	  const budgetChart = new Chart(ctx, {
		type: 'bar',
		data: {
		  labels: budgetChartLabels,
		  datasets: [
			{ label: 'Budget', data: budgetChartBudgetData, backgroundColor: '#0d6efd' },
			{ label: 'Actual', data: budgetChartActualData, backgroundColor: '#198754' }
		  ]
		},
		options: { responsive: true, plugins: { legend: { position: 'top' } } }
	  });
  
	  // Budget Utilization Pie Chart
	  const pieCtx = document.getElementById('pieChart').getContext('2d');
	  const pieChart = new Chart(pieCtx, {
		type: 'pie',
		data: {
		  labels: ['Spent', 'Remaining'],
		  datasets: [{ data: pieChartData, backgroundColor: ['#0d6efd', '#198754'] }]
		},
		options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
	  });
</script>
