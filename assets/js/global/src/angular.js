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
	.otherwise('/');

	
});

app.controller('UserController', function($scope){
	window.getUserData();
});

app.controller('HomeController', function($scope){
	console.log('Home controller');
	if(window.userLocation!== undefined && window.userLocation !== null){
		window.currateHomepageSections(userLocation);
	}
});

app.controller('ResultsController', function($scope){
	
	// Get the search parameters from the URL
	let params = window.getSearchParameters(window.location.href);
	
	// Initialize the map
	window.initMap(window.userLocation);
	
	console.log($scope.location);
	
	// Perform the search
	//terms, searchLocation, distance, page, limit, offset
	var offset = (params.page - 1) * 25;
	window.performSearch(params.terms, params.location, params.distance, params.page, 25, 0);
	
});

app.controller('SingleController', function($scope){
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

