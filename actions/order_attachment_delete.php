<?php
declare(strict_types=1);

require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	http_response_code(403);
	exit('Forbidden');
}

$orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
$token = trim((string) ($_POST['token'] ?? ''));

if (!$orderId || $token === '') {
	header('Location: ../index.php?page=orders&status=error');
	exit;
}

$order = new Order($orderId);
if (!isset($order->id)) {
	header('Location: ../index.php?page=orders&status=error');
	exit;
}

$removed = $order->removeAttachment($token);
$redirectUrl = '../index.php?page=order&id=' . $orderId . '&attachment_status=' . ($removed ? 'deleted' : 'error');

header('Location: ' . $redirectUrl);
exit;
