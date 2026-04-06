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
$status = 'error';

try {
	if ($action === 'create') {
		$created = CostCentre::create([
			'code' => $_POST['code'] ?? '',
			'name' => $_POST['name'] ?? '',
			'description' => $_POST['description'] ?? null,
		]);
		$status = $created ? 'cost_centre_created' : 'error';
	} elseif ($action === 'update') {
		$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
		if ($id === false || $id === null) {
			throw new InvalidArgumentException('Invalid cost centre ID.');
		}

		$updated = CostCentre::updateById($id, [
			'code' => $_POST['code'] ?? '',
			'name' => $_POST['name'] ?? '',
			'description' => $_POST['description'] ?? null,
		]);
		$status = $updated ? 'cost_centre_updated' : 'error';
	} elseif ($action === 'delete') {
		$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
		if ($id === false || $id === null) {
			throw new InvalidArgumentException('Invalid cost centre ID.');
		}

		$deleted = CostCentre::deleteById($id);
		$status = $deleted ? 'cost_centre_deleted' : 'error';
	}
} catch (Throwable $e) {
	$status = 'error';
}

header('Location: ../index.php?page=cost_centres&year=' . urlencode((string) $year) . '&status=' . urlencode($status));
exit;
