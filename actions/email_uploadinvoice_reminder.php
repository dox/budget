<?php
require_once('../inc/autoload.php');

$usernameUID = $_GET['uid'];

$users_class = new class_users;
$user = $users_class->getOne($usernameUID);
$_SESSION['department'] = $user['department'];

$orders_class = new class_orders;
$orders = $orders_class->all();



$output  = "Dear " . $user['firstname'] . ",<br />";
$output .= "Please ensure that any required invoices for the following POs have been uploaded to the <a href=\"http://budget.seh.ox.ac.uk\">Budget System</a>";
$output .= "<ul>";

$ordersNum = 0;
foreach ($orders AS $order) {
  if(!isset($order['paid']) && $order['username'] == $user['uid']) {
    $uploads_class = new class_uploads;
    $uploads = $uploads_class->all($order['uid']);

    if (!$uploads) {
      $suppliers_class = new class_suppliers;
      $supplier = $suppliers_class->getOne($order['supplier']);

      $orderText = $order['po'] . " " . $supplier['name'] . " " . $order['name'] . " (&pound;" . number_format($order['value'],2) . ") <br />";
      $orderText = "<a href=\"http://budget.seh.ox.ac.uk/index.php?n=orders_unique&uid=" . $order['uid'] . "\">" . $orderText . "</a>";
      $output .= "<li>" . $orderText . "</li>";

      $ordersNum = $ordersNum + 1;
    }
  }
}
$output .= "</ul>";

if ($ordersNum > 0) {
  sendMail("Orders requiring invoices", array($user['email']), $output, "noreply@seh.ox.ac.uk", "SEH Budget System");
  echo $output;
} else {
  echo "No invoices needed chasing";
}
?>
