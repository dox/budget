<?php
session_start();

require_once('inc/config.php');
require_once('inc/global_functions.php');
require_once('database/MysqliDb.php');
require_once('inc/adLDAP/adLDAP.php');
require_once('inc/logs.php');
require_once('inc/departments.php');
require_once('inc/cost_centres.php');
require_once('inc/orders.php');
require_once('inc/users.php');
require_once('inc/suppliers.php');
require_once('inc/uploads.php');

$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);

if (isset($_SESSION['username'])) {
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="export.csv"');
  header('Pragma: no-cache');
  header('Expires: 0');
  echo exportToCsv("orders");
}


function exportToCsv($type) {
  global $db;

  if ($type == "orders") {
    $sql  = "SELECT * FROM ((orders INNER JOIN users ON orders.username = users.uid INNER JOIN cost_centres ON orders.cost_centre = cost_centres.uid) INNER JOIN departments ON cost_centres.department = departments.uid)";
    $sql .= " WHERE cost_centres.department = '" . $_SESSION['department'] . "'";
  	$sql .= " ORDER BY orders.date DESC, orders.po DESC;";
    $dbRows = $db->rawQuery($sql);

    $arrayKeys = array_keys($dbRows[0]);
  }

  $output = implode(",", $arrayKeys) . "\n";
  foreach ($dbRows AS $dbRow) {

    $rowOutput = array();
    foreach ($dbRow AS $column) {
      $column = str_replace(",", "", $column);
      $column = str_replace('"', '"', $column);
      $column = str_replace("\r", "", $column);

      $rowOutput[] = str_replace(",", "", $column);
    }
    $output .= implode(",", $rowOutput) . "\n";
  }

  return $output;
}
?>
