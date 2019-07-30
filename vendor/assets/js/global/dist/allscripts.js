
var offset = "0";
var page = "1";
var ppp = null;

$(document).ready(function () {
	var currPathName = window.location.pathname;
    var newItemNumber = 1;

	if(currPathName == '/addEditItems.php' || currPathName == '/addEditItems' || currPathName == '/utterfare/addEditItems'){
		getItems();
	}
	
    $('.addItemButton').on('click', function () {
        addItem(newItemNumber++);
    });

    $('.saveChangesButton').on('click', function () {
        saveChanges();
    });  
    
});


$(window).on('load', function(){
	var search = window.location.search;
	if(search){
		var page = search.split("=")[1];
		getPage(page);
	}
});

function setImage(row){
	var imageInput = $('input[name=itemImage]');
	var file = $("." + row + " input[name=itemImage]").prop('files')[0];
	var src = $(imageInput).val();
	var imageView = document.getElementsByClassName('item-image');
	if(file){
		var reader = new FileReader();
		reader.onload = function(event){
			$("." + row + " .item-image").attr('src', event.target.result);
		}
		reader.readAsDataURL(file)
	}
	return false;
}

/*
* Handle menu item file upload
*/
function uploadMenuItems(){
	$('#fileupload').trigger('click');
	$("#fileupload").change(function(){
		var file = $(this)[0].files[0];
		if(file){
			console.log("file");
			saveMenuItems(file);
			
		}
	});
	return false;
}

function saveMenuItems(file){
	
    
    var formData = new FormData();
    formData.append('action', 'uploadMenuFile');
    formData.append('file', file);
		
	$.ajax({
		url: 'includes/php/AddEditItems.php', 
		data: formData,
		cache: false,
		contentType: false, 
		processData: false,
		method: 'post',  
		success:function(response){
			console.log(response);
			var received = JSON.parse(response);
			
			if(received.Existing){
				showInformationSnackbar( "Items already exist", 10000, "OK");
				$.each(received.Existing, function(k,v){
					$('.error-message').append('<p class="error-text">' + v + ' already exists. Try adding an item with a different name or manually enter the item.</p>')
				});
			}
			
			if(received.Error){	
				showInformationSnackbar("Error uploading Items", 10000, "OK");	
			}
			
			if(received.Success){
				showInformationSnackbar("Items sucessfully uploaded", 10000, "OK");
				window.location.reload();
			}
		},
		error: function(error){
			console.log("Error: ");
			console.log(error);
		}
	});
}

function showInformationSnackbar( message, timeout, actionText){
	var notification = document.querySelector('.mdl-js-snackbar');
	var data = {
		message: message,
		actionHandler: function(event){},
		actionText: actionText,
		timeout: timeout
	};
	notification.MaterialSnackbar.showSnackbar(data);
}

function editItem(className) {
    var itemName = $("." + className + "-item-name").html();
    var itemDescription = $("." + className + "-item-description").html();
    $("." + className).removeAttr("onclick");
    $("." + className + "-item-image").append("<input type='file' name='itemImage' value='" + itemName + "' accept='.png,.jpg,.jpeg,.gif'  onchange='return setImage(" + className + ");'/>");
    $("." + className + "-item-name").html("<input type='text' name='itemName' value='" + itemName + "' class='itemNameInput'/>");
    $("." + className + "-item-description").html("<textarea cols='30' name='itemDescription' class='itemDescriptionInput'>" + itemDescription + "</textarea>");
    $("." + className).append("<td class='actions'><button onclick='return applyChanges(" + className + ")'><i class='glyphicon glyphicon-check'></i>Apply</button><br/><button onclick='return deleteItem(" + className + ")'><i class='glyphicon glyphicon-trash'></i>Delete</button></td>");
    return false;
}

function deleteItem(itemId){
	var conf = confirm("Are you sure you want to delete that item? This action cannot be undone.");
	if(conf == true){
		var data = {
			action: "deleteItem",
			"itemId": itemId
		};
		
		$.ajax({
			data: data,
			url: "includes/php/AddEditItems.php",
			method: "post", 
			success: function(results){
				if(results == true){
					getItems();
					showSnackbar("Item Successfully Deleted", "long");
				}else{
					showSnackbar("Error Removing Item", "long");
				}
			},error: function(error){
				showSnackbar("Error removing item.", "short");
			}
		});
	}
}

function applyChanges(itemID) {
    var itemImage = $("input[name='itemImage']").prop('files')[0];
    var itemName = $('input[name="itemName"]').val();
    var itemDescription = $('textarea[name="itemDescription"]').val();

    var data = {
        action: "updateItem",
        "ID": itemID,
        "itemName": itemName,
        "itemDescription": itemDescription
    };
    $.ajax({
        data: data,
        url: "includes/php/AddEditItems.php",
        type: "post",
        success: function (results) {
            if (itemImage !== null) {
                uploadImage(itemID, itemImage);
                showSnackbar("Changes Saved", "short");
            }
        }
    });
    return false;
}

function getItems() {
    this.ppp = $("select[name='limit']").val();
    var data = {
        'action': 'getItems',
        'limit': ppp,
        'offset': this.offset
    };
    $.ajax({
        url: 'includes/php/AddEditItems.php',
        data: data,
        method: 'post',
        success: function (results) {
            $(".currentItems__body").html(results);
            getPagination();
        }
    });
}

function getPagination() {
    var data = {
        action: 'pagination',
        'itemsPerPage': $('select[name="limit"]').val()
    };
    $.ajax({
        data: data,
        url: 'includes/php/AddEditItems.php',
        method: 'POST',
        success: function (results) {
            $('.table-footer__pagination').html(results);
        }
    });
}

function saveChanges() {
    var saveChanges = [];
    $('.newItem').each(function (index, row) {
        var itemInformation = [];
        itemInformation["ITEM_IMAGE"] = $(row).find(".item-image").prop('files')[0];
        itemInformation["ITEM_NAME"] = $(row).find(".item-name").val();
        itemInformation["ITEM_DESCRIPTION"] = $(row).find(".item-description").val();
        saveChanges.push(itemInformation);
    });


    for (var i = 0; i < saveChanges.length; i++) {
        var itemInfo = saveChanges[i];
        var data = {
            action: 'addNewItem',
            itemName: itemInfo["ITEM_NAME"],
            itemDescription: itemInfo["ITEM_DESCRIPTION"]
        };

        $.ajax({
            url: 'includes/php/AddEditItems.php',
            data: data,
            type: 'post',
            success: function (results, status, xhr) {
                if (itemInfo["ITEM_IMAGE"] != null) {
                    uploadImage(results, itemInfo["ITEM_IMAGE"]);
                }
                getItems();
            }, error: function (error) {
                console.log(error);
            }
        });
    }
}

function uploadImage(itemID, image) {
    var data = {
        action: "addImage",
        "itemID": itemID,
        "itemImage": image
    };

    var formData = new FormData();
    formData.append("action", "addImage");
    formData.append("itemID", itemID);
    formData.append("itemImage", image);
    $.ajax({
        url: 'includes/php/AddEditItems.php',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        method: 'post',
        success: function (results) {
            getItems();
        }
    });
}

function addItem(newItemNumber) {
    $(".currentItems__body").prepend("<tr class='newItem' id='" + newItemNumber + "'>"
            + "<td><input type='file' name='item_image' class='item-image' accept='.png,.jpg,.jpeg,.gif' onchange='return setImage(" + newItemNumber + ");'/></td>"
            + "<td><input type='text' name='item_name' class='item-name'/></td>"
            + "<td><textarea name='item_description' class='item-description'></textarea></td>"
            + "</tr>");
}

function getPage(page) {
    this.offset = this.ppp * (page - 1);
    var data = {
        'action': 'getItems',
        'limit': ppp,
        'offset': offset
    };
    $.ajax({
        url: 'includes/php/AddEditItems.php',
        data: data,
        method: 'post',
        success: function (results) {
            $(".currentItems__body").html(results);
            getPagination();
            var newUrl = window.location.protocol + '//' + window.location.host + window.location.pathname + "?page=" + page;
		    window.history.pushState({path:newUrl}, '', newUrl);
        }
    });
}
$(document).ready(function(){
	if(document.getElementsByClassName("registrationForm")){
		var numPages = $('.registrationForm>fieldset').length;	
		setRegistrationPages(numPages);
	}
	
	// Hide the company and contact information when the document is ready
	$('#contactInformation').hide();
	$('#companyInformation').hide();
	
	// Select2 select boxes
	$("select[name='state']").select2({width:'100%', placeholder: "Select a state", allowClear: false});
	$("select[name='country']").select2({width:'100%', placeholder: "Select a country"});
	$("select[name='web-prefix']").select2({width:'fit-content'});
	$("select[name='role']").select2({width:'100%', placeholder: "Role", alowClear: false});
	
	$('input[name=phone]').keydown(function(){
		$(this).val($(this).val().replace(/[^0-9\(\)\-]/g, '')); 
	});
	
	$('input[name=phone]').keyup(function(){
		$(this).val($(this).val().replace(/^(\d{3})(\d{3})(\d)+$/, "($1)$2-$3"));
	});
	
	navigationButtons();
	goToPage();
	pages();
	keylisteners();		
});

function recaptchaChecked(){
	$('.next--submit-form').fadeIn('fast');
}

function navigationButtons(){
	// Hide the next buttons
	$('.next--contact-information').hide();
	$('.next--company-information').hide();
	$('.next--submit-form').hide();
	
	$('.registrationPageLink').click(function(event){
		event.preventDefault();
	});
}

function goToPage(){
	$('.registrationForm button').click(function(){		
		var navigateTo = $(this).data('page');			
		var currentPage = null;
		var selectedPage = null;
		$('.registrationForm>fieldset').each(function(){
			var page = $(this).attr('id');
			if($("#" + page).is(":visible")){
				currentPage = page;
			}
		});
		if(navigateTo === 'confirm'){
			$('.registrationForm').hide();
			$('.loader>i').after('<h2>Loading...</h2>');
			$('.loader').show();
			registerCompany();
			return false;
		}else if(navigateTo === 'next'){
			selectedPage = $('fieldset:visible').next().attr('data-page');
			var nextPage = $('#' + currentPage).next()
			$("#" + currentPage).hide();
			$(nextPage[0]).fadeIn('slow');

			$('.page-count--page-number').removeClass('active');
			$('.page-count--page-number[data-page=' + selectedPage + ']').addClass('active');


		}else{
			selectedPage = $('fieldset:visible').prev().attr('data-page');
			var prevPage = $('#' + currentPage).prev();
			$("#" + currentPage).hide();
			$(prevPage[0]).fadeIn('slow');
			$('.page-count--page-number').removeClass('active');
			$('.page-count--page-number[data-page=' + selectedPage + ']').addClass('active');
		}
	});
}

function pages(){
	$('.page-count--page-number>a').click(function(e){
		e.preventDefault();
		$(this).parent().addClass('active');		
		var goToPage = $(this).attr('href');
		var goToPageId = $(goToPage).attr('id');
		$('.page-count--page-number').removeClass('active');
		$(this).parent().addClass('active');	
		
		$(goToPage).attr('active', 'true');
		$('fieldset').each(function(){
			if($(this).attr('id') !== $(goToPage).attr('id')){
				$(this).attr('active', 'false');	
			}
			
			if($(this).attr('active') == 'true'){
				$(this).fadeIn('slow');
			}else{
				$(this).hide();
			}
		});
	});

}

function keylisteners(){	
	if($('form[name=registrationform]').index() > -1){
		$('input[name=username]').keyup(function(){
			var username = $(this).val();
			validateUsername(username);
		});	
		
		$('.mdl-textfield__input[name=password]').keyup(function(){
			// Make sure the password is the proper length
			if($(this).val().length >= 8){
				$(this).css('outline', '2px solid green');
				$(this).css('border-bottom', '2px solid green');
			}else{
				$(this).css('outline', '2px solid red');
				$(this).css('border-bottom', '2px solid red');
			}
		});
		
		$('.mdl-textfield__input[name=confirm_password]').keyup(function(){
			// Confirm the passwords match. 
			var confirmPassword = $(this).val();
			var password = $('.mdl-textfield__input[name=password]').val();
			if(confirmPassword === password){
				$('.next--contact-information').fadeIn('fast');
				$(this).css('outline', '2px solid green');
				$(this).css('border-bottom', '2px solid green');
			}else{
				$('.next--contact-information').fadeOut('fast');
				$(this).css('outline', '2px solid red');
				$(this).css('border-bottom', '2px solid red');
			}
		});
	
	
		// Company Information
		$('input[name=company_name]').keyup(function(){
			var company_name = $(this).val();
			if(company_name.length > 0){
				$('.next--company-information').fadeIn('fast');
			}else{
				
				$('.next--company-information').hide();
			}
		});
	}
}

function setRegistrationPages(numPages){
	for(i = 1; i <= numPages; i++){
		var page = $('fieldset[data-page=' + i + ']');
		var goToPage = $(page).attr('id');
		$('.page-count').append("<div class='page-count--page-number page-" + i +"' data-page='"+i+"'><a class='registrationPageLink' href='#" + goToPage + "'>" + i + "</div>");
		if(i > 1 && i < 3){
			$('.page-count--page-number').append("<span class='page-count--page-separator'></span>");	
		}
	}
	
	var activePage = location.search;
	if($('#contactInformation').attr("active") == "true"){
		$(".page-2").addClass("active");
	}else if($('#companyInformation').attr("active") == "true"){

		var pageNumber = $('.page-count--page-number').data('page', 3);
		$(".page-3").addClass("active");
	}else{
		var pageNumber = $('.page-count--page-number').data('page', 1);
		$('.page-1').addClass("active");
	}
	
	
}



function validatePassword() {
    var pwdInput = $("input[name='password']");
    var cnfPwdIpt = $("input[name='confirm_password']");
    var submitButton = $("button[type='submit']");
    if (pwdInput.val().length < 4) {
        $(pwdInput).css('outline', '1px solid red');
    } else {
        $(pwdInput).css('outline', '1px solid green');
        if (pwdInput.val() !== cnfPwdIpt.val()) {
            $(cnfPwdIpt).css('outline', '1px solid red');
        } else {
            $(cnfPwdIpt).css('outline', '1px solid green');
            $(submitButton).attr('disabled', false);
        }
    }

}

function registerCompany() {
    var data = "action=registerNewVendor";
    data += "&" + $('form[name="registrationform"]').serialize();
	$.ajax({
        data: data,
        url: "includes/php/VendorRegistration.php",
        method: "post",
        success: function (response) {
            if (response === "success") {
                window.location.href = "userHome.php";
            } else if (response === "exists") {
                window.location.href = "userHome.php";
            } else {
                console.log("Response: " + response);
            }
        }
    });
    return false;
}

function registrationNotification(email){
	var data = "action=registrationNotification";
	data += "&email=" + email;
	
	$.ajax({
		data:data,
		url: "includes/php/VendorRegistration.php",
		method: 'post',
		success:function(response){
			if(!response){
				$('.next--contact-information').is(":disabled");
				$('input[name=username]').css('outline', 'red solid 1px');
			}else{
				$('.next--contact-information').is(":enabled");
				$('input[name=username]').css('outline', 'green solid 1px');
			}
		},
		error: function(error){
			console.log(error);
		}
	});
}

function validateUsername(username){
	var unameInput = $('input[name="username"]');
	var submitButton = $("button[type='submit']");
	
	var data = {
		action: 'validateUsername',
		'username': username,
	}
	
	$.ajax({
		data:data, 
		url: "includes/php/VendorRegistration.php",
		method: 'post', 
		success:function(result){
			if(result == false){ 
				$(unameInput).css('outline', '1px solid red');
				$(submitButton).attr('disabled', true);				
			}else{
				$(unameInput).css('outline', '1px solid green');
				$(submitButton).attr('disabled', false);
			}
		}
	});
}



var loginAttempt = 0;

function validateLoginForm(){
	console.log('validatingForm');
    var username = $("input[name='username']").val();
    var password = $("input[name='password']").val();
    
    var loginFormValid = new Array();
    
    if(username === "" || username === null || username.length < 5){
        loginFormValid["username"] = false;
    }else{
        loginFormValid["username"] = true;
    }
    
    if(password === "" || password === null || password.length < 8){
        loginFormValid["password"] = false;
    }else{
        loginFormValid["password"] = true;
    }
    
    if(loginFormValid["username"] === false || loginFormValid["password"] === false){
       $("input[name='username']").css("outline","1px solid red");
       $("input[name='username']").css("border","1px solid red");
       $("label[for='username']").css("color","red");
       $("input[name='password']").css("outline","1px solid red");
       $("input[name='password']").css("border","1px solid red");
       $("label[for='password']").css("color","red");
    }else{
	    console.log('signing in');
        signIn();
    }
    return false;
}

function signIn(){
    var data = $('form[name="loginForm"]').serialize();
    data += "&action=sign_in";
    
    console.log(data);
    $.ajax({
        url:"includes/php/UserLogIn.php",
        data:data,
        method:"post",
        success:function(results){
	        console.log(results);
            if(results === "success"){
                window.location="userHome.php";
            }else{
	            console.log("Results: " + results);
            }
        }, 
        error:function(error){
	        console.log('error');
        }
    });
}
    
function signOut(){
	var data = {
		action: 'sign_out'
	};
	$.ajax({
		url: 'includes/php/UserLogIn.php',
		data: data,
		method: 'post', 
		success:function(results){
			console.log(results);
			if(results){
				window.location = 'login';
			}
		}
	});
	return true;
}

var app = angular.module('utterfare', ['ngRoute']);

app.config(function($routeProvider, $locationProvider){	
	
	$routeProvider
	.when('/', {
		templateUrl: "page-templates/main.php",
		controller: 'HomeController'
	})
	.when('/results', {
		templateUrl: "page-templates/results.php",
		controller: 'ResultsController'
	})
	.when('/single', {
		templateUrl: "page-templates/single.php",
		controller: 'SingleController'
	})
	.when('/sign-up', {
		templateUrl: 'page-templates/newUser.php'
	})
	.when('/user/account', {
		templateUrl: 'page-templates/user/account.php',
		controller: 'UserController'
	});

	
});

app.controller('UserController', function($scope){
	window.getUserData();
});

app.controller('HomeController', function($scope){
	console.log('Home controller');
	if(window.userLocation!== undefined && window.userLocation !== null){
		window.curateHomepageSections(userLocation);
	}
});

app.controller('ResultsController', function($scope, $routeParams){
	
	var params = $routeParams;
		
	// Initialize the map
	window.initMap(window.userLocation);
		
	// Perform the search
	//terms, searchLocation, distance, page, limit, offset
	var offset = (params.page - 1) * 25;
	window.performSearch(params.terms, window.userSearchLocation, window.searchDistance, params.page, 25, 0);
	
});

app.controller('SingleController', function($scope, $routeParams){
	window.showSingleItem($routeParams.id);
	window.getSingleVendorItems();
	window.getItemReviews();
	window.getItemRating();
});

app.controller('SignInController', function($scope){
	$scope.signUserIn = function(user){
		window.userSignIn(user.username, user.password);
	};
});

app.controller('SearchController',  function($scope, $http, $location){
		
	console.log("Search");
	$scope.location = window.userLocation;
	
	if($('.search-form__input').is(":focus")){
		$('.search-form__input').focusout();
	}
		
	$scope.search = function(data){
		window.goToSearchPage(data.terms, $scope.location, 10, 1, 25, 0);
	};
	
	$scope.setManualSearchLocation = function(data){
		$('.search-form__input').data('location', data.location);
		$('.search-form__input').data('distance', data.distance);
		$('.recent-searches--search-location').text(data.location);
	};
	
});

app.controller('UserController', function($scope){
	
	$scope.newUser = function(data){
		
		if(data.password !== data.confirm_password){
			
			$scope.password_match = "Your passwords do not match.";
			
		}
		
		window.insertNewUser(data);	
		
	};
	
});


$(document).ready(function(){
	
});

function getCompanyInformation(){

}

function updateCompanyInformation(){
	var data = "action=updateCompanyInformation&"; 
	data += $("form[name='companyInformation']").serialize();
	$.ajax({
		data: data,
		method: 'post', 
		url: 'includes/php/CompanyInformation.php',  
		success:function(result){
			console.log(result);
			if(result == true){
				showSnackbar("Company Information Updated", "long");
			}else{
				showSnackbar("Error: Company Information Was Not Updated", "short");
			}
		}, 
		error: function(error){
			showSnackbar("Error: Company Information Was Not Updated", "short");
		}
	});
	
	return false;
}

function updateProfilePicture(){
	var image = $('.profile-picture__upload').prop('files')[0];
	//setProfilePreview();
	var data = new FormData();
	data.append("action", "updateProfilePicture");
	data.append("picture", image);
	console.log(image);
	$.ajax({
		data: data,
		type: 'post', 
		url: 'includes/php/CompanyInformation.php', 
        cache: false,
        contentType: false,
        processData: false,
		success:function(data){
			$('.profile-picture').attr('src', data + "?ver=" + String(Math.random()));
			$(".profile-picture").css('background-color', 'transparent');
			$('.profile-picture__upload').val('');
			showSnackbar('Image successfully uploaded.', 'long');
		}, error: function(error){
			showSnackbar('Error uploading image', 'long');
		}
	});
	return false;
}
var main;
var results;
var loadingIndicator; 
var recommendationsCarousel;

$(document).ready(function(){
	main = $('.main');
	results = $('.results');
	loadingIndicator = $('.loading-indicator--section');
	
	main.detach();
	
	recommendationsCarousel = $('.recommendations-carousel__inner');
	
});

function curateHomepageSections(user_location){		
	main.detach();
	$('.content').append(loadingIndicator);
	
	getTopItems();
	getRecommendations(user_location);
		
	$('.content').append(main);
}

function getTopItems(){
	var data = {
		'action': 'get_top_items'
	}
	
	var top_items = '';
	
	$.post(window.search_url, data, function(response){
		$.each(response, function(k, v){
						
			var address_parts = $.parseJSON(v.address);
			
			var address = address_parts._city + ", " + address_parts._state.toUpperCase();
			top_items += '<div class="col mx-auto d-flex">';
			top_items += '<div class="card featured-item">';
			top_items += '<img src="' + v.primary_image + '" class="card-img-top" alt="' + v.item_name + '">';
			top_items += '<div class="card-body">';
			top_items += '<div class="card-title">';
			top_items += '<h3>' + v.item_name + '</h3>'
			top_items += '</div>';
			top_items += '<div class="card-text">';
			top_items += '<i class="featured-item__location">' + address + '</i><br/>';
			top_items += '<strong class="featured-item__vendor">' + v.vendor_name + '</strong><br/>';
			top_items += '<p class="featured-item__short-description">' + v.item_short_description + '</p>';
			top_items += '<a href="#!/single?id=' + v.item_id + '" type="button" class="btn btn-light">More Info</a>'; 
			top_items += '</div></div></div></div>';

		});
	}, 'json')
	.done(function(){
		$('.featured-items-row--top-items').append(top_items);
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
	
	var recommendations = "<div class='carousel-item active'><div class='row'>";
	
	var count = 0;


	$.post(window.search_url, data, function(response){
		
		$.each(response, function(key, value){
			var address_parts = $.parseJSON(value.address);
			var address = address_parts._city + ", " + address_parts._state.toUpperCase();
			
			recommendations += '<div class="col-md-3 mx-auto d-flex">';
			recommendations += '<div class="card recommendation">'; 
			recommendations += '<img src="' + value.primary_image + '" class="card-img-top" alt="' + value.item_name + '">';
			recommendations += '<div class="card-body">';
			recommendations += '<div class="card-title"><h3>' + value.item_name + '</h3></div>';
			recommendations += '<div class="card-text">'; 
			recommendations += '<i class="recommendation__location">' + address + "</i>";
			recommendations += '<h4 class="recommendation__vendor">' + value.vendor_name + "</h4>";
			recommendations += '<p>' + value.item_short_description + '</p>';
			recommendations += '<a href="#!/single?id=' + value.item_id + '" type="button" class="btn btn-light">More Info</a>'; 
			recommendations += '</div>'; // .card-text
			recommendations += '</div>'; // .card-body
			recommendations += '</div>'; // .recommendation
			recommendations += '</div>'; // .col-md-3
			
			count += 1;
			
			if(count === 4){
				recommendations += "</div></div><div class='carousel-item'><div class='row'>";
			}else if(count === 8){
				recommendations += "</div></div>";
			}
		});

	}, 'json')
	.done(function(){
		$('.recommendations-carousel__inner').html(recommendations);
		loadingIndicator.detach();
	});
		
}


var map;
var userLocation;
var userSearchLocation;
var searchDistance = 10;

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

/*
* Create the location popoever. 
* This popover will allow the user to input a location and distance manually.
*/
function showLocationPopover(){
	var locationInputContent = "<label for='userSearchLocationInput'><strong>Location:</strong>";
	locationInputContent += "<input type='text'name='userSearchLocationInput' value='" + window.userLocation + "'>";
	locationInputContent += "<label for='userSearchDistance'><strong>Distance</strong></label>";
	locationInputContent += "<select name='userSearchDistance' value='" + window.searchDistance + "'}>";
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
		
		console.log(window.searchDistance);
	});
}


$(document).ready(function(){
	var url = window.location.href;
	console.log(url);
	if(url.indexOf('userHome') > -1){
		topTermsChart();
		searchCountChart();
		platformChart();
	}
});

function topTermsChart(){
	var data = {
		action: 'getTermsData'
	};
	$.ajax({
		data: data, 
		method: 'post', 
		url: 'includes/php/SearchAnalytics.php',
		success: function(response){
			drawChart('topTermsChart','Top 5 Search Terms','doughnut', response);
		},
		error:function(){
			console.log('error');
		}
	});
}

function searchCountChart(){	
	var data = {
		action: 'getSearchCount'
	};
	
	$.ajax({
		data: data, 
		method: 'post', 
		url: 'includes/php/SearchAnalytics.php',
		success: function(response){
			drawChart('searchCountChart','Search Appearances','line', response);
		},
		error:function(){
			console.log('error');
		}
	})
}

function platformChart(){
	var data = {
		action: 'get_platforms'
	};
	
	$.ajax({
		data: data, 
		method: 'post', 
		url: 'includes/php/SearchAnalytics.php',
		success: function (response){
			drawChart('searchPlatformChart', "Searches by Platform", 'bar', response);
		}
	})
}

function drawChart(container, title, chartType, response){
	var label = [];
	var count = [];
	var totalCount = 0;
	
	$.each(JSON.parse(response), function(term, total){
		label.push(term);
		count.push(parseInt(total));
		totalCount += total;
	});
	
	var fill = null, options = null, borderColor = null;
	if(chartType === 'bar' || chartType === 'line'){
		borderColor = 'rgba(2,136,209,1.0)';
		fill = false;
		options = {
			title: {
				display: true,
				text: title,
				fontSize: 24,
			},
			responsive: true,
			scales :{ yAxes: 
				[{ 
					display: true, 
					ticks: {
						suggestedMin: 0,
						steps: 0.5,				
					}
				}]
			}
		};
	}else if(chartType === 'doughnut'){
		borderColor = 'rgba(0,0,0,0)';
		options = {
			title: {
				display: true,
				text: title,
				fontSize: 24,
			},
			responsive: true,
		}
	}

	var ctx = $("#" + container);
	var myChart = new Chart(ctx, {
	    type: chartType,
	    data: {
	        labels: label,
	        datasets: [{
	            label: title, 
	            data: count,  
	            fill: fill,
	            borderColor: borderColor,
	            backgroundColor: [
                'rgba(2,136,209, 1.0)',
                'rgba(2,136,209, 0.80)',
                'rgba(2,136,209, 0.60)',
                'rgba(2,136,209, 0.40)',
                'rgba(2,136,209, 0.20)',
				],
	        }],
	    },
	    options: options
	});
	
	if(myChart.data.datasets[0].data.length === 0){
		Chart.plugins.register({
			afterDraw: function(chart){
				ctx = myChart.chart.ctx;
				var width = myChart.chart.width;
				var height = myChart.chart.height
				//myChart.clear();
				
				ctx.save();
				ctx.textAlign = 'center';
				ctx.textBaseline = 'middle';
				ctx.font = "16px normal 'Helvetica Nueue'";
				ctx.canvas.title = myChart.options.title.text;
				ctx.fillText('No data to display', width / 2, height / 2);
				//ctx.restore();

			}
		})
	}
	/*Chart.plugins.register({
		afterDraw: function(chart){
			$(chart.canvas).each(function(){
				if(myChart.data.datasets.data === undefined){
					var ctx = myChart.chart.ctx;
					var width = myChart.chart.width;
					var height = myChart.chart.height
					myChart.clear();
					
					ctx.save();
					ctx.textAlign = 'center';
					ctx.textBaseline = 'middle';
					ctx.font = "16px normal 'Helvetica Nueue'";
					ctx.fillText('No data to display', width / 2, height / 2);
					ctx.restore();
				}
			});
		}
	});*/

} 
var limit = 25;
var page = 1;
var offset = '0';
var distance = 10;
var params={};
var setMarkers = [];
var markers = [];


$(document).ready(function () {
    $('.search-form__input').focusin(function(){
		//showSuggestions();
		$('.search-form__input').attr('placeholder', 'Try "Impossible Burger"');
    });       
});

$(window).on('load', function(){
	var url = window.location.href;
	var parameters;
	
	
	if(url.indexOf('results') > -1){
		parameters = getSearchParameters(url);

		var offset = 25 * (parameters.page - 1);
		//performSearch(parameters.terms, decodeURIComponent(parameters.location), parameters.distance, parseInt(parameters.page), 25, parseInt(offset));
		
		if($('.search-form__input').val() === undefined || $('.search-form__input').val() === null || $('.search-form__input').val() === ""){
			$('.search-form__input').val(decodeURIComponent(parameters.terms));
			$('.search-form__input').text(decodeURIComponent(parameters.terms));
		}
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
			
			goToSearchPage($(this).text(), $('.location-link').text(), 10, 1, 25, 0);
		});
		
	}
}

function changeLocation(){
	var searchDistance = $('.search-form__input').data('distance');
	var searchLocation = $('.search-form__input').data('location');
	
	$('select[name="distance"]').val(searchDistance);
	$('input[name="location"]').val(searchLocation);
	
	$('#locationModal').modal('show');
	window.userLocation = searchLocation;
}

/**
* Populate the top picks section
*/


function goToSearchPage(terms, searchLocation, distance, page, limit, offset){	
	var url = window.location.protocol + "//" + window.location.host + window.location.pathname + "#!/results";
	var searchParameters = "?action=search&terms=" + encodeURI(terms) + "&page=" + page + "&location=" + encodeURI(searchLocation.replace(/[(,)+]/g, '')) + "&distance=" + distance;
	
	window.location.href = url + searchParameters;
	
}

/*
* Perform the search based on the passed values
*/
function performSearch(terms, searchLocation, distance, page, limit, offset){			
		
	var data = {
		'action' : 'search',
		'location': searchLocation,
		'terms': terms,
		'limit': limit,
		'page': page,
		'distance': distance,
		'offset': offset
	};
	
	
	var display = '';
	var map;

	// Initialize the map		
	initMap(searchLocation);

	// Run the search
	$.ajax({
		url: window.search_url,
		data: data,
		method: 'post',
		dataType: 'json',
		success: function (response) {
			console.log(response.length)
			if(response.length > 0 ){
				$.each(response, function(index, result){
	
					display += '<li class="results-list--item" data-item-id="' + result.item_id + '">';
					
					display += '<div class="card mb-3"></div><div class="row no-gutters">';
					
					display += '<div class="col-md-4">';
	
					display += '<img src="' + result.primary_image + '" class="card-img" alt="' + result.item_name + '">';
					
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
			}else{
				display += "<h3>It looks like there is nothing there.</h3><p>Try expanding your search location or searching for something else.</p>";
			}
			
		}, 
		error: function (jqXHR, error, errorThrown) {
			console.log("error");
			console.log(jqXHR);
			console.log(error);
			console.log(errorThrown);
			
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
				var itemId = $(this).attr('data-item-id');
				console.log($(this));
				console.log(itemId);
				window.location.href="#!/single?id=" + itemId;
			});
			
			$('.results-list--item').on('mouseleave', function(){
				markers.forEach(function(marker){
					marker.setAnimation(null);
				});
			});						
			window.loadingIndicator.detach();
			
		}
	});
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


/*
* Get the item information to be shown on the single item page
*/
function showSingleItem(itemId){
	
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
	});
}

/*
* Handle the single item data 
*/
function populateSingleItemInformation(data){
	var data = JSON.parse(data);
	
	console.log(data);
	
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
	
	
	addMarkers({lat: data.latitude, lng: data.longitude, title: data.vendor_name});
	$('.item-description').text(data.item_description);
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

