<?php
class Vendor{
	
	function __construct(){
		$action = filter_input(INPUT_POST, 'action');
		
		switch($action){
			case 'vendorSignIn':
				$this->vendorSignin();
				break;
			default: break;
		}
		
	}
	
	private function vendorSignIn(){
		include 'DbConnection.php';
		
		$username = filter_input(INPUT_POST, 'username');
		$password = filter_input(INPUT_POST, 'password');
		
		$sql = "SELECT user_id, vendor_id FROM vendor_users WHERE username = ? AND password = MD5(?);";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $username);
		$stmt->bindParam(2, $password);
		
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$stmt->execute();
		
		$results = $stmt->fetch();
		
		$success = $results->rowCount() > 0;
		
		if($success){
			$_SESSION['UF_VENDOR_ID'] = $results['vendor_id'];
			$_SESSION['UF_VENDOR_USER_ID'] = $results['user_id'];
		}
		
		echo json_encode($success);
	}
	
	private function createNewVendor(){
		include 'DbConnection.php';
		
		$vendor_name = filter_input(INPUT_POST, 'vendor_name');
		$vendor_address = filter_input(INPUT_POST, 'vendor_address');
		$vendor_secondary_address = filter_input(INPUT_POST, 'vendor_secondary_address');
		$vendor_city = filter_input(INPUT_POST, 'vendor_address');
		$vendor_state = filter_input(INPUT_POST, 'vendor_address');
		$vendor_postal_code = filter_input(INPUT_POST, 'vendor_address');
		$vendor_telephone = filter_input(INPUT_POST, 'vendor_telephone');
		
		
		
	}
	
	
}new Vendor();