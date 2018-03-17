<?php
session_start();
include 'header.php';
mainHeader();
include_once("analyticstracking.php") ?>
        <main class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class='hidden-xs hidden-sm page-title'>Utterfare</h1>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-md-12" align="center">
                        <form method="post" onsubmit='return formSearch()'>
                            <div class='row'>
                                <div class="col-md-6 ml-auto">
                                    <div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label search-div' style="width:100%">
	                                    <label for="search" class="mdl-textfield__label">Search...</label>
                                        <input type="search" name="search" class="mdl-textfield__input searchInput" style="width:100%"/>
                            		</div>
                            	</div>
                                <div class="col-md-1 mr-auto">
                                    <button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">Search</button> 
                                </div>
                            </div> 
                            <br/>
                            <div class="row">
	                            <div class="col-xs-8 col-md-4 ml-auto">
	                                <a class="mdl-link mdl-link--dark locationLink" data-location="" onclick="changeLocation()">Current Location</a>
	                                <input type="text" data-location="" class="locationInput" value="" placeholder="City, St or Zip"/>
	                            </div>
	                            <div class="col-xs-4 col-md-1 mr-auto">
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
                        </form>
                    </div>
                </div>
                        <div class="row">
                            <div class="col-md-10 mx-auto" align="center">
	                            <div class="loader"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>
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
 <?php include_once('footer.php');
