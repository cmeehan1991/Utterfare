$(document).ready(function(){});

function getSingleVendorItems(){
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
	$.post(window.single_item_url, params, function(data, textStatus, jqXHR){
		$.each(data, function(index, item){
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
		window.loadingIndicator.detach();
		$('.related-items').append(items_section);
		items_section.html(related_items);
		
		$('.item-btn').on('click', function(e){
			e.preventDefault();
			window.showItem($(this).data('id'));
			window.scrollTo(0, 0);
		});
	});
}

function getItemReviews(){
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

function getItemRating(){
	var url = window.location.href;
	params = {
		item_id: params.id, 
		action: 'get_item_ratings'
	};
		
	$.post(window.single_item_url, params, function(data, textStatus, jqXHR){
		
	}, 'json')
	.done(function(){
	});
}
