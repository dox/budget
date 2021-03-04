<?php
require_once('inc/autoload.php');

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
    $sql  = "SELECT * FROM orders ";
    $sql .= "WHERE cost_centre in (SELECT uid FROM cost_centres WHERE department = '" . $_SESSION['department'] . "') ";
    $sql .= "ORDER BY orders.date DESC, orders.po DESC;";

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
