window.curateHomepageSections = function(user_location){	
	$('#loadingModal').modal('show');	
	window.geolocation();
	//getTopItems(user_location);
	
}

window.getTopItems = function(user_location){
	var data = {
		'action': 'get_top_items'
	}
	
	var top_items = '';
	
	$.post(window.search_url, data, function(response){
				
		var count = 0;
				
		$.each(response, function(k, v){
						
			top_items += '<div class="card featured-item">';
			if(count == 2){
				top_items += "<div class='row no-gutters'>";
				top_items += "<div class='col-sm-12 col-md-4'>";
			}
			top_items += '<img src="' + v.primary_image + '" class="card-img-top" alt="' + v.item_name + '">';			
			if(count == 2){
				top_items += "</div><div class='col-sm-12 col-md-8'>";
			}
			top_items += '<div class="card-body">';
			top_items += '<div class="card-title">';
			top_items += '<h3>' + v.item_name + '</h3>'
			top_items += '</div>';
			top_items += '<div class="card-text">';
			top_items += '<i class="featured-item__location">' + v.address + '</i><br/>';
			top_items += '<strong class="featured-item__vendor">' + v.vendor_name + '</strong><br/>';
			top_items += '<p class="featured-item__short-description">' + v.item_short_description + '</p>';
			top_items += '<a href="#!/single?id=' + v.item_id + '" type="button" class="btn btn-light">More Info</a>'; 
			top_items += '</div></div></div>';
			if(count == 2){
				top_items += "</div></div>";
			}
			count += 1;

		});
	}, 'json')
	.fail(function(error){
		console.log("Failed");
		console.log(error);
	})
	.done(function(){
		$('.featured-items-row--top-items').html(top_items);

		getRecommendations(user_location);
	});
}



/*
* Populate the recommended items section
*/
function getRecommendations(user_location){

	var data = {
		"action": "get_recommendations",
		"location": user_location
	};
	

	var recommendations = '';	
	var count = 0;
	
	$.post(window.search_url, data, function(response){

		if(response != ''){
		
			$.each(response, function(key, value){
				var address = value.address;
				
				recommendations += '<div class="recommendation">'; 
				recommendations += '<a href="#!/single?id=' + value.item_id + '">'
				recommendations += '<img src="' + value.primary_image + '" class="card-img-top" alt="' + value.item_name + '">';
				recommendations += '<div class="recommendation-body">';
				recommendations += '<div class="recommendation-title"><h3>' + value.item_name + '</h3></div>';
				recommendations += '</div>'; // .card-body
				recommendations += '</a>'; 
				recommendations += '</div>'; // .recommendation
			});
		}else{
			recommendations = '';
		}

	}, 'json')
	.fail(function(error){
		console.log("Fail");
		console.log(error);
	})
	.done(function(){
		if(recommendations != ''){
			$('.recommendations-section').html(recommendations);
		}		
		$('#loadingModal').modal('hide');
	});
		
}

