var main;
var results;
var loadingIndicator; 
var recommendationsCarousel;

$(document).ready(function(){
	main = $('.main');
	results = $('.results');
	loadingIndicator = $('.loading-indicator--section');
	
	main.detach();
	
	recommendationsCarousel = $('.recommendations-carousel__inner');
	
});

function curateHomepageSections(user_location){		
	main.detach();
	$('.content').append(loadingIndicator);
	
	getTopItems();
	getRecommendations(user_location);
		
	$('.content').append(main);
}

function getTopItems(){
	var data = {
		'action': 'get_top_items'
	}
	
	var top_items = '';
	
	$.post(window.search_url, data, function(response){
		$.each(response, function(k, v){
						
			var address_parts = $.parseJSON(v.address);
			
			var address = address_parts._city + ", " + address_parts._state.toUpperCase();
			top_items += '<div class="col mx-auto d-flex">';
			top_items += '<div class="card featured-item">';
			top_items += '<img src="' + v.primary_image + '" class="card-img-top" alt="' + v.item_name + '">';
			top_items += '<div class="card-body">';
			top_items += '<div class="card-title">';
			top_items += '<h3>' + v.item_name + '</h3>'
			top_items += '</div>';
			top_items += '<div class="card-text">';
			top_items += '<i class="featured-item__location">' + address + '</i><br/>';
			top_items += '<strong class="featured-item__vendor">' + v.vendor_name + '</strong><br/>';
			top_items += '<p class="featured-item__short-description">' + v.item_short_description + '</p>';
			top_items += '<a href="#!/single?id=' + v.item_id + '" type="button" class="btn btn-light">More Info</a>'; 
			top_items += '</div></div></div></div>';

		});
	}, 'json')
	.done(function(){
		$('.featured-items-row--top-items').append(top_items);
	});
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


	$.post(window.search_url, data, function(response){
		
		$.each(response, function(key, value){
			var address_parts = $.parseJSON(value.address);
			var address = address_parts._city + ", " + address_parts._state.toUpperCase();
			
			recommendations += '<div class="col-md-3 mx-auto d-flex">';
			recommendations += '<div class="card recommendation">'; 
			recommendations += '<img src="' + value.primary_image + '" class="card-img-top" alt="' + value.item_name + '">';
			recommendations += '<div class="card-body">';
			recommendations += '<div class="card-title"><h3>' + value.item_name + '</h3></div>';
			recommendations += '<div class="card-text">'; 
			recommendations += '<i class="recommendation__location">' + address + "</i>";
			recommendations += '<h4 class="recommendation__vendor">' + value.vendor_name + "</h4>";
			recommendations += '<p>' + value.item_short_description + '</p>';
			recommendations += '<a href="#!/single?id=' + value.item_id + '" type="button" class="btn btn-light">More Info</a>'; 
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
		loadingIndicator.detach();
	});
		
}

