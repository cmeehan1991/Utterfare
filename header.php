<!DOCTYPE html>
<html>
	<head>
		<?php 
		include('partials/session.php');
		include('partials/definitions.php');
		include('partials/meta.php');
		//include('partials/tags.php');
		include('partials/scripts.php'); 
		include('partials/styles.php');
		?>        
		<title>Utterfare</title>

	</head>
	<body ng-app="utterfare">
		<nav class="navbar navbar-expand-md navbar-light bg-light">
			<a class="navbar-brand" href="http://localhost/utterfare">
				<img  class="profile-image__small" src="<?php echo BASE_URL; ?>assets/img/favicon.ico"/>
			</a>
		    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		    	      <span class="navbar-toggler-icon"></span>

		    </button>
			<form novalidate class="form-inline my-2 my-lg-0 search-form" ng-submit="search(search)" ng-controller="SearchController">
				<input class="form-control mr-sm-2 search-form__input" type="search" placeholder="Search" aria-label="Search" ng-model="search.terms">
			</form>
			
		    <div class="collapse navbar-collapse" id="navbarSupportedContent">
		        <ul class="navbar-nav ml-auto">
	                <li class="nav-item"><a class="nav-link" href="login">Saved</a></li>
	                <li class="nav-item dropdown">
	                	<a class="nav-link dropdown-toggle" id="navbarUserDropdownMenuLink" role="button" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false" href="#"><img class="profile-image__small" src="assets/img/favicon.ico" alt="User profile picture"></a>
		                <?php if($is_signed_in): ?>
			                <div class="dropdown-menu dropdown-menu-right user-signed-in" aria-labelledby="navbarUserDropdownMenuLink">
				                <a class="dropdown-item" href="#">Account</a>
				                <a class="dropdown-item" href="" onClick="return userSignOut()">Sign Out</a>
			                </div>
		                <?php else: ?>
			                <div class="dropdown-menu dropdown-menu-right user-not-signed-in" aria-labelledby="navbarUserDropdownMenuLink">
				                <a class="dropdown-item" href="#signInModal" data-toggle="modal">Sign In</a>
				                <a class="dropdown-item" href="#!/sign-up">Sign Up</a>
			                </div>
		                <?php endif; ?>
	                </li>
	            </ul>
		    </div>
		    
		</nav>


<?php 
function userHeader($title) {
    ?>
<title><?php echo $title = $title != null ? $title . ' | ': '';?> Utterfare&trade;</title>
	</head>
		<body>
		<nav class="navbar navbar-expand-md navbar-light bg-light ">
		    <a class="navbar-brand" href="index">Utterfare</a>
		    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		    	<img src="includes/img/fast-food-99180_640.png" alt="" width="25px" height="25px"/>
		    </button>
		    <div class="collapse navbar-collapse" id="navbarSupportedContent">
		        <ul class="navbar-nav ml-auto">
	                <li class="nav-item"><a class="nav-link" href="userHome">Home</a></li>
	                <li class="nav-item"><a class="nav-link" href="addEditItems">Add/Edit Items</a></li>
	                <li class="nav-item"><a class="nav-link" href="companyInformation">Company Information</a></li>
	                <li class="nav-item"><a class="nav-link" href="" onclick="return signOut()">Log Out</a></li>
	            </ul>
			</div>
		</nav>
    <?php
}
include('partials/loading.php');