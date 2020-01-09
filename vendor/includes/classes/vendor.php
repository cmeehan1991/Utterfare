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
				default: break;
			}
		}
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
			return isset($_SERVER['HTTPS']) ? 'https' . ";//" . $_SERVER['HTTP_HOST'] . '/' . $path . '/' . $file_path : 'http' . '://' . $_SERVER['HTTP_HOST'] . '/' . $path . '/' . $file_path;
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
			return isset($_SERVER['HTTPS']) ? 'https://' . $_SERVER['HTTP_HOST'] . '/' . $path . '/' . $file_path: 'http' . '://' . $_SERVER['HTTP_HOST'] . '/' . $path . '/' . $file_path;
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