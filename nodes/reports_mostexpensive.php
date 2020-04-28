<?php
$orders_class = new class_orders;
$orders = $orders_class->all();

$valueColumn  = array_column($orders, 'value');
array_multisort($valueColumn, SORT_DESC, $orders);
$orders = array_slice($orders, 0, 20);
?>

<h2>Top 20 Most Expensive Orders <small class="text-muted"><?php echo "Budget Year: " . BUDGET_STARTDATE . " - " . BUDGET_ENDDATE; ?></small></h2>

<?php
echo $orders_class->table($orders);
?>
