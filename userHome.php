<?php
session_start();
include 'header.php';
include 'includes/php/SearchAnalytics.php';
$search_analytics = new SearchAnalytics();
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <!-- Google Analytics-->

        <script>
            (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
            })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
            ga('create', 'UA-74857924-3', 'auto');
            ga('send', 'pageview');
            ga('set', 'userId', {{USER_ID}}); // Set the user ID using signed-in user_id.

        </script>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name='description' content='Utterfare is the premier search application for your food cravings. If you are having a craving for something specific, are new to the area, or just want to change things up some, then this is the right application for you.'>
        <meta name='keyworkds' content='food, drink, cravings, date night, lunch, dinner, supper, breakfast, brunch, menu, restaurant'>
        <meta name='author' content='CBM Web Development'>
        <!--JQuery-->
        <script src="includes/jquery/jquery-3.1.1.min.js" type="text/javascript"></script>

        <!-- Google API-->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script> 

        <!--Bootstrap-->
        <link href="includes/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script src="includes/bootstrap/bootstrap.min.js" type="text/javascript"></script>

        <!--Styles and Scripts-->
        <link href="includes/css/main.css" rel="stylesheet" type="text/css"/>
        <link href="includes/css/userMain.css" rel="stylesheet" type="text/css"/>

        <title>Utterfare</title>
    </head>
    <body>
        <?php userHeader(); ?>
        <?php include 'sidebar.php'; ?>
        <div class="main">
            <div class="container-fluid" align="center">
                <form>
                    <fieldset>
                        <legend>User Information</legend>
                        <div class="row"  >
                            <div class="col-xs-12 col-md-4" >
                                <div class="activity-section">
	                                <div class="row" >
		                                <div class="col-md-12" align="center">
			                                <b>Search Summary</b>
		                                </div>
		                                <div class="row">
			                                <div class="col-md-12">
				                                <table class="search-summary-table">
					                                <tr>
						                                <td><label>Avg./Day:</label></td><td><?php $search_analytics->get_average_searches(); ?></td>
					                                </tr>
					                                <tr>
						                                <td><label>High/Low:</label></td><td><?php $search_analytics->get_max_min_searches_today(); ?></td>
					                                </tr>
					                                <tr>
						                                <td><label>Today:</label></td><td><?php $search_analytics->get_total_daily_searches();?></td>
					                                </tr>
					                                <tr>
						                                <td rowspan="5" align="top"><label>Top 5 Terms:</label></td>
						                                <td rowspan="5">
							                               <ol>
								                               <?php echo $search_analytics->get_top_terms(); ?>
							                               </ol>
						                                </td>
					                                </tr>
				                                </table>
			                                </div>
		                                </div>
	                                </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4" align="center">
                                <div class="activity-section">
	                                <div class="row">
		                                <div class="col-md-12">
			                                <b>Vendor Summary</b>
		                                </div>
	                                </div>
	                                <div class="row">
		                                <div class="col-md-12">
			                                <label for="subscription-type">Subscription Type:</label> <span name="subscription-type">Free</span>
		                                </div>
	                                </div>
	                                <div class="row">
		                                <div class="col-md-12">
			                                <label for="subscription-type">Subscription Type:</label> <span name="subscription-type">Free</span>
		                                </div>
	                                </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4" align="center">
                                <div class="activity-section">
	                                <div class="row">
		                                <div class="col-md-12">
			                                <b>Traffic Summary</b>
		                                </div>
	                                </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </body>
</html>