<?php
$currentBudgetYear = BudgetYear::current();
$budgetYear = BudgetYear::fromRequest();
$selectedYear = $budgetYear->year;
$costCentres = CostCentre::all($budgetYear);

$status = $_GET['status'] ?? null;
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Cost Centres</h1>

	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group me-2">
			<a href="index.php?page=cost_centre_addedit&action=add" class="btn btn-sm btn-outline-secondary">
				<i class="bi bi-plus-circle" aria-hidden="true"></i> New
			</a>
		</div>
	</div>
</div>

<?php if ($status === 'created'): ?>
	<div class="alert alert-success" role="alert">Cost centre created.</div>
<?php elseif ($status === 'updated'): ?>
	<div class="alert alert-success" role="alert">Cost centre updated.</div>
<?php elseif ($status === 'deleted'): ?>
	<div class="alert alert-success" role="alert">Cost centre deleted.</div>
<?php elseif ($status === 'error'): ?>
	<div class="alert alert-danger" role="alert">There was a problem saving that cost centre.</div>
<?php endif; ?>

<table class="table table-striped">
	<thead>
		<tr>
			<th>Code</th>
			<th>Name</th>
			<th>Description</th>
			<th class="text-end">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($costCentres as $cc): ?>
			<tr>
				<td><?= htmlspecialchars($cc->code) ?></td>
				<td><?= htmlspecialchars($cc->name) ?></td>
				<td><?= htmlspecialchars($cc->description ?? '') ?></td>
				<td class="text-end">
					<div class="d-inline-flex gap-2">
						<a href="index.php?page=cost_centre_addedit&action=edit&id=<?= $cc->id ?>" class="btn btn-sm btn-outline-secondary">
							<i class="bi bi-pencil" aria-hidden="true"></i> Edit
						</a>
						<form method="post" action="actions/cost_centre.php" onsubmit="return confirm('Delete this cost centre and all of its yearly budgets?');">
							<input type="hidden" name="action" value="delete">
							<input type="hidden" name="id" value="<?= $cc->id ?>">
							<button type="submit" class="btn btn-sm btn-outline-danger">
								<i class="bi bi-trash" aria-hidden="true"></i> Delete
							</button>
						</form>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<style>
	td.numeric { text-align: right; white-space: nowrap; }
</style>
