<?php
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$id = (int) $_GET['id'];
	$order = new Order($id);
	$costCentre = $order->costCentreModel();
	$supplierName = trim((string) ($order->supplier ?? ''));
	$supplierLabel = $supplierName !== '' ? $supplierName : 'Unknown supplier';
	$supplier = Supplier::findByName($supplierName);
	$supplierUrl = 'index.php?page=supplier&name=' . urlencode($supplierLabel);
	$attachments = $order->attachments();
	$attachmentStatus = $_GET['attachment_status'] ?? null;
	$items = json_decode((string) $order->items, true);
	$items = is_array($items) ? $items : [];
} else {
	// Handle invalid or missing ID
	die('Invalid order ID.');
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Purchase Order <?php echo "#" . $order->id; ?></h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group me-2">
			<a href="index.php?page=order_addedit&action=edit&id=<?php echo $order->id; ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil" aria-hidden="true"></i> Edit</a>
			<a href="index.php?page=order_addedit&action=clone&id=<?php echo $order->id; ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-copy" aria-hidden="true"></i> Clone</a>
		</div>
		<form method="post" action="actions/order_delete.php" onsubmit="return confirm('Delete this order and all of its attachments?');">
			<input type="hidden" name="order_id" value="<?php echo (int) $order->id; ?>">
			<button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash" aria-hidden="true"></i> Delete</button>
		</form>
	</div>
</div>

<?php if ($attachmentStatus === 'deleted'): ?>
	<div class="alert alert-success" role="alert">Attachment deleted.</div>
<?php elseif ($attachmentStatus === 'error'): ?>
	<div class="alert alert-danger" role="alert">Unable to delete attachment.</div>
<?php endif; ?>

<div class="row mb-4">
	<div class="col text-end">
	  <h6 class="mb-0">Date</h6>
	  <p class="fw-semibold mb-0"><?php echo date("Y-m-d", strtotime($order->date_created)); ?></p>
	</div>
</div>

<div class="row mb-4">
	<div class="col-md-6">
	  <h6 class="text-uppercase text-muted">From</h6>
	  <p class="mb-1 fw-semibold">St Edmund Hall Design & Build Ltd</p>
	  <p class="mb-1">Queen’s Lane, Oxford OX1 4AR</p>
	  <p class="mb-1">help@seh.ox.ac.uk</p>
	  <p class="mb-1"><?php echo $order->username; ?></p>
	  <p class="mb-1">
		<?php if ($costCentre): ?>
			<a href="index.php?page=cost_centre&id=<?php echo $costCentre->id; ?>&year=<?php echo $costCentre->budgetYear->year; ?>">
				<?php echo htmlspecialchars($costCentre->code . ' - ' . $costCentre->name); ?>
			</a>
		<?php else: ?>
			<?php echo htmlspecialchars((string) $order->cost_centre); ?>
		<?php endif; ?>
	  </p>
	</div>
	<div class="col-md-6 text-md-end">
	  <h6 class="text-uppercase text-muted">To</h6>
	  <p class="mb-1 fw-semibold">
	  	<a href="<?= htmlspecialchars($supplierUrl) ?>"><?= htmlspecialchars($supplier?->name() ?? $supplierLabel) ?></a>
	  </p>
	  <p class="mb-1"><?php echo "Order #:" . htmlspecialchars((string) $order->order_num); ?></p>
	  <?php if ($supplier?->accountNumber()): ?>
	  	<p class="mb-1">Account #: <?= htmlspecialchars($supplier->accountNumber()) ?></p>
	  <?php endif; ?>
	  <?php foreach ($supplier?->addressLines() ?? [] as $addressLine): ?>
	  	<p class="mb-1"><?= htmlspecialchars($addressLine) ?></p>
	  <?php endforeach; ?>
	  <?php if ($supplier?->telephone()): ?>
	  	<p class="mb-1">Tel: <?= htmlspecialchars($supplier->telephone()) ?></p>
	  <?php endif; ?>
	  <?php if ($supplier?->mobile()): ?>
	  	<p class="mb-1">Mobile: <?= htmlspecialchars($supplier->mobile()) ?></p>
	  <?php endif; ?>
	  <?php if ($supplier?->email()): ?>
	  	<p class="mb-0"><?= htmlspecialchars($supplier->email()) ?></p>
	  <?php elseif ($supplier?->website()): ?>
	  	<p class="mb-0"><?= htmlspecialchars($supplier->website()) ?></p>
	  <?php endif; ?>
	</div>
  </div>

  <!-- Item Table -->
  <div class="table-responsive mb-4">
	<table class="table table-bordered align-middle">
	  <thead >
		<tr>
		  <th scope="col">#</th>
		  <th scope="col">Description</th>
		  <th scope="col">Qty.</th>
		  <th scope="col" class="text-end">Price</th>
		  <th scope="col" class="text-end">Total</th>
		</tr>
	  </thead>
	  <tbody>
		  <?php
		  $i = 1;

		  foreach ($items as $item) {
			  $quantity = (float) ($item['item_qty'] ?? 0);
			  $price = (float) ($item['item_value'] ?? 0);
			  $lineTotal = $quantity * $price;
			  ?>
			  <tr>
			  	<td><?= $i ?></td>
			  	<td><?= htmlspecialchars((string) ($item['item_name'] ?? '')) ?></td>
			  	<td><?= htmlspecialchars((string) ($item['item_qty'] ?? '')) ?></td>
			  	<td class="text-end"><?= htmlspecialchars(formatMoney($price)) ?></td>
			  	<td class="text-end"><?= htmlspecialchars(formatMoney($lineTotal)) ?></td>
			  </tr>
			  <?php
			  $i++;
		  }
		  ?>
	  </tbody>
	  <tfoot>
		<tr>
		  <th colspan="4" class="text-end">Total</th>
		  <th class="text-end"><?php echo formatMoney($order->value); ?></th>
		</tr>
	  </tfoot>
	</table>
  </div>

  <?php if ($attachments !== []): ?>
	<div class="row mb-4">
		<div class="col">
			<h6 class="text-uppercase text-muted">Attachments</h6>
			<ul class="list-group">
				<?php foreach ($attachments as $attachment): ?>
					<?php
					$token = (string) ($attachment['token'] ?? '');
					$fileName = (string) ($attachment['original_name'] ?? 'Attachment');
					$fileSize = (int) ($attachment['size'] ?? 0);
					$downloadUrl = 'actions/order_attachment.php?order_id=' . (int) $order->id . '&token=' . urlencode($token);
					?>
					<li class="list-group-item d-flex justify-content-between align-items-center">
						<div>
							<a href="<?= htmlspecialchars($downloadUrl) ?>"><?= htmlspecialchars($fileName) ?></a>
							<?php if ($fileSize > 0): ?>
								<span class="text-muted small"><?= htmlspecialchars(number_format($fileSize / 1024, 1)) ?> KB</span>
							<?php endif; ?>
						</div>
						<form method="post" action="actions/order_attachment_delete.php" onsubmit="return confirm('Delete this attachment?');" class="m-0">
							<input type="hidden" name="order_id" value="<?= (int) $order->id ?>">
							<input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
							<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
						</form>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
  <?php endif; ?>

  <!-- Notes -->
  <div class="row">
	<div class="col">
	  <p class="mb-0">
	  	<?php
		  if ($order->notes) {
			  echo "<h6 class=\"text-uppercase text-muted\">Notes</h6>";
				echo "<p>" . htmlspecialchars((string) $order->notes) . "</p>";
			};
			?>
		</p>
	</div>
  </div>
