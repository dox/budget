<?php
session_start();

require_once('../inc/config.php');
require_once('../inc/global_functions.php');
require_once('../database/MysqliDb.php');
require_once('../inc/adLDAP/adLDAP.php');
require_once('../inc/logs.php');
require_once('../inc/departments.php');
require_once('../inc/cost_centres.php');
require_once('../inc/orders.php');
require_once('../inc/users.php');
require_once('../inc/suppliers.php');
require_once('../inc/uploads.php');

$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);
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
