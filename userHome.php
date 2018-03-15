<?php
session_start();
include 'header.php';
include 'includes/php/SearchAnalytics.php';
$search_analytics = new SearchAnalytics();
userHeader("Home");
?>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script src="includes/js/jquery.searchAnalytics.js" type="text/javascript"></script>
<div class="main">
    <div class="container-fluid" align="center">
        <form>
            <fieldset>
                <legend>User Information</legend>
                <div class="row">
	                <div class="col-xs-12 col-md-4">
		                <div id="topTermsContainer" style="height: 370px; width: 100%;"></div>
	                </div>
                </div>
                <div class="row"  >
                    <div class="col-xs-12 col-md-4" >
                        <div class="activity-section">
                            <div class="row" >
                                <div class="col-md-12" align="center">
	                                <b>Search Summary</b>
                                </div>
                                <div class="container-fluid">
	                                <div class="row">
		                                <div class="col-md-12 mx-auto">
			                                <table class="search-summary-table">
				                                <tr>
					                                <td><label>Avg./Day:</label></td><td><?php $search_analytics->get_average_searches(); ?></td>
				                                </tr>
				                                <tr>
					                                <td><label>High/Low:</label></td><td><?php echo $search_analytics->get_max_min_searches_today(); ?></td>
				                                </tr>
				                                <tr>
					                                <td ><label>Today:</label></td><td><?php $search_analytics->get_total_daily_searches();?></td>
				                                </tr>
				                                <tr>
					                                <td rowspan="5" valign="top"><label>Top 5 Terms:</label></td>
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