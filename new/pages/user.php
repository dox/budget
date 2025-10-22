<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Your Account</h1>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 mb-3">
	<div class="col">
		<?php
		if ($user->isLoggedIn()) {
			$text = "Logged in";
			$class = "text-bg-success";
			$subtext = "9 mins";
		} else {
			$text = "Not logged in";
			$class = "text-bg-danger";
			$subtext = "";
		}
		?>
		<div class="card <?php echo $class; ?> shadow-sm">
			<div class="card-body">
				<p class="card-text"><?php echo $text; ?></p>
				<div class="text-end">
					<small class="text-body-secondary"><?php echo $subtext; ?></small>
				</div>
			</div>
		</div>
	</div>
	<div class="col">
		<?php
		$adminGroup = "CN=IT Support,OU=SEH Groups,DC=SEH,DC=ox,DC=ac,DC=uk";
		
		if ($user->isMemberOf($adminGroup)) {
			$text = "Admin member";
			$class = "text-bg-success";
			$subtext = $adminGroup;
		} else {
			$text = "Not admin member";
			$class = "text-bg-danger";
			$subtext = $adminGroup;
		}
		?>
		<div class="card <?php echo $class; ?> shadow-sm">
			<div class="card-body">
				<p class="card-text"><?php echo $text; ?></p>
				<div class="text-end text-truncate">
					<small class="text-body-secondary"><?php echo $subtext; ?></small>
				</div>
			</div>
		</div>
	</div>
	<div class="col">
		<?php
		if ($user->isLoggedIn()) {
			$text = "Logged in";
			$class = "text-bg-success";
			$subtext = "9 mins";
		} else {
			$text = "Not logged in";
			$class = "text-bg-danger";
			$subtext = "";
		}
		?>
		<div class="card <?php echo $class; ?> shadow-sm">
			<div class="card-body">
				<p class="card-text"><?php echo $text; ?></p>
				<div class="text-end">
					<small class="text-body-secondary"><?php echo $subtext; ?></small>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="mb-3">
	<label class="form-label text-uppercase text-muted small">Full Name</label>
	<input type="text" class="form-control" value="<?php echo $user->getFullname(); ?>" readonly>
</div>

<div class="mb-3">
	<label class="form-label text-uppercase text-muted small">Email</label>
	<input type="email" class="form-control" value="<?php echo $user->getEmail(); ?>" readonly>
</div>

<div class="row">
	<div class="col-6 mb-3">
		<label class="form-label text-uppercase text-muted small">Member Of</label>
		<ul class="list-group">
			<?php
			$userGroups = $user->groups();
			
			foreach ($user->memberOf() AS $group) {
				if (in_array($group, $userGroups)) {
					$badge = "<span class=\"badge text-bg-primary rounded-pill\">Group</span>";
				} else {
					$badge = "";
				}
				echo "<li class=\"list-group-item d-flex justify-content-between align-items-start\">";
				echo "<div class=\"ms-2 me-auto\">";
				echo $group;
				echo "</div>";
				echo $badge;
				echo "</li>";
			}
			?>
		</ul>
	</div>
	<div class="col-6 mb-3">
		
	</div>
</div>


<div class="mb-3">
	<label class="form-label text-uppercase text-muted small">Appearance</label>
	<div class="dropdown">
		<button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" aria-label="Toggle theme (auto)">
			<svg class="bi my-1 theme-icon-active" aria-hidden="true">
				<use href="assets/icons/icons.svg#circle-half"></use>
			</svg>
			<span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
		</button>
		<ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
			<li>
				<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
					<svg class="bi me-2 opacity-50" aria-hidden="true">
						<use href="assets/icons/icons.svg#sun-fill"></use>
					</svg>
	
					Light
					<svg class="bi ms-auto d-none" aria-hidden="true">
						<use href="assets/icons/icons.svg#check2"></use>
					</svg>
				</button>
			</li>
			<li>
				<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
					<svg class="bi me-2 opacity-50" aria-hidden="true">
						<use href="assets/icons/icons.svg#moon-stars-fill"></use>
					</svg>
	
					Dark
					<svg class="bi ms-auto d-none" aria-hidden="true">
						<use href="assets/icons/icons.svg#check2"></use>
					</svg>
				</button>
			</li>
			<li>
				<button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
					<svg class="bi me-2 opacity-50" aria-hidden="true">
						<use href="assets/icons/icons.svg#circle-half"></use>
					</svg>
	
					Auto
					<svg class="bi ms-auto d-none" aria-hidden="true">
						<use href="assets/icons/icons.svg#check2"></use>
					</svg>
				</button>
			</li>
		</ul>
	</div>
</div>