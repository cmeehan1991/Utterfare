<?php
session_start();
include 'header.php';
include 'includes/php/SearchAnalytics.php';
$search_analytics = new SearchAnalytics();
userHeader("Home");
?>
<div class="main">
    <div class="container-fluid" align="center">
	    <h1>Dashboard</h1>
        <div class="row">
            <div class="col-md-6 mx-auto">
                <canvas id="topTermsChart"></canvas>
            </div>
            <div class="col-md-6 mx-auto">
                <canvas id="searchCountChart"></canvas>
            </div>
            <div class="col-md-6 mx-auto">
                <canvas id="searchPlatformChart"></canvas>
            </div>
        </div>
    </div>
</div>
</body>
</html>