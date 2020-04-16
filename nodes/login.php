<h2 class="text-center mb-3">Log in</h2>
<div class="container">
	<div class="row">
		<div class="col">
			
		</div>
		<div class="col-6 bg-white">
			<form method="POST" action="index.php?n=dashbosard">
				<div class="form-group">
					<label for="username" class="form-label">Username</label>
					<input type="text" class="form-control" id="username" name="username">
				</div>
				
				<div class="form-group">
					<label for="password" class="form-label">Password</label>
					<input type="password" class="form-control" id="password" name="password">
				<br />
				</div>
				<div class="form-group">
					<button class="btn btn-primary">Log in</button>
					<button type="button" class="btn btn-link" data-toggle="tooltip" data-placement="top" title="This is your St Edmund Hall username/password (the same as you use to login to the SEH computers)">Forgot your password?</button>
					<input type="hidden" name="loginformsubmit">
				</div>
			</form>
		</div>
		<div class="col">
			
		</div>
	</div>
</div>

			


<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>