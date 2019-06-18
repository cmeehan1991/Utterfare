<?php
class SingleItem{
	function __construct(){
		$action = filter_input(INPUT_POST, 'action');

		switch($action){
			case 'get_item_ratings':
				$this->getItemRatings();
				break;
			case 'get_item_reviews': 
				$this->getItemReviews();
				break;
			case 'get_vendor_items':
				$this->getVendorItems();
				break;
			default: break;
		}
		
	}
	
	
	private function getItemsRatings(){
		include 'DbConnection.php';
		
		$item_id = filter_input(INPUT_POST, 'item_id');
		
		$sql = "SELECT review_id, rating FROM item_reviews WHERE item_id = $item_id";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->execute();
		
		echo json_encode($stmt->fetchAll());		
	}
	
	private function getItemReviews(){
		include 'DbConnection.php';
		
		$item_id = filter_input(INPUT_POST, 'item_id');
		
		$sql = "SELECT review_id, review_title, review_text, user_id, review_date FROM item_reviews WHERE item_id = $item_id";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->execute();
		
		return json_encode($stmt->fetchall());
	}
	
	private function getVendorItems(){
		include 'DbConnection.php';
		
		$item_id = filter_input(INPUT_POST, 'item_id');
		
		
		$vendor_id = $this->getVendorId($item_id);
		
		$sql = "SELECT item_id, item_name, item_short_description, primary_image FROM menu_items WHERE vendor_id = $vendor_id AND item_id != $item_id LIMIT 10;";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->execute();
		
		echo json_encode($stmt->fetchAll());
		
		
	}
	
	private function getVendorId($item_id){
		include 'DbConnection.php';
				
		$sql = "SELECT vendor_id FROM menu_items WHERE item_id = $item_id";
		
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		$vendor_id = $stmt->fetch()['vendor_id'];
		
		
		return $vendor_id;
		
	}
	
	
}new SingleItem();