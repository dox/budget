<?php
require_once('../inc/autoload.php');

$uploads_class = new class_uploads;

header('Content-Type: text/plain; charset=utf-8');

try {

    // Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($_FILES['upfile']['error']) ||
        is_array($_FILES['upfile']['error'])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }

    // Check $_FILES['upfile']['error'] value.
    switch ($_FILES['upfile']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    // You should also check filesize here.
    if ($_FILES['upfile']['size'] > 1000000) {
        throw new RuntimeException('Exceeded filesize limit.');
    }

    // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
    // Check MIME Type by yourself.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (false === $ext = array_search(
        $finfo->file($_FILES['upfile']['tmp_name']),
        array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'vnd.openxmlformats-officedocument.wordprocessingml.document',
        ),
        true
    )) {
        throw new RuntimeException('Invalid file format.');
    }

    // You should name it uniquely.
    // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
    // On this example, obtain safe unique name from its binary data.
    $target_file = '../uploads/' . $_POST['orderUID'] . "_" . date('Y-m-d H:i:s') . "." . $ext;
    if (!move_uploaded_file($_FILES['upfile']['tmp_name'], $target_file)) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    $logMessage = "File " . $target_file;
    $log->insert("file", $logMessage);

    $data = Array (
			"name" => $_FILES['upfile']['name'],
			"date_upload" => date('Y-m-d H:i:s'),
			"path" => $target_file,
			"order_uid" => $_POST['orderUID']
		);

		$uploads_class->insert($data);

    echo 'File is uploaded successfully.';

} catch (RuntimeException $e) {
  $logMessage = "File '" . $_FILES['upfile']['name'] . "' FAILED to upload. Error: " . $e->getMessage();
  $log->insert("file", $logMessage);

    echo $e->getMessage();

}
?>
