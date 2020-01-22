
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
		      <div class="form-group row">
				     <div class="col">
				      <label for="location">Search Location</label>
				      <input type="text" name="search-location" class="form-control" aria-describedby="location-help">
				      <small id="location-help" class="form-text text-muted">ex. 6 Kent Ct, Hilton Head Island, SC; Raleigh, NC</small>
			      </div>
		      </div>
		      <div class="form-group">
			      <label for="search-distance">Distance</label>
			      <select class="custom-select" name="search-distance" required> 
				      <option value="1">1 Mile</option>
				      <option value="2">2 Miles</option>
				      <option value="5">3 Miles</option>
				      <option value="10">10 Miles</option>
				      <option value="15">15 Miles</option>
				      <option value="20">20 Miles</option>
			      </select>
		      </div>
		      <p>

	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary" onclick="saveSearchLocation()">Save changes</button>
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

<div class="toast mx-auto" id="open-in-app-toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
	<div class="toast-header">
		<img src="assets/img/UF%20Logo.png" alt="UF Logo" width="25" height="25" />		
		<strong class="mr-auto">Open in App</strong>
		<button type="button" class="ml-2 mb-1 close btn-light" data-dismiss="toast" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<div class="toast-body">
		<p>Use the Utterfare&trade; app for the best experience.</p>
		<a href="" class="btn btn-outline-light open-in-app-button">Open in App</a>
	</div>
</div>