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
			$this->setNewpassword();
			break;
			case "reset_password_request":
			$this->resetpasswordRequest();
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
			$this->setNewpassword();
			break;
			case "fb_log_in":
			$this->login_fb_user();
			break;
			default:break;
		}
	}
	
	public function login_fb_user(){
		include('DbConnection.php');
		$email = filter_input(INPUT_POST, 'email');
		$fb_id = filter_input(INPUT_POST, 'fb_id');
		
		$sql = "SELECT user_id FROM all_users WHERE email = ? AND fb_id = ?";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $email);
		$stmt->bindParam(2, $fb_id);
		$stmt->execute();
		
		$results = $stmt->fetch();
		
		if($results){
			$results["SUCCESS"] = true;
			$results["RESPONSE"] = "";
			$results["user_id"] = $results["user_id"];
		}else{
			$results = $this->insertFbUser();
		}
		
		echo json_encode($results);
	}
	
	private function insertFbUser(){
		include('DbConnection.php');
		$email = filter_input(INPUT_POST, 'email');
		$fb_id = filter_input(INPUT_POST, 'fb_id');
		$fullname = filter_input(INPUT_POST, 'fullname');
		
		$first_name = explode(' ', $fullname)[0];
		$last_name = explode(' ', $fullname)[1];
		
		$sql = 'INSERT INTO all_users (email, fb_id, first_name, last_name) VALUES(?, ?, ?, ?) ON DUPLICATE KEY UPDATE fb_id = ?';
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $email);
		$stmt->bindParam(2, $fb_id);
		$stmt->bindParam(3, $first_name);
		$stmt->bindParam(4, $last_name);
		$stmt->bindParam(5, $fb_id);
		
		$stmt->execute();
		
		$user_id = $conn->lastInsertId();
		if($user_id){
			$response = array(
				"SUCCESS" 	=> true,
				"user_id"		=> $user_id,
				'RESPONSE'	=> "SUCCESS"
			);
		}else{
			$response = array(
				'SUCCESS'	=> false, 
				'user_id'	=> '',
				'RESPONSE'	=> 'FAIL'
			);
		}
		
		return $response;
		
	}
	
	/*
	* Get the user's information to return to the app
	*/
	private function getUser(){
		include('DbConnection.php');
		$user_id = filter_input(INPUT_POST, 'user_id');
		
		$sql = "SELECT first_name, last_name, primary_address, secondary_address, city, state, postal_code, gender, email, telephone_number FROM all_users WHERE user_id = :user_id";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":user_id", $user_id);
		
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$results = $stmt->fetch();
		if($results){
			$results["SUCCESS"] = true;
			$results["RESPONSE"] = "";
		}else{
			$results["SUCCESS"] = false;			
			$results["RESPONSE"] = "User user_id does not match records.";
		}
		
		echo json_encode($results);
	}
		
	/*
	* Update the user's information
	*/
	private function setUser(){
		include('DbConnection.php');
		$user_id = filter_input(INPUT_POST, 'user_id');
		$first_name = filter_input(INPUT_POST, 'first_name');
		$last_name = filter_input(INPUT_POST, 'last_name');
		$primary_address = filter_input(INPUT_POST, 'primary_address');
		$secondary_address = filter_input(INPUT_POST, '$secondary_address');
		$city = filter_input(INPUT_POST, 'city');
		$state = filter_input(INPUT_POST, 'state');
		$postal_code = filter_input(INPUT_POST, 'postal_code');
		$email = filter_input(INPUT_POST, 'email_address');
		$telephone_number = filter_input(INPUT_POST, 'telephone_number');
		$gender = filter_input(INPUT_POST, 'gender');
		$birthday = filter_input(INPUT_POST, 'birthday');
		
		
		$sql = "UPDATE all_users SET first_name = :first_name, last_name = :last_name, primary_address = :primary_address, secondary_address = :secondary_address, city = :city, state = :state, postal_code = :postal_code, email = :email, telephone_number = :telephone_number, birthday = :birthday, gender = :gender WHERE user_id = :user_id";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":user_id", $user_id);
		$stmt->bindParam(":first_name", $first_name);
		$stmt->bindParam(":last_name", $last_name);
		$stmt->bindParam(":primary_address", $primary_address);
		$stmt->bindParam(":secondary_address", $secondar_address);
		$stmt->bindParam(":city", $city);
		$stmt->bindParam(":state", $state);
		$stmt->bindParam(":postal_code", $postal_code);
		$stmt->bindParam(":email", $email);
		$stmt->bindParam(":telephone_number", $telephone_number);
		$stmt->bindParam(":birthday", $birthday);
		$stmt->bindParam(":gender", $gender);
		
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
		include('DbConnection.php');
		$user_id = filter_input(INPUT_POST, 'user_id');
		
		$sql = "DELETE FROM USERS WHERE user_id = :user_id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":user_id", $user_id);
		
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
	* Verify the reset code is valuser_id for that user
	*/
	private function verifyResetCode(){
		include('DbConnection.php');
		
		$username = filter_input(INPUT_POST, 'username');
		$reset_code = filter_input(INPUT_POST, 'reset_code');
		
		$sql = "SELECT user_id FROM USERS WHERE RESET_CODE = :RESET_CODE  AND username = :username";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":RESET_CODE", $reset_code);
		$stmt->bindParam(":username", $username);
		$exec = $stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$res = $stmt->fetch();
		
		echo json_encode(array("RESPONSE" => $exec, "user_id" => $res['user_id']));
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
	private function clearResetCode($user_id){
		include('DbConnection.php');
		$sql = "UPDATE USERS SET RESET_CODE = null WHERE user_id = :user_id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":user_id", $user_id);
		$stmt->execute();
	}
	
	/*
	* Set a new password based on the user input
	* then clear the password reset code. 
	*/
	private function setNewpassword(){
		include('DbConnection.php');
		$user_id = filter_input(INPUT_POST, 'user_id');
		$password = filter_input(INPUT_POST, 'password');
		
		$sql = "UPDATE USERS SET password = md5(:password) WHERE user_id = :user_id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":password", $password);
		$stmt->bindParam(":user_id", $user_id);
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
	private function setpasswordResetCode(){
		include('DbConnection.php');
		
		$username = filter_input(INPUT_POST, 'email');
		$reset_code = $this->generateCode(0, 1000);
		
		$sql = "UPDATE USERS SET RESET_CODE = :RESET_CODE WHERE username = :username";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":RESET_CODE", $reset_code);
		$stmt->bindParam(":username", $username);
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
	private function getphoneNumber($email){
		include('DbConnection.php');
		$sql = 'SELECT phone FORM USERS WHERE email = :email';
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":email");
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$results = $stmt->fetch();
		if($results){
			return $results['phone'];
		}else{
			return false;
		}
	}	
	/*
	* Send the user a password reset notificaiton email with the reset code. 
	*/
	private function sendResetCodeNotification($email, $reset_code){
		$to = $email;

		$subject = "Utterfare password Reset";
		$headers = "From: donotreply@utterfare.com" . "\r\n";
		$headers .= "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1" . "\r\n";
		
		$message = "password Reset Code: " . $reset_code;
		$message .= "<br/><br/>";
		$message .= "If you duser_id not request a password reset please ignore this email or contact customer service at <a href='mailto:customer.service@utterfare.com?suject=User Account Support Request'>customer.service@utterfare.com</a>.";
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
	private function resetpasswordRequest(){
		$username = filter_input(INPUT_POST, 'email');
		if($this->checkUserExists($username)){
			$password_reset_code = $this->setpasswordResetCode();
			if($password_reset_code != null){
				$this->sendResetCodeNotification($username, $password_reset_code);
			}else{
				echo json_encode(array('SUCCESS' => FALSE, 'RESPONSE' => 'Failed to generate reset code'));
			}
		}else{
			echo json_encode(array('SUCCESS' => FALSE, 'RESPONSE' => 'username/email address does not exist.'));
		}
	}
	
	/*
	* This signs the user in and returns an user_id
	*/
	private function signIn(){
		include('DbConnection.php');
		
		$username = filter_input(INPUT_POST, 'username');
		$password = filter_input(INPUT_POST, 'password');
		
		$sql = "SELECT user_id FROM all_users WHERE username = :username AND password = MD5(:password)";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":username", $username);
		$stmt->bindParam(":password", $password);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$results = $stmt->fetch();
		
		$response = null;
		if($stmt->rowCount() > 0){
			$response = array(
				'user_id' 		=> $results['user_id'],
				'response' 	=> 'SUCCESS'
			);
		}else{
			$response = array(
				'user_id' 		=> 0,
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
		include('DbConnection.php');
		$sql = "SELECT user_id FROM all_users WHERE username = :username";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":username", $username);
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
		include('DbConnection.php');
		$email = filter_input(INPUT_POST, 'email');
		$cell_phone = filter_input(INPUT_POST, 'cell_phone');
		$first_name = filter_input(INPUT_POST, 'first_name');
		$last_name = filter_input(INPUT_POST, 'last_name');
		$city =  filter_input(INPUT_POST, 'city');
		$state  = filter_input(INPUT_POST, 'state');
		$postal_code = filter_input(INPUT_POST, 'postal_code');
		$gender = filter_input(INPUT_POST, 'gender');
		$birthday = filter_input(INPUT_POST, 'birthday');
		$password = filter_input(INPUT_POST, 'password');
		$date_registered = date("Y-m-d H:i:s");
		
		if(!$this->checkUserExists($username)){
			$sql = "INSERT INTO all_users(username, password, email, telephone_number, first_name, last_name, city, state, postal_code, gender, birthday, user_since) VALUES(:username, MD5(:password), :email, :telephone_number, :first_name, :last_name, :city, :state, :postal_code, :gender, :birthday, :user_since)";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":username", $email);
			$stmt->bindParam(":password", $password);
			$stmt->bindParam(":email", $email);
			$stmt->bindParam(":telephone_number", $cell_phone);
			$stmt->bindParam(":first_name", $first_name);
			$stmt->bindParam(":last_name", $last_name);
			$stmt->bindParam(":city", $city);
			$stmt->bindParam(":state", $state);
			$stmt->bindParam(":postal_code", $postal_code);
			$stmt->bindParam(":gender", $gender);
			$stmt->bindParam(":birthday", $birthday);
			$stmt->bindParam(":user_since", $date_registered);
			$stmt->execute();
			
			$num_rows = $stmt->rowCount();
			
			if($num_rows > 0){
				$response = array(
					"SUCCESS" 	=> true,
					"user_id"		=> $conn->lastInsertId(),
					'RESPONSE'	=> "SUCCESS"
				);
				$this->notifyNewUser($email);
			}else{
				$response = array(
					"SUCCESS" 	=> false,
					"user_id" 		=> "",
					"RESPONSE"	=> "FAILURE"
				);
			}
			echo json_encode($response);
		}else{
			$response = array(
				'SUCCESS' 	=> false, 
				"user_id"		=> "",
				"RESPONSE" 	=> "username already exists."
			);
			echo json_encode($response);
		}
	}
}
new Users();
	