<?php
$userObject = new user($_SESSION['username']);
?>

<h2><i class="fas fa-lock fa-sm"></i> User Settings <small class="text-muted"><?php echo $userObject->username;?></small></h2>
<form>
	<div class="mb-3">
		<label for="firstname">First Name</label>
		<input type="text" class="form-control" id="firstname" value="<?php echo $userObject->firstname; ?>">
	</div>
	<div class="mb-3">
		<label for="lastname">Last Name</label>
		<input type="text" class="form-control" id="lastname" value="<?php echo $userObject->lastname; ?>">
	</div>
	<div class="mb-3">
		<label for="type">User Type</label>
		<select class="form-select" id="type">
			<option <?php if ($userObject->type == "administrator") { echo " selected "; } ?>value="administrator">Administrator</option>
			<option <?php if ($userObject->type == "accountant") { echo " selected "; } ?>value="accountant">Accountant</option>
			<option <?php if ($userObject->type == "purchaser") { echo " selected "; } ?>value="purchaser">Purchaser</option>
			<option <?php if ($userObject->type == "viewer") { echo " selected "; } ?>value="viewer">Viewer</option>
		</select>
	</div>
	<div class="mb-3">
		<label for="department">Department</label>
		<select class="form-select" id="department">
			<?php
			foreach (class_departments::all() AS $department) {
				if ($department['uid'] == $userObject->department) {
					$selected = " selected ";
				} else {
					$selected = "";
				}

				$output  = "<option value=\"\" " . $selected . ">" . $department['name'] . "</option>";

				echo $output;
			}
			?>
		</select>
	</div>
	<button disabled type="submit" class="btn btn-primary">Submit</button>
</form>

<hr />
<h2><i class="fas fa-lock fa-sm"></i> LDAP Account</h2>
<pre><?php print_r($_SESSION); ?></pre>

<hr />
<h2><i class="fas fa-cogs fa-sm"></i> System Settings</h2>
<?php echo "Budget Start Date: " . budgetStartDate() . "<br />"; ?>
<?php echo "Budget End Date: " . budgetEndDate() . "<br /><br />"; ?>
<?php echo "Percentage Into the Budget Year: " . percentageIntoBudget() ."%"; ?>

<hr />
<h2><i class="fas fa-wrench fa-sm"></i> System Maintenance</h2>
<a href="index.php?n=index&logout" class="btn btn-warning"><i class="fas fa-sign-out-alt"></i> Sign Out</a><br /><br />

<?php if ($userObject->type == "administrator" || $userObject->type == "accountant") { ?>
<div class="dropdown">
	<a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Temporarily Change Deparments</a>

	<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
		<?php
		foreach (class_departments::all() AS $department) {
			if ($department['uid'] == $userObject->department) {
				$selected = " selected ";
			} else {
				$selected = "";
			}

			$output  = "<a class=\"dropdown-item\" onclick=\"departmentChange(this.id)\" id=\"" . $department['uid'] . "\">" . $department['name'] . "</a>";

			echo $output;
		}
		?>
  </div>
</div>
<?php } ?>
