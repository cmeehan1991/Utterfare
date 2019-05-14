<!DOCTYPE html>
<html>
	<head>
		<?php 
		include('partials/meta.php');
		include('partials/tags.php');
		include('partials/scripts.php'); 
		include('partials/styles.php');
		?>        
		
		<title>Utterfare</title>

	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-light bg-light">
		    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		    	<img src="assets/img/fast-food-99180_640.png" alt="" width="25px" height="25px"/>
		    </button>
			<form class="form-inline my-2 my-lg-0 search-form">
				<input class="form-control mr-sm-2 search-form__input" type="search" placeholder="Search" aria-label="Search">
			</form>
		    <div class="collapse navbar-collapse" id="navbarSupportedContent">
		        <ul class="navbar-nav ml-auto">
	                <li class="nav-item"><a class="nav-link" href="login">Saved</a></li>
	                <li class="nav-item"><a class="nav-link" href="#"><img class="profile-image__small" src="assets/img/favicon.ico" alt="User profile picture"></a></li>
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
