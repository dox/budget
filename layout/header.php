<header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
	<a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="#">
		<i class="bi bi-piggy-bank" aria-hidden="true"></i>
		<?php echo APP_NAME; ?>
	</a>
	<ul class="navbar-nav flex-row d-md-none">
		<li class="nav-item text-nowrap">
			<button class="nav-link px-3 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSearch" aria-controls="navbarSearch" aria-expanded="false" aria-label="Toggle search">
				<i class="bi bi-search" aria-hidden="true"></i>
			</button>
		</li>
		<li class="nav-item text-nowrap">
			<button class="nav-link px-3 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
				<i class="bi bi-list" aria-hidden="true"></i>
			</button>
		</li>
	</ul>
	<form id="navbarSearch" class="navbar-search w-100 collapse" method="get" action="index.php">
		<input type="hidden" name="page" value="order_search">
		<input class="form-control w-100 rounded-0 border-0" type="search" name="q" value="<?= htmlspecialchars((string) ($_GET['q'] ?? '')) ?>" placeholder="Search orders" aria-label="Search all orders">
	</form>
</header>
