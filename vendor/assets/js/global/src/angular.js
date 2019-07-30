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
		controller: 'vendorItemsController'
	})
	.when('/sign-out', {
		controller: 'SignOutController', 
	})
	.otherwise('/')
});


vendor.controller('vendorItemsController', function($scope){
	window.getMenuItems(1);
});

vendor.controller('vendorController', function($scope){

	var vendorStatus = window.getVendorStatus();
	console.log(vendorStatus);
	if(!vendorStatus){
		window.location.href = "#!/sign-in"
	}
});

vendor.controller('editItemController', function($scope, $routeParams){
	window.getItem($routeParams.item_id)
});

vendor.controller('vendorLoginController', function($scope){
	console.log("Login Controller");
});



