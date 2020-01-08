<div class="container-fluid vh-100">
		<div class="col-md-6 col-lg-3 mx-auto my-auto">
			<h2>Vendor Sign In</h2>
			<form name="vendorSignInForm" class="vendor-login-form" onsubmit="return vendorSignIn();">

			<div class="form-group">
				<label for="username">Username/Email address</label>
				<input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Enter email" name="username">
			</div>
			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" class="form-control" placeholder="Password" name="password">
			</div>
			<div class="form-group form-check">
				<input type="checkbox" class="form-check-input" id="exampleCheck1" name="remember-me">
				<label class="form-check-label" for="exampleCheck1">Remember Me</label>
			</div>
			<button type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>
	</div>
</div>