<?php
require_once('inc/autoload.php');

$sql  = "SELECT * FROM orders ";
$sql .= "WHERE cost_centre in (SELECT uid FROM cost_centres WHERE department = '" . $_SESSION['department'] . "') ";
$sql .= "ORDER BY orders.date DESC, orders.po DESC;";

$orders = $db->rawQuery($sql);

$i = 0;
foreach ($orders AS $order) {
  $cost_centre_class = new class_cost_centres;
  $cost_centre = $cost_centre_class->getOne($order['cost_centre']);

  $users_class = new class_users;
  $user = $users_class->getOne($order['username']);

  $row['uid']         = $order['uid'];
  $row['username']    = $user['username'];
  $row['date']        = $order['date'];

  $row['cost_centre_code'] = $cost_centre['code'];
  $row['cost_centre_name'] = $cost_centre['name'];

  $row['po']          = $order['po'];
  $row['order_num']   = $order['order_num'];
  $row['name']        = $order['name'];
  $row['value']       = $order['value'];
  $row['supplier']    = $order['supplier'];
  $row['description'] = $order['description'];
  $row['paid']        = $order['paid'];

  $prod[$i] = $row;
  $i++;
}

$output = fopen("php://output",'w') or die("Can't open php://output");
header("Content-Type:application/csv");
header("Content-Disposition:attachment;filename=budget_export.csv");
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="budget_export.csv"');
header('Pragma: no-cache');
header('Expires: 0');

fputcsv($output, array_keys($row));

if (isset($_SESSION['username'])) {
  foreach($prod as $product) {
      fputcsv($output, $product);
  }

  fclose($output) or die("Can't close php://output");
}
?>
