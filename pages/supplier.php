<?php
$uid = filter_input(INPUT_GET, 'uid', FILTER_VALIDATE_INT);
$uid = $uid !== false && $uid !== null ? $uid : filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$supplierName = trim((string) ($_GET['name'] ?? ''));

if ($uid !== false && $uid !== null) {
	$supplier = Supplier::findByUid((int) $uid);
} elseif ($supplierName !== '') {
	$supplier = Supplier::findOrCreateByName($supplierName);
} else {
	die('Invalid supplier.');
}

if (!$supplier) {
	die('Supplier not found.');
}

$status = $_GET['status'] ?? null;
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Edit Supplier</h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<a href="index.php?page=suppliers" class="btn btn-sm btn-outline-secondary">Back to Suppliers</a>
	</div>
</div>

<?php if ($status === 'updated'): ?>
	<div class="alert alert-success" role="alert">Supplier updated.</div>
<?php elseif ($status === 'error'): ?>
	<div class="alert alert-danger" role="alert">Unable to save supplier changes.</div>
<?php endif; ?>

<form method="post" action="actions/supplier.php" class="needs-validation" novalidate>
	<div class="card mb-4">
		<div class="card-header fw-bold">Supplier Details</div>
		<div class="card-body">
			<div class="row g-3">
				<div class="col-md-8">
					<label for="name" class="form-label">Name</label>
					<input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($supplier->name() ?? '') ?>" required>
				</div>
				<div class="col-md-4">
					<label for="account_number" class="form-label">Account Number</label>
					<input type="text" class="form-control" id="account_number" name="account_number" value="<?= htmlspecialchars($supplier->accountNumber() ?? '') ?>">
				</div>
				<div class="col-12">
					<label for="address" class="form-label">Address</label>
					<textarea class="form-control" id="address" name="address" rows="4"><?= htmlspecialchars($supplier->value('address') ?? '') ?></textarea>
				</div>
				<div class="col-md-6">
					<label for="telephone" class="form-label">Telephone</label>
					<input type="text" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($supplier->telephone() ?? '') ?>">
				</div>
				<div class="col-md-6">
					<label for="mobile" class="form-label">Mobile</label>
					<input type="text" class="form-control" id="mobile" name="mobile" value="<?= htmlspecialchars($supplier->mobile() ?? '') ?>">
				</div>
				<div class="col-md-6">
					<label for="email" class="form-label">Email</label>
					<input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($supplier->email() ?? '') ?>">
				</div>
				<div class="col-md-6">
					<label for="website" class="form-label">Website</label>
					<input type="text" class="form-control" id="website" name="website" value="<?= htmlspecialchars($supplier->website() ?? '') ?>">
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="action" value="update">
	<input type="hidden" name="uid" value="<?= $supplier->uid() ?>">

	<div class="text-end">
		<button type="submit" class="btn btn-primary">Save Changes</button>
		<a href="index.php?page=suppliers" class="btn btn-secondary">Cancel</a>
	</div>
</form>
