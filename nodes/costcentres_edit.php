<?php
$costCentreObject = new cost_centre($_GET['uid']);

if (isset($_POST['code'])) {
	$data = Array (
		"uid" => $_POST['uid'],
		"code" => $_POST['code'],
		"name" => $_POST['name'],
		"department" => $_POST['department'],
		"group_name" => $_POST['group_name'],
		"description" => $_POST['description'],
		"colour" => $_POST['colour'],
		"value" => $_POST['budget']
	);

	$costCentreObject->update($data);
}
?>

<h2>Edit Cost Centre '<?php echo $costCentreObject->name;?>'</h2>

<form method="POST" action="index.php?n=costcentres_edit&uid=<?php echo $costCentreObject->uid;?>">
	<div class="row">
		<div class="col-1">
			<div class="mb-3">
				<label for="colour">Colour</label>
				<input type="color" class="form-control form-control-color" name="colour" value="<?php echo $costCentreObject->colour;?>" title="Colour">
			</div>
		</div>
		<div class="col">
			<div class="mb-3">
				<label for="name">Name</label>
				<input type="text" class="form-control" id="name" name="name" placeholder="Name" value="<?php echo $costCentreObject->name;?>">
			</div>
		</div>

	</div>

	<div class="row">
		<div class="col">
			<div class="mb-3">
				<label for="code">Code</label>
				<input type="text" class="form-control" id="code" name="code" placeholder="Code" value="<?php echo $costCentreObject->code;?>">
			</div>
		</div>
		<div class="col">
			<div class="mb-3">
				<label for="budget">Budget (£)</label>
				<input type="text" class="form-control" id="budget" name="budget" placeholder="Budget (without £ or commas)" value="<?php echo $costCentreObject->value;?>">
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
						if ($department['uid'] == $costCentreObject->department) {
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
				<input class="form-control" list="datalistOptions" id="group_name" name="group_name" placeholder="Group Name" value="<?php echo $costCentreObject->group_name;?>">
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
		<textarea class="form-control" id="description" name="description" rows="3"><?php echo $costCentreObject->description;?></textarea>
	</div>

	<input type="hidden" name="uid" value="<?php echo $costCentreObject->uid;?>">
	<button type="submit" class="btn btn-primary">Submit</button>
</form>
