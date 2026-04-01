<?php
$currentBudgetYear = BudgetYear::current();
$budgetYear = BudgetYear::fromRequest();
$selectedYear = $budgetYear->year;

$orders = (new Orders())->allForBudgetYear($budgetYear);
$supplierSummaries = [];

foreach ($orders as $order) {
	$supplierName = trim((string) ($order->supplier ?? ''));
	if ($supplierName === '') {
		$supplierName = 'Unknown supplier';
	}

	if (!isset($supplierSummaries[$supplierName])) {
		$supplierSummaries[$supplierName] = [
			'name' => $supplierName,
			'total_spend' => 0.0,
			'order_count' => 0,
			'last_order_date' => null,
		];
	}

	$supplierSummaries[$supplierName]['total_spend'] += (float) $order->value;
	$supplierSummaries[$supplierName]['order_count']++;

	$orderTimestamp = strtotime((string) $order->date_created);
	$currentLastOrder = $supplierSummaries[$supplierName]['last_order_date'];

	if ($currentLastOrder === null || $orderTimestamp > strtotime((string) $currentLastOrder)) {
		$supplierSummaries[$supplierName]['last_order_date'] = $order->date_created;
	}
}

$supplierSummaries = array_values($supplierSummaries);

usort($supplierSummaries, function (array $a, array $b): int {
	$totalComparison = $b['total_spend'] <=> $a['total_spend'];
	if ($totalComparison !== 0) {
		return $totalComparison;
	}

	return strcmp($a['name'], $b['name']);
});

$yearOptions = BudgetYear::dropdownOptions();
$currentLabel = (new BudgetYear($selectedYear))->label();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Suppliers</h1>
	<div class="btn-toolbar mb-2 mb-md-0">
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

<h2>Suppliers (<?= htmlspecialchars((string) $budgetYear) ?>)</h2>
<div class="table-responsive small">
	<table class="table table-striped table-sm">
		<thead>
			<tr>
				<th scope="col">Supplier</th>
				<th scope="col" class="text-end">Orders</th>
				<th scope="col" class="text-end">Total Spend</th>
				<th scope="col">Last Order</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($supplierSummaries as $supplier): ?>
				<?php
				$supplierName = trim((string) $supplier['name']);
				$supplierUrl = $supplierName !== '' ? 'index.php?page=supplier&name=' . urlencode($supplierName) : null;
				?>
				<tr>
					<td>
						<?php if ($supplierUrl): ?>
							<a href="<?= htmlspecialchars($supplierUrl) ?>"><?= htmlspecialchars($supplierName) ?></a>
						<?php else: ?>
							<?= htmlspecialchars($supplierName) ?>
						<?php endif; ?>
					</td>
					<td class="text-end"><?= htmlspecialchars((string) $supplier['order_count']) ?></td>
					<td class="text-end"><?= htmlspecialchars(formatMoney((float) $supplier['total_spend'])) ?></td>
					<td><?= $supplier['last_order_date'] ? htmlspecialchars(date('Y-m-d', strtotime((string) $supplier['last_order_date']))) : 'N/A' ?></td>
				</tr>
			<?php endforeach; ?>
			<?php if ($supplierSummaries === []): ?>
				<tr>
					<td colspan="4" class="text-muted">No supplier spend found for this budget year.</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
