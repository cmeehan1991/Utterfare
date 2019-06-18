var main;
var results;
var loadingIndicator; 
var recommendationsCarousel;

$(document).ready(function(){
	main = $('.content');
	results = $('.results');
	loadingIndicator = $('.loading-indicator--section');

	console.log('detatch main content');
	
	main.detach();
	
	recommendationsCarousel = $('.recommendations-carousel__inner');
});

function curateHomepageSections(user_location){
	main.detach();
		
	getRecommendations(user_location);
	
	$('.content').append(window.main);
}

/*
* Populate the recommended items section
*/
function getRecommendations(user_location){
	
	var data = {
		"action": "get_recommendations",
		"location": user_location
	};
	
	var recommendations = "<div class='carousel-item active'><div class='row'>";
	
	var count = 0;

	console.log(window.search_url);

	$.post(window.search_url, data, function(response){

		$.each(response, function(key, value){
			console.log(value.address);
			recommendations += '<div class="col-md-3 mx-auto">';
			recommendations += '<div class="card recommendation">'; 
			recommendations += '<img src="assets/img/mahi-mahi.jpg" class="card-img-top" alt="...">';
			recommendations += '<div class="card-body">';
			recommendations += '<div class="card-title"><h3>' + value.item_name + '</h3></div>';
			recommendations += '<div class="card-text">'; 
			recommendations += '<i class="recommendation__location">' + $.parseJSON(value.address)._address + "</i>";
			recommendations += '<h4 class="recommendation__vendor">' + value.vendor_name + "</h4>";
			recommendations += '<p>' + value.item_short_description + '</p>'; 
			recommendations += '</div>'; // .card-text
			recommendations += '</div>'; // .card-body
			recommendations += '</div>'; // .recommendation
			recommendations += '</div>'; // .col-md-3
			
			count += 1;
			
			if(count === 4){
				recommendations += "</div></div><div class='carousel-item'><div class='row'>";
			}else if(count === 8){
				recommendations += "</div></div>";
			}
		});

	}, 'json')
	.done(function(){
		$('.recommendations-carousel__inner').html(recommendations);
		console.log('done');
		loadingIndicator.detach();
	});
		
}

