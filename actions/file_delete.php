<?php
require_once('../inc/autoload.php');

$uploads_class = new class_uploads;
$upload = $uploads_class->getOne($_POST['uploadUID']);
$uploadOk = 0;

if (isset($upload['path'])) {
	$target_file = UPLOAD_DIR . $upload['path'];
} else {
	$target_file = null;
}


// Check if file already exists
if (file_exists($target_file)) {
    //echo "Sorry, file already exists.";
    //$logMessage = "File '" . $target_file . "' already exists";
    $uploadOk = 0;
    echo "ready to delete file ". $target_file;
    $uploads_class->delete($upload['uid']);

} else {
	echo "doesn't exist";
}


if ($uploadOk == 1) {
} else {
}

//$log->insert("file", $logMessage);
?>
