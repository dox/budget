<?php
$orders_class = new class_orders;
$orders = $orders_class->all();
?>

<h2>Orders <small class="text-muted"><?php echo "Budget Year: " . budgetStartDate() . " - " . budgetEndDate(); ?></small></h2>

<?php
echo $orders_class->table($orders);
?>
