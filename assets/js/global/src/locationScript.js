var map;
var userLocation;

$(document).ready(function () {
    geolocation();
});

function setManualSearchLocation(){
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

function geolocation() {
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

                $('.locationLink').html("<i class='fa fa-map-marker' aria-hidden='true'></i>" + results[0].formatted_address);       
                //$('.locationLink').attr('data-location', results[0].formatted_address);
                
               //$('.search-form__input').data('location', results[0].formatted_address);
                $('.search-form__input').data('location', 'Hilton Head Island, SC 29926');
                $('input[name="location"]').val('Hilton Head Island, SC 29926');
                
                if($('.results').is(":visible") === false){
	                userLocation = "Hilton Head Island, SC, 29926";
               		window.curateHomepageSections(userLocation);
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


