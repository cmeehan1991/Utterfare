<?php 

class UserItems{
	function __construct(){
		$action = 'get_items'; //filter_input(INPUT_POST, 'action');
		$this->init($action);
	}
	
	private function init($action){
		switch($action){
			case "get_items":
			$this->getItems();
			break;
			case "remove_item":
			$this->removeItem();
			break;
			case "add_item": 
			$this->addItem();
			default: break;
		}
	}
	
	/**
	* Get the user's saved items 
	*/
	private function getItems(){
		include('../connection/DBConnection.php');	
		
		$user_id = '1'; //filter_input(INPUT_POST, 'user_id');
		$sql = "SELECT ID, ITEM_ID, ITEM_DATA_TABLE, ITEM_NAME, ITEM_IMAGE_URL FROM SAVED_ITEMS WHERE USER_ID = :USER_ID ORDER BY DATE_SAVED DESC";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":USER_ID", $user_id);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$results = $stmt->fetchAll();
		
		echo json_encode($results);
	}
	
	/**
	* Remove an item from the user's saved items
	*/
	private function removeItem(){
		include('../connection/DBConnection.php');	
		
		$user_id = filter_input(INPUT_POST, 'user_id');
		$item_id = filter_input(INPUT_POST, 'item_id');
		$item_data_table = filter_input(INPUT_POST, 'data_table');
		
		$sql = "DELETE FROM SAVED_ITEMS WHERE USER_ID = :USER_ID AND ITEM_ID = :ITEM_ID AND ITEM_DATA_TABLE = :ITEM_DATA_TABLE";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":USER_ID", $user_id);
		$stmt->bindParam(":ITEM_ID", $item_id);
		$stmt->bindParam(":ITEM_DATA_TABLE", $item_data_table);
		$exec = $stmt->execute();
		
		$response = array();
		if($exec){
			$response['STATUS'] = true;
			$response['RESPONSE'] = '';
		}else{
			$response['STATUS'] = false;
			$response['RESPONSE'] = 'Failed to remove item.';
		}
		
		echo json_encode($response);
	}
	
	/**
	* Add an item to the user's favorites. 
	*/
	private function addItem(){
		include('../connection/DBConnection.php');
		
		$user_id = filter_input(INPUT_POST, 'user_id');
		$item_id = filter_input(INPUT_POST, 'item_id');
		$item_name = filter_input(INPUT_POST, 'item_name');
		$item_data_table = filter_input(INPUT_POST, 'data_table');
		$item_image = filter_input(INPUT_POST, 'item_image_url');
		
		$results = array();
		$saved = $this->checkForItem($user_id, $item_id, $item_data_table);
		if(!$saved){
			$sql = "INSERT INTO SAVED_ITEMS(USER_ID, ITEM_ID, ITEM_DATA_TABLE, ITEM_NAME, ITEM_IMAGE_URL) VALUES(:USER_ID, :ITEM_ID, :ITEM_DATA_TABLE, :ITEM_NAME, :ITEM_IMAGE_URL)";
			
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":USER_ID", $user_id);
			$stmt->bindParam(":ITEM_ID", $item_id);
			$stmt->bindParam(":ITEM_DATA_TABLE", $item_data_table);
			$stmt->bindParam(":ITEM_NAME", $item_name);
			$stmt->bindParam(":ITEM_IMAGE_URL", $item_image);
			$exec = $stmt->execute();
			$results['STATUS'] = $exec;
			$results['RESPONSE'] = '';
		}else{
			$results['STATUS'] = false;
			$results['RESPONSE'] = 'You have already saved this item.';
		}
		
		echo json_encode($results);
	}
	
	/**
	* Check to see if the item exists before it is added.
	*/
	private function checkForItem($user_id, $item_id, $item_data_table){
		include('../connection/DBConnection.php');
		
		$sql = "SELECT ID FROM SAVED_ITEMS WHERE USER_ID = :USER_ID AND ITEM_ID = :ITEM_ID AND ITEM_DATA_TABLE = :ITEM_DATA_TABLE";
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(":USER_ID", $user_id);
		$stmt->bindParam(":ITEM_ID", $item_id);
		$stmt->bindParam(":ITEM_DATA_TABLE", $item_data_table);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$res = $stmt->fetch();	
		
		return count($res['ID']) > 0;
	}
	
	
}new UserItems();

