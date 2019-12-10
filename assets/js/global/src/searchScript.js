var limit = 25;
var page = 1;
var offset = '0';
var distance = 10;
var params={};
var setMarkers = [];
var markers = [];

$(window).on('load', function(){
	var url = window.location.href;
	var parameters;
	
	
	if(url.indexOf('results') > -1){
		parameters = getSearchParameters(url);

		var offset = 25 * (parameters.page - 1);
		initMap(parameters.terms, decodeURIComponent(parameters.location), parameters.distance, parseInt(parameters.page), 25, parseInt(offset));
		
		if($('.search-form__input').val() === undefined || $('.search-form__input').val() === null || $('.search-form__input').val() === ""){
			$('.search-form__input').val(decodeURIComponent(parameters.terms));
			$('.search-form__input').text(decodeURIComponent(parameters.terms));
		}
	}
});


$(document).mouseup(function(e){
	var searchContainer = $('.search-form__input');
	var recentItems = $('.recent-searches--item');
	var changeLocation = $('.recent-searches--search-location');
	
	if(!searchContainer.is(e.target) && !recentItems.is(e.target) && !changeLocation.is(e.target) && searchContainer.has(e.target).length === 0){
		removeSuggestions();
	}
});

window.getSearchParameters = function(url){
	var allParameters = url.split("?")[1].split("&");
	
	
	var data = {};
	allParameters.forEach(function(value){
		var param = value.split("=");
		data[param[0]] = param[1];
	});
	
	return data;
}
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
		
		suggestions += "<li class='recent-searches--search-location'><i class='fas fa-map-marker-alt'></i> " + $('.search-form__input').data('location') + "";
		
		suggestions += "<li><i>Recent Searches</i></li>";
		suggestions += "<li class='recent-searches--item' ng-click='search(search)' ng-model='search.terms'>Cheeseburger</li>";
		
		suggestions += "</ul>";
	
		$('.search-form__input').after(suggestions);
		
		$('.recent-searches--search-location').on('click', function(){
			changeLocation();
		});
		
		$('.recent-searches--item').on('click', function(){
			removeSuggestions();
			$('.search-form__input').val($(this).text());
			
			goToSearchPage($(this).text(), $('.location-link').text(), 10, 1, 25, 0);
		});
		
	}
}

function changeLocation(){
	var searchDistance = $('.search-form__input').data('distance');
	var searchLocation = $('.search-form__input').data('location');
	
	$('select[name="distance"]').val(searchDistance);
	$('input[name="location"]').val(searchLocation);
	
	$('#locationModal').modal('show');
	window.userLocation = searchLocation;
}

/**
* Populate the top picks section
*/


window.goToSearchPage = function(terms, searchLocation, distance, page, limit, offset){	
	var url = window.location.protocol + "//" + window.location.host + window.location.pathname + "#!/results";
	var searchParameters = "?action=search&terms=" + encodeURI(terms) + "&page=" + page + "&location=" + encodeURI(searchLocation.replace(/[(,)+]/g, '')) + "&distance=" + distance;
	
	window.location.href = url + searchParameters;
}

/*
* Perform the search based on the passed values
*/
function performSearch(terms, searchLocation, distance, page, limit, offset, map){			
	
	$("#loadingModal").modal("show");
	
	var data = {
		'action' : 'search',
		'location': searchLocation,
		'terms': terms,
		'limit': limit,
		'page': page,
		'distance': distance,
		'offset': offset
	};
		
	var display = '';

	// Initialize the map			
	$.post(window.search_url, data, function(response){		
		if(response.length > 0 ){
			$.each(response, function(index, result){
	
				display += '<li class="results-list--item" data-item-id="' + result.item_id + '">';
				
				display += '<div class="card mb-3"></div><div class="row no-gutters">';
				
				display += '<div class="col-md-4">';

				display += '<img src="' + result.primary_image + '" class="card-img" alt="' + result.item_name + '">';
				
				display += "</div>";
				
				display += '<div class="col-md-8">';
				
				display += '<div class="card-body">';
				
				display += '<h3 class="card-title">' + result.item_name + '</h3>';

				display += '<h4 class="card-title card-title--vendor-name">' + result.vendor_name + '</h4>';
									
				display += '<p class="card-text"><small class="text-muted"></small></p>';
				
				display += '<p class="card-text">' + result.item_short_description + '</p>';					
				
				display += "</div></div>";
				
				display += '</div></div></li>';
				
				
				window.addMarkers({
					'lat': result.latitude,
					'lng': result.longitude, 
					'title': result.vendor_name, 
				}, map);
				
			});
			}else{
				display += "<h3>It looks like there is nothing there.</h3><p>Try expanding your search location or searching for something else.</p>";
			}
	}, 'json')
	.fail(function(error){
		console.log("Search Error (performSearch())");
		console.log(error);
	})
	.done(function(){
		
		$('.results-list').html(display);		
		
					
		$('.results-list--item').on('mouseenter', function(){

			var title = $(this).find('.card-title--vendor-name').text();

			markers.forEach(function(marker){

				if(marker.title === title){

					marker.setAnimation(google.maps.Animation.BOUNCE);
				}else{
					marker.setAnimation(null);
				}
			});
		});
		
		
		$('.results-list--item').on('click', function(){
			var itemId = $(this).attr('data-item-id');

			window.location.href="#!/single?id=" + itemId;
		});
		
		$('.results-list--item').on('mouseleave', function(){
			markers.forEach(function(marker){
				marker.setAnimation(null);
			});
		});		
		
		$("#loadingModal").modal("hide");				
	});
}
/*
* Initialize the results map*/
window.initMap = function(terms, searchLocation, distance, page, limit, offset){

	
	var url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' + encodeURIComponent(searchLocation) + '&key=AIzaSyBNOJbx_2Q5h8f0ONZ4Abf5ULE0w4B-VTc';
		
	var latlng = {};
	var map; 
	if(document.getElementById('map') !== undefined && document.getElementById('map') !== null){
		$.get(url, null, function(success){
			
			if(success.results[0].geometry !== undefined){
				latlng.lat = success.results[0].geometry.location.lat;
				latlng.lng = success.results[0].geometry.location.lng;
			}else{
				latlng.lat = 32.229004;
				latlng.lng = -80.740280;
			}
		})
		.done(function(){
			var map = new google.maps.Map(document.getElementById('map'), {
				center: latlng, 
				zoom: 12
			});
		
			var marker = new google.maps.Marker({
				position: latlng, 
				title: "You", 
				map: map, 
				visible: true,
			});
			
			markers.push(marker);

			performSearch(terms, searchLocation, distance, page, limit, offset, map)
		});
	}
}

/**
* Add marrkers to the map. 
* We will only add each marker once by checking the restaurante title
*/
window.addMarkers = function(data, map){
	
	var position = new google.maps.LatLng(data.lat, data.lng);
	var marker = new google.maps.Marker({
		position: position,
		title: data.title,
		animation: google.maps.Animation.DROP,
		map: map,
		visible: true,
	});
		
	marker.addListener('click', function(){
		var infowindow = new google.maps.InfoWindow({
			content: data.title
		});
		
		infowindow.open(map, marker);
	});
		
	
		
	if($.inArray(data.title, setMarkers) <= 0){		
		setMarkers.push(data.title);
		markers.push(marker);
	}
}



function get_map_center(address){
	var encoded_address = encodeURI(address);

	$.ajax({
		url: 'https://maps.googleapis.com/maps/api/geocode/json?address=' + encoded_address + '&key=AIzaSyBNOJbx_2Q5h8f0ONZ4Abf5ULE0w4B-VTc',
		datatype: 'json',
		method: 'get',
		success: function(response){
			var lat = response.results[0].geometry.location.lat;
			var lng = response.results[0].geometry.location.lng;
			return {lat: lat, lng: lng};
		}, 
		error: function(error){
			console.log(error);
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
	performSearch(terms, location, distance, page, limit, offset);
}

function getLatLng(location){
	var geocoder = new google.maps.Geocoder();
	var latLng = location;
	geocoder.geocode({'address' : location}, function(results, status){
		if(status === 'OK'){
			var lat = results[0].geometry.location.latitude;
			var lng = results[0].geometry.location.longitude;
			latLng = lat + '+' + lng;
		}
	});
	
	return latLng;
}

