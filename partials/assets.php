<?php 
$whitelist = array(
    '127.0.0.1',
    '::1',
);

if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
	?>
	<script src="./assets/js/global/dist/allscripts.min.js" type="text/javascript"></script>
	<link href="./assets/styles/css/dist/allstyles.min.css" rel="stylesheet">
	<?php
}else{?>
	
	<script src="./assets/js/global/dist/app.js" type="text/javascript"></script>
	<link href="./assets/styles/css/dist/app.css" rel="stylesheet">
<?php 
}
?>
