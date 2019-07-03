<div class="container-fluid">
	<div class="row">
		<div class="col-md-6 mx-auto justify-content-center">
			<h1>New User</h1>
		</div>
	</div>
	<div class="row">
		<div class='col-md-6 mx-auto justify-content-center'>
			<form class="newUserForm" ng-submit="newUser(user)" ng-controller="UserController">
				<div class="form-group">
					<label for="username">Email Address</label>
					<input type="email" name="username" class="form-control" aria-describedby="usernameHelp" placeholder="Enter email" ng-model="user.username" required>
					<small id="emailHelp" class="form-text text-muted">We&apos;ll never share your email with anyone else.</small>
				</div>
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" name="password" class="form-control" placeholder="Password" ng-model="user.password" required>
				</div>
				<div class="form-group">
					<label for="confirm_password">Confirm Password</label>
					<input type="confirm_password" name="password" class="form-control" placeholder="Password" ng-model="user.confirm_password" required>
					<span style="color:red">{{password_match}}</span>
				</div>
				<div class="form-group">
					<label for="first_name">First Name</label>
					<input type="text" name="first_name" class="form-control" placeholder="First Name" ng-model="user.first_name">
				</div>
				<div class="form-group">
					<label for="last_name">Last Name</label>
					<input type="text" name="last_name" class="form-control" placeholder="Last Name" ng-model="user.last_name">
				</div>
				<div class="form-group">
					<label for="telephone_number">Telephone Number</label>
					<input type="tel" name="telephone_number" class="form-control" placeholder="(###)###-####" ng-model="user.telephone_number">
				</div>
				<button type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>
	</div>
</div>