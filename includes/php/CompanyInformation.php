<?php 
class Company_Information{
	
	function __construct(){
		session_start();
		$action = filter_input(INPUT_POST, 'action');
		switch($action){
			case 'updateProfilePicture':
				$this->updateProfilePicture();
				break;
			case 'updateCompanyInformation':
				$this->updateCompanyInformation();
				break;
			default:break;
		}
	}
	
	private function updateCompanyInformation(){
		include 'DbConnection.php';
		
		// Set variables
		$data_table = $_SESSION['DATA_TABLE'] . '_customer';
		$company_id = $_SESSION['COMPANY_ID'];
		
		// Get the form data
		$company_name = filter_input(INPUT_POST, 'restaurant-name');
		$street_address = filter_input(INPUT_POST, 'street-address');
		$secondary_address = filter_input(INPUT_POST, 'secondary-address');
		$postal_code = filter_input(INPUT_POST, 'postal-code');
		$city = filter_input(INPUT_POST, 'city');
		$state = filter_input(INPUT_POST, 'state');
		$telephone = filter_input(INPUT_POST, 'telephone');
		$email = filter_input(INPUT_POST, 'email');
		$website = filter_input(INPUT_POST, 'website');
		
		$sql = "UPDATE $data_table SET COMPANY_NAME = :COMPANY_NAME, PRIMARY_ADDRESS = :PRIMARY_ADDRESS, SECONDARY_ADDRESS = :SECONDARY_ADDRESS, CITY = :CITY, STATE = :STATE, ZIP = :ZIP, PRIMARY_PHONE = :PRIMARY_PHONE, COMPANY_URL = :COMPANY_URL, PRIMARY_EMAIL = :PRIMARY_EMAIL WHERE ID = :ID";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':COMPANY_NAME', $company_name);
		$stmt->bindParam(':PRIMARY_ADDRESS', $street_address);
		$stmt->bindParam(':SECONDARY_ADDRESS', $secondary_address);
		$stmt->bindParam(':CITY', $city);
		$stmt->bindParam(':STATE', $state);
		$stmt->bindParam(':ZIP', $postal_code);
		$stmt->bindParam(':PRIMARY_PHONE', $telephone);
		$stmt->bindParam(':COMPANY_URL', $website);
		$stmt->bindParam(':PRIMARY_EMAIL', $email);
		$stmt->bindParam(':ID', $company_id);
		$updated = $stmt->execute();
		
		if($updated == true){
			$_SESSION['COMPANY_NAME'] = $company_name;
			$_SESSION['PRIMARY_ADDRESS'] = $street_address;
			$_SESSION['SECONDARY_ADDRESS'] = $secondary_address;
			$_SESSION['ZIP'] = $postal_code;
			$_SESSION['CITY'] = $city;
			$_SESSION['STATE'] = $state;
			$_SESSION['PHONE'] = $telephone;
			$_SESSION['EMAIL'] = $email;
			$_SESSION['LINK'] = $website;
			echo true;
		}else{
			echo false;
		}
	}
	
	/**
	* Updates the profile picture for the company.
	* Will return the image address or null if there was an error saving the image.
	*/ 
	private function updateProfilePicture(){
		include 'DbConnection.php';
		
		// Datatable information
		$data_table = $_SESSION['DATA_TABLE'] . '_customer';
		$company_id = $_SESSION['COMPANY_ID'];
		
		// Get the image data
		$image = file_get_contents($_FILES['picture']['tmp_name']); 
		$image_name = $_FILES['picture']['name'];
		$extension = pathinfo($image_name, PATHINFO_EXTENSION);
		$png_image = null; 
		switch($extension){
			case "jpg":
            $png_image = imagecreatefromjpeg($_FILES['picture']['tmp_name']);
            break;
        case "jpeg":
            $png_image = imagecreatefromjpeg($_FILES['picture']['tmp_name']);
            break;
        case "gif":
            $png_image = imagecreatefromgif($_FILES['picture']['tmp_name']);
            break;
        case "png":
            $png_image = imagecreatefrompng($_FILES['picture']['tmp_name']);
            break;
        default:
            break;

		}
		
		$directory = dirname(getcwd(), 2);
		if(!file_exists($directory . '/images/profile_pictures/' . $_SESSION['DATA_TABLE'] .'_profiles')){
			mkdir($directory . '/images/profile_pictures/' . $_SESSION['DATA_TABLE'] .'_profiles');
		}
		$file_path = 'images/profile_pictures/' . $_SESSION['DATA_TABLE'] . '_profiles/' . md5($_SESSION['COMPANY_ID']) . '.png';
		if (imagepng($png_image, $directory . '/' . $file_path) == true) {
			$item_image_url = $file_path;
			$sql = "UPDATE $data_table SET PROFILE_PICTURE = :PROFILE_PICTURE WHERE ID = :ID";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":PROFILE_PICTURE", $item_image_url);
			$stmt->bindParam(":ID", $company_id);
			$stmt->execute();
			
			$_SESSION['PROFILE_PICTURE'] = $item_image_url;
			echo $item_image_url;
	    } else {
		    echo "Error";
	    }
	}
} new Company_Information();