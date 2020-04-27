<?php
$users_class = new class_users;
$user = $users_class->getOne($_SESSION['username']);

$departments_class = new class_departments;
$departments = $departments_class->all();
?>

<h2><i class="fas fa-lock fa-sm"></i> User Settings <small class="text-muted"><?php echo $_SESSION['username'];?></small></h2>
<form>
	<div class="form-group">
		<label for="firstname">First Name</label>
		<input type="text" class="form-control" id="firstname" value="<?php echo $user['firstname']; ?>">
	</div>
	<div class="form-group">
		<label for="lastname">Last Name</label>
		<input type="text" class="form-control" id="lastname" value="<?php echo $user['lastname']; ?>">
	</div>
	<div class="form-group">
		<label for="type">User Type</label>
		<select class="form-control" id="type">
			<option <?php if ($user['type'] == "administrator") { echo " selected "; } ?>value="administrator">Administrator</option>
			<option <?php if ($user['type'] == "accountant") { echo " selected "; } ?>value="accountant">Accountant</option>
			<option <?php if ($user['type'] == "purchaser") { echo " selected "; } ?>value="purchaser">Purchaser</option>
			<option <?php if ($user['type'] == "viewer") { echo " selected "; } ?>value="viewer">Viewer</option>
		</select>
	</div>
	<div class="form-group">
		<label for="department">Department</label>
		<select class="form-control" id="department">
			<?php
			foreach ($departments AS $department) {
				if ($department['uid'] == $user['department']) {
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
<?php echo "Budget Start Date: " . BUDGET_STARTDATE . "<br />"; ?>
<?php echo "Budget End Date: " . BUDGET_ENDDATE . "<br /><br />"; ?>
<?php echo "Percentage Into the Budget Year: " . percentageIntoBudget() ."%"; ?>

<hr />
<h2><i class="fas fa-wrench fa-sm"></i> System Maintenance</h2>
<a href="index.php?n=index&logout" class="btn btn-warning">Log Out</a><br /><br />

<div class="dropdown">
	<a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Temporarily Change Deparments</a>

	<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
		<?php
		foreach ($departments AS $department) {
			if ($department['uid'] == $user['department']) {
				$selected = " selected ";
			} else {
				$selected = "";
			}

			$output  = "<a class=\"dropdown-item emailParcelButton1\" href=\"#\" id=\"" . $department['uid'] . "\">" . $department['name'] . "</a>";

			echo $output;
		}
		?>
  </div>
</div>
