<?php
class UF_Sessions{
	
	function __construct(){
		session_start();
		
		$this->userSession();
		
		$this->vendorSession();
	}
	
	private function generateSessionId(){
		return session_regenerate_id();
	}
	
	
	private function userSession(){
		$session_id = session_regenerate_id();
		
		$user_is_signed_in = $_SESSION['UF_USER_SIGNED_IN'];
		
		if($is_signed_in){
			$user_id = $_SESSION['UF_USER_ID'];
		}

	}
	
	
	private function vendorSession(){
		$vendor_is_signed_in = $_SESSION['UF_VENDER_SIGNED_IN'];

		if($vendor_is_signed_in){
			$vendor_id = $_SESSION['UF_VENDOR_ID'];
		}
	}
	
}//new UF_Sessions();



