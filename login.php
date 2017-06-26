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
        <script src="includes/js/VendorSignIn.js" type="text/javascript"></script>
        
        <title>Utterfare</title>
    </head>
    <body>
        <?php mainHeader(); ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-md-4 col-md-offset-4"> 
                    <form onsubmit="return validateForm()">
                        <div class="row">
                            <div class="col-md-12">
                                <h2>Utterfare Sign In</h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="username">Username</label><br/>
                                <input type="text" name="username" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="password">Password</label><br/>
                                <input type="password" name="password"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit">Sign In</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-4 col-md-offset-4">
                    <a href="registration.php">Register</a>
                </div>
            </div>
        </div>
    </body>
</html>