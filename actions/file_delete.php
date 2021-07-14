<?php
require_once('../inc/autoload.php');

$uploads_class = new class_uploads;

$uploads_class->delete($_POST['uploadUID']);

?>
