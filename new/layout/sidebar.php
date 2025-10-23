<div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
	<div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
		<div class="offcanvas-header">
			<h5 class="offcanvas-title" id="sidebarMenuLabel">Budget</h5>
			<button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
		</div>
		<div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
			<ul class="nav flex-column">
				<li class="nav-item">
					<a class="nav-link d-flex align-items-center gap-2 active" aria-current="page" href="index.php">
						<i class="bi bi-house-fill" aria-hidden="true"></i>
						Dashboard
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link d-flex align-items-center gap-2" href="index.php?page=orders">
						<i class="bi bi-file-earmark" aria-hidden="true"></i>
						Orders
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link d-flex align-items-center gap-2" href="index.php?page=cost_centres">
						<i class="bi bi-receipt" aria-hidden="true"></i>
						Cost Centres
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link d-flex align-items-center gap-2" href="index.php?page=customers">
						<i class="bi bi-people" aria-hidden="true"></i>
						Suppliers
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link d-flex align-items-center gap-2" href="index.php?page=reports">
						<i class="bi bi-graph-up" aria-hidden="true"></i>
						Reports
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link d-flex align-items-center gap-2" href="integrations">
						<i class="bi bi-puzzle" aria-hidden="true"></i>
						Integrations
					</a>
				</li>
			</ul>
			<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-body-secondary text-uppercase">
				<span>Saved reports</span>
				<a class="link-secondary" href="index.php?page=reports_new" aria-label="Add a new report">
					<i class="bi bi-plus-circle" aria-hidden="true"></i>
				</a>
			</h6>
			<ul class="nav flex-column mb-auto">
				<li class="nav-item">
					<a class="nav-link d-flex align-items-center gap-2" href="index.php?page=report1">
						<i class="bi bi-file-earmark-text" aria-hidden="true"></i>
						Current month
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link d-flex align-items-center gap-2" href="index.php?page=report1">
						<i class="bi bi-file-earmark-text" aria-hidden="true"></i>
						Last month
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link d-flex align-items-center gap-2" href="index.php?page=report1">
						<i class="bi bi-file-earmark-text" aria-hidden="true"></i>
						Year-to-date
					</a>
				</li>
			</ul>
			<hr class="my-3">
			<ul class="nav flex-column mb-auto">
				<li class="nav-item">
					<a class="nav-link d-flex align-items-center gap-2" href="index.php?page=settings">
						<i class="bi bi-gear-wide-connected" aria-hidden="true"></i>
						Settings
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link d-flex align-items-center gap-2" href="index.php?page=user">
						<i class="bi bi-person-fill" aria-hidden="true"></i>
						Your Account
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link d-flex align-items-center gap-2" href="index.php?page=logs">
						<i class="bi bi-clock-history" aria-hidden="true"></i>
						Logs
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link d-flex align-items-center gap-2" href="logout.php">
						<i class="bi bi-door-closed" aria-hidden="true"></i>
						Sign out
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>