
<div class="modal" id="locationModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Change the Search Location</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form name="searchLocationForm" onsubmit="return setManualSearchLocation()">
	      <div class="modal-body">
		      <p>
		      	<label for="location"><strong>City &amp; State or Zip Code</strong></label>
			  	<input type="text" name="location" required>
		      </p>
		      <p>
			      <label for="distance"><strong>Distance</strong></label>
			      <select name="distance" required> 
				      <option value="1">1 Mile</option>
				      <option value="2">2 Mile</option>
				      <option value="5">3 Mile</option>
				      <option value="10" selected>10 Mile</option>
				      <option value="15">15 Mile</option>
				      <option value="20">20 Mile</option>
			      </select>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary">Save changes</button>
	      </div>
      </form>
    </div>
  </div>
</div>


<div class="modal" id="signInModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" ng-controller="SignInController">
      <div class="modal-header">
        <h5 class="modal-title">Sign In</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	      <p>
	      	<label for="username"><strong>Username</strong></label>
		  	<input type="text" name="username" ng-model="user.username">
	      </p>
	      <p>
		      <label for="password"><strong>Password</strong></label>
			  <input type="password" name="password" ng-model="user.password">
	      </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" ng-click="setManualSearchLocation(data)">Close</button>
        <button type="submit" class="btn btn-primary" ng-click="signUserIn(user)">Sign In</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="noticeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" ng-controller="SignInController">
      <div class="modal-header alert alert-danger">
        <h5 class="modal-title">Holy guacamole!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	     <p>It looks like you forgot to search for something!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" ng-click="setManualSearchLocation(data)">Ok!</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="loadingModal" data-backdrop="static" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<div class="d-flex justify-content-center">
					<div class="spinner-border text-primary" role="status">
						<span class="sr-only">Loading...</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>