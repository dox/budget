<?php
declare(strict_types=1);

require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	http_response_code(403);
	exit('Forbidden');
}

$orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);

if (!$orderId) {
	header('Location: ../index.php?page=orders&status=error');
	exit;
}

$order = new Order($orderId);
if (!isset($order->id)) {
	header('Location: ../index.php?page=orders&status=error');
	exit;
}

$deleted = $order->delete();

header('Location: ../index.php?page=orders&status=' . ($deleted ? 'deleted' : 'error'));
exit;
