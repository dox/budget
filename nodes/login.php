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
  <svg width="200px" height="200px" style="color: #f77b7c">
    <use xlink:href="img/icons.svg#piggy-bank-regular"/>
  </svg>

	<h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
	<label for="username" class="visually-hidden">Username</label>
	<input type="text" id="username" name="username" class="form-control" placeholder="Username" required autofocus>
	<label for="password" class="visually-hidden">Password</label>
	<input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
	<!--<div class="checkbox mb-3">
		<label><input type="checkbox" name="remember-me" value="remember-me"> Remember me</label>
	</div>-->

	<button class="btn btn-lg btn-primary w-100" type="submit">Sign in</button>
  <?php
  if (RESET_URL != null) {
    echo "<hr /><a href=\"" . RESET_URL . "\">Forgot your password?</a>";
  }
  ?>
	<input type="hidden" name="loginformsubmit">
</form>
