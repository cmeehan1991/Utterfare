<?php 
	
class UserData{
	
	public function __construct(){
		
		$action = filter_input(INPUT_POST, 'action');
		$this->do_action($action);
	}
	
	public function do_action($action){
		switch($action){
			case 'new_user_survey':
			$this->new_user_survey();
			break;
			default: 
			break;
		}
	}
	
	public function new_user_survey(){
		include 'DbConnection.php';
		
		$referral = filter_input(INPUT_POST, 'referral');
		$explanation = filter_input(INPUT_POST, 'explain');
		
		$sql = "INSERT INTO REFERRAL_DATA(REFERRAL, EXPLANATION) VALUES(:REFERRAL, :EXPLANATION)";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":REFERRAL", $referral);
		$stmt->bindParam(":EXPLANATION", $explanation);
		$res = $stmt->execute();				
	}
}
new UserData();