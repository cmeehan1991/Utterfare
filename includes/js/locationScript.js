$(document).ready(function () {
    $('.locationInput').hide();
    $('.locationLink').html('Finding Location...');
    $('.locationInput').on('keyup', function (e) {
        validateInput($(this).val(), e);
    });

    locationLink = $('.locationLink');
    geolocation();

});

function validateInput(input, e) {
    var matchesNumber = input.match(/\d+/g);
    var matchesLetter = input.match(/^[a-zA-Z\s]+$/);
    /*if(input.length == 5 && matchesLetter != null){
        inputNotValid();
    }else if(input.length < 5){
        inputNotValid();
    }else{
        inputValid();
    }*/
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
    } else {
        console.log("Geolocation is not supported by this browser");
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
            if (results[1]) {
                $('.locationLink').text(results[0].address_components[2].long_name + ", " + results[0].address_components[5].short_name + " " + results[0].address_components[7].long_name);
                $('.locationLink').data('location', results[0].address_components[7].long_name);
            }
        } else {
            console.log('No Results Found');
        }
    });
}


function changeLocation() {
    console.log('Change Location Clicked');
    $('.locationInput').show();
    $('.locationLink').hide();
    var city = $('.locationLink').text().split(',');
    var state = city[1];
    var state = state.split(' ');
    $('.locationInput').val(city[0] + ", " + state[1]);
}


