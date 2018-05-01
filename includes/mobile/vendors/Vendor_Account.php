<?php 
	
class Vendor_Account{

	function __construct(){
		$action = filter_input(INPUT_POST, 'action');		
		$this->init($action);
	}
	
	private function init($action){
		switch($action){
			case "sign_in":
				if(isset($username) && isset($password)){
					$this->login($username, $password);
				}
				break;
			default:
				echo json_encode(array("Action" => "No action"));
				break;
		}
	}
	
	private function login($username, $password){
		include '../Connection/DBConnection.php';
		
		$username = filter_input(INPUT_POST, 'username');
		$password = filter_input(INPUT_POST, 'password');
		
		$sql = "SELECT ID, DATA_TABLE, COMPANY_ID FROM VENDOR_LOGIN WHERE USERNAME = :USERNAME AND PASSWORD = MD5(:PASSWORD);";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":USERNAME", $username);
		$stmt->bindParam(":PASSWORD", $password);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$stmt->execute();
		
		$num_rows = $stmt->rowcount();
		if($num_rows > 0){
			$results = $stmt->fetch();
		}else{
			$results = array("Result" => "No Results");
		}
				
		echo json_encode($results);		
	}
	
	
}

new Vendor_Account();