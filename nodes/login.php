<style>
.form-signin {
  width: 100%;
  max-width: 330px;
  padding: 15px;
  margin: auto;
}
.form-signin .checkbox {
  font-weight: 400;
}
.form-signin .form-control {
  position: relative;
  box-sizing: border-box;
  height: auto;
  padding: 10px;
  font-size: 16px;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="text"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
</style>

<form class="form-signin text-center" method="POST" action="index.php?n=dashbosard">
	<!--  <img class="mb-4" src="/docs/4.4/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">-->
  <i class="fas fa-8x fa-flip-horizontal fa-piggy-bank" style="color: #F86380;"></i>
	<h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
	<label for="username" class="sr-only">Username</label>
	<input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
	<label for="password" class="sr-only">Password</label>
	<input type="password" name="password" class="form-control" placeholder="Password" required>
	<!--<div class="checkbox mb-3">
		<label><input type="checkbox" name="remember-me" value="remember-me"> Remember me</label>
	</div>-->

	<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
  <?php
  if (RESET_URL != null) {
    echo "<hr /><a href=\"" . RESET_URL . "\">Forgot your password?</a>";
  }
  ?>
	<input type="hidden" name="loginformsubmit">
</form>

<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
