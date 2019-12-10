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
	})
	.when('/vendor/', {
		templateUrl: 'vendor/page-templates/vendor.php',
		controller: 'VendorController'
	})
	.when('/vendor/sign-in', {
		templateUrl: 'vendor/page-templates/sign-in.php',
		controller: 'VendorSignInController'
	})
	.when('/404', {
		templateUrl: 'page-templates/404.php'
	})
	.otherwise('/404');

	
});

app.controller('VendorController', function($scope){
	let vendorStatus = window.getVendorStatus();
	
	if(!vendorStatus){
		window.location.href = '#!/vendor/sign-in'
	}
})

app.controller('UserController', function($scope){
	window.getUserData();
});

app.controller('HomeController', function($scope){
	window.curateHomepageSections();
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

