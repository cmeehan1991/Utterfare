<?php
session_start();
include 'header.php';

?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name='description' content='Utterfare is the premier search application for your food cravings. If you are having a craving for something specific, are new to the area, or just want to change things up some, then this is the right application for you.'>
        <meta name='keyworkds' content='food, drink, cravings, date night, lunch, dinner, supper, breakfast, brunch, menu, restaurant'>
        <meta name='author' content='CBM Web Development'>
        <!--JQuery-->
        <script src="includes/jquery/jquery-3.1.1.min.js" type="text/javascript"></script>

        <!-- Google maps API-->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script> 

        <!--Bootstrap-->
        <link href="includes/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script src="includes/bootstrap/bootstrap.min.js" type="text/javascript"></script>

        <!--Styles and Scripts-->
        <link href="includes/css/main.css" rel="stylesheet" type="text/css"/>
        <script src="includes/js/searchScript.js" type="text/javascript"></script>
        <script src="includes/js/locationScript.js" type="text/javascript"></script>
        <link href="includes/css/home.css" rel="stylesheet" type="text/css"/>
        
        <script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
		
		  ga('create', 'UA-74857924-3', 'auto');
		  ga('send', 'pageview');

		</script>

        <title>Utterfare</title>
    </head>
    <body>
	    <?php include_once("analyticstracking.php") ?>
        <nav class="hidden-xs hidden-sm navbar navbar-normal">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index">Utterfare</a>
                </div>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" arai-haspopup="true" aria-expanded="false"><img class="hidden-xs hidden-sm" src="includes/img/fast-food-99180_640.png" alt="" width="50px" height="50px"/></a>
                        <ul class="dropdown-menu">
                            <li><a href="login.php">Vendor Login</a></li>
                            <li><a href="">Contact Us</a></li>
                            <li><a href="">About</a></li>
                            <li><a href="">Terms</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <nav class="hidden-md hidden-lg navbar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <img src="includes/img/fast-food-99180_640.png" alt="" width="40px" height="40px"/>
                    </button>
                    <a class="navbar-brand" href="index">Utterfare</a>
                </div>
                <div class="collapse navbar-collapse" id="navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="login.php">Vendor Login</a></li>
                        <li><a href="">Contact Us</a></li>
                        <li><a href="">About</a></li>
                        <li><a href="">Terms</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <nav class="navbar navbar-presearch">
            <div class="container-fluid">
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" arai-haspopup="true" aria-expanded="false"><img class="hidden-xs hidden-sm" src="includes/img/fast-food-99180_640.png" alt="" width="50px" height="50px"/></a>
                        <ul class="dropdown-menu">
                            <li><a href="login">Vendor Login</a></li>
                            <li><a href="">Contact Us</a></li>
                            <li><a href="">About</a></li>
                            <li><a href="">Terms</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class='hidden-xs hidden-sm page-title'>Utterfare</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" align="center">
                        <form method="post" onsubmit='return formSearch()'>
                            <div class='row'>
                                <div class="col-md-12">
                                    <div class='search-div'>
                                        <button type="submit" class="visible-xs visible-sm hidden-md hidden-lg " class="search" style="float:right"><i class="glyphicon glyphicon-search"></i></button>
                                        <input type="search" name="search" class="searchInput" style="width:85%"/></div>
                                </div>
                                <div class="row">
                                    <a class="locationLink" data-location="" onclick="changeLocation()">Current Location</a>
                                    <input type="text" data-location="" class="locationInput" value="" placeholder="City, St or Zip"/>
                                    <select name="distance" class="distance">
                                        <option value="2">2 Miles</option>
                                        <option value="5">5 Miles</option>
                                        <option value="10">10 Miles</option>
                                        <option value="15">15 Miles</option>
                                        <option value="20">20 Miles</option>
                                        <option value="25">25 Miles</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hidden-xs hidden-sm col-md-6" align='right'>
                                    <button type="submit" class="search">Search</button> 
                                </div>
                                <div class="hidden-xs hidden-sm col-md-6" align='left'>
                                    <button type="submit feeling-lucky" class="search" >I'm Feeling Lucky</button>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="results"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="loadmore">
                                <button type="button" name="loadmore-button" class="loadmore-button" onclick="return loadMore();" >Load More</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
