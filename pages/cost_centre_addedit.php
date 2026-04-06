<?php
$action = strtolower($_GET['action'] ?? 'add');
$validActions = ['add', 'edit'];

if (!in_array($action, $validActions, true)) {
	die('Invalid action.');
}

$budgetYear = BudgetYear::fromRequest();
$costCentre = null;

if ($action === 'edit') {
	$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
	if ($id === false || $id === null) {
		die('Invalid cost centre ID.');
	}

	$costCentre = new CostCentre($id, $budgetYear);
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2"><?= $action === 'add' ? 'New Cost Centre' : 'Edit Cost Centre' ?></h1>
</div>

<form method="post" action="actions/cost_centre.php" class="needs-validation" novalidate>
	<div class="card mb-4">
		<div class="card-header fw-bold">Cost Centre Details</div>
		<div class="card-body">
			<div class="row g-3">
				<div class="col-md-4">
					<label for="code" class="form-label">Code</label>
					<input type="text" class="form-control" id="code" name="code" value="<?= htmlspecialchars($costCentre->code ?? '') ?>" required>
				</div>
				<div class="col-md-8">
					<label for="name" class="form-label">Name</label>
					<input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($costCentre->name ?? '') ?>" required>
				</div>
				<div class="col-12">
					<label for="description" class="form-label">Description</label>
					<textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($costCentre->description ?? '') ?></textarea>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="action" value="<?= $action === 'add' ? 'create' : 'update' ?>">
	<input type="hidden" name="year" value="<?= $budgetYear->year ?>">
	<?php if ($costCentre): ?>
		<input type="hidden" name="id" value="<?= $costCentre->id ?>">
	<?php endif; ?>

	<div class="text-end">
		<button type="submit" class="btn btn-primary"><?= $action === 'add' ? 'Create Cost Centre' : 'Save Changes' ?></button>
		<a href="index.php?page=cost_centres&year=<?= $budgetYear->year ?>" class="btn btn-secondary">Cancel</a>
	</div>
</form>
