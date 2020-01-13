<?php 
class Vendor{
	
	public function __construct(){
		session_start(); 
		
		$action = filter_input(INPUT_POST, 'action');

		if($action){
			switch($action){
				case 'vendor_sign_in':
					$this->vendor_sign_in();
					break;
				case 'sign_user_out':
					$this->sign_out();
					break;
				case 'get_menu_items':
					$this->get_menu_items();
					break;
				case 'get_item':
					$this->get_item();
					break;
				case 'update_item': 
					$this->update_item();
					break;
				case 'add_new_item':
					$this->add_new_item();
					break;
				case 'delete_item':
					$this->delete_item();
					break;
				case 'get_company_profile':
					$this->get_company_profile();
					break;
				case 'save_company_profile':
					$this->save_company_profile();
					break;
				case 'get_vendor_searches_by_day':
					$this->get_vendor_searches_by_day();	
					break;
				case 'get_vendor_results_per_search':
					$this->get_vendor_results_per_search();
					break;
				case 'get_top_search_terms':
					$this->getTopSearchTerms();
					break;
				case 'claim_listing':
					$this->claimListing();
					break;
				case 'registerNewUser':
					$this->register_new_user();
				case 'check_email_address':
					$this->check_vendor_email();
					break;
				case 'register_new_vendor':
					$this->register_new_vendor();
					break;				
				default: break;
			}
		}
	}
	
	private function register_new_user($email, $password, $first_name, $last_name, $vendor_id){
		include ('../functions/DBConnection.php');
		
		$user_id = $this->check_vendor_email($email, true);
		
		if($user_id){
			return $user_id;
		}
		
		$sql = "INSERT INTO vendor_users(username, password, email_address, first_name, last_name, vendor_id) VALUES(?, ?, ?, ?, ?, ?);";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $email);
		$stmt->bindParam(2, $password);
		$stmt->bindParam(3, $email);
		$stmt->bindParam(4, $first_name);
		$stmt->bindParam(5, $last_name);
		$stmt->bindParam(6, $vendor_id);
	}
	
	private function register_new_vendor(){
		include('../functions/DBConnection.php');
		
		// Get the arguments
		$vendor_name = filter_input(INPUT_POST, 'venueName');
		$website = filter_input(INPUT_POST, 'website');
		$telephone = filter_input(INPUT_POST, 'telephone');
		$first_name = filter_input(INPUT_POST, 'firstName');
		$last_name = filter_input(INPUT_POST, 'lastName');
		$email_address = filter_input(INPUT_POST, 'email');
		$password = filter_input(INPUT_POST, 'password');
		$primary_address = filter_input(INPUT_POST, 'primaryAddress');
		$secondary_address = filter_input(INPUT_POST, 'secondaryAddress');
		$country = filter_input(INPUT_POST, 'country');
		$city = filter_input(INPUT_POST, 'city');
		$state = filter_input(INPUT_POST, 'state');
		$postal_code = filter_input(INPUT_POST, 'postalCode');
		
		$location = $primary_address;
		
		if($secondary_address){
			$location .= ',' . $secondary_address;
		}
		
		$location .= ',' . $city;
		
		if($state){
			$location .= ',' . $state;
		}
		
		$location .= ',' . $postal_code;
		
		$location .= ',' . $country;
		
		$latlng = $this->getLatLng($location);
		
		
		// SQL Statement to insert the new vendor
		$sql = "INSERT INTO vendors(vendor_name, latitude, longitude, primary_address, secondary_address, city, state, postal_code, country, telephone, email_address, website_url) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $vendor_name);
		$stmt->bindParam(2, $latlng['lat']);	
		$stmt->bindParam(3, $latlng['lng']);	
		$stmt->bindParam(4, $primary_address);	
		$stmt->bindParam(5, $secondary_address);	
		$stmt->bindParam(6, $city);	
		$stmt->bindParam(7, $state);	
		$stmt->bindParam(8, $postal_code);	
		$stmt->bindParam(9, $county);	
		$stmt->bindParam(10, $telephone);	
		$stmt->bindParam(11, $email_address);	
		$stmt->bindParam(12, $website);			

		$exec = $stmt->execute();
		
		if($exec){
			$vendor_id = $conn->lastInsertId();
			$user_id = $this->register_new_user($email, $password, $first_name, $last_name, $vendor_id);
			
			echo json_encode(array(
				'success'	=> true, 
				'vendor_id'	=> $vendor_id, 
				'user_id'	=> $user_id,
			));
		}else{
			echo json_encode(array(
				'success'	=> false,
			));
		}
		
	}
	
	/**
	 * Convert a physical address to an array of latitude & longitude coordinates
	 * 
	 * @params $location - string value of the physical address
	 * 
	 * @returns 
	 */	
	private function getLatLng($location){
		
				// Form the search location
		$location = urlencode($location);
		
		// Get the JSON contents and decode for reading		
		$json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$location&key=AIzaSyBNOJbx_2Q5h8f0ONZ4Abf5ULE0w4B-VTc");
		
		$obj = json_decode($json, true);
		
		// Get the latitude and longitude
        $lat = $obj['results'][0]['geometry']['location']['lat'];
        $lng = $obj['results'][0]['geometry']['location']['lng'];
        
        return array('lat'=>$lat, 'lng'=>$lng);
		
	}
	
	
	/**
	 * Check the vendor email to see if it already exists
	 */ 
	private function check_vendor_email($email = null, $internal = false){
		include ('../functions/DBConnection.php');
		if($email == null){
			$email = filter_input(INPUT_POST, 'emailAddress');
		}
		
		$sql = "SELECT vendor_id FROM vendor_users WHERE email_address = ?";

		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $email);
				
		$exec = $stmt->execute();
		
		if($internal == true){
			$result = $stmt->fetch();
			
			return $result['vendor_id'];
		}	
			
		echo $stmt->rowCount();

	}
	
	private function notifyNewVendor($email, $first_name, $last_name, $venue_name){
		$msg = "<html>";
		
		$msg .= "<p>Dear $first_name $last_name,";
		$msg .= "<br/>";
		$msg .= "<p>Thank you for registering the new venue, $venue_name, with <a href='https://www.utterfare.com'>Utterfare.com</a>. You can now <a href='https://vendor.utterfare.com'>log in</a> with your username/email and password that you registered with to maintain your listing. Please let us know if there is anything we can do to help you!</p>";
		$msg .= "<br/><br/>";
		$msg .= "Sincerely,";
		$msg .= "<br/>";
		$msg .= "Connor Meehan (Co-Owner) & the Utterfare Team";
		
				
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		
		// More headers
		$headers .= 'From: <listings@utterfare.com>' . "\r\n";
		
		
		mail('listings@utterfare.com', 'New Vendor Registration', $msg, $headers);
		
	}
	
	
	private function claimListing(){
		
		$venue = filter_input(INPUT_POST, 'venueName');
		$email = filter_input(INPUT_POST, 'emailAddress');
		$telephone = filter_input(INPUT_POST, 'telephoneNumber');
		$first_name = filter_input(INPUT_POST, 'firstName');
		$last_name = filter_input(INPUT_POST, 'lastName');
		
		
		$msg = "<html>"; 
		$msg .= "<head><title>Listing Claim Email</title></head>";
		$msg .= "<body>";
		$msg .= "<table>";
		$msg .= "<tr>";
		$msg .= "<td><b>Venue:</b></td><td>$venue</td>";
		$msg .= "</tr>";
		$msg .= "<tr>";
		$msg .= "<td><b>First Name:</b></td><td>$first_name</td>";
		$msg .= "</tr>";
		$msg .= "<tr>";
		$msg .= "<td><b>Last Name:</b></td><td>$last_name</td>";
		$msg .= "</tr>";
		$msg .= "<tr>";
		$msg .= "<td><b>Telephone:</b></td><td>$telephone</td>";
		$msg .= "</tr>";
		$msg .= "<tr>";
		$msg .= "<td><b>Email Address: </b></td><td>$email</td>";
		$msg .= "</tr>";
		$msg .= "</table>";
		$msg .= "</body>";
		$msg .= "</html>";
		
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		
		// More headers
		$headers .= 'From: <listings@utterfare.com>' . "\r\n";
		$success = mail('listings@utterfare.com', 'Listing Request Form', $msg, $headers);
		
		if($success == true){
			$this->notifyRequestee($email);
		}
		
		echo $success;
	}
	
	private function notifyRequestee($email){
		$msg = "Thank you for contacting us. Your request has been received and a representative will be in touch with you.";
		$headers = "From: <listings@utterfare.com>";
		
		mail($email, 'Utterfare Form Request', $msg, $headers);
	}
	
	private function getTopSearchTerms(){
		include('../functions/DBConnection.php');
		
		$sql = "SELECT count(terms) as num_times, terms FROM utterfare.searchdata_queries WHERE terms IS NOT NULL GROUP BY terms ORDER BY num_times";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->execute();
		
		$results = $stmt->fetchall();
		
		echo json_encode($results);
	}
	
	private function get_vendor_results_per_search(){
		include ('../functions/DBConnection.php');
		
		$vendor_id = $_SESSION['UF_VENDOR_ID'];
		
		$sql = "SELECT COUNT(search_id) as 'num_times', DATE_FORMAT(search_date, '%m-%d') AS date_of_search FROM searchdata_results WHERE vendor_id = ? AND search_date >= DATE(NOW()) + INTERVAL -7 DAY GROUP BY date_of_search";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $vendor_id);
		
		$stmt->execute();
		
		$results = $stmt->fetchall();
		
		echo json_encode($results);
	}
	
	private function get_vendor_searches_by_day(){
		include ('../functions/DBConnection.php');
		
		
		$vendor_id = $_SESSION['UF_VENDOR_ID'];
				
		$sql = "SELECT COUNT(DISTINCT search_id) as 'num_times', DATE_FORMAT(search_date, '%m-%d') AS date_of_search FROM searchdata_results WHERE vendor_id = ? AND search_date >= DATE(NOW()) + INTERVAL -7 DAY GROUP BY date_of_search";
		
	
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $vendor_id);
		
		$stmt->execute();
		$results = $stmt->fetchall();			
				
				
		echo json_encode($results);
	}
	
	
	/* 
	 * Create a new file for the company profile picture
	 */ 
	private function save_profile_picture($profile_picture, $company_id){
		
		// Get the image contents
		$image_file = file_get_contents($profile_picture['tmp_name']);
		
		// Get the image name
		$image_name = $profile_picture['name'];
		
		// Get the image type: .png, .jpg, etc.
		$extension = pathinfo($image_name, PATHINFO_EXTENSION);
		
		$png_image = null;
		
		// Convert the image to a .png
		switch ($extension) {
	        case "jpg":
	            $png_image = imagecreatefromjpeg($profile_picture['tmp_name']);
	            break;
	        case "jpeg":
	            $png_image = imagecreatefromjpeg($profile_picture['tmp_name']);
	            break;
	        case "gif":
	            $png_image = imagecreatefromgif($profile_picture['tmp_name']);
	            break;
	        case "png":
	            $png_image = imagecreatefrompng($profile_picture['tmp_name']);
	            break;
	        default:
	            break;
	    }
		
		$directory = dirname(getcwd(), 5);
		$path = 'uploads/images/' . date('Y') . '/' . date('m');
		
		$file_directory = $directory . '/' . $path;
		
		if(!file_exists($file_directory)){
			mkdir($file_directory, 0777, true);
		}
		
		$file_path = md5($vendor_id) . ".png";
		
		$save_image = imagepng($png_image, $file_directory . '/' . $file_path);
		
		if($save_image){		
			return isset($_SERVER['HTTPS']) ? 'https' . "://" . 'www.utterfare.com' . '/' . $path . '/' . $file_path : 'http' . '://' . 'www.utterfare.com' . '/' . $path . '/' . $file_path;
		}else{
			return false;
		}
		

	}
	
	/*
	 * Save the company data 
	 */
	private function save_company_profile(){
		include('../functions/DBConnection.php');

		$company_name = filter_input(INPUT_POST, 'company-name');
		$primary_address = filter_input(INPUT_POST, 'street-address');
		$secondary_address = filter_input(INPUT_POST, 'secondary-address');
		$city = filter_input(INPUT_POST, 'city');
		$state = filter_input(INPUT_POST, 'state');
		$postal_code = filter_input(INPUT_POST, 'postal-code');
		$telephone = filter_input(INPUT_POST, 'telephone');
		$email_address = filter_input(INPUT_POST, 'email');	
		$website = filter_input(INPUT_POST, 'website');
		$profile_picture = $_FILES['profile-picture'];
		$company_id = $_SESSION['UF_VENDOR_ID'];
		
		
		$sql = "UPDATE vendors SET vendor_name = ?, primary_address = ?, secondary_address = ?, city = ?, state = ?, postal_code = ?, telephone = ?, email_address = ?, website_url = ?";
				
		if($profile_picture || $profile_picture['size'] > 0){
			$profile_picture = $this->save_profile_picture($profile_picture, $company_id);
			$sql .= ", profile_picture = ? WHERE vendor_id = ?";
		}else{
			$sql .= " WHERE vendor_id = ?";
		}
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $company_name);
		$stmt->bindParam(2, $primary_address);
		$stmt->bindParam(3, $secondary_address);
		$stmt->bindParam(4, $city);
		$stmt->bindParam(5, $state);
		$stmt->bindParam(6, $postal_code);
		$stmt->bindParam(7, $telephone);
		$stmt->bindParam(8, $email_address);
		$stmt->bindParam(9, $website);
		
		if($profile_picture){
			
			$stmt->bindParam(10, $profile_picture);
			$stmt->bindParam(11, $company_id);
		}else{
		
			$stmt->bindParam(10, $company_id);
		
		}
		
		$update = $stmt->execute();
		echo json_encode(array(
			'updated'	=> $update, 
			'profile_picture'	=> $profile_picture, 
		));			
	}
	
	
	private function get_company_profile(){
		include('../functions/DBConnection.php');
		
		$company_id = $_SESSION['UF_VENDOR_ID'];

		$sql = "SELECT vendor_name, primary_address, secondary_address, city, state, postal_code, telephone, email_address, website_url, profile_picture FROM vendors WHERE vendor_id = ?";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $company_id);
		
		$stmt->execute();
		
		$result = $stmt->fetch();

		echo json_encode($result);
	}
	
	private function delete_item(){
		include('../functions/DBConnection.php');
		
		$item_id = filter_input(INPUT_POST, 'item_id');
		
		if($item_id){
			$sql = "DELETE FROM menu_items WHERE item_id=?";
			
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(1, $item_id);
			
			$success = $stmt->execute();
			
			echo json_encode(array('success' => $success, 'message' => 'delete item'));
			
		}else{
			echo json_encode(array('success' => false, 'message' => 'invalid ID'));
		}
	}
	
	private function vendor_sign_in(){
		include('../functions/DBConnection.php');
		
		$username = filter_input(INPUT_POST, 'username');
		$password = filter_input(INPUT_POST, 'password');
		
		$sql = "SELECT user_id, vendors.vendor_id, vendor_name FROM vendor_users INNER JOIN vendors ON vendors.vendor_id = vendor_users.vendor_id WHERE username = ? and password = MD5(?)";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $username);
		$stmt->bindParam(2, $password);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$result = $stmt->fetch();
		
		if($result['user_id']){
			
			$result['success'] = true;
			$_SESSION['user_id'] = $result['user_id'];
			$_SESSION['UF_VENDOR_USER_SIGNED_IN'] = true;
			$_SESSION['UF_VENDOR_USER_ID'] = $result['user_id'];
			$_SESSION['UF_VENDOR_ID'] = $result['vendor_id'];
			
			$result['session']	 = $_SESSION;
			
		}else{
			$result['success'] = false;
		}
		
		echo json_encode($result);
	}
	
	private function get_menu_items(){
		include('../functions/DBConnection.php');
		
		$limit = filter_input(INPUT_POST, 'limit');
		$offset = filter_input(INPUT_POST, 'offset');
		$vendor_id = $_SESSION['UF_VENDOR_ID'];
				
		$sql = "SELECT item_id, primary_image, item_name, item_description FROM menu_items WHERE vendor_id = $vendor_id ORDER BY item_name LIMIT $limit OFFSET $offset;";
										
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$results = $stmt->fetchall();
		
		$i = 0;
		
		foreach($results as $result){
			$results[$i]['total_items'] = $this->get_total_items($vendor_id)['total_items'];
			$i++;
		}
		
		$results = $results;
		
		echo json_encode($results);
	}
	
	private function get_total_items($vendor_id){
		include('../functions/DBConnection.php');
		
		$sql = "SELECT COUNT(*) AS total_items FROM menu_items WHERE vendor_id = $vendor_id";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stmt->fetch();
		
		return $result;
	}
	
	private function get_item(){
		include('../functions/DBConnection.php');
		$item_id = filter_input(INPUT_POST, 'item_id');
		
		$sql = "SELECT item_name, item_description, primary_image FROM menu_items WHERE item_id = ?";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $item_id);
		
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$results = $stmt->fetch();
		
		echo json_encode($results);
	}
	
	private function sign_out(){
		echo 'Sign out';
		session_destroy();
	}
	
	
	private function save_image($primary_image, $item_id, $vendor_id){

		$image_file = file_get_contents($primary_image['tmp_name']);

		$image_name = $primary_image['name'];
		
		$extension = pathinfo($image_name, PATHINFO_EXTENSION);
		
		$png_image = null;
		
		switch ($extension) {
	        case "jpg":
	            $png_image = imagecreatefromjpeg($primary_image['tmp_name']);
	            break;
	        case "jpeg":
	            $png_image = imagecreatefromjpeg($primary_image['tmp_name']);
	            break;
	        case "gif":
	            $png_image = imagecreatefromgif($primary_image['tmp_name']);
	            break;
	        case "png":
	            $png_image = imagecreatefrompng($primary_image['tmp_name']);
	            break;
	        default:
	            break;
	    }
		
		$directory = dirname(getcwd(), 5);
		$path = 'uploads/images/' . date('Y') . '/' . date('m');
		
		$file_directory = $directory . '/' . $path;
		
		if(!file_exists($file_directory)){
			mkdir($file_directory, 0777, true);
		}
		
		$file_path = md5($item_id . $vendor_id) . ".png";
		
		$save_image = imagepng($png_image, $file_directory . '/' . $file_path);
		
		if($save_image){		
			return isset($_SERVER['HTTPS']) ? 'https://' . 'www.utterfare.com' . '/' . $path . '/' . $file_path: 'http' . '://' . 'www.utterfare.com' . '/' . $path . '/' . $file_path;
		}else{
			return null;
		}
		
	}
	
	
	private function update_item(){
		include ('../functions/DBConnection.php');
		$item_id = filter_input(INPUT_POST, 'item-id');
		$item_name = filter_input(INPUT_POST, 'item-name');
		$item_description = filter_input(INPUT_POST, 'item-description');
		$primary_image = isset($_FILES['item-image']) ? $_FILES['item-image'] : false;
		
		$sql = 'UPDATE menu_items SET item_name = :item_name, item_description = :item_description';
				
		$new_image = null;
		if($primary_image['size'] > 0){
			$new_image = $this->save_image($primary_image, $item_id, $_SESSION['UF_VENDOR_ID']);
			
			$sql .= ', primary_image = :primary_image';
		} 
		
		$sql .= ' WHERE item_id = :item_id';
				
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":item_name", $item_name);
		$stmt->bindParam(":item_description", $item_description);
		
		if($new_image){
			$stmt->bindParam(":primary_image", $new_image);
		}
		
		$stmt->bindParam(":item_id", $item_id);
		
		$result = $stmt->execute();
		
		echo json_encode(array('success' => $result));
	}
	
	private function add_new_item(){
		include('../functions/DBConnection.php');

		$item_name = filter_input(INPUT_POST, 'item-name');
		$item_description = filter_input(INPUT_POST, 'item-description');
		$primary_image = isset($_FILES['item-image']) ? $_FILES['item-image'] : false;
		
		$sql = "INSERT INTO menu_items SET item_name = :item_name, item_description = :item_description, vendor_id = :vendor_id";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":item_name", $item_name);
		$stmt->bindParam(":item_description", $item_description);
		$stmt->bindParam(":vendor_id", $_SESSION['UF_VENDOR_ID']);
		
		$exec = $stmt->execute();
		$last_insert_id = null;
		if($exec){

			$last_insert_id = $conn->lastInsertId();

			if($primary_image['size'] > 0){
				$new_image = $this->save_image($primary_image, $last_insert_id, $_SESSION['UF_VENDOR_ID']);
				$last_insert_id = $this->update_item_image($last_insert_id, $new_image);
			}	
		}
		
		echo json_encode(array('item_id' => $last_insert_id));
	}
	
	private function update_item_image($item_id, $item_image){
		include('../functions/DBConnection.php');
		
		$sql = "UPDATE menu_items SET primary_image = ? WHERE item_id = ?;";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $item_image);
		$stmt->bindParam(2, $item_id);
		
		$success = $stmt->execute();
				
		if($success){
			return $item_id;
		}else{
			return false;
		}
	}
}
new Vendor();