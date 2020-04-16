<h2 class="text-center mb-3">Log in</h2>


			<form method="POST" action="index.php?n=dashbosard">
				<div class="form-group">
					<label for="username">Username</label>
					<input type="text" readonly class="form-control" id="username" name="username">
				</div>
				
				<div class="form-group">
					<label for="password" class="form-label">Password</label>
					<input type="password" readonly class="form-control" id="password" name="password">
				<br />
				</div>
				<div class="form-group">
					<button class="btn btn-primary">Log in</button>
					<button type="button" class="btn btn-link pull-right" data-toggle="tooltip" data-placement="top" title="This is your St Edmund Hall username/password (the same as you use to login to the SEH computers)">Forgot your password?</button>
					<input type="hidden" name="loginformsubmit">
				</div>
			</form>


<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>