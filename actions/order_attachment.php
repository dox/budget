<?php
declare(strict_types=1);

require_once '../inc/autoload.php';

if (!$user->isLoggedIn()) {
	http_response_code(403);
	exit('Forbidden');
}

$orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
$token = trim((string) ($_GET['token'] ?? ''));

if (!$orderId || $token === '') {
	http_response_code(400);
	exit('Invalid attachment request.');
}

$order = new Order($orderId);
if (!isset($order->id)) {
	http_response_code(404);
	exit('Order not found.');
}

$attachment = null;
foreach ($order->attachments() as $candidate) {
	if (($candidate['token'] ?? '') === $token) {
		$attachment = $candidate;
		break;
	}
}

if ($attachment === null) {
	http_response_code(404);
	exit('Attachment not found.');
}

$absolutePath = Order::absoluteAttachmentPath($attachment);
if ($absolutePath === null || !is_file($absolutePath)) {
	http_response_code(404);
	exit('Attachment file missing.');
}

$downloadName = Order::sanitiseAttachmentName((string) ($attachment['original_name'] ?? 'attachment'));
$mimeType = (string) ($attachment['mime_type'] ?? 'application/octet-stream');
$fileSize = filesize($absolutePath);
$encodedDownloadName = rawurlencode($downloadName);

header('Content-Description: File Transfer');
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . str_replace('"', '', $downloadName) . '"; filename*=UTF-8\'\'' . $encodedDownloadName);
header('Content-Length: ' . ($fileSize !== false ? (string) $fileSize : '0'));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

readfile($absolutePath);
exit;
