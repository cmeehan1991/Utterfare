<!DOCTYPE html>
<html>
	<head>
		
		<?php 
		include('partials/session.php');
		include('partials/definitions.php');
		include('partials/meta.php');
		//include('partials/tags.php');
		include('partials/scripts.php'); 
		include('partials/styles.php');
		?>        
		<title>Utterfare</title>
	</head>
	<body ng-app="utterfare-vendor">
		<nav class="navbar navbar-expand-md navbar-light bg-light">
			<a class="navbar-brand" href="http://localhost/utterfare">
				<img  class="profile-image__small" src="<?php echo BASE_URL; ?>/assets/img/favicon.ico"/>
			</a>
		    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
		    </button>		    
		</nav>