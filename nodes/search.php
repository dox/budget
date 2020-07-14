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
	<div class="form-group mb-2">
		<input class="form-control form-control-lg" type="search" name="search" placeholder="Search" aria-label="Search" value="<?php echo $_POST['search']; ?>">
	</div>
	<div class="form-group mx-sm-3 mb-2">
		<button type="submit" class="btn btn-primary">Submit</button>
	</div>
</form>

<?php
echo $orders_class->table($orders);
?>

<h2>Orders In Previous Budget Years</h2>
<?php
echo $orders_class->table($ordersPrevious);
?>
