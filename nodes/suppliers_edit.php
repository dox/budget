<?php
$suppliers_class = new class_suppliers;
$supplier = $suppliers_class->getOne($_GET['name']);
?>

<h2>Edit Supplier '<?php echo $_GET['name'];?>'</h2>

<form method="POST" action="index.php?n=suppliers_unique&name=<?php echo $_GET['name']; ?>">
	<div class="form-group">
		<label for="name">Name</label>
		<input type="text" class="form-control" readonly id="name" name="name" placeholder="Name" value="<?php echo $_GET['name']; ?>">
	</div>
	<div class="form-group">
		<label for="account_number">Account #</label>
		<input type="text" class="form-control" id="account_number" name="account_number" placeholder="Account #" value="<?php echo $supplier['account_number']; ?>">
	</div>
	<div class="form-group">
		<label for="address">Address</label>
		<textarea class="form-control" id="address" name="address" rows="3"><?php echo $supplier['address'];?></textarea>
	</div>
	<div class="form-group">
		<label for="telephone">Telephone</label>
		<input type="text" class="form-control" id="telephone" name="telephone" placeholder="Telephone" value="<?php echo $supplier['telephone']; ?>">
	</div>
	<div class="form-group">
		<label for="mobile">Mobile</label>
		<input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile" value="<?php echo $supplier['mobile']; ?>">
	</div>
	<div class="form-group">
		<label for="email">Email</label>
		<input type="text" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $supplier['email']; ?>">
	</div>
	<div class="form-group">
		<label for="website">Website</label>
		<input type="text" class="form-control" id="website" name="website" placeholder="www.website.com" value="<?php echo $supplier['website']; ?>">
	</div>
	<button type="submit" class="btn btn-primary">Update</button>
</form>