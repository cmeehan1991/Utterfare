var limit = 25;
var page = 1;
var offset = '0';

$(document).ready(function () {
    $('.loadmore-button').hide();
    $('.navbar-normal').hide();
});

// Save the current search parameters and run the search again

$(window).unload(function(){
	var terms = $('.searchInput').val();
	var location = null;
    if ($('.locationLink').is(":visible")) {
        location = $('.locationLink').data('location');
    } else {
        location = $(".locationInput").val();
    }
    var distance = $('.distance').val();
});

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
                if (results !== "No Results") {
                    $('.results').html(results);
                    $('.page-title').hide();
                    $('.navbar-normal').show();
                    $('.navbar-presearch').hide();
                    $('.loadmore-button').show();
                } else {
                    $('.results').html("We're afraid that nothing was returned for your search. Please try something else!");
                }
            }, error: function (jqXHR, error, errorThrown) {
                console.log('error');
                console.log(jqXHR);
                console.log(error);
                console.log(errorThrown);
            }
        });
    } else {
        console.log('false');
        $('.results').html('Please be sure to fill out the location, distance, and search parameters');
    }
    return false;
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

    console.log(distance);
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
            if (results !== "No Results") {
                $('.results').append(results);
                $('.page-title').hide();
                $('.navbar-normal').show();
                    $('.navbar-presearch').hide();
                $('loadmore-button').show();
            } else {
                $('.loadmore-button').hide();
            }
        }, error: function (jqXHR, error, errorThrown) {
            console.log('error');
            console.log(jqXHR);
            console.log(error);
            console.log(errorThrown);
        }
    });
}


