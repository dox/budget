<?php
$query = trim((string) ($_GET['q'] ?? ''));
$orders = new Orders();
$results = $query !== '' ? $orders->search($query) : [];
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Order Search</h1>
</div>

<form method="get" action="index.php" class="mb-4">
	<input type="hidden" name="page" value="order_search">
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
	</div>
</form>

<?php if ($query === ''): ?>
	<div class="alert alert-info" role="alert">Enter a search term to find orders across every budget year.</div>
<?php else: ?>
	<div class="d-flex justify-content-between align-items-center mb-2">
		<h2 class="h5 mb-0">Results for &ldquo;<?= htmlspecialchars($query) ?>&rdquo;</h2>
		<span class="badge text-bg-secondary"><?= count($results) ?> found</span>
	</div>

	<div class="table-responsive small">
		<table class="table table-striped table-sm align-middle">
			<thead>
				<tr>
					<th scope="col">Date</th>
					<th scope="col">Budget Year</th>
					<th scope="col">PO</th>
					<th scope="col">Cost Centre</th>
					<th scope="col">Supplier</th>
					<th scope="col">Name</th>
					<th scope="col" class="text-end">Value</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($results as $order): ?>
					<?php
					$orderBudgetYear = BudgetYear::fromDate($order->date_created);
					$linkedCostCentre = $order->costCentreModel();
					$costCentreURL = $linkedCostCentre
						? 'index.php?page=cost_centre&id=' . $linkedCostCentre->id . '&year=' . $orderBudgetYear->year
						: '#';
					$costCentreLabel = $linkedCostCentre ? $linkedCostCentre->code : (string) $order->cost_centre;
					$supplierLabel = trim((string) ($order->supplier ?? '')) !== '' ? (string) $order->supplier : 'Unknown supplier';
					$poLabel = trim((string) ($order->po ?? '')) !== '' ? (string) $order->po : 'No PO';
					$orderName = trim((string) ($order->name ?? ''));
					?>
					<tr>
						<td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($order->date_created))) ?></td>
						<td><?= htmlspecialchars((string) $orderBudgetYear) ?></td>
						<td><a href="index.php?page=order&id=<?= (int) $order->id ?>"><?= htmlspecialchars($poLabel) ?></a></td>
						<td><a href="<?= htmlspecialchars($costCentreURL) ?>"><?= htmlspecialchars($costCentreLabel) ?></a></td>
						<td><?= htmlspecialchars($supplierLabel) ?></td>
						<td><a href="index.php?page=order&id=<?= (int) $order->id ?>"><?= htmlspecialchars($orderName !== '' ? $orderName : 'Order #' . $order->id) ?></a></td>
						<td class="text-end"><?= htmlspecialchars(formatMoney((float) $order->value)) ?></td>
					</tr>
				<?php endforeach; ?>

				<?php if ($results === []): ?>
					<tr>
						<td colspan="7" class="text-muted">No orders found across any budget year.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>
