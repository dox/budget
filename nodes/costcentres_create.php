<?php
function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}
function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}
?>

<h2>Create New Cost Centre</h2>

<form method="POST" action="index.php?n=costcentres_all">
	<div class="row">
    <div class="col-1">
			<div class="mb-3">
				<label for="colour">Colour</label>
          <input type="color" class="form-control form-control-color" name="colour" value="#<?php echo random_color(); ?>" title="Colour">
			</div>
		</div>
		<div class="col">
			<div class="mb-3">
				<label for="name">Name</label>
				<input type="text" class="form-control" id="name" name="name" placeholder="Name">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<div class="mb-3">
				<label for="code">Code</label>
				<input type="text" class="form-control" id="code" name="code" placeholder="Code">
			</div>
		</div>
		<div class="col">
			<div class="mb-3">
				<label for="budget">Budget (£)</label>
				<input type="text" class="form-control" id="budget" name="budget" placeholder="Budget (without £ or commas)" value="0">
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm">
			<div class="mb-3">
				<label for="department">Department</label>
				<select class="form-select" name="department">
					<?php
					foreach (class_departments::all() AS $department) {
						if ($department['uid'] == $_SESSION['department']) {
							$selected = " selected";
						} else {
							$selected = " ";
						}
						$output  = "<option " . $selected . " value=\"" .  $department['uid'] . "\">" . $department['name'] . "</option>";

						echo $output;
					}
					?>
				</select>
			</div>
		</div>
		<div class="col-sm">
			<div class="mb-3">
				<label for="group_name">Group Name</label>
				<input class="form-control" list="datalistOptions" id="group_name" name="group_name" placeholder="Group Name">
				<datalist id="datalistOptions">
					<?php
					foreach (class_cost_centres::groups() AS $group) {
						$output = "<option value=\"" . $group['group_name'] . "\">";

						echo $output;
					}
					?>
				</datalist>
			</div>
		</div>
	</div>
	<div class="mb-3">
		<label for="description">Description</label>
		<textarea class="form-control" id="description" name="description" rows="3"></textarea>
	</div>

	<button type="submit" class="btn btn-primary">Submit</button>
</form>
