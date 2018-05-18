<?php 
	// action=reset_password_request&email=cmeehan1991@gmail.com

class Users{
	function __construct(){
		$action = filter_input(INPUT_POST, 'action');
		$this->init($action);		
	}
	
	private function init($action){
		switch($action){
			case "set_new_password":
			$this->setNewPassword();
			break;
			case "reset_password_request":
			$this->resetPasswordRequest();
			break;
			case "verify_reset_code":
			$this->verifyResetCode();
			break;
			case "log_in":
			$this->signIn();
			break;
			case "new_user":
			$this->registerUser();
			break;
			case 'send_text':
			$this->sendResetCodeTextMessage();
			break;
			case "get_user":
			$this->getUser();
			break;
			case "set_user":
			$this->setUser();
			break;
			case "remove_user":
			$this->removeUser();
			break;
			case "update_password":
			$this->setNewPassword();
			default:break;
		}
	}
	
	/*
	* Get the user's information to return to the app
	*/
	private function getUser(){
		include('../connection/DBConnection.php');
		$user_id = filter_input(INPUT_POST, 'user_id');
		
		$sql = "SELECT FIRST_NAME, LAST_NAME, CITY, STATE, EMAIL FROM USERS WHERE ID = :ID";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":ID", $user_id);
		
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$results = $stmt->fetch();
		if($results){
			$results["SUCCESS"] = true;
			$results["RESPONSE"] = "";
		}else{
			$results["SUCCESS"] = false;			
			$results["RESPONSE"] = "User ID does not match records.";
		}
		
		echo json_encode($results);
	}
		
	/*
	* Update the user's information
	*/
	private function setUser(){
		include('../connection/DBConnection.php');
		$user_id = filter_input(INPUT_POST, 'user_id');
		$first_name = filter_input(INPUT_POST, 'first_name');
		$last_name = filter_input(INPUT_POST, 'last_name');
		$city = filter_input(INPUT_POST, 'city');
		$state = filter_input(INPUT_POST, 'state');
		$email = filter_input(INPUT_POST, 'email_address');
		
		$sql = "UPDATE USERS SET FIRST_NAME = :FIRST_NAME, LAST_NAME = :LAST_NAME, CITY = :CITY, STATE = :STATE, EMAIL = :EMAIL WHERE ID = :ID";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":ID", $user_id);
		$stmt->bindParam(":FIRST_NAME", $first_name);
		$stmt->bindParam(":LAST_NAME", $last_name);
		$stmt->bindParam(":CITY", $city);
		$stmt->bindParam(":STATE", $state);
		$stmt->bindParam(":EMAIL", $email);
		
		$results = $stmt->execute();
		
		$response = array();
		if($results){
			$response["SUCCESS"] = true;
			$response["RESPONSE"] = "";
		}else{
			$response["SUCCESS"] = false;			
			$response["RESPONSE"] = "Failed to update user records.";
		}
		
		echo json_encode($response);

	}
		
	/*
	* Remove the user completely
	* This does not remove user data such as saved items. 
	* This information is retained for data analysis purposes. 
	*/
	private function removeUser(){
		include('../connection/DBConnection.php');
		$user_id = filter_input(INPUT_POST, 'user_id');
		
		$sql = "DELETE FROM USERS WHERE ID = :ID";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":ID", $user_id);
		
		$user_removed = $stmt->execute();
		
		$response = array();
		if($user_removed){
			$response['STATUS'] = true;
			$response['RESPONSE'] = "Success";
		}else{
			$response['STATUS'] = false;
			$response['RESPONSE'] = "Failed to remove user.";
		}
		
		echo json_encode($response);
	}
	
	/*
	* Verify the reset code is valid for that user
	*/
	private function verifyResetCode(){
		include('../connection/DBConnection.php');
		
		$username = filter_input(INPUT_POST, 'username');
		$reset_code = filter_input(INPUT_POST, 'reset_code');
		
		$sql = "SELECT ID FROM USERS WHERE RESET_CODE = :RESET_CODE  AND USERNAME = :USERNAME";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":RESET_CODE", $reset_code);
		$stmt->bindParam(":USERNAME", $username);
		$exec = $stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$res = $stmt->fetch();
		
		echo json_encode(array("RESPONSE" => $exec, "ID" => $res['ID']));
	}
	
	/*
	* Generate a random number code. 
	*/
	private function generateCode($min, $max){
		$rand_arr = array();
		$i = 0;
		while($i < 4){
			array_push($rand_arr, rand(0, 9));
			$i += 1;
		}
		return implode('', $rand_arr);
	}

	/*
	* Clears the password reset code and sets it to null
	* This should only be called after the user succesfully resets their password.
	*/
	private function clearResetCode($id){
		include('../connection/DBConnection.php');
		$sql = "UPDATE USERS SET RESET_CODE = null WHERE ID = :ID";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":ID", $id);
		$stmt->execute();
	}
	
	/*
	* Set a new password based on the user input
	* then clear the password reset code. 
	*/
	private function setNewPassword(){
		include('../connection/DBConnection.php');
		$user_id = filter_input(INPUT_POST, 'user_id');
		$password = filter_input(INPUT_POST, 'password');
		
		$sql = "UPDATE USERS SET PASSWORD = md5(:PASSWORD) WHERE ID = :ID";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":PASSWORD", $password);
		$stmt->bindParam(":ID", $user_id);
		$success = $stmt->execute();
		
		if($success){
			$this->clearResetCode($user_id);
			echo json_encode(array("SUCCESS" => true));
		}else{
			echo json_encode(array("SUCCESS" => false));
		}
	}
	
	/*
	* Sets the password reset code.
	*/
	private function setPasswordResetCode(){
		include('../connection/DBConnection.php');
		
		$username = filter_input(INPUT_POST, 'email');
		$reset_code = $this->generateCode(0, 1000);
		
		$sql = "UPDATE USERS SET RESET_CODE = :RESET_CODE WHERE USERNAME = :USERNAME";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":RESET_CODE", $reset_code);
		$stmt->bindParam(":USERNAME", $username);
		$res = $stmt->execute();
		
		if($res){
			return $reset_code;
		}else{
			return null;
		}
		
	}
	
	private function sendResetCodeTextMessage(){
		echo mail("3362600061", "", "Your packaged has arrived!", "From: <donotreply@utterfare.com>\r\n");

	}
	private function getPhoneNumber($email){
		include('../connection/DBConnection.php');
		$sql = 'SELECT PHONE FORM USERS WHERE EMAIL = :EMAIL';
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":EMAIL");
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$results = $stmt->fetch();
		if($results){
			return $results['PHONE'];
		}else{
			return false;
		}
	}	
	/*
	* Send the user a password reset notificaiton email with the reset code. 
	*/
	private function sendResetCodeNotification($email, $reset_code){
		$to = $email;

		$subject = "Utterfare Password Reset";
		$headers = "From: donotreply@utterfare.com" . "\r\n";
		$headers .= "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1" . "\r\n";
		
		$message = "Password Reset Code: " . $reset_code;
		$message .= "<br/><br/>";
		$message .= "If you did not request a password reset please ignore this email or contact customer service at <a href='mailto:customer.service@utterfare.com?suject=User Account Support Request'>customer.service@utterfare.com</a>.";
		$message .= "<br/></br>";
		$message .= "Sincerely,";
		$message .= "<br/>";
		$message .= "The Utterfare Team";
		$message .= "<br/><br/>";
		$message .= "<b>Do NOT reply to this email.</b>";
		$message .= "For assistance please contact <a href='mailto:customer.service@utterfare.com?suject=User Account Support Request'>Customer Service</a>.";

				
		$send_mail = mail($to, $subject, $message, $headers);
		
		echo json_encode(array('SUCCESS' => $send_mail, 'RESPONSE' => 'NONE'));

	}
	
	/*
	* Handle a password reset request
	*/
	private function resetPasswordRequest(){
		$username = filter_input(INPUT_POST, 'email');
		if($this->checkUserExists($username)){
			$password_reset_code = $this->setPasswordResetCode();
			if($password_reset_code != null){
				$this->sendResetCodeNotification($username, $password_reset_code);
			}else{
				echo json_encode(array('SUCCESS' => FALSE, 'RESPONSE' => 'Failed to generate reset code'));
			}
		}else{
			echo json_encode(array('SUCCESS' => FALSE, 'RESPONSE' => 'Username/Email address does not exist.'));
		}
	}
	
	/*
	* This signs the user in and returns an ID
	*/
	private function signIn(){
		include('../connection/DBConnection.php');
		
		$username = filter_input(INPUT_POST, 'username');
		$password = filter_input(INPUT_POST, 'password');
		
		$sql = "SELECT ID FROM USERS WHERE USERNAME = :USERNAME AND PASSWORD = MD5(:PASSWORD)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":USERNAME", $username);
		$stmt->bindParam(":PASSWORD", $password);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$results = $stmt->fetch();
		
		$response = null;
		if($stmt->rowCount() > 0){
			$response = array(
				'ID' 		=> $results['ID'],
				'RESPONSE' 	=> 'SUCCESS'
			);
		}else{
			$response = array(
				'ID' 		=> 0,
				'RESPONSE' 	=> 'FAIL'
			);
		}
		echo json_encode($response);
	}
	
	/*
	* Check if the user exists. 
	* This can be done while signing in, registering, or resetting the user information
	*/
	private function checkUserExists($username){
		include('../connection/DBConnection.php');
		$sql = "SELECT ID FROM USERS WHERE USERNAME = :USERNAME";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":USERNAME", $username);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$num_rows = $stmt->rowCount();
		if($num_rows > 0){
			return true;
		}else{
			return false;
		}
	}
	
	/*
	* Reset the user login if they forgot their information
	*/
	private function resetUser(){
		
	}
	
	/*
	* Notify the new user that they signed up successfully and welcome them. 
	*/
	private function notifyNewUser($email){
		$to = $email;
		$subject = "Utterfare New User Sign Up";
		$headers = "From: donotreply@utterfare.com" . "\r\n";
		$headers .= "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1" . "\r\n";
		
		$message = "Thank you for signing up to use Utterfare. Your account has been successfully created. You can use your account to save and share menu items.";
		$message .= "<br/><br/>";
		$message .= "Please take a moment to <a href='https://www.utterfare.com/survey'>take this survey</a> and let us know how you found us.";
		$message .= "Sincerely,";
		$message .= "<br/>";
		$message .= "The Utterfare Team";
		$message .= "<br/><br/>";
		$message .= "<b>Do NOT reply to this email.</b>";
		$message .= "For assistance please contact <a href='mailto:customer.service@utterfare.com?suject=User Account Support Request'>Customer Service</a>.";
		
		mail($to, $subject, $message, $headers);
	}
	
	/* 
	* Register the user
	*/
	private function registerUser(){
		include('../connection/DBConnection.php');
		$username =  filter_input(INPUT_POST, 'email');
		$password = filter_input(INPUT_POST, 'password');
		$first_name = filter_input(INPUT_POST, 'first_name');
		$last_name = filter_input(INPUT_POST, 'last_name');
		$city =  filter_input(INPUT_POST, 'city');
		$state  = filter_input(INPUT_POST, 'state');
		$email = filter_input(INPUT_POST, 'email');
		$phone = filter_input(INPUT_POST, 'phone');
		$date_registered = date("Y-m-d H:i:s");
		
		if(!$this->checkUserExists($username)){
			$sql = "INSERT INTO USERS(USERNAME, PASSWORD, FIRST_NAME, LAST_NAME, CITY, STATE, EMAIL, PHONE, DATE_REGISTERED) VALUES(:USERNAME, MD5(:PASSWORD), :FIRST_NAME, :LAST_NAME, :CITY, :STATE, :EMAIL, :PHONE, :DATE_REGISTERED)";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":USERNAME", $username);
			$stmt->bindParam(":PASSWORD", $password);
			$stmt->bindParam(":FIRST_NAME", $first_name);
			$stmt->bindParam(":LAST_NAME", $last_name);
			$stmt->bindParam(":CITY", $city);
			$stmt->bindParam(":STATE", $state);
			$stmt->bindParam(":EMAIL", $email);
			$stmt->bindParam(":PHONE", $phone);
			$stmt->bindParam(":DATE_REGISTERED", $date_registered);
			$stmt->execute();
			
			$num_rows = $stmt->rowCount();
			
			if($num_rows > 0){
				$response = array(
					"SUCCESS" 	=> true,
					"ID"		=> $conn->lastInsertId(),
					'RESPONSE'	=> "SUCCESS"
				);
				$this->notifyNewUser($email);
			}else{
				$response = array(
					"SUCCESS" 	=> false,
					"ID" 		=> "",
					"RESPONSE"	=> "FAILURE"
				);
			}
			echo json_encode($response);
		}else{
			$response = array(
				'SUCCESS' 	=> false, 
				"ID"		=> "",
				"RESPONSE" 	=> "Username already exists."
			);
			echo json_encode($response);
		}
	}
}
new Users();
	