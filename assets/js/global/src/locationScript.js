var map;
var userLocation;
var userSearchLocation;
var searchDistance = 10;

window.showLocationModal = function(){
	$('input[name=search-location]').val(userSearchLocation);
	$('select[name=search-distance]').val(searchDistance);
	$('#locationModal').modal('toggle');
}

window.saveSearchLocation = function(){
	userSearchLocation = $('input[name=search-location]').val();
	searchDistance = $('select[name=search-distance]').val();
	
	if(userSearchLocation !== undefined && userSearchLocation !== null){
		$('.location-link').text(userSearchLocation);
		$('.search-form__input').attr('data-location', userSearchLocation);
		$('.search-form__input').attr('data-distance', searchDistance);
		$('#locationModal').modal('toggle');
		window.userSearchLocation = userSearchLocation;
	}
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
        navigator.geolocation.getCurrentPosition(showPosition, locationErrorCallback, {timeout: 10000, enableHighAccurace: false});
    } 
}

function locationErrorCallback(err){
	  console.warn(`ERROR(${err.code}): ${err.message}`);
	  console.log(err);
}

function showPosition(position) {
    var lat = position.coords.latitude;
    var lng = position.coords.longitude;
	
	console.log(lat);
	console.log(lng);
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
               		
               		
			   		$('.search-form__input').attr('data-location', userSearchLocation);
					$('.search-form__input').attr('data-distance', searchDistance);
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
	locationInputContent += "<input type='text' class='form-control' name='userSearchLocationInput' value='" + window.userLocation + "'>";
	locationInputContent += "<label for='userSearchDistance'><strong>Distance</strong></label>";
	locationInputContent += "<select class='custom-select' name='userSearchDistance'>";
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
		window.searchDistance = $('select[name="userSearchDistance"]').val();
		
		$('.location-link').text(window.searchDistance + " miles from " + window.userSearchLocation);
		console.log(window.searchDistance);
		$('select[name=userSearchDistance] option[value=' + window.searchDistance + ']').attr('selected', 'selected');
		console.log($('select[name=userSearchDistance]').val());
	});
}

