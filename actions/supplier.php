<?php
declare(strict_types=1);

require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	http_response_code(403);
	exit('Forbidden');
}

$action = $_POST['action'] ?? '';
$uid = filter_input(INPUT_POST, 'uid', FILTER_VALIDATE_INT);
$status = 'error';

try {
	if ($action !== 'update') {
		throw new InvalidArgumentException('Invalid supplier action.');
	}

	if ($uid === false || $uid === null) {
		throw new InvalidArgumentException('Invalid supplier ID.');
	}

	$updated = Supplier::updateByUid((int) $uid, [
		'name' => $_POST['name'] ?? '',
		'account_number' => $_POST['account_number'] ?? null,
		'address' => $_POST['address'] ?? null,
		'telephone' => $_POST['telephone'] ?? null,
		'mobile' => $_POST['mobile'] ?? null,
		'email' => $_POST['email'] ?? null,
		'website' => $_POST['website'] ?? null,
	]);

	$status = $updated ? 'updated' : 'error';
} catch (Throwable $e) {
	$status = 'error';
}

header('Location: ../index.php?page=supplier&uid=' . urlencode((string) $uid) . '&status=' . urlencode($status));
exit;
