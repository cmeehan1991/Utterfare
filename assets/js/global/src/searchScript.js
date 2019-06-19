var limit = 25;
var page = 1;
var offset = '0';
var distance = 10;
var params={};
var setMarkers = [];
var markers = [];


$(document).ready(function () {
    $('.search-form__input').focusin(function(){
		showSuggestions();
		$('.search-form__input').attr('placeholder', 'Try Searching: Impossible Burger');
    });       
});

$(window).on('load', function(){
	var url = window.location.href;
	var parameters;
	
	
	if(url.indexOf('results') > -1){
		parameters = getSearchParameters(url);

		var offset = 25 * (parameters.page - 1);
		performSearch(parameters.terms, decodeURIComponent(parameters.location), parameters.distance, parseInt(parameters.page), 25, parseInt(offset));
		
		if($('.search-form__input').val() === undefined || $('.search-form__input').val() === null || $('.search-form__input').val() === ""){
			$('.search-form__input').val(decodeURIComponent(parameters.terms));
			$('.search-form__input').text(decodeURIComponent(parameters.terms));
		}
	}
	
	if(url.indexOf('single') > -1){
		parameters = getSearchParameters(url);
		
		showItem(parameters.id);		
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

function getSearchParameters(url){
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
			performSearch($(this).text(), $('.search-form__input').data('location'), 10, 1, 25, 0);
		});
		
	}
}

function changeLocation(){
	var searchDistance = $('.search-form__input').data('distance');
	var searchLocation = $('.search-form__input').data('location');
	
	$('select[name="distance"]').val(searchDistance);
	$('input[name="location"]').val(searchLocation);
	
	$('#locationModal').modal('show');
}

/**
* Populate the top picks section
*/
function getTopPicks(){
	
}

/*
* Perform the search based on the passed values
*/
function performSearch(terms, location, distance, page, limit, offset){
				
	main.detach();
	$('.content').append(loadingIndicator);
			
	var url = window.location.protocol + "//" + window.location.host + window.location.pathname + "#!/results";
	var searchParameters = "?action=search&terms=" + encodeURI(terms) + "&page=" + page + "&location=" + encodeURI(location) + "&distance=" + distance;
	
	window.location.href = url + searchParameters;
		
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
	var map;

	// Initialize the map		
	initMap(location);

	// Run the search
	$.ajax({
		url: window.search_url,
		data: data,
		method: 'post',
		success: function (response) {
			$.each(JSON.parse(response), function(index, result){

				display += '<li class="results-list--item" data-item-id="' + result.item_id + '">';
				
				display += '<div class="card mb-3"></div><div class="row no-gutters">';
				
				display += '<div class="col-md-4">';

				display += '<img src="..." class="card-img" alt="...">';
				
				display += "</div>";
				
				display += '<div class="col-md-8">';
				
				display += '<div class="card-body">';
				
				display += '<h3 class="card-title">' + result.item_name + '</h3>';

				display += '<h4 class="card-title card-title--vendor-name">' + result.vendor_name + '</h4>';
									
				display += '<p class="card-text"><small class="text-muted"></small></p>';
				
				display += '<p class="card-text">' + result.item_short_description + '</p>';					
				
				display += "</div></div>";
				
				display += '</div></div></li>';
				
			
				addMarkers({
					'lat': result.latitude,
					'lng': result.longitude, 
					'title': result.vendor_name, 
				});
			});
			
		}, 
		error: function (jqXHR, error, errorThrown) {
			console.log("error");
			console.log(jqXHR);
			console.log(error);
			
		}, 
		complete: function(){

			
			$('.content').append(results);
			results.hide();
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
				var itemId = $(this).data('item-id');
				showItem(itemId);
			});
			
			$('.results-list--item').on('mouseleave', function(){
				markers.forEach(function(marker){
					marker.setAnimation(null);
				});
			});
			
			
			loadingIndicator.detach();
						
		}
	});
}

/*
* Get the item information to be shown on the single item page
*/
function showItem(itemId){
	
	var singleUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + "#!/single";
	window.location.href = singleUrl + "?id=" + itemId;
	
	var queryUrl = "includes/php/search.php";
	var data = {
		'action': 'getSingleItem', 
		'item_id': itemId,
	};
	$.post(queryUrl, data, 'json')
	.done(function(response){
		populateItemInformation(response);
	});
}

/*
* Handle the single item data 
*/
function populateItemInformation(data){
	var data = JSON.parse(data);
	$('.item-name').text(data.item_name);
	//$('.item-image').attr('src', data.primary_image).attr('alt', data.item_name);
	$('.item-image').attr('src', 'http://localhost/utterfare/assets/img/new-york-strip.jpg').attr('alt', data.item_name);
	$('.vendor-address').attr('href', "http://maps.google.com/maps?q=" + JSON.parse(data.address)._address).text(JSON.parse(data.address)._address);
	latlng = {
		lat: parseFloat(data.latitude),
		lng: parseFloat(data.longitude),
	};
	
	map = new google.maps.Map(document.getElementById('single-item--map'), {
		center: {lat: latlng.lat, lng: latlng.lng},
		zoom:14
	});
	
	
	addMarkers({lat: data.latitude, lng: data.longitude, title: data.vendor_name});
	$('.item-description').text(data.item_description);
}

/*
* Initialize the results map*/
function initMap(searchLocation){
	
	var url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' + encodeURIComponent(searchLocation) + '&key=AIzaSyBNOJbx_2Q5h8f0ONZ4Abf5ULE0w4B-VTc';
		
	var latlng = {};
	
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
			map = new google.maps.Map(document.getElementById('map'), {
				center: latlng, 
				zoom: 12
			});
			
		});
	}
}

/**
* Add marrkers to the map. 
* We will only add each marker once by checking the restaurante title
*/
function addMarkers(data){
		var position = new google.maps.LatLng(data.lat, data.lng);
		var marker = new google.maps.Marker({
			position: position,
			title: data.title,
			animation: google.maps.Animation.DROP,
		});
		
		
		marker.addListener('click', function(){
			var infowindow = new google.maps.InfoWindow({
				content: data.title
			});
			
			infowindow.open(map, marker);
		});
		
		
	if($.inArray(data.title, setMarkers) <= 0){		
		marker.setMap(map);
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

