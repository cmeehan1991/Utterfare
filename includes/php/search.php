<?php
class Item_Search{
	private $terms, $location, $page, $offset, $distance, $type, $platform, $search_id, $search_analytics;
	function __construct(){
		$this->init();
	}
	
	private function init(){
		$action = filter_input(INPUT_POST, 'action');
		
		
		switch($action){
			case 'get_recommendations': 
				$this->getRecommendations();
				break;
			case 'search':
				$this->doSearch();
				break;
			case 'getSingleItem': 
				$this->getSingleItem();
				break;
			default: break;
		}
	}	
	
	
	private function getSingleItem(){
		include 'DbConnection.php';
		$item_id = filter_input(INPUT_POST, 'item_id');
		
		$sql = "SELECT item_name, item_description, primary_image, menu_items.vendor_id, latitude, longitude FROM menu_items INNER JOIN vendors ON vendors.vendor_id = menu_items.vendor_id WHERE item_id = ?";
		
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(1, $item_id);
		
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$result = $stmt->fetch();
		
		$result['address'] =  $this->get_vendor_meta($result['vendor_id'], '_address');
		$result['telephone'] =  $this->get_vendor_meta($result['vendor_id'], '_telephone');
		
		echo json_encode($result);
		
	}
	
	private function doSearch(){
		$distance = filter_input(INPUT_POST, 'distance');
		$location = filter_input(INPUT_POST, 'location');
		$terms = filter_Input(INPUT_POST, 'terms');
		$limit = filter_input(INPUT_POST, 'limit');
		$page = filter_input(INPUT_POST, 'page');
		$offset = filter_input(INPUT_POST, 'offset');
				
		// Form the search location
		$search_location = urlencode($location);
		$json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$search_location&key=AIzaSyBNOJbx_2Q5h8f0ONZ4Abf5ULE0w4B-VTc");
		$obj = json_decode($json, true);
		
		// Get the latitude and longitude
        $lat = $obj['results'][0]['geometry']['location']['lat'];
        $lng = $obj['results'][0]['geometry']['location']['lng'];
        
        //$this->addRecentSearch($terms);
        
        echo $this->search($distance, $lat, $lng, $limit, $page, $offset, $terms);
	}
	
	private function getTopItems(){
		include 'DbConnection.php';
		
		$sql = "SELECT DISTINCT item_id, item_name, item_short_description, primary_image, SUM(item_reviews.rating) AS 'rating' FROM menu_items INNER JOIN item_reviews ON item_reviews.item_id = menu_items.item_id ORDER BY rand() LIMIT 3";
	}
	
	/*
	* Get top 8 recommended items based on the user's previous searches, location
	*/
	private function getRecommendations(){
		$location = filter_input(INPUT_POST, 'location');				
		$distance = 10;
		
		// Form the search location
		$search_location = urlencode($location);
		$json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$search_location&key=AIzaSyBNOJbx_2Q5h8f0ONZ4Abf5ULE0w4B-VTc");
		$obj = json_decode($json, true);
	
		// Get the latitude and longitude
        $lat = $obj['results'][0]['geometry']['location']['lat'];
        $lng = $obj['results'][0]['geometry']['location']['lng'];
        
       
		echo $this->search(10, $lat, $lng, 8);
	}
	
	
	
	private function search($distance = 10, $latitude = null, $longitude = null, $ppp = null, $page = 1, $offset = 0, $terms = null){
		include 'DbConnection.php';
				
		$sql = "SELECT DISTINCT item_id, item_name, item_short_description, primary_image, vendors.vendor_id, vendors.vendor_name, md5(vendors.vendor_name) as 'name_hash', vendors.latitude, vendors.longitude
		FROM menu_items 
		INNER JOIN vendors ON vendors.vendor_id = menu_items.vendor_id
		INNER JOIN vendor_meta ON vendor_meta.vendor_id = vendors.vendor_id
		WHERE DEGREES((ACOS(SIN(RADIANS($latitude))*SIN(RADIANS(vendors.latitude))+ COS(RADIANS($latitude))*COS(RADIANS(vendors.latitude))*COS(RADIANS((vendors.longitude)-($longitude)))))) * 60 * 1.1515 <= $distance";

		if($terms){
			$sql .= " AND (item_name = '$terms' OR item_name like '%$terms%' OR item_short_description = '$terms' OR item_short_description LIKE '%$terms%' OR item_description = '$terms' OR item_description LIKE '%$terms%' OR vendor_name = '$terms' OR vendor_name LIKE '%$terms%')";
		}
	
		
		$sql .= " LIMIT $ppp OFFSET $offset";	
		
		
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$results = $stmt->fetchall();
		
		for($i = 0; $i < count($results); $i++){
			$results[$i]['address'] =  $this->get_vendor_meta($results[$i]['vendor_id'], '_address');
			$results[$i]['profile_picture'] = $this->get_vendor_meta($results[$i]['vendor_id'], '_profile_picture');
		}
				
		return json_encode($results);
	}
	
	private function get_vendor_meta($vendor_id, $keyword = null){
		include 'DbConnection.php';
		
		
		$sql = "SELECT meta_value FROM vendor_meta WHERE vendor_id = ?";
		
		if($keyword){
			$sql .= " AND meta_keyword = ?";
		}
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindParam(1, $vendor_id);
		
		if($keyword){
			$stmt->bindParam(2, $keyword);
		}
		
		$stmt->execute();
		
		$stmt->setFetchMode(PDO::FETCH_ASSOC);	
		$results = $stmt->fetch();
		
		
		if($keyword){
			$results = array($keyword => $results['meta_value']);
		};
		
		return json_encode($results);
		
		
	}
	
	/*
	private addRecentSearch($terms){
		if(isset($_SESSION['RECENT_SEARCHES'])){
			$recent_searches = $_SESSION['RECENT_SEARCHES'];
			
			// Check for the search
			$key = array_search($terms, $recent_searches);
			
			if($key){
				unset($recent_searches[$key]);
			}
			
			// Add the search to the top of the list. 
			array_push($recent_searches, $terms);
			
		}else{
			$_SESSION['RECENT_SEARCHES'] = array($terms);
		}
	}
	*/
	
} new Item_search();