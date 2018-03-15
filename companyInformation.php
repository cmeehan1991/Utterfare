<?php
session_start();
if (!isset($_SESSION["COMPANY_ID"])) {
    header("location:login.php");
}
$company_name = $_SESSION['COMPANY_NAME'];
$street_address = $_SESSION['PRIMARY_ADDRESS'];
$secondary_address = $_SESSION['SECONDARY_ADDRESS'];
$postal_code = $_SESSION['ZIP'];
$city = $_SESSION['CITY'];
$state = $_SESSION['STATE'];
$telephone = $_SESSION['PHONE'];
$email = $_SESSION['EMAIL'];
$website = $_SESSION['LINK'];
$profile_picture = $_SESSION['PROFILE_PICTURE'];
include 'header.php';
userHeader('Company Information');
?>
        <div class="main">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-5  mx-auto">
	                    <h2>Contact Information</h2>
	                    <form name="companyInformation" onsubmit="return updateCompanyInformation();" method="post">
		                    <div class="container-fluid">
			                    <div class="row">
				                    <div class="col-md-12 mx-auto mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					                    <label for="company-name" class="mdl-textfield__label">Restaurant Name:*</label>
					                    <input type="text" name="restaurant-name"  class="mdl-textfield__input" value="<?php echo $company_name; ?>" required/>
				                    </div>
			                    </div>
			                    <div class="row">
				                    <div class="col-md-12 mx-auto mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					                    <label for="street-address" class="mdl-textfield__label">Street Address:*</label>
					                    <input type="text" name="street-address" class="mdl-textfield__input" value="<?php echo $street_address; ?>" required/>
				                    </div>
			                    </div>
			                    <div class="row">
				                    <div class="col-md-12 mx-auto mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					                    <label for="suite" class="mdl-textfield__label">Suite/Unit:</label>
					                    <input type="text" name="secondary-address" class="mdl-textfield__input" value="<?php echo $secondary_address; ?>"/>
				                    </div>
			                    </div>
			                    <div class="row">
				                    <div class="col-md-12 mx-auto mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					                    <label for="postal-code" class="mdl-textfield__label">Postal Code:*</label>
					                    <input type="text" name="postal-code" class="mdl-textfield__input" value="<?php echo $postal_code; ?>" required/>
				                    </div>
			                    </div>
			                    <div class="row">
				                    <div class="col-md-12 mx-auto mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					                    <label for="city" class="mdl-textfield__label">City:*</label>
					                    <input type="text" name="city" class="mdl-textfield__input" value="<?php echo $city; ?>" required/>
				                    </div>
			                    </div>
			                    <div class="row">
				                    <div class="col-md-12 mx-auto mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					                    <label for="state"  class="mdl-textfield__label">State:*</label>
					                    <input type="text" name="state" class="mdl-textfield__input" value="<?php echo $state; ?>" required/>
				                    </div>
			                    </div>
			                    <div class="row">
				                    <div class="col-md-12 mx-auto mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					                    <label for="telephone"  class="mdl-textfield__label">Telephone:*</label>
					                    <input type="telephone" name="telephone" class="mdl-textfield__input" value="<?php echo $telephone; ?>" required/>
				                    </div>
			                    </div>
			                    <div class="row">
				                    <div class="col-md-12 mx-auto mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<label for="email"  class="mdl-textfield__label">Email:*</label>
					                    <input type="email" name="email" class="mdl-textfield__input" value="<?php echo $email; ?>" required/>
				                    </div>
			                    </div>
			                    <div class="row">
				                    <div class="col-md-12 mx-auto mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<label for="website"  class="mdl-textfield__label">Website:</label>
					                    <input type="url" name="website" class="mdl-textfield__input" value="<?php echo $website; ?>" />
				                   	</div>
			                    </div>
			                    <div class="row">
				                    <div class="col-md-12">
					                    <button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">Save Changes</button>
				                    </div>
			                    </div>
		                    </div>
	                    </form>
                    </div>
                    <div class="col-md-6 mx-auto">
	                    <h2>Profile Picture</h2>
	                    <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="profile-picture"/><br/>
	                    <input type="file" name="profile-picture" class="profile-picture__upload" onchange="return updateProfilePicture();"/>
	                    <p >The profile picture will also serve as the default picture for any items that do not have pictures.</p>
                    </div>
                </div>
            </div>
        <div id="snackbar"></div>
        </div>
    </div>
</body>
</html>