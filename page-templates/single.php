<div class="container-fluid single-item singleItemController" ng-app="SingleItem">
	
	<!-- Item hero section --> 
	<div class="row single-item--hero">
			<div class="col-md-4 mx-auto single-item--title-section d-flex align-items-center">
				<h2 class="item-name"></h2>
			</div>
			<div class="col-md-8 mx-auto single-item--image-section">
				<img class="item-image">
			</div>
	</div>
	
	<!-- Item description section --> 
	<div class="row">
		<div class="col-md-12 mx-auto">
			<p class="item-description"></p>
		</div>
	</div>
	
	<!-- Restaurant Location Section -->
	<div class="row">
		<div class="col-md-12 mx-auto">
			<h3>Location</h3>
			<a class="vendor-address" target="_blank">Address</a>
			<div id="single-item--map"></div>
		</div>
	</div>
	
	<!-- Related items section -->
	<div class="row">
		<div class="col-md-12 related-items">
			<ul class="related-vendor-items">
			</ul>
		</div>
	</div>

</div>