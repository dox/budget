<?php
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null) {
	die('Invalid cost centre ID.');
}

$budgetYear = BudgetYear::fromRequest();
$costCentre = new CostCentre($id, $budgetYear);
$orders = (new Orders())->forCostCentre($budgetYear, $costCentre);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2"><?= htmlspecialchars($costCentre->code) ?> <?= htmlspecialchars($costCentre->name) ?></h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group me-2">
			<a href="index.php?page=cost_centres&year=<?= $budgetYear->year ?>" class="btn btn-sm btn-outline-secondary">Back to Cost Centres</a>
			<a href="index.php?page=orders&year=<?= $budgetYear->year ?>" class="btn btn-sm btn-outline-secondary">All Orders</a>
		</div>
	</div>
</div>

<p class="text-muted mb-4">
	Budget year: <?= htmlspecialchars((string) $budgetYear) ?>
	<?php if ($costCentre->description): ?>
		<br><?= htmlspecialchars($costCentre->description) ?>
	<?php endif; ?>
</p>

<div class="row g-3 mb-4">
	<div class="col-md-4">
		<div class="card p-3">
			<div class="text-muted small">Budget</div>
			<div class="fs-4"><?= htmlspecialchars(formatMoney($costCentre->budgetValue)) ?></div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card p-3">
			<div class="text-muted small">Orders</div>
			<div class="fs-4"><?= count($orders) ?></div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card p-3">
			<div class="text-muted small">Spend</div>
			<div class="fs-4"><?= htmlspecialchars(formatMoney(array_reduce($orders, fn(float $carry, Order $order): float => $carry + (float) $order->value, 0.0))) ?></div>
		</div>
	</div>
</div>

<div class="table-responsive small">
	<table class="table table-striped table-sm">
		<thead>
			<tr>
				<th scope="col">Date</th>
				<th scope="col">Order</th>
				<th scope="col">Supplier</th>
				<th scope="col">Value</th>
				<th scope="col"></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($orders as $order): ?>
				<tr>
					<td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($order->date_created))) ?></td>
					<td><a href="index.php?page=order&id=<?= $order->id ?>"><?= strip_tags($order->name()) ?></a></td>
					<td><?= htmlspecialchars((string) $order->supplier) ?></td>
					<td><?= htmlspecialchars(formatMoney((float) $order->value)) ?></td>
					<td>
						<div class="action-icons">
							<a href="index.php?page=order_addedit&action=edit&id=<?= $order->id ?>"><i class="bi bi-pencil"></i></a>
							<a href="index.php?page=order_addedit&action=clone&id=<?= $order->id ?>"><i class="bi bi-copy"></i></a>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
			<?php if ($orders === []): ?>
				<tr>
					<td colspan="5" class="text-muted">No orders for this cost centre in this budget year.</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
