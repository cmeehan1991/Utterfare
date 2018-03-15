<!DOCTYPE html>
<html>
	<head>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<!--<script async src="https://www.googletagmanager.com/gtag/js?id=UA-74857924-3"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());
		
		  gtag('config', 'UA-74857924-3');
		</script>-->
		<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name='description' content='Utterfare is the premier search application for your food cravings. If you are having a craving for something specific, are new to the area, or just want to change things up some, then this is the right application for you.'>
        <meta name='keyworkds' content='food, drink, cravings, date night, lunch, dinner, supper, breakfast, brunch, menu, restaurant'>
        <meta name='author' content='CBM Web Development'>
        <!--required scripts-->
        <script src="includes/jquery/jquery-3.1.1.min.js" type="text/javascript"></script>
		
        <!-- Google maps API-->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCfPuT0e1aszJ7ac7ePqH9qHwcxaQAxvsk"></script> 

		<!-- MDL-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.light_blue-blue.min.css">
		<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>

        <!--Bootstrap-->
        <!--<link href="includes/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script src="includes/bootstrap/bootstrap.min.js" type="text/javascript"></script>-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

		<!--Font Awesome 4.7.0-->
		<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

        <!--Styles and Scripts-->
        <link href="includes/css/home.css" rel="stylesheet" type="text/css"/>
        <link href="includes/css/main.css" rel="stylesheet" type="text/css"/>        
        <link href="includes/css/userMain.css" rel="stylesheet" type="text/css"/>
        <link href="includes/css/addEditItems.css" rel="stylesheet" type="text/css"/>
        <link href="includes/css/companyInformation.css" rel="stylesheet" type="text/css"/>
        <link href="includes/css/registrationStyle.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
        
        <script src="includes/js/global.js" type="text/javascript"></script>
        <script src="includes/js/script.js" type="text/javascript"></script>
        <script src="includes/js/searchScript.js" type="text/javascript"></script>
        <script src="includes/js/locationScript.js" type="text/javascript"></script>
        <script src="includes/js/AddEditItems.js" type="text/javascript"></script>
        <script src="includes/js/companyInformation.js" type="text/javascript"></script>
        <script src="includes/js/companyInformation.js" type="text/javascript"></script>
        <script src="includes/js/VendorRegistration.js" type="text/javascript"></script>
        <script src="includes/js/VendorSignIn.js" type="text/javascript"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
        
		<title>Utterfare&trade;</title>
<?php

function mainHeader() { ?>
	</head>
<body>
	<nav class="navbar navbar-light bg-light">
	    <a class="navbar-brand" href="index">Utterfare</a>
	    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	    	<img src="includes/img/fast-food-99180_640.png" alt="" width="25px" height="25px"/>
	    </button>
	    <div class="collapse navbar-collapse" id="navbarSupportedContent">
	        <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="login">Vendor Login</a></li>
                <li class="nav-item"><a class="nav-link" href="contact">Contact Us</a></li>
                <li class="nav-item"><a class="nav-link" href="about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Terms</a></li>
            </ul>
	    </div>
	</nav>
<?php
}

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
	                <li class="nav-item"><a class="nav-link" href="?signout=true">Log Out</a></li>
	            </ul>
			</div>
		</nav>
    <?php
}
