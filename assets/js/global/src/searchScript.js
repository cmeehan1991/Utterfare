var limit = 25;
var page = 1;
var offset = '0';

$(document).ready(function () {
	$('.loading-indicator--section').show();
	$('.main').hide();
    $('.loadmore-button').hide();
    $('.navbar-normal').hide();
    
    
    $('.search-form__input').focusin(function(){
	   showSuggestions();
	   $('.search-form__input').attr('placeholder', 'Try Searching: Impossible Burger');
    });   
});

$(document).mouseup(function(e){
	var searchContainer = $('.search-form__input');
	var recentItems = $('.recent-searches--item');
	var changeLocation = $('.recent-searches--search-location');
	
	if(!searchContainer.is(e.target) && !recentItems.is(e.target) && !changeLocation.is(e.target) && searchContainer.has(e.target).length === 0){
		removeSuggestions();
	}
});

/**
* Collapse the suggestions box from the view
*/
function removeSuggestions(){
	$('.recent-searches').remove();
	$('.search-form__input').attr('placeholder', 'Search');
}

/*
* Display the suggestions box
*/
function showSuggestions(){
	if($('.recent-searches').length <= 0){
		var suggestions = "<ul class='recent-searches'>";
		
		suggestions += "<li class='recent-searches--search-location'>" + $('.search-form__input').data('location') + "<br/><button type='button' class='btn btn-primary change-location-button'>Change Location</button></li>";
		
		suggestions += "<li class='recent-searches--item'>Cheeseburger</li>";
		
		suggestions += "</ul>"
	
		$('.search-form__input').after(suggestions)
		
		$('.change-location-button').click(function(){
			changeLocation();
		});
		
		$('.recent-searches li').on('click', function(){
			removeSuggestions()
			$('.loading-indicator--section').show();
			$('.main').hide();
			$('.search-form__input').val($(this).text());
			performSearch($(this).text(), $('.search-form__input').data('location'), 10, 1, 25, 0);
			$('.results--list').show();
			$('.loading-indicator--section').hide();
		});
		
		$('.recent-searchs--item').click(function(e){
			console.log($(this));
		});
	}
}

function changeLocation(){
	console.log('modal toggle');
	//$('#locationModal').modal('show');
}

/**
* Populate the top picks section
*/
function getTopPicks(){
	
}




/*
* Populate the recommended items section
*/
function getRecommendations(location){
	console.log(location);
	
	var data = {
		"action": "get_recommendations",
		"location": location
	}
	
	var recommendations = "<div class='carousel-item active'><div class='row'>";
	
	var count = 0;
	
	$.ajax({
		data: data,
		url: 'includes/php/search.php', 
		method: 'post',
		datatype: 'json',
		success: function(response){
			$.each(JSON.parse(response), function(key, value){
				
				recommendations += '<div class="col-md-3 mx-auto">';
				recommendations += '<div class="card recommendation">'; 
				recommendations += '<img src="assets/img/mahi-mahi.jpg" class="card-img-top" alt="...">'
				recommendations += '<div class="card-body">';
				recommendations += '<div class="card-title"><h3>' + value['item_name'] + '</h3></div>';
				recommendations += '<div class="card-text">'; 
				recommendations += '<i class="recommendation__location">' + value['vendor_address'] + "</i>";
				recommendations += '<h4 class="recommendation__vendor">' + value['vendor_name'] + "</h4>";
				recommendations += '<p>' + value['item_short_description'] + '</p>'; 
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
			
		}, 
		error: function(error){
			console.log("Error: ");
			console.log(error);
		}, 
		complete: function(){
			console.log('complete');
			$('.recommendations-carousel__inner').html(recommendations);
			$('.loading-indicator--section').hide();
			$('.main').show();
		}
	});
}

/*
* Perform the search based on the passed values
*/
function performSearch(terms, location, distance, page, limit, offset){
	$('.loader').show();
	var data = {
		'action' : 'search',
		'location': location,
		'terms': terms,
		'limit': limit,
		'page': page,
		'distance': distance,
		'offset': offset
	};
	
	var display = '';
	
	$.ajax({
		url: 'includes/php/search.php',
		data: data,
		method: 'post',
		success: function (results) {
			$.each(JSON.parse(results), function(index, result){
				display += '<li class="results-list--item">';
				
				display += '<div class="card mb-3"></div><div class="row no-gutters">';
				
				display += '<div class="col-md-4">';

				display += '<img src="' + result['primary_image'] + '" class="card-img" alt="...">';
				
				display += "</div>";
				
				display += '<div class="col-md-8">';
				
				display += '<div class="card-body">';
				
				display += '<h3 class="card-title">' + result['item_name'] + '</h3>';

				display += '<h4 class="card-title">' + result['vendor'] + '</h4>';
									
				display += '<p class="card-text"><small class="text-muted">Address</small></p>';
				
				display += '<p class="card-text">' + result['item_short_description'] + '</p>';					
				
				display += "</div></div>";
				
				display += '</div></div></li>';
			});
		}, 
		error: function (jqXHR, error, errorThrown) {
			console.log("error");
			console.log(jqXHR);
			console.log(error);
		    $('.loading-indocator--section').hide();
		}, 
		complete: function(){
			$('.results-list').html(display);
			
			$('.loading-indicator--section').hide();
			
			$('.results').show();
		}
	});
}

function loadMore() {
    this.page += 1;
    var offset = this.limit * this.page;
    var limit = this.limit;

    var terms = $('.searchInput').val();

    var location = null;
    if ($('.locationLink').is(":visible")) {
        location = $('.locationLink').data('location');
    } else {
        location = $(".locationInput").val();
    }
    

    var distance = $('.distance').val();
		performSearch(terms, location, distance, page, limit, offset)
}

function getLatLng(location){
	var geocoder = new google.maps.Geocoder();
	var latLng = location;
	geocoder.geocode({'address' : location}, function(results, status){
		if(status === 'OK'){
			//console.log(results.geometry.location);
			var lat = results[0].geometry.location.latitude;
			var lng = results[0].geometry.location.longitude;
			latLng = lat + '+' + lng;
			//console.log(latLng);
		}
	});
	
	return latLng;
}

