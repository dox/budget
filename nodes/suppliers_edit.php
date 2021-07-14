<?php
$supplierObject = new supplier($_GET['name']);

$supplierURL = "index.php?n=suppliers_unique&name=" . urlencode($_GET['name']);
?>

<h2>Edit Supplier '<?php echo $supplierObject->name;?>'</h2>

<form method="POST" action="<?php echo $supplierURL; ?>">
	<div class="mb-3">
		<label for="name">Name</label>
		<input type="text" class="form-control" readonly id="name" name="name" placeholder="Name" value="<?php echo $supplierObject->name; ?>">
	</div>
	<div class="mb-3">
		<label for="account_number">Account #</label>
		<input type="text" class="form-control" id="account_number" name="account_number" placeholder="Account #" value="<?php echo $supplierObject->account_number; ?>">
	</div>
	<div class="mb-3">
		<label for="address">Address</label>
		<textarea class="form-control" id="address" name="address" rows="3"><?php echo $supplierObject->address; ?></textarea>
	</div>
	<div class="mb-3">
		<label for="telephone">Telephone</label>
		<input type="text" class="form-control" id="telephone" name="telephone" placeholder="Telephone" value="<?php echo $supplierObject->telephone; ?>">
	</div>
	<div class="mb-3">
		<label for="mobile">Mobile</label>
		<input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile" value="<?php echo $supplierObject->mobile; ?>">
	</div>
	<div class="mb-3">
		<label for="email">Email</label>
		<input type="text" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $supplierObject->email; ?>">
	</div>
	<div class="mb-3">
		<label for="website">Website</label>
		<input type="text" class="form-control" id="website" name="website" placeholder="www.website.com" value="<?php echo $supplierObject->website; ?>">
	</div>
	<button type="submit" class="btn btn-primary">Update</button>
	<input type="hidden" id="uid" name="uid" value="<?php echo $supplierObject->uid; ?>" />
</form>
