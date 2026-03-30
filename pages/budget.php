<?php
$currentBudgetYear = BudgetYear::current();
$budgetYear = BudgetYear::fromRequest();
$selectedYear = $budgetYear->year;
$costCentres = CostCentre::all($budgetYear);
$orders = (new Orders())->allForBudgetYear($budgetYear);

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

$ytdSpendByCostCentre = [];
foreach ($costCentres as $costCentre) {
	$ytdSpendByCostCentre[$costCentre->id] = 0.0;
}

foreach ($orders as $order) {
	$orderDate = new DateTime($order->date_created);
	if ($orderDate > $ytdCutoff) {
		continue;
	}

	foreach ($costCentres as $costCentre) {
		if ((int) $order->cost_centre === $costCentre->id) {
			$ytdSpendByCostCentre[$costCentre->id] += (float) $order->value;
			break;
		}
	}
}

// Prepare dropdown options
$yearOptions = [
	['label' => 'Last Year', 'year' => BudgetYear::yearsAgo(1)->year],
	['label' => 'This Year', 'year' => $currentBudgetYear->year],
	['label' => 'Next Year', 'year' => BudgetYear::yearsFromNow(1)->year],
];
$currentLabel = $budgetYear->label();
$status = $_GET['status'] ?? null;
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Budget</h1>

	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="dropdown">
			<button
				class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1"
				type="button"
				id="budgetYearDropdown"
				data-bs-toggle="dropdown"
				aria-expanded="false"
			>
				<i class="bi bi-calendar3" aria-hidden="true"></i>
				<?= htmlspecialchars($currentLabel) ?>
			</button>

			<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="budgetYearDropdown">
				<?php foreach ($yearOptions as $opt): ?>
					<li>
						<form method="post" class="dropdown-item p-0">
							<input type="hidden" name="year" value="<?= $opt['year'] ?>">
							<button type="submit" class="btn btn-link w-100 text-start px-3 py-1">
								<?= htmlspecialchars($opt['label']) ?>
							</button>
						</form>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

	</div>
</div>

<?php if ($status === 'created'): ?>
	<div class="alert alert-success" role="alert">Budget created.</div>
<?php elseif ($status === 'updated'): ?>
	<div class="alert alert-success" role="alert">Budget updated.</div>
<?php elseif ($status === 'deleted'): ?>
	<div class="alert alert-success" role="alert">Budget deleted.</div>
<?php elseif ($status === 'error'): ?>
	<div class="alert alert-danger" role="alert">There was a problem saving that budget.</div>
<?php endif; ?>

<table class="table table-striped">
	<thead>
		<tr>
			<th>Code</th>
			<th>Name</th>
			<th>Description</th>
			<th class="numeric">Budget (£)</th>
			<th class="numeric">YTD Budget (£)</th>
			<th class="numeric">YTD Spend (£)</th>
			<th class="text-end">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($costCentres as $cc): ?>
			<?php
			$ytdBudget = $cc->budgetValue * $elapsedRatio;
			$ytdSpend = $ytdSpendByCostCentre[$cc->id] ?? 0.0;
			$costCentreUrl = 'index.php?page=cost_centre&id=' . $cc->id . '&year=' . $selectedYear;
			?>
			<tr>
				<td><a href="<?= htmlspecialchars($costCentreUrl) ?>"><?= htmlspecialchars($cc->code) ?></a></td>
				<td><a href="<?= htmlspecialchars($costCentreUrl) ?>"><?= htmlspecialchars($cc->name) ?></a></td>
				<td><?= htmlspecialchars($cc->description ?? '') ?></td>
				<td class="numeric"><?= number_format($cc->budgetValue, 2) ?></td>
				<td class="numeric"><?= number_format($ytdBudget, 2) ?></td>
				<td class="numeric"><?= number_format($ytdSpend, 2) ?></td>
				<td class="text-end">
					<div class="d-inline-flex gap-2">
						<a href="index.php?page=budget_addedit&action=edit&id=<?= $cc->id ?>&year=<?= $selectedYear ?>" class="btn btn-sm btn-outline-secondary">
							<i class="bi bi-pencil" aria-hidden="true"></i> <?= $cc->hasBudget ? 'Edit' : 'Add' ?>
						</a>
						<?php if ($cc->hasBudget): ?>
							<form method="post" action="actions/budget.php" onsubmit="return confirm('Delete this budget for <?= htmlspecialchars($budgetYear->label(), ENT_QUOTES) ?>?');">
								<input type="hidden" name="action" value="delete">
								<input type="hidden" name="cost_centre_id" value="<?= $cc->id ?>">
								<input type="hidden" name="year" value="<?= $selectedYear ?>">
								<button type="submit" class="btn btn-sm btn-outline-danger">
									<i class="bi bi-trash" aria-hidden="true"></i> Delete
								</button>
							</form>
						<?php endif; ?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<style>
	td.numeric { text-align: right; white-space: nowrap; }
</style>
