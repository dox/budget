<?php
$suppliers_class = new class_suppliers;
$supplier = $suppliers_class->getOne($_GET['name']);

$supplierURL = "index.php?n=suppliers_unique&name=" . urlencode($_GET['name']);
?>

<h2>Edit Supplier '<?php echo $_GET['name'];?>'</h2>

<form method="POST" action="<?php echo $supplierURL; ?>">
	<div class="mb-3">
		<label for="name">Name</label>
		<input type="text" class="form-control" readonly id="name" name="name" placeholder="Name" value="<?php if (isset($_GET['name'])) { echo $_GET['name'];} ?>">
	</div>
	<div class="mb-3">
		<label for="account_number">Account #</label>
		<input type="text" class="form-control" id="account_number" name="account_number" placeholder="Account #" value="<?php if (isset($supplier['account_number'])) { echo $supplier['account_number'];} ?>">
	</div>
	<div class="mb-3">
		<label for="address">Address</label>
		<textarea class="form-control" id="address" name="address" rows="3"><?php if (isset($supplier['address'])) { echo $supplier['address'];} ?></textarea>
	</div>
	<div class="mb-3">
		<label for="telephone">Telephone</label>
		<input type="text" class="form-control" id="telephone" name="telephone" placeholder="Telephone" value="<?php if (isset($supplier['telephone'])) { echo $supplier['telephone'];} ?>">
	</div>
	<div class="mb-3">
		<label for="mobile">Mobile</label>
		<input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile" value="<?php if (isset($supplier['mobile'])) { echo $supplier['mobile'];} ?>">
	</div>
	<div class="mb-3">
		<label for="email">Email</label>
		<input type="text" class="form-control" id="email" name="email" placeholder="Email" value="<?php if (isset($supplier['email'])) { echo $supplier['email'];} ?>">
	</div>
	<div class="mb-3">
		<label for="website">Website</label>
		<input type="text" class="form-control" id="website" name="website" placeholder="www.website.com" value="<?php if (isset($supplier['website'])) { echo $supplier['website'];} ?>">
	</div>
	<button type="submit" class="btn btn-primary">Update</button>
</form>
