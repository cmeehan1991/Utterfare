var vendor = angular.module('utterfare-vendor', ['ngRoute']);


vendor.config(function($routeProvider, $locationProvider){
	$routeProvider
	.when('/', {
		templateUrl: 'page-templates/vendor.php', 
		controller: 'vendorController'
	})
	.when('/sign-in', {
		templateUrl: 'page-templates/login.php', 
		controller: 'vendorLoginController'
	})
	.when('/items/', {
		templateUrl: 'page-templates/items.php', 
		controller: 'vendorItemsController'
	})
	.when('/items/edit', {
		templateUrl: 'page-templates/edit-item.php', 
		controller: 'editItemController'
	})
	.when('/company', {
		templateUrl: 'page-templates/company.php', 
		controller: 'companyProfileController'
	})
	.when('/sign-out', {
		controller: 'SignOutController', 
	})
	.when('/claim', {
		templateUrl: 'page-templates/claim-listing.php',
		controller: 'claimListingController', 
	})
	.when('/sign-up', {
		templateUrl: 'page-templates/sign-up.php',
		controller: 'newVendorController',
	})
	.otherwise('/404')
});


vendor.controller('newVendorController', function($scope){
	
});

vendor.controller('claimListingController', function($scope){
	
});

vendor.controller('vendorItemsController', function($scope){
	window.getMenuItems(1); 
});

vendor.controller('vendorController', function($scope){

	var vendorStatus = window.getVendorStatus();	
	if(vendorStatus === false){
		window.location.href = "#!/sign-in";
	}
	
	window.getVendorData();
	
});

vendor.controller('editItemController', function($scope, $routeParams){
	window.getItem($routeParams.item_id)
});

vendor.controller('vendorLoginController', function($scope){

	if(window.getVendorStatus() != false){
		window.location.href="#!/";
	}
	
	console.log(window.session);
});

vendor.controller('companyProfileController', function($scope){
	window.getCompanyProfile();
});



