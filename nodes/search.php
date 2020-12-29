<?php
if (isset($_POST['search'])) {
} else {
	$_POST['search'] = "";
}

$orders_class = new class_orders;
$orders = $orders_class->all(null, null, null, $_POST['search']);
$ordersPrevious = $orders_class->all_previous_years($_POST['search']);

$log = new class_logs;
$log->insert("search", $db->getLastQuery());
?>

<h2>Search Orders <small class="text-muted"><?php echo "Budget Year: " . budgetStartDate() . " - " . budgetEndDate(); ?></small></h2>
<form class="form-inline" method="POST" action="index.php?n=search">
	<div class="input-group mb-3">
		<input type="text" class="form-control" name="search" placeholder="Search" aria-label="Search" value="<?php echo $_POST['search']; ?>">
		<button class="btn btn-primary" type="submit" id="button-addon2">Search</button>
	</div>
</form>

<?php
echo $orders_class->table($orders);
?>

<h2>Orders In Previous Budget Years</h2>
<?php
echo $orders_class->table($ordersPrevious);
?>
