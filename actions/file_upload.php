<?php
require_once('../inc/autoload.php');

$uploads_class = new class_uploads;

$orderUID = $_GET['orderUID'];
$original_file = basename($_FILES["fileToUpload"]["name"]);;
$imageFileType = strtolower(pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION));
$target_file = $orderUID . "_" . date('Y-m-d-H-i-s') . "." . $imageFileType;

$target_file_path = UPLOAD_DIR . $target_file;

$uploadOk = 1;

// Check if file already exists
if (file_exists($target_file)) {
    //echo "Sorry, file already exists.";
    $logMessage = "File '" . $target_file . "' already exists";
    $uploadOk = 0;
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 50000000) {
    $logMessage = "File '" . $target_file . "' is too large";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "xls" && $imageFileType != "xlsx") {
    $logMessage = "File type for file '" . $target_file . "' not allowed";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 1) {
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file_path)) {
        $logMessage = "File '" . $target_file . "' uploaded";

        $data = Array (
			"name" => $original_file,
			"date_upload" => date('Y-m-d H:i:s'),
			"path" => $target_file,
			"order_uid" => $_GET['orderUID']
		);

		$uploads_class->insert($data);
        $output = array("success" => true, "message" => "Success!");
    } else {
		$logMessage = "There was an unknown error trying to upload file '" . $target_file . "' to '" . $target_file_path . "'";
		$output = array("success" => false, "error" => "Failure!");
    }
} else {
	$output = array("success" => false, "error" => "Failure!");
}

$log->insert("file", $logMessage);


header("Content-Type: application/json; charset=utf-8");
echo json_encode($output);

?>
