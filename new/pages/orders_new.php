<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">New Order</h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group me-2">
			<a href="index.php?page=orders_new" class="btn btn-sm btn-outline-secondary">Upload attachment</a>
		</div>
		<div class="dropdown">
			<button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
				<svg class="bi" aria-hidden="true">
					<use xlink:href="assets/icons/icons.svg#calendar3"></use>
				</svg> Edit
			</button>
			<ul class="dropdown-menu">
				<li><a class="dropdown-item" href="#">Clone</a></li>
				<li><a class="dropdown-item text-danger" href="#">Delete</a></li>
			</ul>
		</div>
	</div>
</div>

<form method="POST" id="create_order_form" action="index.php?n=orders_all" class="needs-validation" novalidate>
  <div class="row g-3">
	<!-- Date -->
	<div class="col-md-6">
	  <label for="date" class="form-label">Date</label>
	  <input type="text" class="form-control" id="date" name="date" value="2025-10-22 13:57" required>
	  <div class="invalid-feedback">Please provide a valid date.</div>
	</div>

	<!-- Purchase Order -->
	<div class="col-md-6">
	  <label for="po" class="form-label">Purchase Order #</label>
	  <input type="text" class="form-control" id="po" name="po" value="IT01601" required>
	  <div class="form-text">Auto-generated based on the last order.</div>
	  <div class="invalid-feedback">Please provide a valid Purchase Order number.</div>
	</div>
  </div>

  <div class="row g-3 mt-0">
	<!-- Supplier -->
	<div class="col-md-6">
	  <label for="supplier" class="form-label">Supplier</label>
	  <input class="form-control" list="supplierList" id="supplier" name="supplier" placeholder="Select or type supplier">
	  <datalist id="supplierList">
		<option value="3D People">
		<option value="Amazon">
		<option value="Wireless Sensors">
		<option value="eBay">
		<option value="Comms-Express">
		<option value="Insight">
		<option value="ITSS">
		<option value="Apple">
		<option value="Dell">
		<option value="Monica">
		<option value="Printerland.co.uk">
		<option value="FedEx">
		<option value="Vision Secure">
		<option value="IT and General Ltd">
		<option value="RS Components">
		<option value="Jigsaw24">
		<option value="Cloud Controlled">
		<option value="OpenVPN">
		<option value="PA Collacott">
		<option value="AirTame">
		<option value="123-reg.co.uk">
		<option value="Panic">
		<option value="WP Rocket">
		<option value="SearchWP">
		<option value="1Password">
		<option value="GOV.UK">
		<option value="The Pi Hut">
		<option value="Edwardes">
		<option value="miniOrange">
		<option value="Onkron">
	  </datalist>
	</div>

	<!-- Supplier Order # -->
	<div class="col-md-6">
	  <label for="order_num" class="form-label">Supplier Order #</label>
	  <input type="text" class="form-control" id="order_num" name="order_num" placeholder="Supplier Order #">
	</div>
  </div>

  <!-- Order Name -->
  <div class="mb-3 mt-3">
	<label for="name" class="form-label">Order Name</label>
	<input type="text" class="form-control" id="name" name="name" placeholder="Enter order name" required>
	<div class="invalid-feedback">Please provide an order name.</div>
  </div>

  <div class="row g-3">
	<!-- Cost Centre -->
	<div class="col-md-6">
	  <label for="cost_centre" class="form-label">Cost Centre</label>
	  <select class="form-select" id="cost_centre" name="cost_centre" required>
		<option value="">Select Cost Centre</option>
		<optgroup label="1. Software">
		  <option value="1">87200 - IT Costs - Purchased Software</option>
		  <option value="4">87500 - IT Costs - Software maintenance</option>
		  <option value="6">87500 - IT Costs - Software maintenance</option>
		  <option value="7">87600 - IT Costs - Wireless Licences</option>
		</optgroup>
		<optgroup label="2. Telephony">
		  <option value="25">80210 - Telephone costs - Chorus/Teams</option>
		  <option value="26">80220 - Telephone Costs - mobiles</option>
		</optgroup>
		<optgroup label="3. Photocopiers">
		  <option value="29">80156 - Photocopiers</option>
		</optgroup>
		<optgroup label="4. Hardware">
		  <option value="5">87300 - IT Costs - consumables</option>
		  <option value="279">87100 - IT Costs - Hardware</option>
		  <option value="2">87400 - IT Costs - Hardware maintenance</option>
		</optgroup>
		<optgroup label="5. Other">
		  <option value="22">773 4620 - EQU-DATA CABLING</option>
		  <option value="280">87700 - IT Costs - IT Services contribution</option>
		  <option value="281">NSE - NSE Build</option>
		  <option value="30">OTHER - OTHER</option>
		</optgroup>
	  </select>
	  <div class="invalid-feedback">Please select a Cost Centre.</div>
	</div>

	<!-- Value -->
	<div class="col-md-6">
	  <label for="value" class="form-label">Value (£)</label>
	  <input type="number" step="0.01" class="form-control" id="value" name="value" placeholder="Enter value (without £ or commas)" required>
	  <div class="invalid-feedback">Please provide an order value.</div>
	</div>
  </div>

  <!-- Description -->
  <div class="mb-3 mt-3">
	<label for="description" class="form-label">Description</label>
	<textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter description"></textarea>
  </div>

  <!-- Submit -->
  <div class="d-flex justify-content-end">
	<button type="button" id="test" class="btn btn-primary" onclick="createOrder(this.id)">
	  Submit Order
	</button>
  </div>
</form>
