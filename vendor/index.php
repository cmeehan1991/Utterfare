<?php
session_start();
include 'header.php';

?>
<div class="container-fluid">
	<div class="row">
		<?php if(UF_VENDOR_USER_SIGNED_IN): ?>
		<div class="col-md-2">
			<ul class="nav flex-column">
				<li class="nav-item">
					<a class="nav-link active" href="#!/"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#!/items"><i class="fas fa-list"></i> Menu Items</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#!/company"><i class="fas fa-address-card"></i> Company Profile</a>
				</li>
				<li class="nav-item">
					<a class="nav-item" href="" onclick="return signUserOut();">Sign Out</a>
				</li>
			</ul>
			
		</div>
		<?php endif; ?>
		
		<div class="col-md-10 content mx-auto" ng-view>
		</div>
	</div>
</div>
<?php include_once('footer.php'); ?>