<?php
session_start();
include 'header.php'; 
?>
<div class="container-fluid content" ng-view>
	
	<div class="main-content">
	<!-- Featured Items-->
		<div class="row">
			<div class="col-md-6 mr-auto ml-5">
				<h2>Find the food you want and when you want it<br/>wheverever you go</h2>			
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 mx-auto">
				<div class="row featured-items-row--top-items"></div>
			</div>
		</div>
		
		<!-- Recommendations based on location and recent searches -->
		<div class="row">
			<div class="col-md-6 mr-auto ml-5">
				<h2>Recommended for you</h2>
			</div>
			<div class="row">
				<div class="col-md-10 mx-auto">
					<div id="#recommendationsCarousel" class="carousel slide" data-ride="carousel">
						<div class="carousel-inner recommendations-carousel__inner"></div> <!-- .carousel-inner -->	
						<a class="carousel-control-prev" href="#recommendationsCarousel" role="button" data-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="sr-only">Previous</span>
						</a>
						<a class="carousel-control-next" href="#recommendationsCarousel" role="button" data-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="sr-only">Next</span>
						</a>			
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<?php include('footer.php'); 