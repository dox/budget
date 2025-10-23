<?php
$log->add("404 error: {$_SERVER['REQUEST_URI']}", Log::ERROR);
?>

<div class="d-flex align-items-center justify-content-center min-vh-100 px-2">
	<div class="text-center">
		<h1 class="display-1 fw-bold">404</h1>
		<p class="fs-2 fw-medium mt-4">Oops! Page not found</p>
		<p class="mt-4 mb-5"><mark><?php echo $_SERVER['REQUEST_URI']; ?></mark></p>
		<p class="mt-4 mb-5">The page you're looking for doesn't exist or has been moved.</p>
		<a href="index.php" class="btn btn-light fw-semibold rounded-pill px-4 py-2 custom-btn">
			Go Home
		</a>
	</div>
</div>