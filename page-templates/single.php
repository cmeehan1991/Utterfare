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

	<!-- Rating and review section --> 
	<div class="row">
		<div class="col-md-12 mx-auto">
			<h3>Rating</h3>
			<ul class="item-rating">
				<li class="item-rating--star" data-rating="1"><i class="far fa-star"></i></li>
				<li class="item-rating--star" data-rating="2"><i class="far fa-star"></i></li>
				<li class="item-rating--star" data-rating="3"><i class="far fa-star"></i></li>
				<li class="item-rating--star" data-rating="4"><i class="far fa-star"></i></li>
				<li class="item-rating--star" data-rating="5"><i class="far fa-star"></i></li>
				<li class="item-rating--number"></li>
			</ul>
			<h3>Reviews</h3>
			<ul class="item-reviews"></ul>
			
			<?php
			if(SIGNED_IN): ?>
			<div class="item-review">
				<form name="item-review-form" ng-submit="submitItemReview">
					<div class="form-group">
						<label for="item-rating">Overall Rating</label>
						<ul class="item-rating">
							<li class="item-rating--star" data-rating="1"><i class="far fa-star"></i></li>
							<li class="item-rating--star" data-rating="2"><i class="far fa-star"></i></li>
							<li class="item-rating--star" data-rating="3"><i class="far fa-star"></i></li>
							<li class="item-rating--star" data-rating="4"><i class="far fa-star"></i></li>
							<li class="item-rating--star" data-rating="5"><i class="far fa-star"></i></li>
							<li class="item-rating--number"></li>
						</ul>
					</div>
					<div class="form-group">
						<label for="form-title">Add a headline</label>
						<input type="text" name="review-title" class="form-control" placholder="Enter a title for your review">
					</div>
					<div class="form-group">
						<label for="form-text">Write your review</label>
						<textarea name="review-text" class="form-control" rows="3"></textarea>
					</div>
					<button type="submit" class="btn btn-secondary">Submit</button>
				</form> 
			</div>
			<?php endif; ?>
		</div>
	</div>	
</div>