<?php
session_start();
include 'header.php';

?>
<?php include('partials/loading.php'); ?>
<div class="content" ng-view></div>
<?php include_once('footer.php'); ?>