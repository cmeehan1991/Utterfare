<?php 

class UserItems{
	function __construct(){
		$action = filter_input(INPUT_POST, 'action');
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
		include('DbConnection.php');	
		include('search.php');
		
		$search = new Item_search();
		
		$user_id = filter_input(INPUT_POST, 'user_id');
		$sql = "SELECT user_item_id, user_items.item_id, menu_items.item_name, menu_items.item_short_description, menu_items.primary_image, vendors.vendor_id, vendors.vendor_name 
		FROM user_items 
		INNER JOIN menu_items ON menu_items.item_id = user_items.item_id 
		INNER JOIN vendors ON menu_items.vendor_id = vendors.vendor_id 
		WHERE user_id = :user_id ORDER BY DATE_SAVED DESC";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":user_id", $user_id);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		
		
		$results = $stmt->fetchAll();
		
		for($i = 0; $i < count($results); $i++){		
				
			
			if(!$search->check_image($results[$i]['primary_image'], $results[$i]['vendor_id']) || strpos($search->check_image($results[$i]['primary_image'], $results[$i]['vendor_id']), '.png') === false){
				$results[$i]['primary_image'] = "https://www.utterfare.com/assets/img/UF%20Logo.png";
			}else{
				$results[$i]['primary_image'] = $search->check_image($results[$i]['primary_image'], $results[$i]['vendor_id']);
			}
		}		
		
		
		echo json_encode($results);
	}
		
	/**
	* Remove an item from the user's saved items
	*/
	private function removeItem(){
		include('DbConnection.php');	
		
		$user_id = filter_input(INPUT_POST, 'user_id');
		$item_id = filter_input(INPUT_POST, 'item_id');
		$user_item_id = filter_input(INPUT_POST, 'user_item_id');
		
		$sql = "DELETE FROM user_items WHERE user_id = :user_id AND item_id = :item_id AND user_item_id = :user_item_id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":user_id", $user_id);
		$stmt->bindParam(":item_id", $item_id);
		$stmt->bindParam(":user_item_id", $user_item_id);
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
		include('DbConnection.php');
		
		$user_id = filter_input(INPUT_POST, 'user_id');
		$item_id = filter_input(INPUT_POST, 'item_id');
		
		$results = array();
		
		$saved = $this->checkForItem($user_id, $item_id);
		
		if(!$saved){
			$sql = "INSERT INTO user_items(user_id, item_id) VALUES(:user_id, :item_id)";
			
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(":user_id", $user_id);
			$stmt->bindParam(":item_id", $item_id);
			
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
	private function checkForItem($user_id, $item_id){
		include('DbConnection.php');
		
		$sql = "SELECT user_item_id FROM user_items WHERE user_id = :user_id AND item_id = :item_id";
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(":user_id", $user_id);
		$stmt->bindParam(":item_id", $item_id);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$res = $stmt->fetch();	
		
		return count($res['ID']) > 0;
	}
	
	
}new UserItems();

