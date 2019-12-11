var map;
var userLocation;
var userSearchLocation;
var searchDistance = 10;

window.setManualSearchLocation = function(){
	userLocation = $('input[name="location"]').val();
	var distance = $('select[name="distance"]').val();
	if(userLocation !== undefined && userLocation !== null){
		$('.search-form__input').data('location', userLocation);
		$('.search-form__input').data('distance', distance);
		window.distance = distance;
		window.getRecommendations(userLocation);
	}
	
	$('#locationModal').modal('hide');
	return false;
}

function validateInput(input, e) {
    var matchesNumber = input.match(/\d+/g);
    var matchesLetter = input.match(/^[a-zA-Z\s]+$/);
    inputValid();
}


function inputNotValid(){
    $('.locationInput').css('border','1px solid red');
    $('.locationInput').css('outline','1px solid red');
    $('.search').prop('disabled',true);
}

function inputValid(){
    $('.locationInput').css('border','1px solid green');
    $('.locationInput').css('outline','1px solid green');
    $('.search').prop('disabled',false);
}

function isNumeric(input){
    return !isNaN(parseFloat(input)) && isFinite(input);
}

window.geolocation = function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } 
}

function showPosition(position) {
    var lat = position.coords.latitude;
    var lng = position.coords.longitude;

    codeLatLng(lat, lng);
}

function codeLatLng(lat, lng) {
    var geocoder = new google.maps.Geocoder();
    var latLng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'latLng': latLng}, function (results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
	        if (results[0]) {
				userLocation = results[0].formatted_address;
				userSearchLocation = userLocation;
				
				var appElement = document.querySelector("[ng-app=utterfare]");
				var $scope = angular.element(appElement).scope();
				
				$scope = $scope.$$childHead;
				
				$scope.$apply(function(){
					$scope.location = userLocation;
					window.userSearchLocation = $scope.location;
					window.searchDistance = 10;
				});
				
                if($('.results').is(":visible") === false){
	                window.userLocation = userLocation;
	                window.searchDistance = 10;
               		window.getTopItems(userLocation);
                }
            }
        }
    });
}

function changeLocation() {
    $('.locationInput').show();
    $('.locationLink').hide();
    var city = $('.locationLink').text().split(',');
    var state = city[1];
    var state = state.split(' ');
    $('.locationInput').val(city[0] + ", " + state[1]);
}

/*
* Create the location popoever. 
* This popover will allow the user to input a location and distance manually.
*/
window.showLocationPopover = function(){
	var locationInputContent = "<label for='userSearchLocationInput'><strong>Location:</strong>";
	locationInputContent += "<input type='text'name='userSearchLocationInput' value='" + window.userLocation + "'>";
	locationInputContent += "<label for='userSearchDistance'><strong>Distance</strong></label>";
	locationInputContent += "<select name='userSearchDistance'>";
	locationInputContent += "<option value='1'>1 Mile</option>";
	locationInputContent += "<option value='2'>2 Mile</option>";
	locationInputContent += "<option value='5'>5 Miles</option>";
	locationInputContent += "<option value='10'>10 Miles</option>";
	locationInputContent += "<option value='15'>15 Miles</option>";
	locationInputContent += "<option value='20'>20 Miles</option>";
	locationInputContent += "</select>";
	
	
	
	$('.location-link').popover({
		content: locationInputContent,
		title: "Search Area",
		html: true,
		placement: 'bottom',
		sanitize: false,
	},'toggle');
	
	$('.location-link').on('hide.bs.popover', function(){
		window.userSearchLocation = $('input[name="userSearchLocationInput"]').val();
		window.searchDistance = $('input[name="userSearchDistance"]').val();
	});
}

