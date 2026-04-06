<?php
$action = strtolower($_GET['action'] ?? 'add');
$validActions = ['add', 'edit'];

if (!in_array($action, $validActions, true)) {
	die('Invalid action.');
}

$budgetYear = BudgetYear::fromRequest();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null) {
	die('Invalid cost centre ID.');
}

$costCentre = new CostCentre($id, $budgetYear);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2"><?= $costCentre->hasBudget ? 'Edit Budget' : 'Add Budget' ?></h1>
</div>

<form method="post" action="actions/budget.php" class="needs-validation" novalidate>
	<div class="card mb-4">
		<div class="card-header fw-bold">Budget Details</div>
		<div class="card-body">
			<div class="row g-3">
				<div class="col-md-4">
					<label class="form-label">Budget Year</label>
					<input type="text" class="form-control" value="<?= htmlspecialchars((string) $budgetYear) ?>" readonly>
				</div>
				<div class="col-md-4">
					<label class="form-label">Cost Centre Code</label>
					<input type="text" class="form-control" value="<?= htmlspecialchars($costCentre->code) ?>" readonly>
				</div>
				<div class="col-md-4">
					<label class="form-label">Cost Centre Name</label>
					<input type="text" class="form-control" value="<?= htmlspecialchars($costCentre->name) ?>" readonly>
				</div>
				<div class="col-md-6">
					<label for="budget_value" class="form-label">Budget Amount (£)</label>
					<input type="number" class="form-control" id="budget_value" name="budget_value" min="0" step="0.01" value="<?= htmlspecialchars(number_format($costCentre->budgetValue, 2, '.', '')) ?>" required>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="action" value="save">
	<input type="hidden" name="cost_centre_id" value="<?= $costCentre->id ?>">
	<input type="hidden" name="year" value="<?= $budgetYear->year ?>">

	<div class="text-end">
		<button type="submit" class="btn btn-primary"><?= $costCentre->hasBudget ? 'Save Budget' : 'Create Budget' ?></button>
		<a href="index.php?page=cost_centres&year=<?= $budgetYear->year ?>" class="btn btn-secondary">Cancel</a>
	</div>
</form>
