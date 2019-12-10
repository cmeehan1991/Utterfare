<div class="container-fluid">
	<div class="row">
		<div class="col">
			<h2>Company Profile</h2>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<form name="company-profile-form" onsubmit="return saveCompanyProfile(this);">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="company-name">Company Name &ast;</label>
							<input type="text" name="company-name" class="form-control" required>
						</div>
						<div class="form-group">
							<label for="street-address">Street Address &ast;</label>
							<input type="text" name="street-address" class="form-control" required>
						</div>
						<div class="form-group">
							<label for="secondary-address">Secondary Address</label>
							<input type="text" name="secondary-address" aria-describedby="secondary-address-descriptoin" class="form-control">
							<small id="secondary-address-description" class="form-text text-muted">Suite, Unit #, etc.</small>
						</div>
						<div class="form-row">
							<div class="col-6">
								<label for="city">City &ast;</label>
								<input type="text" name="city" class="form-control">
							</div>
							<div class="col-3">
								<label for="state">State &ast;</label>
								<select name="state" class="custom-select custom-select-md md-3" required>
									<option value="AL">AL</option>
									<option value="AK">AK</option>
									<option value="AR">AR</option>	
									<option value="AZ">AZ</option>
									<option value="CA">CA</option>
									<option value="CO">CO</option>
									<option value="CT">CT</option>
									<option value="DC">DC</option>
									<option value="DE">DE</option>
									<option value="FL">FL</option>
									<option value="GA">GA</option>
									<option value="HI">HI</option>
									<option value="IA">IA</option>	
									<option value="ID">ID</option>
									<option value="IL">IL</option>
									<option value="IN">IN</option>
									<option value="KS">KS</option>
									<option value="KY">KY</option>
									<option value="LA">LA</option>
									<option value="MA">MA</option>
									<option value="MD">MD</option>
									<option value="ME">ME</option>
									<option value="MI">MI</option>
									<option value="MN">MN</option>
									<option value="MO">MO</option>	
									<option value="MS">MS</option>
									<option value="MT">MT</option>
									<option value="NC">NC</option>	
									<option value="NE">NE</option>
									<option value="NH">NH</option>
									<option value="NJ">NJ</option>
									<option value="NM">NM</option>			
									<option value="NV">NV</option>
									<option value="NY">NY</option>
									<option value="ND">ND</option>
									<option value="OH">OH</option>
									<option value="OK">OK</option>
									<option value="OR">OR</option>
									<option value="PA">PA</option>
									<option value="RI">RI</option>
									<option value="SC">SC</option>
									<option value="SD">SD</option>
									<option value="TN">TN</option>
									<option value="TX">TX</option>
									<option value="UT">UT</option>
									<option value="VT">VT</option>
									<option value="VA">VA</option>
									<option value="WA">WA</option>
									<option value="WI">WI</option>	
									<option value="WV">WV</option>
									<option value="WY">WY</option>
								</select>		
							</div>
							<div class="col-3">
								<label for="postal-code">Postal Code &ast;</label>
								<input type="text" name="postal-code" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="telephone">Telephone &ast;</label>
							<input type="tel" name="telephone" class="form-control" required>
						</div>
						<div class="form-group">
							<label for="email">Email Address &ast;</label>
							<input type="email" name="email" class="form-control" required>
						</div>
						<div class="form-group">
							<label for="website">Website &ast;</label>
							<input type="url" name="website" class="form-control" aria-describedby="websiteDescription" required>
							<small id="websiteDescription" class="form-text text-muted">Can be a link to your Facebook, LinkedIn, Yelp, Instagram, etc.</small>

						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="profile-picture">Profile Picture</label>
							<img class="profile-picture">
							<input name="profile-picture" type="file" aria-describedby="profilePictureDescription">
							<small id="profilePictureDescription" class="form-text text-muted">Image size recommendations 400px X 400px, 800px X 800px, or 1400px X 1400px.<br/>All images will be cropped to a 1:1 ratio when saved.</small>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>