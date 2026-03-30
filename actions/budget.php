<?php
declare(strict_types=1);

require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	http_response_code(403);
	exit('Forbidden');
}

$action = $_POST['action'] ?? '';
$year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);
$year = $year !== false && $year !== null ? $year : BudgetYear::current()->year;
$budgetYear = new BudgetYear($year);
$status = 'error';

try {
	$costCentreId = filter_input(INPUT_POST, 'cost_centre_id', FILTER_VALIDATE_INT);
	if ($costCentreId === false || $costCentreId === null) {
		throw new InvalidArgumentException('Invalid cost centre ID.');
	}

	if ($action === 'save') {
		$budgetValue = filter_input(INPUT_POST, 'budget_value', FILTER_VALIDATE_FLOAT);
		if ($budgetValue === false || $budgetValue === null) {
			throw new InvalidArgumentException('Invalid budget value.');
		}

		$costCentre = new CostCentre($costCentreId, $budgetYear);
		$saved = CostCentre::saveBudget($costCentreId, $budgetYear, (float) $budgetValue);
		$status = $saved ? ($costCentre->hasBudget ? 'updated' : 'created') : 'error';
	} elseif ($action === 'delete') {
		$deleted = CostCentre::deleteBudget($costCentreId, $budgetYear);
		$status = $deleted ? 'deleted' : 'error';
	}
} catch (Throwable $e) {
	$status = 'error';
}

header('Location: ../index.php?page=budget&year=' . urlencode((string) $budgetYear->year) . '&status=' . urlencode($status));
exit;
