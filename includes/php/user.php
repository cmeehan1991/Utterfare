<?php 
session_start();
	
class User{
	function __construct(){
		$action = filter_input(INPUT_POST, 'action');

		switch($action){
			case 'sign_in': 
				$this->signIn();
				break;
			case 'new_user':
				$this->insertNewUser();
				break;
			case 'update_user':
				$this->updateUserInformation();
				break;
			case 'signout': 
				$this->signUserOut();
				break;
		default: break;
		}
	}
	
	private function signUserOut(){
		session_destroy();
	}
	
	/*
	* Sign the user in
	* @return string user_id
	*/
	private function signIn(){
		include 'DbConnection.php';
		
		$username = filter_input(INPUT_POST, 'username');
		$password = filter_input(INPUT_POST, 'password');
				
		$sql = "SELECT DISTINCT user_id FROM all_users WHERE (username = ? or email = ?) AND password = MD5(?)";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $username);
		$stmt->bindParam(2, $username);
		$stmt->bindParam(3, $password);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$result = $stmt->fetch();
		
		if($result['user_id']){
			$_SESSION['USER_ID'] = $result['user_id'];
			$_SESSION['IS_SIGNED_IN'] = true;
			
			echo json_encode(array(
				'user_id' 	=> $result['user_id'],
				'success'	=> true,
			) );
		}else{
			echo json_encode(array(
			'success'	=>false,
			'reason'	=> 'Invalid username/password combination'
			) );
		}
		
	}
	
	/**
	* Validates whether the username, email, and phone number already exist
	* @return boolean true if the value exists. 
	*/
	private function usernameExists($username, $email = null, $user_id = null){
		include 'DbConnection.php';
		$sql = "SELECT COUNT(user_id) as 'COUNT' FROM all_users WHERE ";
		
		$args = array();
		
		if($username){
			$args[] = 'username = "' . $username . '"';	
		}
		
		if($email){
			$args[] = 'email= "' . $email . '"';
		}
		
		if(count($args) > 1){
			$args = implode(' OR ', $args);
		}else{
			$args = $args[0];
		}
		
		$sql .= $args;
		
		if($user_id){
			$args = ' AND user_id != ' . $user_id;
			$sql .= $args;
		}
		
				
		$stmt = $conn->prepare($sql);	
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$result = $stmt->fetch();
		
		return $result;
	}
	
	/**
	* Insert a new user
	* @return user_id, error
	*/
	private function insertNewUser(){
		include 'DbConnection.php';
		
		$username = filter_input(INPUT_POST, 'username');
		$password = filter_input(INPUT_POST, 'password');
		$first_name = filter_input(INPUT_POST, 'first_name');
		$last_name = filter_input(INPUT_POST, 'last_name');
		$telephone_number = filter_input(INPUT_POST, 'telephone_number');
		$user_since = date('Y-m-d H:i:s');
						
		if( $this->usernameExists($username, $username)['COUNT'] > 0){
			echo json_encode(array('success' => false, 'reason' => "Username/email is taken"));
			die();
		}
		
		$sql = "INSERT INTO all_users(username, password, email, first_name, last_name, telephone_number, user_since) VALUES(?, MD5(?), ?, ?, ?, ?, ?);";

		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $username);
		$stmt->bindParam(2, $password);
		$stmt->bindParam(3, $username);
		$stmt->bindParam(4, $first_name);
		$stmt->bindParam(5, $last_name);
		$stmt->bindParam(6, $telephone_number);
		$stmt->bindParam(7, $user_since);
		
		$exec = $stmt->execute();
		
		echo json_encode(array('success' => $exec));

	}
	
	/**
	* Update the user's information
	*/
	private function updateUserInformation(){
		include 'DbConnection.php';
		
		$user_id = filter_input(INPUT_POST, 'user_id');
		$username = filter_input(INPUT_POST, 'username');
		$email = filter_input(INPUT_POST, 'email');
		$password = filter_input(INPUT_POST, 'password');
		$first_name = filter_input(INPUT_POST, 'first_name');
		$last_name = filter_input(INPUT_POST, 'last_name');
		$telephone_number = filter_input(INPUT_POST, 'telephone_number');
		$profile_picture = filter_input(INPUT_FILES, 'profile_picture');

		
		if(usernameExists($username, $email, $user_id)){
			return json_encode(array('success' => false, 'reason' => "Username/email is taken"));
		}
		
		$sql = "UPDATE all_users SET";
		
		$args = array();
		if($username){
			$args[] = "username = '$username'";
		}
		
		if($email){
			$args[] = "email = '$email'";
		}
		
		if($password){
			$args[] = "password = md5($password)";
		}
		
		if($first_name){
			$args[] = "irst_name = '$first_name'";
		}
		
		if($last_name){
			$args[] = "last_name = '$last_name'";
		}
		
		if($telephone_number){
			$args[] = "telephone_number = '$telephone_number'";
		}
		
		if(count($args) > 1){
			$sql .= $args[0];
		}else{
			$sql .= implode(', ', $args);
		}
		
		$stmt = $conn->prepare($sql);
		
		echo json_encode(array('success' => $stmt->execute()));
	}
	
	
}new User();