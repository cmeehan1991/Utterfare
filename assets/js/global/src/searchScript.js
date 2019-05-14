var limit = 25;
var page = 1;
var offset = '0';

$(document).ready(function () {
    $('.loadmore-button').hide();
    $('.navbar-normal').hide();
    
    
    $('.search-form__input').focusin(function(){
	   showSuggestions();
	   $('.search-form__input').attr('placeholder', 'Try Searching: Impossible Burger');
    });
       
    
});

$(document).mouseup(function(e){
	var searchContainer = $('.search-form__input');
	if(!searchContainer.is(e.target) && searchContainer.has(e.target).length === 0){
		removeSuggestions();
	}
});

function removeSuggestions(){
	$('.recent-searches').remove();
	$('.search-form__input').attr('placeholder', 'Search');
}

function showSuggestions(){
	if($('.recent-searches').length <= 0){
		var suggestions = "<ul class='recent-searches'>";
		
		suggestions += "<li>" + $('.search-form__input').data('location') + "<br/><button type='button' class='btn btn-primary change-location-button'>Change Location</button></li>";
		
		suggestions += "<li>Cheeseburger</li>";
		
		suggestions += "</ul>"
	
		$('.search-form__input').after(suggestions)
		
		$('.change-location-button').click(function(){
			console.log('clicked');
			changeLocation();
		});
		
		$('.recent-searches li').on('click', function(){
			console.log('hello');
		});
	}
}

function changeLocation(){
	console.log('modal toggle');
	//$('#locationModal').modal('show');
}

function authorized(terms, location, distance) {
    var isValid = false;
    if (terms === '' || terms === null) {
        isValid = false;
    }else if (location === '' || location === null || location.length < 5) {
        isValid = false;
    }else if(distance === '' || distance === null) {
        isValid = false;
    }else {
        isValid = true;
    }
    return isValid;
}

function formSearch() {
    var limit = this.limit;
    var page = this.page;
    var offset = this.offset;
    var terms = $('.searchInput').val();
    var location = null;
    if ($('.locationLink').is(":visible")) {
        location = $('.locationLink').data('location');
    } else {
        location = $(".locationInput").val();
    }
    var distance = $('.distance').val();

    // Check if all of the inputs have been filled
    if (authorized(terms, location, distance) === true) {
	    location = getLatLng(location);
		performSearch(terms, location, distance, page, limit, offset);    
	} else {
        //console.log('false');
        $('.results').html('Please be sure to fill out the location, distance, and search parameters');
    }
    return false;
}

/*
* Perform the search based on the passed values
*/
function performSearch(terms, location, distance, page, limit, offset){
	$('.loader').show();
	var data = {
		'location': location,
		'terms': terms,
		'limit': limit,
		'page': page,
		'distance': distance,
		'offset': offset
	};
	
	$.ajax({
		url: 'includes/php/search.php',
		data: data,
		method: 'post',
		success: function (results) {
		    $('.loader').hide();
		    if (results !== "No Results") {
				/*var parameters = $.param(data, true);
				var newUrl = window.location.protocol + '//' + window.location.host + window.location.pathname + "?" + parameters;
		        window.history.pushState({path:newUrl}, '', newUrl);*/ 
		        //console.log("results");
		        //$('.results').html("");
		        $('.results').append(results);
		        $('.page-title').hide();
		        $('.navbar-normal').show();
		        $('.navbar-presearch').hide();
		        $('.loadmore-button').show();
		    } else {
		        $('.results').html("We're afraid that nothing was returned for your search. Please try something else!");
		    }
		}, error: function (jqXHR, error, errorThrown) {
		    $('.loader').hide();
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

