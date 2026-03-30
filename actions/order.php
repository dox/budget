<?php
declare(strict_types=1);
require_once '../inc/autoload.php';

$response = ['success' => false];
$log = $log ?? new Log();

$action = $_POST['action'] ?? '';
$log->add("Action received: $action", Log::INFO);

switch ($action) {
	case 'order_insert':
		$data = buildOrderPayload();
		$order = new Order();
		$createdId = $order->insert($data);

		if ($createdId) {
			$createdId = (int) $createdId;
			$generatedPo = Order::generatePoReference($createdId);

			try {
				$order->update([
					'id' => $createdId,
					'po' => $generatedPo,
				]);

				$attachments = processOrderAttachments($createdId, []);
				if (Order::hasAttachmentsColumn() && $attachments !== []) {
					$order->update([
						'id' => $createdId,
						'attachments' => $attachments,
					]);
				}

				$response['success'] = true;
				$response['redirect_url'] = 'index.php?page=order&id=' . $createdId;
			} catch (Throwable $e) {
				$response['error'] = $e->getMessage();
			}
		} else {
			$response['error'] = 'Failed to insert order.';
		}
	break;
	
	case 'order_update':
		$data = buildOrderPayload();
		$data['id'] = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
		if ($data['id'] === false || $data['id'] === null) {
			$response['error'] = 'Invalid order ID.';
			break;
		}

		$order = new Order();
		$existingOrder = new Order($data['id']);
		$existingAttachments = $existingOrder->attachments();
		if (!isset($existingOrder->po) || trim((string) $existingOrder->po) === '') {
			$data['po'] = Order::generatePoReference((int) $data['id']);
		}

		try {
			$updatedAttachments = processOrderAttachments($data['id'], $existingAttachments);
			if (Order::hasAttachmentsColumn()) {
				$data['attachments'] = $updatedAttachments;
			}
		} catch (Throwable $e) {
			$response['error'] = $e->getMessage();
			break;
		}

		if ($order->update($data)) {
			$response['success'] = true;
			$response['redirect_url'] = 'index.php?page=order&id=' . $data['id'];
		} else {
			$response['error'] = 'Failed to update order.';
		}
	break;
	
	default:
		$response['error'] = "Unknown action: $action";
		break;
}

header('Content-Type: application/json');
echo json_encode($response);
exit;

function buildOrderPayload(): array {
	global $user;

	$names = $_POST['itemName'] ?? [];
	$qtys = $_POST['itemQty'] ?? [];
	$prices = $_POST['itemPrice'] ?? [];

	$names = is_array($names) ? $names : [$names];
	$qtys = is_array($qtys) ? $qtys : [$qtys];
	$prices = is_array($prices) ? $prices : [$prices];

	$items = [];
	$length = max(count($names), count($qtys), count($prices));
	$totalValue = 0.0;

	for ($i = 0; $i < $length; $i++) {
		$itemName = trim((string) ($names[$i] ?? ''));
		if ($itemName === '') {
			continue;
		}

		$itemQty = max(1, (int) ($qtys[$i] ?? 1));
		$itemValue = max(0, (float) ($prices[$i] ?? 0));
		$totalValue += $itemQty * $itemValue;

		$items[] = [
			'item_name' => $itemName,
			'item_qty' => $itemQty,
			'item_value' => $itemValue,
		];
	}

	return [
		'username' => $user->getUsername(),
		'order_num' => trim((string) ($_POST['order_num'] ?? '')) ?: null,
		'date_created' => $_POST['date_created'] ?? null,
		'value' => $totalValue,
		'cost_centre' => filter_input(INPUT_POST, 'cost_centre', FILTER_VALIDATE_INT) ?: null,
		'supplier' => trim((string) ($_POST['supplier'] ?? '')) ?: null,
		'name' => trim((string) ($_POST['name'] ?? '')) ?: null,
		'notes' => trim((string) ($_POST['notes'] ?? '')) ?: null,
		'items' => $items,
	];
}

function processOrderAttachments(int $orderId, array $existingAttachments): array {
	$attachmentsToDelete = $_POST['delete_attachments'] ?? [];
	$attachmentsToDelete = is_array($attachmentsToDelete) ? $attachmentsToDelete : [$attachmentsToDelete];
	$attachmentsToDelete = array_values(array_filter(array_map('strval', $attachmentsToDelete)));
	$uploadedAttachments = normaliseUploadedFilesArray($_FILES['attachments'] ?? null);

	if (!Order::hasAttachmentsColumn()) {
		if ($attachmentsToDelete !== [] || hasUploadedAttachments($uploadedAttachments)) {
			throw new RuntimeException('The attachments column has not been added to the orders table yet.');
		}

		return [];
	}

	$remainingAttachments = [];

	foreach ($existingAttachments as $attachment) {
		$token = (string) ($attachment['token'] ?? '');
		if ($token === '') {
			continue;
		}

		if (in_array($token, $attachmentsToDelete, true)) {
			$path = Order::absoluteAttachmentPath($attachment);
			if ($path !== null && is_file($path)) {
				@unlink($path);
			}
			continue;
		}

		$remainingAttachments[] = $attachment;
	}

	if ($uploadedAttachments === []) {
		return $remainingAttachments;
	}

	$targetDirectory = Order::ensureAttachmentDirectory($orderId);

	foreach ($uploadedAttachments as $uploadedFile) {
		$errorCode = (int) ($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE);
		if ($errorCode === UPLOAD_ERR_NO_FILE) {
			continue;
		}

		if ($errorCode !== UPLOAD_ERR_OK) {
			throw new RuntimeException(uploadErrorMessage($errorCode));
		}

		$tmpName = (string) ($uploadedFile['tmp_name'] ?? '');
		if ($tmpName === '' || !is_uploaded_file($tmpName)) {
			throw new RuntimeException('Uploaded attachment could not be validated.');
		}

		$originalName = Order::sanitiseAttachmentName((string) ($uploadedFile['name'] ?? 'attachment'));
		$extension = pathinfo($originalName, PATHINFO_EXTENSION);
		$token = Order::attachmentToken();
		$storedName = $token . ($extension !== '' ? '.' . strtolower($extension) : '');
		$destination = $targetDirectory . DIRECTORY_SEPARATOR . $storedName;

		if (!move_uploaded_file($tmpName, $destination)) {
			throw new RuntimeException('Unable to save uploaded attachment.');
		}

		$remainingAttachments[] = [
			'token' => $token,
			'original_name' => $originalName,
			'stored_name' => $storedName,
			'path' => Order::relativeAttachmentPath($orderId, $storedName),
			'mime_type' => Order::detectMimeType($destination),
			'size' => filesize($destination) ?: 0,
			'uploaded_at' => date('Y-m-d H:i:s'),
		];
	}

	return $remainingAttachments;
}

function normaliseUploadedFilesArray($files): array {
	if (!is_array($files) || !isset($files['name'])) {
		return [];
	}

	$names = $files['name'];
	if (!is_array($names)) {
		return [$files];
	}

	$normalised = [];
	foreach (array_keys($names) as $index) {
		$normalised[] = [
			'name' => $files['name'][$index] ?? '',
			'type' => $files['type'][$index] ?? '',
			'tmp_name' => $files['tmp_name'][$index] ?? '',
			'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
			'size' => $files['size'][$index] ?? 0,
		];
	}

	return $normalised;
}

function uploadErrorMessage(int $errorCode): string {
	return match ($errorCode) {
		UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Attachment exceeds the allowed upload size.',
		UPLOAD_ERR_PARTIAL => 'Attachment upload was incomplete.',
		UPLOAD_ERR_NO_TMP_DIR => 'Upload failed because the temporary directory is missing.',
		UPLOAD_ERR_CANT_WRITE => 'Upload failed because the file could not be written to disk.',
		UPLOAD_ERR_EXTENSION => 'Upload blocked by a server extension.',
		default => 'Attachment upload failed.',
	};
}

function hasUploadedAttachments(array $uploadedAttachments): bool {
	foreach ($uploadedAttachments as $uploadedAttachment) {
		if ((int) ($uploadedAttachment['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
			return true;
		}
	}

	return false;
}
