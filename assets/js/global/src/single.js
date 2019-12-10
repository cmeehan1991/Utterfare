window.getSingleVendorItems = function(){
	var items_section = $('.related-vendor-items');
	items_section.detach();
	$('.related-items').append(window.loading_indicator);
	
	var url = window.location.href;
	var params = window.getSearchParameters(url);
	params = {
		item_id: params.id,
		action: 'get_vendor_items'
	};
		
		
	var related_items = '';
	$.post(window.single_item_url, params, function(response){
		console.log(response);
		$.each(response, function(index, item){
			related_items += '<li class="related-vendor-item">'; 
			related_items += '<div class="card" style="width: 18rem;">';
			related_items += '<img src="' + item.primary_image + '" class="card-img-top" alt="' + item.item_name + '">';
			related_items += '<div class="card-body">';
			related_items += '<h5 class="card-title">' + item.item_name + '</h5>';
			related_items += '<p class="card-text">' + item.item_short_description + '</p>';
			related_items += '<a href="#" data-id="' + item.item_id + '" class="btn btn-primary item-btn">View Item</a>';
			related_items += '</div>';
			related_items += '</div>';
			related_items += '</li>';
		});
	}, 'json')
	.done(function(){
	
		$('.related-items').html(items_section);
		items_section.html(related_items);
		
		$('.item-btn').on('click', function(e){
			e.preventDefault();
			window.showItem($(this).data('id'));
			window.scrollTo(0, 0);
		});
	});
}


/*
* Get the item information to be shown on the single item page
*/
window.showSingleItem = function(itemId){
	$("#loadingModal").modal('show');
	var singleUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + "#!/single";
	window.location.href = singleUrl + "?id=" + itemId;
	
	var queryUrl = "includes/php/search.php";
	var data = {
		'action': 'getSingleItem', 
		'item_id': itemId,
	};
	$.post(queryUrl, data, 'json')
	.done(function(response){
		populateSingleItemInformation(response);
		$("#loadingModal").modal('hide');
	});
}

/*
* Handle the single item data 
*/
function populateSingleItemInformation(data){
	var data = JSON.parse(data);
		
	$('.item-name').text(data.item_name);
	$('.item-image').attr('src', data.primary_image).attr('alt', data.item_name);
	//$('.item-image').attr('src', 'http://localhost/utterfare/assets/img/new-york-strip.jpg').attr('alt', data.item_name);
	$('.vendor-address').attr('href', "http://maps.google.com/maps?q=" + JSON.parse(data.address)._address).text(JSON.parse(data.address)._address);
	latlng = {
		lat: parseFloat(data.latitude),
		lng: parseFloat(data.longitude),
	};
	
	map = new google.maps.Map(document.getElementById('single-item--map'), {
		center: {lat: latlng.lat, lng: latlng.lng},
		zoom:14
	});
	
	
	window.addMarkers({lat: data.latitude, lng: data.longitude, title: data.vendor_name}, map);
	$('.item-description').text(data.item_description);
}



window.getItemReviews = function(){
	var url = window.location.href;
	var params = window.getSearchParameters(url);
	params = {
		item_id: params.id, 
		action: 'get_item_reviews'
	};
	
	
	var review = "";
	$.post(window.single_item_url, params, function(data, textStatus, jqXHR){
		if(data !== ""){
			$.each(data, function(index, item){
				review += '<li class="item-reviews--reivew">';
				review += '<div class="d-flex align-items-start">';
				review += '<img class="item-reviews--user-profile-picture" src="' + data.profile_image + '" alt="' + item.username + ' Profile Picture">';
				review += '<div class="d-flex align-items-start flex-column">';
				review += '<h3 class="item-reviews--title p-2">' + item.review_title + '</h3>';
				review += '<p class="item-reviews--body p-2">' + item.review_text + '</p>';
				review += '</div>';
				review += '</div>';
				review += '</li>';
			});
		}
	})
	.done(function(){
		if(review === ""){
			$('.item-reviews').prepend('<h4>There are no reviews yet.</h4><p>Be the first person to leave a review for this item!</p>');
		}else{
			$('.item-reviews').html(review);
		}
	});
}

window.getItemRating = function(){
	var url = window.location.href;
	var params = {
		item_id: params.id, 
		action: 'get_item_ratings'
	};
		
	$.post(window.single_item_url, params, function(data, textStatus, jqXHR){
		
	}, 'json')
	.done(function(){
	});
}
