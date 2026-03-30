<?php
if (!isset($_GET['action'])) {
	die('No action specified.');
}

$action = strtolower($_GET['action']);
$validActions = ['add', 'edit', 'clone'];

if (!in_array($action, $validActions, true)) {
	die('Invalid action.');
}

$isExistingOrder = in_array($action, ['edit', 'clone'], true);
$order = new Order();

if ($isExistingOrder) {
	$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
	if ($id === false || $id === null) {
		die('Invalid order ID.');
	}

	$order = new Order($id);
}

$actionLabel = match ($action) {
	'add' => 'Create',
	'edit' => 'Edit',
	'clone' => 'Clone',
};

$formAction = $action === 'edit' ? 'order_update' : 'order_insert';
$formBudgetYear = $isExistingOrder ? $order->budgetYear() : BudgetYear::current();
$availableCostCentres = CostCentre::withBudget($formBudgetYear);
$budgetOptionsByYear = CostCentre::budgetOptionsByYear();
$selectedCostCentre = (int) ($order->cost_centre ?? 0);
$selectedCostCentreExists = array_reduce(
	$availableCostCentres,
	fn(bool $carry, CostCentre $costCentre): bool => $carry || $costCentre->id === $selectedCostCentre,
	false
);

$supplierOptions = Order::recentSuppliers();
$formTitleSuffix = $action === 'add'
	? ''
	: ($action === 'clone' ? '' : ' #' . $order->id);
$items = $order->items();
$existingAttachments = $action === 'edit' ? $order->attachments() : [];
$poPreview = $action === 'add'
	? (Order::nextPoReferencePreview() ?? 'Generated on save')
	: ((isset($order->po) && trim((string) $order->po) !== '') ? (string) $order->po : 'Generated on save');

if ($items === []) {
	$items = [[
		'item_name' => '',
		'item_qty' => 1,
		'item_value' => 0,
	]];
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2"><?= htmlspecialchars($actionLabel) ?> Purchase Order<?= htmlspecialchars($formTitleSuffix) ?></h1>
</div>

<form method="post" id="order_form" class="needs-validation" novalidate enctype="multipart/form-data">
	<div class="card mb-4">
		<div class="card-header fw-bold">Order Details</div>
		<div class="card-body">
			<div class="row g-3">
				<div class="col-md-4">
					<label for="date_created" class="form-label">Order Date</label>
					<input
						type="datetime-local"
						class="form-control"
						id="date_created"
						name="date_created"
						value="<?= htmlspecialchars(Order::formatDateTimeLocal($order->date_created ?? null)) ?>"
						required
					>
				</div>
				<div class="col-md-4">
					<label for="order_num" class="form-label">Supplier Order #</label>
					<input
						type="text"
						class="form-control"
						id="order_num"
						name="order_num"
						value="<?= htmlspecialchars($order->order_num ?? '') ?>"
						placeholder="Supplier order reference"
					>
				</div>
				<div class="col-md-4">
					<label class="form-label">Budget Year</label>
					<input type="text" class="form-control" id="budget_year_display" value="<?= htmlspecialchars((string) $formBudgetYear) ?>" readonly>
				</div>
				<div class="col-md-6">
					<label class="form-label">PO Reference</label>
					<input type="text" class="form-control" value="<?= htmlspecialchars($poPreview) ?>" readonly>
					<div class="form-text">Assigned automatically when the order is saved.</div>
				</div>
				<div class="col-md-6">
					<label for="supplier" class="form-label">Supplier</label>
					<input
						type="text"
						class="form-control"
						id="supplier"
						name="supplier"
						list="supplierOptions"
						value="<?= htmlspecialchars($order->supplier ?? '') ?>"
						required
					>
					<datalist id="supplierOptions">
						<?php foreach ($supplierOptions as $supplier): ?>
							<option value="<?= htmlspecialchars($supplier) ?>"></option>
						<?php endforeach; ?>
					</datalist>
				</div>
				<div class="col-md-6">
					<label for="cost_centre" class="form-label">Cost Centre Budget</label>
					<select class="form-select" id="cost_centre" name="cost_centre" required>
						<option value="">Select a cost centre</option>
						<?php foreach ($availableCostCentres as $costCentre): ?>
							<?php $isSelected = $selectedCostCentre === $costCentre->id; ?>
							<option value="<?= $costCentre->id ?>" <?= $isSelected ? 'selected' : '' ?>>
								<?= htmlspecialchars($costCentre->code . ' - ' . $costCentre->name . ' (' . formatMoney($costCentre->budgetValue) . ')') ?>
							</option>
						<?php endforeach; ?>
						<?php if (!$selectedCostCentreExists && $selectedCostCentre > 0): ?>
							<option value="<?= $selectedCostCentre ?>" selected>
								Cost centre #<?= $selectedCostCentre ?> (not in available budgets for this year)
							</option>
						<?php endif; ?>
					</select>
					<div class="form-text">Only cost centres with a budget in this budget year are listed.</div>
				</div>
				<div class="col-md-8">
					<label for="name" class="form-label">Order Name</label>
					<input
						type="text"
						class="form-control"
						id="name"
						name="name"
						value="<?= htmlspecialchars($order->name ?? '') ?>"
						placeholder="What is being ordered?"
						required
					>
				</div>
				<div class="col-md-4">
					<label for="value" class="form-label">Total Value (£)</label>
					<input
						type="number"
						class="form-control"
						id="value"
						name="value"
						min="0"
						step="0.01"
						value="<?= htmlspecialchars(number_format((float) ($order->value ?? 0), 2, '.', '')) ?>"
						required
						readonly
					>
				</div>
				<div class="col-12">
					<label for="notes" class="form-label">Notes</label>
					<textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($order->notes ?? '') ?></textarea>
				</div>
			</div>
		</div>
	</div>

	<div class="card mb-4">
		<div class="card-header fw-bold d-flex justify-content-between align-items-center">
			<span>Line Items</span>
			<button type="button" class="btn btn-sm btn-outline-primary" id="addItem"><i class="bi bi-plus" aria-hidden="true"></i> Add Item</button>
		</div>
		<div class="card-body">
			<table class="table align-middle">
				<thead>
					<tr>
						<th>Description</th>
						<th style="width: 100px;">Qty</th>
						<th style="width: 150px;">Unit Price (£)</th>
						<th style="width: 150px;">Total (£)</th>
						<th style="width: 60px;"></th>
					</tr>
				</thead>
				<tbody id="itemTable">
					<?php foreach ($items as $item): ?>
						<tr>
							<td><input type="text" name="itemName[]" class="form-control" value="<?= htmlspecialchars((string) ($item['item_name'] ?? '')) ?>" required></td>
							<td><input type="number" name="itemQty[]" class="form-control item-qty" value="<?= htmlspecialchars((string) ($item['item_qty'] ?? 1)) ?>" min="1" required></td>
							<td><input type="number" name="itemPrice[]" class="form-control item-price" value="<?= htmlspecialchars(number_format((float) ($item['item_value'] ?? 0), 2, '.', '')) ?>" min="0" step="0.01" required></td>
							<td class="text-end fw-semibold align-middle item-total"><?= htmlspecialchars(number_format(((float) ($item['item_value'] ?? 0)) * ((int) ($item['item_qty'] ?? 1)), 2)) ?></td>
							<td><button type="button" class="btn btn-outline-danger btn-sm remove-item">&times;</button></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="card mb-4">
		<div class="card-header fw-bold">Attachments</div>
		<div class="card-body">
			<?php if ($existingAttachments !== []): ?>
				<div class="mb-3">
					<div class="form-label">Existing Attachments</div>
					<div class="list-group">
						<?php foreach ($existingAttachments as $attachment): ?>
							<?php
							$token = (string) ($attachment['token'] ?? '');
							$fileName = (string) ($attachment['original_name'] ?? 'Attachment');
							$fileSize = (int) ($attachment['size'] ?? 0);
							$downloadUrl = 'actions/order_attachment.php?order_id=' . (int) $order->id . '&token=' . urlencode($token);
							?>
							<div class="list-group-item d-flex justify-content-between align-items-center gap-3">
								<div>
									<a href="<?= htmlspecialchars($downloadUrl) ?>"><?= htmlspecialchars($fileName) ?></a>
									<?php if ($fileSize > 0): ?>
										<span class="text-muted small">(<?= htmlspecialchars(number_format($fileSize / 1024, 1)) ?> KB)</span>
									<?php endif; ?>
								</div>
								<div class="form-check m-0">
									<input class="form-check-input" type="checkbox" name="delete_attachments[]" value="<?= htmlspecialchars($token) ?>" id="delete_attachment_<?= htmlspecialchars($token) ?>">
									<label class="form-check-label" for="delete_attachment_<?= htmlspecialchars($token) ?>">Remove</label>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<div>
				<label for="attachments" class="form-label">Add Files</label>
				<input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
				<div class="form-text">Attach PDFs or any other supporting files when creating or editing this order.</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="action" value="<?= htmlspecialchars($formAction) ?>">
	<?php if ($action === 'edit'): ?>
		<input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order->id) ?>">
	<?php endif; ?>

	<div class="text-end">
		<button type="submit" class="btn btn-primary"><?= $action === 'edit' ? 'Save Changes' : 'Create Order' ?></button>
		<a href="index.php?page=orders" class="btn btn-secondary">Cancel</a>
	</div>
</form>

<script>
const itemTable = document.getElementById('itemTable');
const totalValueInput = document.getElementById('value');
const orderDateInput = document.getElementById('date_created');
const costCentreSelect = document.getElementById('cost_centre');
const budgetYearDisplay = document.getElementById('budget_year_display');
const budgetOptionsByYear = <?= json_encode($budgetOptionsByYear, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
const initialSelectedCostCentre = <?= json_encode($selectedCostCentre, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

function budgetYearFromInputValue(value) {
	if (!value) {
		return null;
	}

	const [datePart] = value.split('T');
	const date = new Date(`${datePart}T00:00:00`);
	if (Number.isNaN(date.getTime())) {
		return null;
	}

	const year = date.getFullYear();
	const augustFirst = new Date(year, 7, 1);

	return date < augustFirst ? year - 1 : year;
}

function formatBudgetYearLabel(year) {
	return `${year}-08-01 to ${year + 1}-07-31`;
}

function updateCostCentreOptions(preferredValue = null) {
	const budgetYear = budgetYearFromInputValue(orderDateInput.value);
	if (budgetYear === null) {
		return;
	}

	budgetYearDisplay.value = formatBudgetYearLabel(budgetYear);
	const options = budgetOptionsByYear[String(budgetYear)] || [];
	const currentValue = preferredValue ?? costCentreSelect.value;

	costCentreSelect.innerHTML = '';

	const placeholder = document.createElement('option');
	placeholder.value = '';
	placeholder.textContent = options.length > 0 ? 'Select a cost centre' : 'No budgeted cost centres for this year';
	costCentreSelect.appendChild(placeholder);

	let matched = false;

	for (const option of options) {
		const optionElement = document.createElement('option');
		optionElement.value = String(option.id);
		optionElement.textContent = `${option.code} - ${option.name} (£${Number(option.budgetValue).toFixed(2)})`;

		if (currentValue && String(currentValue) === String(option.id)) {
			optionElement.selected = true;
			matched = true;
		}

		costCentreSelect.appendChild(optionElement);
	}

	if (currentValue && !matched) {
		const unavailableOption = document.createElement('option');
		unavailableOption.value = String(currentValue);
		unavailableOption.textContent = `Cost centre #${currentValue} (not in available budgets for this year)`;
		unavailableOption.selected = true;
		costCentreSelect.appendChild(unavailableOption);
	}
}

function recalculateTotals() {
	let grandTotal = 0;

	for (const row of itemTable.querySelectorAll('tr')) {
		const qty = Number(row.querySelector('.item-qty')?.value || 0);
		const price = Number(row.querySelector('.item-price')?.value || 0);
		const lineTotal = qty * price;

		row.querySelector('.item-total').textContent = lineTotal.toFixed(2);
		grandTotal += lineTotal;
	}

	totalValueInput.value = grandTotal.toFixed(2);
}

document.getElementById('addItem').addEventListener('click', () => {
	const row = document.createElement('tr');
	row.innerHTML = `
		<td><input type="text" name="itemName[]" class="form-control" required></td>
		<td><input type="number" name="itemQty[]" class="form-control item-qty" value="1" min="1" required></td>
		<td><input type="number" name="itemPrice[]" class="form-control item-price" value="0.00" min="0" step="0.01" required></td>
		<td class="text-end fw-semibold align-middle item-total">0.00</td>
		<td><button type="button" class="btn btn-outline-danger btn-sm remove-item">&times;</button></td>
	`;
	itemTable.appendChild(row);
	recalculateTotals();
});

document.addEventListener('click', (event) => {
	if (event.target.classList.contains('remove-item')) {
		event.target.closest('tr').remove();
		recalculateTotals();
	}
});

itemTable.addEventListener('input', (event) => {
	if (event.target.classList.contains('item-qty') || event.target.classList.contains('item-price')) {
		recalculateTotals();
	}
});

orderDateInput.addEventListener('change', () => updateCostCentreOptions());

document.getElementById('order_form').addEventListener('submit', async (event) => {
	event.preventDefault();

	const form = event.currentTarget;
	const formData = new FormData(form);

	try {
		const response = await fetch('actions/order.php', {
			method: 'POST',
			body: formData
		});
		const data = await response.json();

		if (data.success && data.redirect_url) {
			window.location.href = data.redirect_url;
			return;
		}

		alert(data.error || 'Unable to save order.');
	} catch (error) {
		alert('Unable to save order.');
	}
});

updateCostCentreOptions(initialSelectedCostCentre);
recalculateTotals();
</script>
