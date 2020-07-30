<?php
require_once('../inc/autoload.php');

if (isset($_POST['departmentUID'])) {
	$log->insert("logon", "Temporary department change (" . $_SESSION['department'] . "->" . $_POST['departmentUID'] . ") successful for " . $_SESSION['username']);

	$title = "Temporary Department Change";
	$message = "You have temporarily changed departments";
	echo toast($title, $message);

	$_SESSION['department'] = $_POST['departmentUID'];
}
?>
